<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AdminMiddleware, CsrfMiddleware};
use App\Repositories\{BugRepository, SuggestionRepository};

class AdminFeedbackController {
    public function index(): string {
        $pdo = Container::get('db');
        AdminMiddleware::check($pdo);
        $view = Container::get('view');
        $bugs = new BugRepository($pdo);
        $suggestions = new SuggestionRepository($pdo);

        $allBugs = $bugs->listAll();
        $allSuggestions = $suggestions->listAll();

        return $view->render('admin/feedback', [
            'bugs' => $allBugs,
            'suggestions' => $allSuggestions,
            'title' => 'Admin · Feedback'
        ]);
    }

    public function updateBugStatus(array $vars): void {
        $pdo = Container::get('db');
        AdminMiddleware::check($pdo);
        CsrfMiddleware::verify();
        $id = (int)($vars['id'] ?? 0);
        $status = $_POST['status'] ?? null;
        $prio = isset($_POST['prio']) ? (int)$_POST['prio'] : null;
        $repo = new BugRepository($pdo);
        if ($status === null && $prio === null) {
            header('Location: ' . url('/admin/feedback'));
            exit;
        }
        $repo->updateAdminFields($id, $status, $prio);
        header('Location: ' . url('/admin/feedback'));
        exit;
    }

    public function updateSuggestionStatus(array $vars): void {
        $pdo = Container::get('db');
        AdminMiddleware::check($pdo);
        CsrfMiddleware::verify();
        $id = (int)($vars['id'] ?? 0);
        $status = $_POST['status'] ?? null;
        $prio = isset($_POST['prio']) ? (int)$_POST['prio'] : null;
        $repo = new SuggestionRepository($pdo);
        if ($status === null && $prio === null) {
            header('Location: ' . url('/admin/feedback'));
            exit;
        }
        $repo->updateAdminFields($id, $status, $prio);
        header('Location: ' . url('/admin/feedback'));
        exit;
    }
}
