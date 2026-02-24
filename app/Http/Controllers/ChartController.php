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

        // Filterparametrar
        $range = $_GET['range'] ?? '30d'; // 30d|90d|1y|plan|custom
        $agg = $_GET['agg'] ?? 'daily';   // daily|weekly
        $startParam = $_GET['start'] ?? null;
        $endParam = $_GET['end'] ?? null;

        $today = new \DateTime('today');
        $plan = $plansRepo->getActiveForUser($userId);

        // För UI: alltid beräkna planens start/slut (slut = min(target_end, idag))
        $planStartUi = null; $planEndUi = null;
        if ($plan && !empty($plan['start_date'])) {
            $planStartUi = $plan['start_date'];
            $tmpEnd = !empty($plan['target_end_date']) ? new \DateTime($plan['target_end_date']) : clone $today;
            $planEndUi = $tmpEnd->format('Y-m-d');
        }

        // Beräkna start och slutdatum
        $startDate = null; $endDate = null;
        switch ($range) {
            case '90d':
                $startDate = (clone $today)->modify('-89 days')->format('Y-m-d');
                $endDate = $today->format('Y-m-d');
                break;
            case '1y':
                $startDate = (clone $today)->modify('-364 days')->format('Y-m-d');
                $endDate = $today->format('Y-m-d');
                break;
            case 'plan':
                if ($plan && !empty($plan['start_date'])) {
                    $startDate = $plan['start_date'];
                    $planEnd = !empty($plan['target_end_date']) ? new \DateTime($plan['target_end_date']) : clone $today;
                    $endDate = $planEnd->format('Y-m-d');
                } else {
                    // Fallback till 30 dagar
                    $startDate = (clone $today)->modify('-29 days')->format('Y-m-d');
                    $endDate = $today->format('Y-m-d');
                    $range = '30d';
                }
                break;
            case 'custom':
                if ($startParam && $endParam) {
                    try {
                        $s = new \DateTime($startParam);
                        $e = new \DateTime($endParam);
                        if ($s <= $e) {
                            $startDate = $s->format('Y-m-d');
                            $endDate = $e->format('Y-m-d');
                            break;
                        }
                    } catch (\Exception $ex) { /* ignore, fall back below */ }
                }
                // Ogiltig custom -> fallback 30d
                $startDate = (clone $today)->modify('-29 days')->format('Y-m-d');
                $endDate = $today->format('Y-m-d');
                $range = '30d';
                break;
            case '30d':
            default:
                $startDate = (clone $today)->modify('-29 days')->format('Y-m-d');
                $endDate = $today->format('Y-m-d');
                $range = '30d';
                break;
        }

        // Hämta data inom intervallet (optimerad SQL)
        $totalsRaw = $dayLogs->getRangeTotalsBetween($userId, $startDate, $endDate);
        $weightsRaw = $weightsRepo->getRangeBetween($userId, $startDate, $endDate);

        // Bygg dagliga serier (fullt spann, inkl. luckor)
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
            $totals[] = $totalsMap[$date] ?? ['date' => $date, 'kcal' => null, 'protein' => null, 'steps' => null];
            $weights[] = $weightsMap[$date] ?? ['date' => $date, 'weight' => null];
            $current->modify('+1 day');
        }

        // Veckovis aggregering om begärt
        if ($agg === 'weekly') {
            $totalsByWeek = [];
            $weightsByWeek = [];
            foreach ($totals as $t) {
                $d = new \DateTime($t['date']);
                $weekStart = $d->modify('Monday this week')->format('Y-m-d');
                if (!isset($totalsByWeek[$weekStart])) {
                    $totalsByWeek[$weekStart] = [
                        'kcal_sum' => 0, 'kcal_has' => false,
                        'protein_sum' => 0, 'protein_has' => false,
                        'steps_sum' => 0, 'steps_has' => false,
                    ];
                }
                if ($t['kcal'] !== null) { $totalsByWeek[$weekStart]['kcal_sum'] += (int)$t['kcal']; $totalsByWeek[$weekStart]['kcal_has'] = true; }
                if ($t['protein'] !== null) { $totalsByWeek[$weekStart]['protein_sum'] += (int)$t['protein']; $totalsByWeek[$weekStart]['protein_has'] = true; }
                if ($t['steps'] !== null) { $totalsByWeek[$weekStart]['steps_sum'] += (int)$t['steps']; $totalsByWeek[$weekStart]['steps_has'] = true; }
            }
            foreach ($weights as $w) {
                if ($w['weight'] === null) continue;
                $d = new \DateTime($w['date']);
                $weekStart = $d->modify('Monday this week')->format('Y-m-d');
                if (!isset($weightsByWeek[$weekStart])) $weightsByWeek[$weekStart] = [];
                $weightsByWeek[$weekStart][] = (float)$w['weight'];
            }
            $weeklyTotals = [];
            $weeklyWeights = [];
            $firstMonday = (new \DateTime($startDate))->modify('Monday this week');
            $lastMonday = (new \DateTime($endDate))->modify('Monday this week');
            for ($d = clone $firstMonday; $d <= $lastMonday; $d->modify('+7 days')) {
                $ws = $d->format('Y-m-d');
                $wk = $totalsByWeek[$ws] ?? null;
                $weeklyTotals[] = [
                    'date' => $ws,
                    'kcal' => ($wk && $wk['kcal_has']) ? (int)$wk['kcal_sum'] : null,
                    'protein' => ($wk && $wk['protein_has']) ? (int)$wk['protein_sum'] : null,
                    'steps' => ($wk && $wk['steps_has']) ? (int)$wk['steps_sum'] : null,
                ];
                if (!empty($weightsByWeek[$ws])) {
                    $avg = array_sum($weightsByWeek[$ws]) / count($weightsByWeek[$ws]);
                    $weeklyWeights[] = ['date' => $ws, 'weight' => (float)$avg];
                } else {
                    $weeklyWeights[] = ['date' => $ws, 'weight' => null];
                }
            }
            $totals = $weeklyTotals;
            $weights = $weeklyWeights;
        }

        return $view->render('charts/index', [
            'totals' => $totals,
            'weights' => $weights,
            'plan' => $plan ? [
                'kcal_target' => $plan['kcal_target'],
                'protein_target' => $plan['protein_target'],
                'steps_target' => $plan['steps_target'],
                'weight_goal' => $plan['weight_goal'],
                'intensity_preset' => $plan['intensity_preset'] ?? null
            ] : null,
            'filters' => [
                'range' => $range,
                'agg' => $agg,
                'start' => $startDate,
                'end' => $endDate,
                'planStart' => $planStartUi,
                'planEnd' => $planEndUi,
            ],
            'title' => 'Grafer'
        ]);
    }
}
