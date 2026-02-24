<?php
namespace App\Repositories;

use PDO;
use App\Core\Crypto;

class WeightRepository extends BaseRepository {
    private Crypto $crypto;

    public function __construct(PDO $pdo, Crypto $crypto) {
        parent::__construct($pdo);
        $this->crypto = $crypto;
    }

    public function setForDate(int $userId, string $date, float $weightKg): void {
        $enc = $this->crypto->encrypt((string)$weightKg);
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('weights')} (user_id, date, weight_ciphertext, weight_iv, weight_tag, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE weight_ciphertext = VALUES(weight_ciphertext), weight_iv = VALUES(weight_iv), weight_tag = VALUES(weight_tag), updated_at = NOW()");
        $stmt->execute([$userId, $date, $enc['ciphertext'], $enc['iv'], $enc['tag']]);
    }

    public function getForDate(int $userId, string $date): ?float {
        $stmt = $this->pdo->prepare("SELECT weight_ciphertext, weight_iv, weight_tag FROM {$this->table('weights')} WHERE user_id = ? AND date = ?");
        $stmt->execute([$userId, $date]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $plain = $this->crypto->decrypt($row['weight_ciphertext'], $row['weight_iv'], $row['weight_tag']);
        return $plain !== false ? (float)$plain : null;
    }

    public function getRangeBetween(int $userId, string $startDate, string $endDate): array {
        $stmt = $this->pdo->prepare("SELECT date, weight_ciphertext, weight_iv, weight_tag FROM {$this->table('weights')} WHERE user_id = ? AND date >= ? AND date <= ? ORDER BY date ASC");
        $stmt->execute([$userId, $startDate, $endDate]);
        $rows = $stmt->fetchAll();
        $weights = [];
        foreach ($rows as $row) {
            $plain = $this->crypto->decrypt($row['weight_ciphertext'], $row['weight_iv'], $row['weight_tag']);
            if ($plain !== false) {
                $weights[] = ['date' => $row['date'], 'weight' => (float)$plain];
            }
        }
        return $weights;
    }
}
