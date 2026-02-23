<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AuthMiddleware, CsrfMiddleware};
use App\Repositories\{DayLogRepository, PlanRepository, WeightRepository, UserRepository};
use App\Services\DayService;

class DayController {
    private DayService $day;

    public function __construct() {
        $pdo = Container::get('db');
        $logger = Container::get('logger');
        $crypto = Container::get('crypto');
        $this->day = new DayService(new DayLogRepository($pdo), new PlanRepository($pdo, $crypto), new WeightRepository($pdo, $crypto), $logger);
    }

    public function index(): string {
        AuthMiddleware::check();
        $view = Container::get('view');
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $users = new UserRepository($pdo);
        $plans = new PlanRepository($pdo, $crypto);
        $user = $users->findById((int)$_SESSION['user_id']);
        $tz = $user['timezone'] ?? 'Europe/Stockholm';
        $date = $_GET['date'] ?? $this->day->userDateToday($tz);
        $data = $this->day->getDayData((int)$_SESSION['user_id'], $date);
        $plan = $plans->getActiveForUser((int)$_SESSION['user_id']);
        return $view->render('day/index', array_merge($data, ['date' => $date, 'plan' => $plan, 'title' => 'Min dag']));
    }

    public function add(): void {
        AuthMiddleware::check();
        CsrfMiddleware::verify();
        $pdo = Container::get('db');
        $users = new UserRepository($pdo);
        $user = $users->findById((int)$_SESSION['user_id']);
        $tz = $user['timezone'] ?? 'Europe/Stockholm';
        $date = $_POST['date'] ?? $this->day->userDateToday($tz);
        $time = $_POST['time'] ?? $this->day->userTimeNow($tz);
        $kcal = isset($_POST['kcal']) ? (int)$_POST['kcal'] : null;
        $protein = isset($_POST['protein']) ? (int)$_POST['protein'] : null;
        $steps = isset($_POST['steps']) ? (int)$_POST['steps'] : null;
        $label = $_POST['label'] ?? null;
        $this->day->addDelta((int)$_SESSION['user_id'], $date, $time, $kcal, $protein, $steps, $label, 'manual');

        $weight = isset($_POST['weight']) && $_POST['weight'] !== '' ? (float)$_POST['weight'] : null;
        if ($weight !== null && $weight > 0) {
            $this->day->setWeight((int)$_SESSION['user_id'], $date, $weight);
        }

        header('Location: ' . url('/?date=' . urlencode($date)));
        exit;
    }

    public function edit(array $vars): string {
        AuthMiddleware::check();
        // MVP: lämna som TODO, visa enkel info
        $view = Container::get('view');
        return $view->render('day/edit', ['id' => (int)($vars['id'] ?? 0), 'title' => 'Redigera post (kommer snart)']);
    }

    public function delete(array $vars): void {
        AuthMiddleware::check();
        CsrfMiddleware::verify();
        $id = (int)($vars['id'] ?? 0);
        $repo = new DayLogRepository(Container::get('db'));
        $repo->delete($id, (int)$_SESSION['user_id']);
        header('Location: ' . url('/'));
        exit;
    }
}
