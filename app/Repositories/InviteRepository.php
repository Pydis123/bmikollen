<?php
namespace App\Repositories;

use PDO;

class InviteRepository extends BaseRepository {
    public function create(?string $email, string $token, ?int $createdBy): int {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('invites')} (token, email, created_by, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$token, $email, $createdBy]);
        return (int)$this->pdo->lastInsertId();
    }

    public function find(string $token): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('invites')} WHERE token = ?");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function consume(string $token): bool {
        $stmt = $this->pdo->prepare("UPDATE {$this->table('invites')} SET consumed_at = NOW() WHERE token = ? AND consumed_at IS NULL");
        return $stmt->execute([$token]);
    }

    public function listAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table('invites')} ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}
