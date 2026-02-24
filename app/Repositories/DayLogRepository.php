<?php
namespace App\Repositories;

use PDO;

class DayLogRepository extends BaseRepository {
    public function getByDate(int $userId, string $date): array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('day_logs')} WHERE user_id = ? AND date = ? ORDER BY time ASC, id ASC");
        $stmt->execute([$userId, $date]);
        return $stmt->fetchAll();
    }

    public function add(int $userId, string $date, string $time, ?int $kcal, ?int $protein, ?int $steps, ?string $label, string $source = 'manual'): int {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('day_logs')} (user_id, date, time, kcal_delta, protein_delta, steps_delta, label, source, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())");
        $stmt->execute([$userId, $date, $time, $kcal ?? 0, $protein ?? 0, $steps ?? 0, $label, $source]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool {
        $columns = [];
        $params = [];
        foreach (['kcal_delta','protein_delta','steps_delta','label','time','date','note','source'] as $col) {
            if (array_key_exists($col, $data)) {
                $columns[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (empty($columns)) return false;
        $params[] = $id;
        $params[] = $userId;
        $sql = "UPDATE {$this->table('day_logs')} SET " . implode(',', $columns) . ", updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id, int $userId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table('day_logs')} WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function getDailyTotals(int $userId, string $date): array {
        $stmt = $this->pdo->prepare("SELECT SUM(kcal_delta) AS kcal, SUM(protein_delta) AS protein, SUM(steps_delta) AS steps FROM {$this->table('day_logs')} WHERE user_id = ? AND date = ?");
        $stmt->execute([$userId, $date]);
        $row = $stmt->fetch();
        return [
            'kcal' => (int)($row['kcal'] ?? 0),
            'protein' => (int)($row['protein'] ?? 0),
            'steps' => (int)($row['steps'] ?? 0),
        ];
    }

    public function getRangeTotalsBetween(int $userId, string $startDate, string $endDate): array {
        $stmt = $this->pdo->prepare("SELECT date, SUM(kcal_delta) AS kcal, SUM(protein_delta) AS protein, SUM(steps_delta) AS steps FROM {$this->table('day_logs')} WHERE user_id = ? AND date >= ? AND date <= ? GROUP BY date ORDER BY date ASC");
        $stmt->execute([$userId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
}
