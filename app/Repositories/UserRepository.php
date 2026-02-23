<?php
namespace App\Repositories;

use PDO;

class UserRepository extends BaseRepository {
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('users')} WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table('users')} WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $email, string $passwordHash, ?string $timezone = null): int {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('users')} (email, password_hash, timezone, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$email, $passwordHash, $timezone ?? 'Europe/Stockholm']);
        return (int)$this->pdo->lastInsertId();
    }

    public function markEmailVerified(int $userId): void {
        $stmt = $this->pdo->prepare("UPDATE {$this->table('users')} SET email_verified_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
}
