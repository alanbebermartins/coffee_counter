<?php
namespace Src\Models;

use Src\Database;

class User {
    private $pdo;

    public function __construct($pdo) { $this->pdo = $pdo; }

    public function create($email, $name, $password) {
        $sql = 'INSERT INTO users(email, name, password) VALUES(:email, :name, :password)';
        $stmt = $this->pdo->prepare($sql);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([':email'=>$email, ':name'=>$name, ':password'=>$hash]);
        return $this->pdo->lastInsertId();
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email'=>$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare('SELECT id AS iduser, email, name FROM users WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }

    public function setToken($id, $token) {
        $stmt = $this->pdo->prepare('UPDATE users SET token = :token WHERE id = :id');
        $stmt->execute([':token'=>$token, ':id'=>$id]);
    }

    public function validatePassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function update($id, $name = null, $password = null) {
        $parts = [];
        $params = [':id' => $id];
        if ($name !== null) { $parts[] = 'name = :name'; $params[':name'] = $name; }
        if ($password !== null) { $parts[] = 'password = :password'; $params[':password'] = password_hash($password, PASSWORD_DEFAULT); }
        if (!count($parts)) return false;
        $sql = 'UPDATE users SET ' . implode(', ', $parts) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function list($limit=20, $offset=0) {
        $stmt = $this->pdo->prepare('SELECT u.id AS iduser, u.email, u.name, IFNULL( (SELECT SUM(d.quantity) FROM drinks d WHERE d.user_id = u.id), 0) as drinkCounter FROM users u ORDER BY u.id LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>