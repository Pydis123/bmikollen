<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AuthMiddleware, CsrfMiddleware};
use App\Repositories\{WeightRepository, UserRepository};

class WeightController {
    public function setWeight(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $repo = new WeightRepository($pdo, $crypto);
        $userRepo = new UserRepository($pdo);
        $view = Container::get('view');
        $user = $userRepo->findById((int)$_SESSION['user_id']);
        $tz = $user['timezone'] ?? 'Europe/Stockholm';
        $date = $_GET['date'] ?? (new \DateTime('now', new \DateTimeZone($tz)))->format('Y-m-d');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfMiddleware::verify();
            $date = $_POST['date'] ?? $date;
            $weight = (float)($_POST['weight'] ?? 0);
            if ($weight > 0) {
                $repo->setForDate((int)$_SESSION['user_id'], $date, $weight);
            }
        }
        $current = $repo->getForDate((int)$_SESSION['user_id'], $date);
        return $view->render('weight/index', ['date' => $date, 'weight' => $current, 'title' => 'Dagens vikt']);
    }
}
