<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AuthMiddleware, CsrfMiddleware};
use App\Repositories\{BugRepository, SuggestionRepository};

class FeedbackController {
    public function index(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $view = Container::get('view');
        $bugs = new BugRepository($pdo);
        $suggestions = new SuggestionRepository($pdo);
        $userId = (int)$_SESSION['user_id'];

        $success = null; $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfMiddleware::verify();
            $type = $_POST['type'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            if ($title === '' || $description === '') {
                $error = 'Titel och beskrivning krävs.';
            } else {
                if ($type === 'bug') {
                    $bugs->create($userId, $title, $description);
                    $success = 'Din bugg har registrerats.';
                } elseif ($type === 'suggestion') {
                    $suggestions->create($userId, $title, $description);
                    $success = 'Ditt förslag har registrerats.';
                } else {
                    $error = 'Ogiltig typ av feedback.';
                }
            }
        }

        $myBugs = $bugs->listByUser($userId);
        $mySuggestions = $suggestions->listByUser($userId);

        return $view->render('feedback/index', [
            'success' => $success,
            'error' => $error,
            'myBugs' => $myBugs,
            'mySuggestions' => $mySuggestions,
            'title' => 'Feedback'
        ]);
    }
}
