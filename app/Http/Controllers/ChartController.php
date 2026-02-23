<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Repositories\{DayLogRepository, WeightRepository, PlanRepository};
use App\Http\Middleware\AuthMiddleware;

class ChartController {
    public function charts(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $view = Container::get('view');
        $userId = (int)$_SESSION['user_id'];

        $dayLogs = new DayLogRepository($pdo);
        $weightsRepo = new WeightRepository($pdo, $crypto);
        $plansRepo = new PlanRepository($pdo, $crypto);

        // Last 30 days
        $startDate = (new \DateTime('-29 days'))->format('Y-m-d');
        $endDate = (new \DateTime())->format('Y-m-d');

        $totalsRaw = $dayLogs->getRangeTotals($userId, $startDate);
        $weightsRaw = $weightsRepo->getRange($userId, $startDate);
        $plan = $plansRepo->getActiveForUser($userId);

        // Fill full range
        $totals = [];
        $weights = [];
        $current = new \DateTime($startDate);
        $until = new \DateTime($endDate . ' +1 day');

        $totalsMap = [];
        foreach ($totalsRaw as $t) { $totalsMap[$t['date']] = $t; }
        $weightsMap = [];
        foreach ($weightsRaw as $w) { $weightsMap[$w['date']] = $w; }

        while ($current < $until) {
            $date = $current->format('Y-m-d');
            $totals[] = $totalsMap[$date] ?? ['date' => $date, 'kcal' => 0, 'protein' => 0, 'steps' => 0];
            $weights[] = $weightsMap[$date] ?? ['date' => $date, 'weight' => null];
            $current->modify('+1 day');
        }

        return $view->render('charts/index', [
            'totals' => $totals,
            'weights' => $weights,
            'plan' => $plan ? [
                'kcal_target' => $plan['kcal_target'],
                'protein_target' => $plan['protein_target'],
                'steps_target' => $plan['steps_target'],
                'weight_goal' => $plan['weight_goal']
            ] : null,
            'title' => 'Grafer'
        ]);
    }
}
