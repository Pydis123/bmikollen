<?php
namespace App\Http\Controllers;

use App\Core\{Container, View};
use App\Repositories\{UserRepository, TokenRepository, ThrottleRepository, InviteRepository};
use App\Services\AuthService;

class AuthController {
    private AuthService $auth;

    public function __construct() {
        $pdo = Container::get('db');
        $mailer = Container::get('mailer');
        $logger = Container::get('logger');
        $this->auth = new AuthService(new UserRepository($pdo), new TokenRepository($pdo), new ThrottleRepository($pdo), $mailer, $logger);
    }

    public function login(): string {
        $view = Container::get('view');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            \App\Http\Middleware\CsrfMiddleware::verify();
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $res = $this->auth->login($email, $password, $_SERVER['REMOTE_ADDR'] ?? '');
            if ($res['ok']) {
                header('Location: ' . url('/'));
                exit;
            }
            return $view->render('auth/login', ['error' => $res['error'] ?? null, 'title' => 'Logga in']);
        }
        return $view->render('auth/login', ['title' => 'Logga in']);
    }

    public function logout(): void {
        \App\Http\Middleware\CsrfMiddleware::verify();
        $this->auth->logout();
        header('Location: ' . url('/auth/login'));
        exit;
    }

    public function register(): string {
        $view = Container::get('view');
        $allowPublic = (($_ENV['ALLOW_PUBLIC_REGISTRATION'] ?? '0') === '1');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            \App\Http\Middleware\CsrfMiddleware::verify();
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $invite = $_POST['invite'] ?? null;

            $accepted = isset($_POST['accept_terms']);
            if (!$accepted) {
                return $view->render('auth/register', ['error' => 'Du måste godkänna integritetstexten och "Om hälsa och tolkning".', 'title' => 'Registrera', 'require_invite' => !$allowPublic]);
            }

            if (!$allowPublic && empty($invite)) {
                return $view->render('auth/register', ['error' => 'Invite krävs för registrering', 'title' => 'Registrera']);
            }
            if (!$allowPublic) {
                $invRepo = new InviteRepository(Container::get('db'));
                $inv = $invRepo->find((string)$invite);
                if (!$inv || !empty($inv['consumed_at'])) {
                    return $view->render('auth/register', ['error' => 'Ogiltig eller förbrukad invite', 'title' => 'Registrera']);
                }
            }
            $res = $this->auth->register($email, $password);
            if ($res['ok']) {
                if (!$allowPublic) {
                    // Förbruka invite vid lyckad registrering
                    $invRepo = $invRepo ?? new InviteRepository(Container::get('db'));
                    $invRepo->consume((string)$invite);
                    Container::get('logger')->security('invite-consumed', ['invite' => (string)$invite]);
                }
                return $view->render('auth/register', ['success' => 'Konto skapat! Kolla din e-post för verifieringslänk.', 'title' => 'Registrera']);
            }
            return $view->render('auth/register', ['error' => $res['error'] ?? null, 'title' => 'Registrera']);
        }
        return $view->render('auth/register', ['title' => 'Registrera', 'require_invite' => !$allowPublic]);
    }

    public function verifyEmail(): void {
        $token = $_GET['token'] ?? '';
        $ok = $this->auth->verifyEmailByToken($token);
        header('Location: ' . url('/auth/login?verified=' . ($ok ? '1' : '0')));
        exit;
    }

    public function forgot(): string {
        $view = Container::get('view');
        // MVP: Visa bara "om kontot finns..." utan att läcka existens
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            \App\Http\Middleware\CsrfMiddleware::verify();
            // TODO: skapa reset-token & maila (logga i app.log om MAIL_DRIVER=log)
            return $view->render('auth/forgot', ['success' => 'Om kontot finns, har vi skickat instruktioner.', 'title' => 'Glömt lösenord']);
        }
        return $view->render('auth/forgot', ['title' => 'Glömt lösenord']);
    }

    public function reset(): string {
        $view = Container::get('view');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            \App\Http\Middleware\CsrfMiddleware::verify();
            // TODO: validera token_hash & sätt nytt lösenord
            return $view->render('auth/reset', ['success' => 'Lösenord uppdaterat om token var giltig.', 'title' => 'Återställ lösenord']);
        }
        return $view->render('auth/reset', ['title' => 'Återställ lösenord', 'token' => ($_GET['token'] ?? '')]);
    }
}
