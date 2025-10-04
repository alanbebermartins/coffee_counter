<?php
namespace Src\Controllers;

use Src\Database;
use Src\Response;
use Src\Models\User;

class AuthController {
    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function login($body) {
        if (empty($body['email']) || empty($body['password'])) {
            return Response::json(['error' => 'Email and password required'], 400);
        }
        $u = $this->userModel->findByEmail($body['email']);
        if (!$u) return Response::json(['error' => 'User does not exist'], 404);
        if (!$this->userModel->validatePassword($body['password'], $u['password'])) {
            return Response::json(['error' => 'Invalid password'], 401);
        }
        // create token
        $token = bin2hex(random_bytes(16));
        $this->userModel->setToken($u['id'], $token);
        // count drinks
        $drinkModel = new \Src\Models\Drink($this->pdo);
        $count = (int)$drinkModel->totalByUser($u['id']);
        return Response::json(['token'=>$token, 'iduser'=>$u['id'], 'email'=>$u['email'], 'name'=>$u['name'], 'drinkCounter'=>$count]);
    }
}
?>