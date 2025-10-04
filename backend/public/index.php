<?php
// HEADERS CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// SERVIR ARQUIVOS ESTÁTICOS DO FRONTEND
$frontendDir = __DIR__ . '/../../frontend';
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$assetFile = realpath($frontendDir . $requestPath);
if ($assetFile && is_file($assetFile)) {
    $ext = pathinfo($assetFile, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2'
    ];
    $mime = $mimeTypes[$ext] ?? 'text/plain';
    header("Content-Type: $mime");
    readfile($assetFile);
    exit;
}

// AUTLOAD
spl_autoload_register(function($class){
    $base = __DIR__ . '/../src/';
    $class = preg_replace('#^Src\\\\#', '', $class);
    $class = str_replace('\\', '/', $class) . '.php';
    $file = $base . $class;
    if (file_exists($file)) require $file;
});

// USE CLASSES
use Src\Database;
use Src\Response;
use Src\Router;
use Src\Controllers\AuthController;
use Src\Controllers\UserController;
use Src\Services\ReportService;

// CONFIGURAÇÃO DO BANCO
$config = require __DIR__ . '/../config.php';
$pdo = Database::getConnection($config);

// SERVE FRONTEND QUANDO RAIZ
$uri = rtrim($_SERVER['REQUEST_URI'], '/');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($uri === '' || $uri === '/')) {
    $frontendIndex = __DIR__ . '/../../frontend/index.html';
    if (file_exists($frontendIndex)) {
        readfile($frontendIndex);
        exit;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Frontend not found";
        exit;
    }
}

// INICIALIZA ROUTER
$router = new Router();
$method = $router->method();
$body = $router->body();

// AUTENTICAÇÃO POR TOKEN
$headers = getallheaders();
$token = $headers['Authorization'] ?? ($headers['authorization'] ?? null);
if ($token && strpos($token, 'Bearer ') === 0) $token = substr($token, 7);

function getUserIdByToken($pdo, $token) {
    if (!$token) return null;
    $stmt = $pdo->prepare('SELECT id FROM users WHERE token = :token');
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch();
    return $row ? (int)$row['id'] : null;
}

$authUserId = getUserIdByToken($pdo, $token);

// ROTAS

// POST /login
if ($method === 'POST' && $uri === '/login') {
    $c = new AuthController($pdo);
    $c->login($body);
    exit;
}

// POST /users
if ($method === 'POST' && $uri === '/users') {
    $c = new UserController($pdo);
    $c->create($body);
    exit;
}

// GET /users (list with paging)
if ($method === 'GET' && $uri === '/users') {
    $c = new UserController($pdo);
    $c->list($_GET);
    exit;
}

// GET /users/{id}
if ($method === 'GET' && preg_match('#^/users/(\d+)$#', $uri, $m)) {
    $c = new UserController($pdo);
    $c->get((int)$m[1]);
    exit;
}

// PUT /users/{id}
if ($method === 'PUT' && preg_match('#^/users/(\d+)$#', $uri, $m)) {
    $c = new UserController($pdo);
    $c->update((int)$m[1], $body, $authUserId);
    exit;
}

// DELETE /users/{id}
if ($method === 'DELETE' && preg_match('#^/users/(\d+)$#', $uri, $m)) {
    $c = new UserController($pdo);
    $c->delete((int)$m[1], $authUserId);
    exit;
}

// POST /users/{id}/drink
if ($method === 'POST' && preg_match('#^/users/(\d+)/drink$#', $uri, $m)) {
    $c = new UserController($pdo);
    $c->addDrink((int)$m[1], $body, $authUserId);
    exit;
}

// GET /users/{id}/history
if ($method === 'GET' && preg_match('#^/users/(\d+)/history$#', $uri, $m)) {
    $svc = new ReportService($pdo);
    $data = $svc->history((int)$m[1]);
    Response::json($data);
    exit;
}

// GET /reports/ranking?date=YYYY-MM-DD ou ?last_days=N
if ($method === 'GET' && $uri === '/reports/ranking') {
    $date = $_GET['date'] ?? null;
    $days = $_GET['last_days'] ?? null;
    $svc = new ReportService($pdo);

    if ($date) {
        Response::json($svc->rankingByDate($date));
    } elseif ($days) {
        Response::json($svc->rankingLastDays((int)$days));
    } else {
        Response::json(['error'=>'Provide date=YYYY-MM-DD or last_days=N'], 400);
    }
    exit;
}

// LISTA TODOS OS USUARIOS E SUAS RESPECTIVAS QUANTIDADES

if ($method === 'GET' && $uri === '/public/users/history') {
    $stmt = $pdo->query('SELECT id, name, email FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    foreach ($users as $user) {
        $stmt2 = $pdo->prepare('SELECT created_at AS date, quantity AS drinkCounter FROM drinks WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt2->execute([':uid' => $user['id']]);
        $history = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $result[] = [
            'name' => $user['name'],
            'email' => $user['email'],
            'history' => $history
        ];
    }

    Response::json($result);
    exit;
}

// DEFAULT 404
Response::json(['error'=>'Not found'], 404);
?>