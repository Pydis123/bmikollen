<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AuthMiddleware, CsrfMiddleware};
use App\Repositories\{DayLogRepository, WeightRepository};

class ExportController {
    public function show(): string {
        AuthMiddleware::check();
        $view = Container::get('view');
        return $view->render('export/index', ['title' => 'Exportera data']);
    }

    public function export(): void {
        AuthMiddleware::check();
        CsrfMiddleware::verify();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $logsRepo = new DayLogRepository($pdo);
        $weightsRepo = new WeightRepository($pdo, $crypto);

        $userId = (int)$_SESSION['user_id'];
        $dates = $_POST['dates'] ?? [];
        $from = $_POST['from'] ?? null;
        $to = $_POST['to'] ?? null;

        // Build date set
        $dateSet = [];
        if ($from && $to) {
            $start = new \DateTime($from);
            $end = new \DateTime($to);
            while ($start <= $end) {
                $dateSet[] = $start->format('Y-m-d');
                $start->modify('+1 day');
            }
        }
        foreach ($dates as $d) {
            if (!in_array($d, $dateSet, true)) $dateSet[] = $d;
        }
        sort($dateSet);

        $filename = 'bmikollen_export_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        $out = fopen('php://output', 'w');

        // Section 1: DAILY_SUMMARY
        fputcsv($out, ['DAILY_SUMMARY']);
        fputcsv($out, ['date', 'kcal_total', 'protein_total', 'steps_total', 'weight_kg']);
        foreach ($dateSet as $date) {
            $tot = $logsRepo->getDailyTotals($userId, $date);
            $w = $weightsRepo->getForDate($userId, $date);
            fputcsv($out, [$date, $tot['kcal'], $tot['protein'], $tot['steps'], $w !== null ? number_format($w, 2, '.', '') : '']);
        }

        // Blank line
        fputcsv($out, []);

        // Section 2: DAY_LOGS
        fputcsv($out, ['DAY_LOGS']);
        fputcsv($out, ['date', 'time', 'kcal_delta', 'protein_delta', 'steps_delta', 'label', 'source']);
        foreach ($dateSet as $date) {
            $rows = $logsRepo->getByDate($userId, $date);
            foreach ($rows as $r) {
                fputcsv($out, [$r['date'], $r['time'], $r['kcal_delta'], $r['protein_delta'], $r['steps_delta'], $r['label'], $r['source']]);
            }
        }

        fclose($out);
        Container::get('logger')->security('export-triggered', ['user_id' => $userId]);
        exit;
    }
}
