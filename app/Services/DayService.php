<?php
namespace App\Services;

use App\Repositories\{DayLogRepository, PlanRepository, WeightRepository};
use App\Core\Logger;

class DayService {
    private DayLogRepository $logs;
    private PlanRepository $plans;
    private WeightRepository $weights;
    private Logger $logger;

    public function __construct(DayLogRepository $logs, PlanRepository $plans, WeightRepository $weights, Logger $logger) {
        $this->logs = $logs;
        $this->plans = $plans;
        $this->weights = $weights;
        $this->logger = $logger;
    }

    public function userDateToday(string $timezone): string {
        $dt = new \DateTime('now', new \DateTimeZone($timezone));
        return $dt->format('Y-m-d');
    }

    public function userTimeNow(string $timezone): string {
        $dt = new \DateTime('now', new \DateTimeZone($timezone));
        return $dt->format('H:i:s');
    }

    public function getDayData(int $userId, string $date): array {
        $logs = $this->logs->getByDate($userId, $date);
        $totals = $this->logs->getDailyTotals($userId, $date);
        $plan = $this->plans->getActiveForUser($userId);
        $weight = $this->weights->getForDate($userId, $date);
        return compact('logs','totals','plan','weight');
    }

    public function addDelta(int $userId, string $date, string $time, ?int $kcal, ?int $protein, ?int $steps, ?string $label, string $source = 'manual'): int {
        return $this->logs->add($userId, $date, $time, $kcal, $protein, $steps, $label, $source);
    }

    public function setWeight(int $userId, string $date, float $weightKg): void {
        $this->weights->setForDate($userId, $date, $weightKg);
    }
}
