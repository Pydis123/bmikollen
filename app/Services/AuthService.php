<?php
namespace App\Services;

use App\Repositories\{UserRepository, TokenRepository, ThrottleRepository};
use App\Core\{Mailer, Logger};

class AuthService {
    private UserRepository $users;
    private TokenRepository $tokens;
    private ThrottleRepository $throttle;
    private Mailer $mailer;
    private Logger $logger;

    public function __construct(UserRepository $users, TokenRepository $tokens, ThrottleRepository $throttle, Mailer $mailer, Logger $logger) {
        $this->users = $users;
        $this->tokens = $tokens;
        $this->throttle = $throttle;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function login(string $email, string $password, string $ip): array {
        // Throttle: lockout after 5 attempts within 15 minutes
        $status = $this->throttle->getStatus($ip, $email);
        if (!empty($status['last_attempt_at'])) {
            $last = strtotime($status['last_attempt_at']);
            $attempts = (int)$status['attempts'];
            if ($attempts >= 5 && (time() - $last) < 15 * 60) {
                return ['ok' => false, 'error' => 'För många försök. Försök igen om en stund.'];
            }
            if ((time() - $last) >= 15 * 60) {
                $this->throttle->reset($ip, $email);
            }
        }

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->throttle->increment($ip, $email);
            $this->logger->security('login-fail', ['email' => $email, 'ip' => $ip]);
            return ['ok' => false, 'error' => 'Felaktiga uppgifter.'];
        }

        // success
        $this->throttle->reset($ip, $email);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['theme_pref'] = $user['theme_pref'] ?? 'system';
        $this->logger->security('login-success', ['user_id' => $user['id'], 'ip' => $ip]);

        return ['ok' => true, 'user' => $user];
    }

    public function logout(): void {
        $this->logger->security('logout', ['user_id' => $_SESSION['user_id'] ?? null]);
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function register(string $email, string $password): array {
        $existing = $this->users->findByEmail($email);
        if ($existing) {
            return ['ok' => false, 'error' => 'E-postadressen är redan registrerad'];
        }
        $hashAlgo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
        $hash = password_hash($password, $hashAlgo);
        $userId = $this->users->create($email, $hash);

        // Email verification token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $this->tokens->deleteByUserType($userId, 'email_verify');
        $this->tokens->create($userId, $tokenHash, 'email_verify', date('Y-m-d H:i:s', time() + 86400));

        $verifyUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/') . '/auth/verify?token=' . $token;
        $this->mailer->send($email, 'Verifiera din e-post', 'Klicka för att verifiera: ' . $verifyUrl);
        $this->logger->security('email-verification-sent', ['user_id' => $userId]);

        return ['ok' => true, 'user_id' => $userId];
    }

    public function verifyEmailByToken(string $token): bool {
        $tokenHash = hash('sha256', $token);
        $row = $this->tokens->findValid($tokenHash, 'email_verify');
        if (!$row) return false;
        $this->users->markEmailVerified((int)$row['user_id']);
        $this->tokens->deleteById((int)$row['id']);
        $this->logger->security('email-verified', ['user_id' => (int)$row['user_id']]);
        return true;
    }
}
