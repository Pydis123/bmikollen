<?php
namespace App\Repositories;

use PDO;

class TokenRepository extends BaseRepository {
    public function create(int $userId, string $tokenHash, string $type, string $expiresAt): int {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('tokens')} (user_id, token_hash, type, expires_at, created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$userId, $tokenHash, $type, $expiresAt]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findValid(string $tokenHash, string $type): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('tokens')} WHERE token_hash = ? AND type = ? AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$tokenHash, $type]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function deleteById(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table('tokens')} WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function deleteByUserType(int $userId, string $type): void {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table('tokens')} WHERE user_id = ? AND type = ?");
        $stmt->execute([$userId, $type]);
    }
}
