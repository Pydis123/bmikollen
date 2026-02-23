<?php
namespace App\Http\Middleware;

use App\Core\Config;
use PDO;

class AdminMiddleware {
    public static function check(PDO $pdo): void {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . url('/auth/login'));
            exit;
        }
        $prefix = Config::get('db.prefix') ?? '';
        $stmt = $pdo->prepare("SELECT 1 FROM {$prefix}user_roles ur JOIN {$prefix}roles r ON ur.role_id = r.id WHERE ur.user_id = ? AND r.name = 'admin' LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        if (!$stmt->fetchColumn()) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}
