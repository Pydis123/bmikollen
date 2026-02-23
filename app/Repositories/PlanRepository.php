<?php
namespace App\Repositories;

use PDO;
use App\Core\Crypto;

class PlanRepository extends BaseRepository {
    private Crypto $crypto;

    public function __construct(PDO $pdo, Crypto $crypto) {
        parent::__construct($pdo);
        $this->crypto = $crypto;
    }

    public function getActiveForUser(int $userId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('plans')} WHERE user_id = ? AND is_active = 1 ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return $this->decryptPlan($row);
    }

    private function decryptPlan(array $row): array {
        foreach (['weight_goal', 'min_weight'] as $f) {
            $cipher = $row[$f . '_ciphertext'] ?? null;
            if ($cipher) {
                $plain = $this->crypto->decrypt($cipher, $row[$f . '_iv'], $row[$f . '_tag']);
                $row[$f] = $plain !== false ? (float)$plain : null;
            } else {
                $row[$f] = null;
            }
        }
        return $row;
    }

    public function deactivateAllForUser(int $userId): void {
        $sql = "UPDATE {$this->table('plans')} SET is_active = 0, closed_at = NOW() WHERE user_id = ? AND is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
    }

    public function create(array $data): int {
        $fields = [
            'user_id','is_active','start_date','target_end_date','height_cm','kcal_target','protein_target','steps_target','intensity_preset','activity_level','training_goal','override_flags'
        ];
        $encFields = ['weight_goal','min_weight'];

        $cols = [];
        $placeholders = [];
        $values = [];

        foreach ($fields as $f) {
            $cols[] = $f;
            $placeholders[] = '?';
            $values[] = $data[$f] ?? null;
        }

        // encrypted fields
        foreach ($encFields as $ef) {
            $val = $data[$ef] ?? null;
            $cipher = $iv = $tag = null;
            if ($val !== null) {
                $e = $this->crypto->encrypt((string)$val);
                $cipher = $e['ciphertext'];
                $iv = $e['iv'];
                $tag = $e['tag'];
            }
            $cols[] = $ef . '_ciphertext';
            $placeholders[] = '?';
            $values[] = $cipher;

            $cols[] = $ef . '_iv';
            $placeholders[] = '?';
            $values[] = $iv;

            $cols[] = $ef . '_tag';
            $placeholders[] = '?';
            $values[] = $tag;
        }

        $sql = "INSERT INTO {$this->table('plans')} (" . implode(',', $cols) . ", created_at, updated_at) VALUES (" . implode(',', $placeholders) . ", NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return (int)$this->pdo->lastInsertId();
    }

    public function getAllForUser(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('plans')} WHERE user_id = ? ORDER BY is_active DESC, start_date DESC, id DESC");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        return array_map([$this, 'decryptPlan'], $rows);
    }

    public function updateTargets(int $planId, array $data): void {
        $columns = [];
        $params = [];
        foreach (['kcal_target','protein_target','steps_target','intensity_preset','activity_level','training_goal','override_flags'] as $col) {
            if (array_key_exists($col, $data)) {
                $columns[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        foreach (['weight_goal','min_weight'] as $ef) {
            if (array_key_exists($ef, $data)) {
                $e = $this->crypto->encrypt((string)$data[$ef]);
                $columns[] = $ef . '_ciphertext = ?';
                $params[] = $e['ciphertext'];
                $columns[] = $ef . '_iv = ?';
                $params[] = $e['iv'];
                $columns[] = $ef . '_tag = ?';
                $params[] = $e['tag'];
            }
        }
        if (empty($columns)) return;
        $params[] = $planId;
        $sql = "UPDATE {$this->table('plans')} SET " . implode(',', $columns) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
