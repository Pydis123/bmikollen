<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AuthMiddleware, CsrfMiddleware};
use App\Repositories\PlanRepository;

class PlanController {
    public function current(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $view = Container::get('view');
        $plansRepo = new PlanRepository($pdo, $crypto);
        $userId = (int)$_SESSION['user_id'];
        $plan = $plansRepo->getActiveForUser($userId);

        if ($plan) {
            // Avkryptera fält
            foreach (['weight_goal', 'min_weight'] as $f) {
                if ($plan[$f . '_ciphertext']) {
                    $plan[$f] = $crypto->decrypt([
                        'ciphertext' => $plan[$f . '_ciphertext'],
                        'iv' => $plan[$f . '_iv'],
                        'tag' => $plan[$f . '_tag']
                    ]);
                } else {
                    $plan[$f] = null;
                }
            }
        }

        return $view->render('plans/current', [
            'plan' => $plan,
            'title' => 'Min nuvarande plan'
        ]);
    }

    public function history(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $view = Container::get('view');
        $plansRepo = new PlanRepository($pdo, $crypto);
        $userId = (int)$_SESSION['user_id'];
        $plans = $plansRepo->getAllForUser($userId);
        return $view->render('plans/history', ['plans' => $plans, 'title' => 'Planhistorik']);
    }

    public function update(): void {
        AuthMiddleware::check();
        CsrfMiddleware::verify();
        // För enkelhet: vidarebefordra till wizard för uppdatering
        header('Location: ' . url('/wizard'));
        exit;
    }

    public function create(): void {
        AuthMiddleware::check();
        CsrfMiddleware::verify();
        // För enkelhet: vidarebefordra till wizard för ny plan
        header('Location: ' . url('/wizard'));
        exit;
    }
}
