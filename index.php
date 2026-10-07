<?php
/**
 * Alumni Tracking System - Front Controller
 * 26-27 Web Programming - Week 04 (MVC Architecture)
 * Assoc. Prof. Dr. Emre Akadal · Istanbul University
 */

// 1. PSR-4 Benzeri Otomatik Yükleyici (Autoloader)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\ApiController;

// 2. Global CORS Başlıkları
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// 3. Router Örneğini Başlat
$router = new Router();

// -------------------------------------------------------------
// WEB ROTALARI (Geriye Dönük Uyumluluk - Week 02)
// -------------------------------------------------------------
$router->get('/',                 [HomeController::class, 'index']);
$router->get('/Alumni',           [HomeController::class, 'alumni']);
$router->get('/hello',            [HomeController::class, 'hello']);
$router->get('/hello/{name}',     [HomeController::class, 'helloName']);
$router->get('/sum/{a}/{b}',      [HomeController::class, 'sum']);
$router->get('/about',            [HomeController::class, 'about']);

// -------------------------------------------------------------
// SİSTEM & DOKÜMANTASYON ROTALARI (Week 03)
// -------------------------------------------------------------
$router->get('/api/health',       [ApiController::class, 'health']);
$router->get('/api/swagger',      [ApiController::class, 'swagger']);
$router->get('/swagger',          [ApiController::class, 'swaggerRedirect']);
$router->get('/api/openapi.json', [ApiController::class, 'openapi']);

// -------------------------------------------------------------
// REST API & CRUD ROTALARI (Week 03 & 04)
// -------------------------------------------------------------
$router->get('/api/users',        [UserController::class, 'index']);
$router->post('/api/users',       [UserController::class, 'store']);
$router->get('/api/users/{id}',   [UserController::class, 'show']);
$router->put('/api/users/{id}',   [UserController::class, 'update']);
$router->patch('/api/users/{id}', [UserController::class, 'patch']);
$router->delete('/api/users/{id}',[UserController::class, 'destroy']);

// 4. İsteği Yönlendir (Dispatch)
$router->dispatch();
