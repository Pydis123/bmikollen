<?php
/**
 * Seeds an admin user.
 * Usage: php scripts/seed_admin.php admin@example.com password
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$email = $argv[1] ?? 'admin@bmikollen.test';
$password = $argv[2] ?? 'password123';
$prefix = $_ENV['DB_PREFIX'] ?? '';

try {
    $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4";
    $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Check if roles exist
    $rolesCount = $pdo->query("SELECT COUNT(*) FROM {$prefix}roles")->fetchColumn();
    if ($rolesCount == 0) {
        $pdo->exec("INSERT INTO {$prefix}roles (name) VALUES ('admin'), ('user')");
        echo "Roles seeded.\n";
    }

    $adminRole = $pdo->query("SELECT id FROM {$prefix}roles WHERE name = 'admin'")->fetchColumn();

    // Create user
    $stmt = $pdo->prepare("INSERT INTO {$prefix}users (email, password_hash, email_verified_at) VALUES (?, ?, NOW())");
    $stmt->execute([$email, password_hash($password, PASSWORD_ARGON2ID)]);
    $userId = $pdo->lastInsertId();

    // Assign role
    $stmt = $pdo->prepare("INSERT INTO {$prefix}user_roles (user_id, role_id) VALUES (?, ?)");
    $stmt->execute([$userId, $adminRole]);

    echo "Admin user created successfully!\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
