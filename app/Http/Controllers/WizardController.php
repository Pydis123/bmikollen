<?php
namespace App\Http\Controllers;

use App\Core\Container;
use App\Http\Middleware\{AuthMiddleware, CsrfMiddleware};
use App\Repositories\{PlanRepository, AuditRepository, UserRepository};

class WizardController {
    public function wizard(): string {
        AuthMiddleware::check();
        $pdo = Container::get('db');
        $crypto = Container::get('crypto');
        $view = Container::get('view');
        $plans = new PlanRepository($pdo, $crypto);
        $audit = new AuditRepository($pdo);
        $users = new UserRepository($pdo);
        $user = $users->findById((int)$_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfMiddleware::verify();

            $mode = $_POST['mode'] ?? 'bmi_goal'; // bmi_goal | stop_date
            $intensity = $_POST['intensity'] ?? 'normal'; // gentle|normal|aggressive
            $activity = $_POST['activity_level'] ?? 'sedentary';
            $heightCm = isset($_POST['height_cm']) ? (float)$_POST['height_cm'] : (float)($user['height_cm'] ?? 170);
            $weightNow = isset($_POST['weight_now']) ? (float)$_POST['weight_now'] : null;
            $gender = $_POST['gender'] ?? 'neutral';
            $age = isset($_POST['age']) ? (int)$_POST['age'] : null;
            $targetEnd = !empty($_POST['target_end_date']) ? $_POST['target_end_date'] : null;
            $weightGoal = isset($_POST['weight_goal']) && $_POST['weight_goal'] !== '' ? (float)$_POST['weight_goal'] : null;
            $minWeight = isset($_POST['min_weight']) && $_POST['min_weight'] !== '' ? (float)$_POST['min_weight'] : null;

            // Compute recommendations
            $proteinPerKg = 2.0; // default per krav
            $proteinTarget = $weightNow ? (int)round($weightNow * $proteinPerKg) : 120;

            // Activity multipliers (reasonable defaults)
            $act = [
                'sedentary' => 1.2,
                'light' => 1.375,
                'moderate' => 1.55,
                'active' => 1.725,
                'very_active' => 1.9,
            ];
            $mult = $act[$activity] ?? 1.2;

            // BMR via Mifflin–St Jeor
            $bmr = null;
            if ($weightNow && $heightCm && $age) {
                $s = match($gender) {
                    'male' => 5,
                    'female' => -161,
                    default => -78, // neutral approx between male & female
                };
                $bmr = (10 * $weightNow) + (6.25 * $heightCm) - (5 * $age) + $s;
            }
            $tdee = $bmr ? ($bmr * $mult) : null;

            // Intensity weekly percent with clamps
            $intensityMap = [
                'gentle' => ['pct' => 0.0025, 'min' => 0.2, 'max' => 0.4],
                'normal' => ['pct' => 0.0050, 'min' => 0.4, 'max' => 0.8],
                'aggressive' => ['pct' => 0.0100, 'min' => 0.8, 'max' => 1.0],
            ];
            $i = $intensityMap[$intensity] ?? $intensityMap['normal'];
            $weeklyKg = $weightNow ? max($i['min'], min($i['max'], $i['pct'] * $weightNow)) : $i['min'];

            // Stopdate mode may imply weeks remaining
            if ($mode === 'stop_date' && $weightNow && $targetEnd) {
                $now = new \DateTime('today');
                $end = new \DateTime($targetEnd);
                $weeks = (int)floor(max(0, $now->diff($end)->days) / 7);
                // keep same weekly rate; UI visar varningar om för hög takt
            }

            // kcal target: TDEE - deficit; deficit ~ 7700 kcal/kg per week / 7
            $deficitPerDay = $weeklyKg * 7700 / 7.0;
            $kcalTarget = $tdee ? (int)round(max(1200, $tdee - $deficitPerDay)) : null; // guardrail min kcal
            $stepsTarget = (int)($_POST['steps_target'] ?? 8000);

            // Allow manual overrides
            if (!empty($_POST['kcal_target'])) $kcalTarget = (int)$_POST['kcal_target'];
            if (!empty($_POST['protein_target'])) $proteinTarget = (int)$_POST['protein_target'];

            // Close existing active plans
            $plans->deactivateAllForUser((int)$_SESSION['user_id']);

            // Create new active plan
            $planId = $plans->create([
                'user_id' => (int)$_SESSION['user_id'],
                'is_active' => 1,
                'start_date' => date('Y-m-d'),
                'target_end_date' => $targetEnd,
                'height_cm' => $heightCm,
                'kcal_target' => $kcalTarget ?? 2000,
                'protein_target' => $proteinTarget,
                'steps_target' => $stepsTarget,
                'intensity_preset' => $intensity,
                'activity_level' => $activity,
                'training_goal' => null,
                'override_flags' => json_encode(['mode' => $mode]),
                'weight_goal' => $weightGoal,
                'min_weight' => $minWeight,
            ]);

            $audit->logPlanChange($planId, (int)$_SESSION['user_id'], 'wizard', [], [
                'kcal_target' => $kcalTarget,
                'protein_target' => $proteinTarget,
                'steps_target' => $stepsTarget,
                'intensity' => $intensity,
                'activity' => $activity,
            ]);

            header('Location: ' . url('/'));
            exit;
        }

        return $view->render('wizard/index', [
            'user' => $user,
            'title' => 'Plan-wizard'
        ]);
    }
}
