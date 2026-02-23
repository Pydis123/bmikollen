<?php
namespace App\Core;

class Config {
    private static array $config = [];

    public static function load(): void {
        self::$config = [
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'BMIKollen',
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
                'url' => $_ENV['APP_URL'] ?? 'http://localhost',
                'key' => $_ENV['APP_MASTER_KEY'] ?? '',
                'timezone' => $_ENV['DEFAULT_TIMEZONE'] ?? 'Europe/Stockholm',
                'allow_registration' => ($_ENV['ALLOW_PUBLIC_REGISTRATION'] ?? '0') === '1',
            ],
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'database' => $_ENV['DB_DATABASE'] ?? '',
                'username' => $_ENV['DB_USERNAME'] ?? '',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'prefix' => $_ENV['DB_PREFIX'] ?? '',
            ],
            'mail' => [
                'driver' => $_ENV['MAIL_DRIVER'] ?? 'log',
                'from' => [
                    'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@bmikollen.test',
                    'name' => $_ENV['MAIL_FROM_NAME'] ?? 'BMIKollen',
                ],
            ]
        ];
    }

    public static function get(string $key, $default = null) {
        $parts = explode('.', $key);
        $value = self::$config;
        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }
}
