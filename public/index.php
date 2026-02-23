<?php
use Dotenv\Dotenv;
use App\Core\{Config, Container, Database, Logger, Mailer, Request, Response, View, Crypto};
use App\Core\SessionHandler as DbSessionHandler;
use FastRoute\Dispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Config
Config::load();

if (Config::get('app.debug')) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Timezone
@date_default_timezone_set(Config::get('app.timezone'));

// Database & Container services
$db = new Database(Config::get('db'));
$pdo = $db->getConnection();
Container::set('db', $pdo);

$logger = new Logger(__DIR__ . '/../storage/logs/app.log');
Container::set('logger', $logger);

$mailer = new Mailer(Config::get('mail.driver'), Config::get('mail.from'), $logger);
Container::set('mailer', $mailer);

// Crypto
$key = Config::get('app.key');
if (!$key) {
    $logger->error('APP_MASTER_KEY missing');
}
$crypto = new Crypto($key);
Container::set('crypto', $crypto);

// Sessions in DB
$handler = new DbSessionHandler($pdo);
session_set_save_handler($handler, true);

// Robust secure flag detection
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] == 443);

// If on localhost without HTTPS, we MUST disable the secure flag
if (str_starts_with(Config::get('app.url'), 'http://localhost') && !$secure) {
    $secure = false;
}

session_name('bmikollen_sess');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/', // Use root path to ensure cookie is sent for all subfolders
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// HTTP Abstractions
$request = new Request();
$response = new Response();
$view = new View();
Container::set('request', $request);
Container::set('response', $response);
Container::set('view', $view);

// Helper for absolute URLs (handles subdirectories)
function url(string $path = ''): string {
    $base = rtrim(Config::get('app.url'), '/');
    $path = ltrim($path, '/');
    return $base . ($path ? '/' . $path : '');
}

// Share CSRF token globally in views
App\Core\Csrf::generateToken();
$view->share('csrf_token', $_SESSION['csrf_token'] ?? '');
$authUserId = $_SESSION['user_id'] ?? null;
$view->share('auth_user_id', $authUserId);

$isAdmin = false;
if ($authUserId) {
    $prefix = Config::get('db.prefix') ?? '';
    $stmt = $pdo->prepare("SELECT 1 FROM {$prefix}user_roles ur JOIN {$prefix}roles r ON ur.role_id = r.id WHERE ur.user_id = ? AND r.name = 'admin' LIMIT 1");
    $stmt->execute([$authUserId]);
    $isAdmin = (bool)$stmt->fetchColumn();
}
$view->share('is_admin', $isAdmin);

// Router
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    require __DIR__ . '/../app/Http/routes.php';
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Robust subdirectory handling: 
// Compare REQUEST_URI with SCRIPT_NAME to find the base path
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g. /bmikollen/public/index.php
$basePath = str_replace('\\', '/', dirname($scriptName)); // e.g. /bmikollen/public
if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}
if ($uri === '') $uri = '/';

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo $view->render('errors/404', ['title' => 'Sidan hittades inte']);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo $view->render('errors/405', ['title' => 'Metod ej tillåten']);
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$class, $method] = is_array($handler) ? $handler : explode('@', $handler);
        
        $controller = new $class();
        echo call_user_func_array([$controller, $method], [$vars]);
        break;
}
