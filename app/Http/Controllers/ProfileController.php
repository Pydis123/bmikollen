<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Repositories\UserRepository;
use App\Http\Middleware\{AuthMiddleware, CsrfMiddleware};

class ProfileController {
    public function profile(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $view = Container::get('view');
        $users = new UserRepository($pdo);
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfMiddleware::verify();
            $height = isset($_POST['height_cm']) ? (float)$_POST['height_cm'] : null;
            $theme = $_POST['theme_pref'] ?? 'system';
            $tz = $_POST['timezone'] ?? 'Europe/Stockholm';

            // Lösenordsbyte
            $currPass = $_POST['current_password'] ?? '';
            $newPass = $_POST['new_password'] ?? '';
            $confPass = $_POST['confirm_password'] ?? '';
            $passwordChanged = false;

            if (!empty($newPass)) {
                $user = $users->findById((int)$_SESSION['user_id']);
                if (!password_verify($currPass, $user['password_hash'])) {
                    $error = 'Nuvarande lösenord är felaktigt.';
                } elseif ($newPass !== $confPass) {
                    $error = 'Nya lösenordet matchar inte bekräftelsen.';
                } elseif (strlen($newPass) < 8) {
                    $error = 'Nya lösenordet måste vara minst 8 tecken.';
                } else {
                    $hashAlgo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
                    $newHash = password_hash($newPass, $hashAlgo);
                    $stmt = $pdo->prepare("UPDATE {$users->table('users')} SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$newHash, (int)$_SESSION['user_id']]);
                    $passwordChanged = true;
                    Container::get('logger')->security('password-changed', ['user_id' => (int)$_SESSION['user_id']]);
                }
            }

            if (!$error) {
                $stmt = $pdo->prepare("UPDATE {$users->table('users')} SET height_cm = ?, theme_pref = ?, timezone = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$height, $theme, $tz, (int)$_SESSION['user_id']]);
                $_SESSION['theme_pref'] = $theme;
                $success = $passwordChanged ? 'Inställningar och lösenord har sparats!' : 'Inställningar har sparats!';
            }
        }

        $user = $users->findById((int)$_SESSION['user_id']);
        return $view->render('profile/index', [
            'user' => $user,
            'title' => 'Profil',
            'error' => $error,
            'success' => $success
        ]);
    }

    public function updateTheme(): string {
        AuthMiddleware::check();
        CsrfMiddleware::verify();
        
        header('Content-Type: application/json');
        $pdo = Container::get('db');
        $theme = $_POST['theme'] ?? 'system';
        
        if (in_array($theme, ['system', 'light', 'dark'])) {
            $users = new UserRepository($pdo);
            $stmt = $pdo->prepare("UPDATE {$users->table('users')} SET theme_pref = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$theme, (int)$_SESSION['user_id']]);
            $_SESSION['theme_pref'] = $theme;
            return json_encode(['ok' => true]);
        }
        
        return json_encode(['ok' => false, 'error' => 'Ogiltigt tema']);
    }
}
