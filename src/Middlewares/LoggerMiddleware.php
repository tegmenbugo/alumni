<?php
namespace App\Middlewares;

/**
 * LoggerMiddleware
 * Client'tan server'a gelen tüm HTTP isteklerinde terminale log basan ara katman (Middleware).
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 07 (Görev 3)
 *
 * Çıktı Örneği:
 * time: 10.20.39 | method: POST | route: /user | response type: 201 | duration: 18ms
 */
class LoggerMiddleware
{
    private static ?float $startTime = null;

    /**
     * İsteğin başında çalıştırılır.
     */
    public static function start(): void
    {
        self::$startTime = microtime(true);

        // İstek sonlandığında (exit veya script bitimi) shutdown handler ile süreyi ve HTTP kodunu yakala
        register_shutdown_function(function () {
            self::log();
        });
    }

    /**
     * İstek bittiğinde süreyi ve durum kodunu terminale loglar.
     */
    public static function log(): void
    {
        static $logged = false;
        if ($logged) {
            return;
        }
        $logged = true;

        $durationMs = self::$startTime !== null 
            ? round((microtime(true) - self::$startTime) * 1000, 2)
            : 0;

        $time = date('H.i.s');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $statusCode = http_response_code() ?: 200;

        // Terminal Log Formatı (Hocanın tam istediği format)
        // example: time 10.20.39 mehod:POST route:/user response type : 201 duration :18ms
        $logMessage = sprintf(
            "time %s mehod:%s route:%s response type : %d duration :%sms\n",
            $time,
            $method,
            $uri,
            $statusCode,
            $durationMs
        );

        // Terminal ve Konsol Çıktısı:
        // 1. PHP built-in web server konsoluna ve stderr/stdout'a doğrudan yaz
        if (defined('STDERR') && is_resource(STDERR)) {
            fwrite(STDERR, $logMessage);
        } else {
            @file_put_contents('php://stderr', $logMessage);
        }

        // 2. error_log üzerinden Docker container ve sunucu loglarına ilet
        error_log(trim($logMessage));
    }
}
