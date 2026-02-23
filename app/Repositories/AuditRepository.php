<?php
namespace App\Repositories;

use PDO;

class AuditRepository extends BaseRepository {
    public function logPlanChange(int $planId, int $userId, string $reason, array $oldTargets, array $newTargets): void {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table('plan_audit')} (plan_id, user_id, reason, old_targets_json, new_targets_json, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$planId, $userId, $reason, json_encode($oldTargets), json_encode($newTargets)]);
    }
}
