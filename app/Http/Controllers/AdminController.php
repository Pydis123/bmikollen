<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AdminMiddleware, CsrfMiddleware};
use App\Repositories\InviteRepository;

class AdminController {
    public function invites(): string {
        AdminMiddleware::check(Container::get('db'));
        $view = Container::get('view');
        $repo = new InviteRepository(Container::get('db'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfMiddleware::verify();
            $email = $_POST['email'] ?? null;
            $token = bin2hex(random_bytes(32));
            $repo->create($email ?: null, $token, (int)$_SESSION['user_id']);
            Container::get('logger')->security('invite-created', ['by' => (int)$_SESSION['user_id'], 'email' => $email]);
        }

        $invites = $repo->listAll();
        return $view->render('admin/invites', ['invites' => $invites, 'title' => 'Invites']);
    }
}
