<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\{Config, Database};
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Config
Config::load();

$db = new Database(Config::get('db'));
$pdo = $db->getConnection();
$prefix = Config::get('db.prefix') ?? '';

function ensurePrioColumn(PDO $pdo, string $table) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'prio'");
    $stmt->execute([$table]);
    $exists = (int)$stmt->fetchColumn() > 0;
    if (!$exists) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `prio` TINYINT NOT NULL DEFAULT 3 AFTER `status`";
        $pdo->exec($sql);
        echo "Added prio column to $table\n";
    } else {
        echo "Prio column already exists on $table\n";
    }
}

try {
    ensurePrioColumn($pdo, $prefix . 'bugs');
    ensurePrioColumn($pdo, $prefix . 'suggestions');
    echo "Migration completed.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}
