<?php
namespace Src\Models;

class Drink {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function add($userId, $quantity = 1) {
        $stmt = $this->pdo->prepare('INSERT INTO drinks (user_id, quantity) VALUES (:uid, :qty)');
        $stmt->execute([':uid' => $userId, ':qty' => (int)$quantity]);
        return $this->pdo->lastInsertId();
    }

    public function totalByUser($userId) {
        $stmt = $this->pdo->prepare('SELECT IFNULL(SUM(quantity),0) AS total FROM drinks WHERE user_id = :uid');
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetchColumn();
    }

    // history per day
    public function historyByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT DATE(created_at) as date, SUM(quantity) as times FROM drinks WHERE user_id = :uid GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC");
        $stmt->execute([':uid'=>$userId]);
        return $stmt->fetchAll();
    }

    // ranking for a specific day
    public function rankingByDate($date) {
        $stmt = $this->pdo->prepare("SELECT u.name, SUM(d.quantity) as times FROM drinks d JOIN users u ON u.id = d.user_id WHERE DATE(d.created_at) = :date GROUP BY u.id ORDER BY times DESC LIMIT 100");
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll();
    }

    // ranking last X days
    public function rankingLastDays($days) {
        $stmt = $this->pdo->prepare("SELECT u.name, SUM(d.quantity) as times FROM drinks d JOIN users u ON u.id = d.user_id WHERE d.created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY) GROUP BY u.id ORDER BY times DESC LIMIT 100");
        $stmt->bindValue(':days', (int)$days, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>