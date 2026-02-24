<?php
/**
 * Seeds a test user with 30 days of dummy data for graphs.
 * Usage: php scripts/seed_demo_data.php testuser@example.com password123
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\{Config, Database, Crypto};
use App\Repositories\{DayLogRepository, WeightRepository, UserRepository, PlanRepository, SuggestionRepository};
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Config
Config::load();

// Database
$db = new Database(Config::get('db'));
$pdo = $db->getConnection();
$prefix = Config::get('db.prefix', 'bmi_');

// Crypto
$crypto = new Crypto(Config::get('app.key'));

$userRepo = new UserRepository($pdo);
$dayLogs = new DayLogRepository($pdo);
$weightsRepo = new WeightRepository($pdo, $crypto);
$plansRepo = new PlanRepository($pdo, $crypto);
$suggestionsRepo = new SuggestionRepository($pdo);

$email = $argv[1] ?? 'test@example.com';
$password = $argv[2] ?? 'lösenord123';

// Find or create a test user
$stmt = $pdo->prepare("SELECT id FROM {$prefix}users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    $userId = $userRepo->create($email, password_hash($password, PASSWORD_DEFAULT));
    echo "Created test user: $email (ID: $userId)\n";
} else {
    $userId = (int)$user['id'];
    echo "Using existing test user: $email (ID: $userId)\n";
}

// Create a plan if not exists
$plan = $plansRepo->getActiveForUser($userId);
if (!$plan) {
    $plansRepo->create([
        'user_id' => $userId,
        'is_active' => 1,
        'start_date' => date('Y-m-d', strtotime('-30 days')),
        'height_cm' => 180,
        'kcal_target' => 2500,
        'protein_target' => 160,
        'steps_target' => 10000,
        'weight_goal' => 80.0
    ]);
    echo "Created a test plan.\n";
}

// Seed data for the last 30 days
echo "Seeding data for the last 30 days...\n";
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    
    // Seed weight (with some variation)
    $weight = 85.0 - (29 - $i) * 0.1 + rand(-5, 5) / 10;
    $weightsRepo->setForDate($userId, $date, $weight);
    
    // Seed logs
    // Only seed some days to test the "missing data" scenario
    if (rand(0, 10) > 2) {
        $dayLogs->add($userId, $date, '08:00:00', rand(400, 600), rand(20, 40), 0, 'Frukost');
        $dayLogs->add($userId, $date, '12:00:00', rand(600, 800), rand(30, 50), 0, 'Lunch');
        $dayLogs->add($userId, $date, '18:00:00', rand(700, 1000), rand(40, 70), 0, 'Middag');
        $dayLogs->add($userId, $date, '20:00:00', 0, 0, rand(5000, 12000), 'Steg');
    }
}

// Seed default suggestions (if not already seeded)
$defaults = [
    ['title' => 'Inför ett Service-lager', 'description' => 'Flytta beräkningslogik (TDEE, BMI, underskott) från controllers till t.ex. NutritionService/HealthService för bättre testbarhet.'],
    ['title' => 'Validerings-system', 'description' => 'Ett centralt valideringslager för formulär för konsekventa felmeddelanden och säkrare indata.'],
    ['title' => 'Databas-migreringar', 'description' => 'Inför ett migrationssystem (Phinx eller liknande) istället för manuell schema.sql-hantering.'],
    ['title' => 'Fler enhetstester', 'description' => 'Utöka tester för beräkningslogik (Mifflin–St Jeor m.m.) för trygg refaktorering.'],
    ['title' => 'Livsmedelsdatabas', 'description' => 'Integrera Open Food Facts (eller liknande) för att söka mat och auto-beräkna Kcal/Protein.'],
    ['title' => 'PWA-stöd', 'description' => 'Lägg till manifest och Service Worker så tjänsten kan installeras och fungera offline.'],
    ['title' => 'Vatten-loggning', 'description' => 'En enkel modul för att logga vattenintag per dag.'],
    ['title' => 'Streaks & notiser', 'description' => 'Motiverande streaks samt e-postpåminnelser vid utebliven loggning.'],
    ['title' => 'PDF-export', 'description' => 'Generera sammanfattnings-PDF med grafer och mål för delning med coach/läkare.'],
    ['title' => 'Milstolpar & firande', 'description' => 'Konfetti/meddelanden vid uppnådda delmål (t.ex. första 5 kg).'],
    ['title' => 'Förbättrad dashboard', 'description' => 'Visa snittförändring 4 veckor och beräknat datum för målvikt.'],
];

$created = 0;
foreach ($defaults as $def) {
    // naive duplicate check by title for this user
    $stmt = $pdo->prepare("SELECT 1 FROM {$prefix}suggestions WHERE user_id = ? AND title = ? LIMIT 1");
    $stmt->execute([$userId, $def['title']]);
    if (!$stmt->fetchColumn()) {
        $suggestionsRepo->create($userId, $def['title'], $def['description']);
        $created++;
    }
}
if ($created > 0) {
    echo "Seeded $created default suggestions for user $email.\n";
}

echo "Done seeding.\n";
echo "You can now log in as '{$email}' with password '{$password}' to see the graphs and feedback.\n";
