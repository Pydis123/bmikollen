<?php
namespace App\Http\Middleware;

use App\Core\Csrf;

class CsrfMiddleware {
    public static function verify(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Csrf::validate($token)) {
                http_response_code(419);
                echo 'CSRF token mismatch';
                exit;
            }
        }
    }
}
