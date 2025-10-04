<?php
namespace Src\Controllers;

use Src\Response;
use Src\Models\User;
use Src\Models\Drink;

class UserController {
    private $pdo;
    private $userModel;
    private $drinkModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->drinkModel = new Drink($pdo);
    }

    public function create($body) {
        if (empty($body['email']) || empty($body['name']) || empty($body['password'])) {
            return Response::json(['error'=>'Missing fields'], 400);
        }
        // check exist
        if ($this->userModel->findByEmail($body['email'])) {
            return Response::json(['error'=>'User already exists'], 409);
        }
        $id = $this->userModel->create($body['email'], $body['name'], $body['password']);
        return Response::json(['iduser'=>$id, 'email'=>$body['email'], 'name'=>$body['name']], 201);
    }

    public function get($id) {
        $u = $this->userModel->findById($id);
        if (!$u) return Response::json(['error'=>'Not found'], 404);
        // drink count
        $count = (int)$this->drinkModel->totalByUser($id);
        $u['drinkCounter'] = $count;
        return Response::json($u);
    }

    public function list($query) {
        $page = isset($query['page']) ? max(1, (int)$query['page']) : 1;
        $per = isset($query['per']) ? max(1, (int)$query['per']) : 20;
        $offset = ($page - 1) * $per;
        $data = $this->userModel->list($per, $offset);
        return Response::json(['page'=>$page,'per'=>$per,'data'=>$data]);
    }

    public function update($id, $body, $authUserId) {
        if ($authUserId != $id) return Response::json(['error'=>'Forbidden'], 403);
        $name = $body['name'] ?? null;
        $password = $body['password'] ?? null;
        $this->userModel->update($id, $name, $password);
        return Response::json(['success'=>true]);
    }

    public function delete($id, $authUserId) {
        if ($authUserId != $id) return Response::json(['error'=>'Forbidden'], 403);
        $this->userModel->delete($id);
        return Response::json(['success'=>true]);
    }

    public function addDrink($id, $body, $authUserId) {
        if ($authUserId != $id) return Response::json(['error'=>'Forbidden'], 403);
        $qty = isset($body['drink']) ? (int)$body['drink'] : 1;
        if ($qty < 1) $qty = 1;
        $this->drinkModel->add($id, $qty);
        $total = (int)$this->drinkModel->totalByUser($id);
        return Response::json(['iduser'=>$id, 'drinkCounter'=>$total]);
    }
}
?>