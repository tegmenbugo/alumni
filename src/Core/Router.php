<?php
namespace App\Core;

use App\Middlewares\LoggerMiddleware;
use App\Middlewares\NotFoundGuardianMiddleware;
use App\Middlewares\ServerErrorGuardianMiddleware;

/**
 * Merkezi Yönlendirici (HTTP Router)
 * MVC mimarisinde gelen istekleri uygun Controller ve metodlarına dağıtır.
 * Hafta 07: LoggerMiddleware, NotFoundGuardianMiddleware ve ServerErrorGuardianMiddleware entegrasyonu.
 */
class Router
{
    private array $routes = [];

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, array $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => '/' . trim($path, '/'),
            'handler' => $handler
        ];
    }

    public function dispatch(): void
    {
        // Hafta 07 (Görev 5): 5xx Guardian - Tüm istisnaları ve sunucu hatalarını yakala
        ServerErrorGuardianMiddleware::register();

        // Hafta 07 (Görev 3 & 6): Logger Middleware - Client'tan gelen ilk anda başlat (Terminal + logs/error.logs)
        LoggerMiddleware::start();

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
        $requestPath = parse_url($rawUri, PHP_URL_PATH);

        // Preflight OPTIONS isteği geldiyse hemen 200 dön
        if ($requestMethod === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // 1) /Alumni GET isteği doğrudan gelirse özel kontrol (Hafta 2 Kuralı)
        $trimmedRaw = trim($requestPath, '/');
        if (strcasecmp($trimmedRaw, 'Alumni') === 0 && $requestMethod === 'GET') {
            Response::text('ok');
        }

        // Alt klasördeyse (/Alumni veya /alumni) öndeki kısmı kırp
        if (stripos($requestPath, '/Alumni') === 0) {
            $requestPath = substr($requestPath, strlen('/Alumni'));
        }

        $requestPath = '/' . trim($requestPath, '/');
        if ($requestPath === '//') {
            $requestPath = '/';
        }

        // Rotaları tara ve eşleştir
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Parametrik desen: {param} -> (?P<param>[^/]+)
            $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#u';

            if (preg_match($pattern, $requestPath, $matches)) {
                $params = [];
                foreach ($matches as $key => $val) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($val);
                    }
                }

                try {
                    [$controllerClass, $action] = $route['handler'];
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $action], $params);
                    return;
                } catch (\Throwable $e) {
                    // 5xx Guardian beklenmeyen controller ve veritabanı hatalarını yakalar
                    ServerErrorGuardianMiddleware::handleException($e);
                    return;
                }
            }
        }

        // Eşleşme bulunamadı: Hafta 07 (Görev 4) - 404 Guardian Middleware devreye girer
        NotFoundGuardianMiddleware::handle($requestPath, $requestMethod, $this->routes);
    }
}
