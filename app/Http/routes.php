<?php
use FastRoute\RouteCollector;

/** @var RouteCollector $r */

// Auth
$r->addRoute(['GET', 'POST'], '/auth/login', [App\Http\Controllers\AuthController::class, 'login']);
$r->addRoute(['POST'], '/auth/logout', [App\Http\Controllers\AuthController::class, 'logout']);
$r->addRoute(['GET', 'POST'], '/auth/register', [App\Http\Controllers\AuthController::class, 'register']);
$r->addRoute(['GET'], '/auth/verify', [App\Http\Controllers\AuthController::class, 'verifyEmail']);
$r->addRoute(['GET', 'POST'], '/auth/forgot', [App\Http\Controllers\AuthController::class, 'forgot']);
$r->addRoute(['GET', 'POST'], '/auth/reset', [App\Http\Controllers\AuthController::class, 'reset']);

// App core
$r->addRoute(['GET'], '/', [App\Http\Controllers\DayController::class, 'index']);
$r->addRoute(['POST'], '/day/log/add', [App\Http\Controllers\DayController::class, 'add']);
$r->addRoute(['GET', 'POST'], '/day/log/edit/{id:\\d+}', [App\Http\Controllers\DayController::class, 'edit']);
$r->addRoute(['POST'], '/day/log/delete/{id:\\d+}', [App\Http\Controllers\DayController::class, 'delete']);
$r->addRoute(['GET', 'POST'], '/weight', [App\Http\Controllers\WeightController::class, 'setWeight']);
$r->addRoute(['GET'], '/overview', [App\Http\Controllers\OverviewController::class, 'overview']);
$r->addRoute(['GET'], '/week', [App\Http\Controllers\OverviewController::class, 'week']);
$r->addRoute(['GET'], '/charts', [App\Http\Controllers\ChartController::class, 'charts']);
$r->addRoute(['GET', 'POST'], '/profile', [App\Http\Controllers\ProfileController::class, 'profile']);
$r->addRoute(['POST'], '/profile/theme', [App\Http\Controllers\ProfileController::class, 'updateTheme']);

// Wizard/Plans
$r->addRoute(['GET', 'POST'], '/wizard', [App\Http\Controllers\WizardController::class, 'wizard']);
$r->addRoute(['GET'], '/plan', [App\Http\Controllers\PlanController::class, 'current']);
$r->addRoute(['POST'], '/plan/update', [App\Http\Controllers\PlanController::class, 'update']);
$r->addRoute(['POST'], '/plan/new', [App\Http\Controllers\PlanController::class, 'create']);
$r->addRoute(['GET'], '/plans/history', [App\Http\Controllers\PlanController::class, 'history']);

// Export
$r->addRoute(['GET'], '/export', [App\Http\Controllers\ExportController::class, 'show']);
$r->addRoute(['POST'], '/export', [App\Http\Controllers\ExportController::class, 'export']);

// Admin
$r->addRoute(['GET', 'POST'], '/admin/invites', [App\Http\Controllers\AdminController::class, 'invites']);
