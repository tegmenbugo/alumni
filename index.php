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
use App\Controllers\ApiUserController;
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
// WEB CONTROLLER ROTALARI: UserController (Week 04 - Görev 3)
// HTML Arayüzü & Web Formları
// -------------------------------------------------------------
$router->get('/users',               [UserController::class, 'index']);
$router->post('/users',              [UserController::class, 'store']);
$router->get('/users/{id}',          [UserController::class, 'show']);
$router->post('/users/{id}/update',  [UserController::class, 'update']);
$router->post('/users/{id}/delete',  [UserController::class, 'destroy']);
$router->delete('/users/{id}',       [UserController::class, 'destroy']);

// -------------------------------------------------------------
// REST API CONTROLLER ROTALARI: ApiUserController (Week 04 - Görev 3)
// JSON Giriş / JSON Çıkış REST CRUD Uç Noktaları
// -------------------------------------------------------------
$router->get('/api/users',        [ApiUserController::class, 'index']);
$router->post('/api/users',       [ApiUserController::class, 'store']);
$router->get('/api/users/{id}',   [ApiUserController::class, 'show']);
$router->put('/api/users/{id}',   [ApiUserController::class, 'update']);
$router->patch('/api/users/{id}', [ApiUserController::class, 'patch']);
$router->delete('/api/users/{id}',[ApiUserController::class, 'destroy']);

// 4. İsteği Yönlendir (Dispatch)
$router->dispatch();
