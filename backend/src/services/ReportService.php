<?php
namespace Src\Services;

use Src\Models\Drink;
use Src\Response;

class ReportService {
    private $drinkModel;
    public function __construct($pdo) { $this->drinkModel = new Drink($pdo); }

    public function history($userId) {
        return $this->drinkModel->historyByUser($userId);
    }

    public function rankingByDate($date) {
        return $this->drinkModel->rankingByDate($date);
    }

    public function rankingLastDays($days) {
        return $this->drinkModel->rankingLastDays($days);
    }
}
?>