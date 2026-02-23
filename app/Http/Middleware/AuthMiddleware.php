<?php
namespace App\Http\Middleware;

class AuthMiddleware {
    public static function check(): void {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . url('/auth/login'));
            exit;
        }
    }
}
