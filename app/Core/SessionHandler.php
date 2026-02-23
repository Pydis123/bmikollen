<?php
namespace App\Core;

use SessionHandlerInterface;
use PDO;

class SessionHandler implements SessionHandlerInterface {
    private PDO $pdo;
    private string $table;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $prefix = Config::get('db.prefix') ?? '';
        $this->table = $prefix . 'sessions';
    }

    public function open($path, $name): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string|false {
        $stmt = $this->pdo->prepare("SELECT data FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $row['data'] : '';
    }

    public function write($id, $data): bool {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $stmt = $this->pdo->prepare("REPLACE INTO {$this->table} (id, data, timestamp, user_id) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$id, $data, time(), $userId]);
        } catch (\Exception $e) {
            // Silently fail to avoid 500 on session shutdown
            return false;
        }
    }

    public function destroy($id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function gc($max_lifetime): int|false {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE timestamp < ?");
        $stmt->execute([time() - $max_lifetime]);
        return $stmt->rowCount();
    }
}
