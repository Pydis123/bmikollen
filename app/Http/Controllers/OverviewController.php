<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\AuthMiddleware;
use App\Repositories\{DayLogRepository, UserRepository, WeightRepository, PlanRepository};

class OverviewController {
    public function overview(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $view = Container::get('view');
        $users = new UserRepository($pdo);
        $logs = new DayLogRepository($pdo);
        $weights = new WeightRepository($pdo, $crypto);
        $plans = new PlanRepository($pdo, $crypto);

        $user = $users->findById((int)$_SESSION['user_id']);
        $tz = $user['timezone'] ?? 'Europe/Stockholm';
        $date = $_GET['date'] ?? (new \DateTime('now', new \DateTimeZone($tz)))->format('Y-m-d');

        $totals = $logs->getDailyTotals((int)$_SESSION['user_id'], $date);
        $weight = $weights->getForDate((int)$_SESSION['user_id'], $date);
        $plan = $plans->getActiveForUser((int)$_SESSION['user_id']);

        $heightM = !empty($user['height_cm']) ? ((float)$user['height_cm'] / 100.0) : null;
        $bmi = ($heightM && $weight) ? round($weight / ($heightM * $heightM), 1) : null;

        return $view->render('overview/index', compact('date','totals','weight','bmi', 'plan') + ['title' => 'Översikt']);
    }

    public function week(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $view = Container::get('view');
        $users = new UserRepository($pdo);
        $logs = new DayLogRepository($pdo);
        $weights = new WeightRepository($pdo, $crypto);
        $plans = new PlanRepository($pdo, $crypto);

        $user = $users->findById((int)$_SESSION['user_id']);
        $tz = $user['timezone'] ?? 'Europe/Stockholm';
        $dateStr = $_GET['date'] ?? (new \DateTime('now', new \DateTimeZone($tz)))->format('Y-m-d');
        $date = new \DateTime($dateStr, new \DateTimeZone($tz));

        // ISO week: Monday to Sunday
        $monday = clone $date; $monday->modify('Monday this week');
        $sunday = clone $monday; $sunday->modify('Sunday this week');

        $dates = [];
        for ($d = clone $monday; $d <= $sunday; $d->modify('+1 day')) {
            $dates[] = $d->format('Y-m-d');
        }

        $sum = ['kcal' => 0, 'protein' => 0, 'steps' => 0];
        $weightStart = $weights->getForDate((int)$_SESSION['user_id'], $dates[0]);
        $weightEnd = $weights->getForDate((int)$_SESSION['user_id'], end($dates));
        foreach ($dates as $d) {
            $t = $logs->getDailyTotals((int)$_SESSION['user_id'], $d);
            $sum['kcal'] += $t['kcal'];
            $sum['protein'] += $t['protein'];
            $sum['steps'] += $t['steps'];
        }

        $plan = $plans->getActiveForUser((int)$_SESSION['user_id']);

        // Beräkna förväntad viktnedgång per vecka utifrån intensitet
        $expectedWeeklyKg = null;
        if ($plan && !empty($plan['intensity_preset'])) {
            $intensityMap = [
                'gentle' => ['pct' => 0.0025, 'min' => 0.2, 'max' => 0.4],
                'normal' => ['pct' => 0.0050, 'min' => 0.4, 'max' => 0.8],
                'aggressive' => ['pct' => 0.0100, 'min' => 0.8, 'max' => 1.0],
            ];
            $i = $intensityMap[$plan['intensity_preset']] ?? $intensityMap['normal'];
            $refWeight = $weightStart ?? $weightEnd; // helst startvikt för veckan
            if ($refWeight !== null) {
                $expectedWeeklyKg = max($i['min'], min($i['max'], $i['pct'] * (float)$refWeight));
            } else {
                $expectedWeeklyKg = $i['min'];
            }
        }

        return $view->render('overview/week', [
            'monday' => $monday->format('Y-m-d'),
            'sunday' => $sunday->format('Y-m-d'),
            'sum' => $sum,
            'weight_start' => $weightStart,
            'weight_end' => $weightEnd,
            'plan' => $plan,
            'expected_weekly_kg' => $expectedWeeklyKg,
            'title' => 'Vecka'
        ]);
    }
}
