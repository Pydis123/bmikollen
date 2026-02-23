<?php
namespace App\Repositories;

use PDO;

class ThrottleRepository extends BaseRepository {
    public function increment(string $ip, string $identifier): void {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('login_throttles')} (ip_address, identifier, attempts, last_attempt_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt_at = NOW()");
        $stmt->execute([$ip, $identifier]);
    }

    public function reset(string $ip, string $identifier): void {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table('login_throttles')} WHERE ip_address = ? AND identifier = ?");
        $stmt->execute([$ip, $identifier]);
    }

    public function getStatus(string $ip, string $identifier): array {
        $stmt = $this->pdo->prepare("SELECT attempts, last_attempt_at FROM {$this->table('login_throttles')} WHERE ip_address = ? AND identifier = ?");
        $stmt->execute([$ip, $identifier]);
        $row = $stmt->fetch();
        return $row ?: ['attempts' => 0, 'last_attempt_at' => null];
    }
}
