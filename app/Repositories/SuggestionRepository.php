<?php
namespace App\Repositories;

use PDO;

class SuggestionRepository extends BaseRepository {
    public function create(int $userId, string $title, string $description): int {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('suggestions')} (user_id, title, description, status, created_at, updated_at) VALUES (?, ?, ?, 'not_started', NOW(), NOW())");
        $stmt->execute([$userId, $title, $description]);
        return (int)$this->pdo->lastInsertId();
    }

    public function listByUser(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT id, title, description, status, created_at FROM {$this->table('suggestions')} WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function listAll(): array {
        $stmt = $this->pdo->query("SELECT s.id, s.user_id, u.email, s.title, s.description, s.status, s.prio, s.created_at FROM {$this->table('suggestions')} s JOIN {$this->table('users')} u ON s.user_id = u.id ORDER BY s.created_at DESC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('suggestions')} WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): bool {
        $allowed = ['not_started','in_progress','done'];
        if (!in_array($status, $allowed, true)) return false;
        $stmt = $this->pdo->prepare("UPDATE {$this->table('suggestions')} SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function updateAdminFields(int $id, ?string $status, ?int $prio): bool {
        $sets = [];
        $params = [];
        if ($status !== null) {
            $allowed = ['not_started','in_progress','done'];
            if (!in_array($status, $allowed, true)) return false;
            $sets[] = 'status = ?';
            $params[] = $status;
        }
        if ($prio !== null) {
            if (!in_array($prio, [1,2,3], true)) return false;
            $sets[] = 'prio = ?';
            $params[] = $prio;
        }
        if (empty($sets)) return false;
        $sql = "UPDATE {$this->table('suggestions')} SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = ?";
        $params[] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
