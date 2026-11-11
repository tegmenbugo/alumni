<?php
namespace App\Middlewares;

/**
 * LoggerMiddleware
 * Client'tan server'a gelen tüm HTTP isteklerini izleyen,
 * hem terminale anlık çıktı basan hem de kalıcı olarak dosyalara (logs/error.logs) yazan ara katman.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 07 (Görev 3 & 6)
 *
 * Terminal Formatı:
 * time 10.20.39 method:POST route:/user response type : 201 duration :18.4ms
 *
 * Dosya Loglama:
 *  - Hata Logları (4xx, 5xx): logs/error.logs
 *  - Tüm İstek Logları:      logs/requests.log
 */
class LoggerMiddleware
{
    private static ?float $startTime = null;
    private static ?string $logDir = null;

    /**
     * Log dizini yolunu döndürür ve yoksa otomatik oluşturur.
     */
    public static function getLogDirectory(): string
    {
        if (self::$logDir === null) {
            self::$logDir = dirname(__DIR__, 2) . '/logs';
        }
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0777, true);
        }
        return self::$logDir;
    }

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
     * İstek bittiğinde süreyi ve durum kodunu hem terminale hem dosyalara loglar.
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
            : 0.0;

        $time = date('H.i.s');
        $fullDate = date('Y-m-d H:i:s');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $statusCode = http_response_code() ?: 200;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 1. Terminal Log Formatı (Hocanın talep ettiği düzeltilmiş "method" formatı)
        // time 10.20.39 method:POST route:/user response type : 201 duration :18ms
        $logMessage = sprintf(
            "time %s method:%s route:%s response type : %d duration :%sms\n",
            $time,
            $method,
            $uri,
            $statusCode,
            $durationMs
        );

        // Terminal & Konsol Çıktısı:
        if (defined('STDERR') && is_resource(STDERR)) {
            fwrite(STDERR, $logMessage);
        } else {
            @file_put_contents('php://stderr', $logMessage);
        }
        error_log(trim($logMessage));

        // 2. Dosyalara Kalıcı Loglama (Görev 6):
        $logDir = self::getLogDirectory();

        // Tüm isteklerin genel kayıt dosyası (logs/requests.log)
        $requestLine = sprintf("[%s] IP:%s %s", $fullDate, $ip, $logMessage);
        @file_put_contents($logDir . '/requests.log', $requestLine, FILE_APPEND | LOCK_EX);

        // Hata durumunda (4xx Client Error veya 5xx Server Error) -> logs/error.logs
        if ($statusCode >= 400) {
            $errorLine = sprintf(
                "[%s] [STATUS %d] IP:%s METHOD:%s ROUTE:%s DURATION:%sms\n",
                $fullDate,
                $statusCode,
                $ip,
                $method,
                $uri,
                $durationMs
            );
            // Hocanın istediği logs/error.logs dosyasına yaz
            @file_put_contents($logDir . '/error.logs', $errorLine, FILE_APPEND | LOCK_EX);
            // Standart error.log dosyasına da yaz
            @file_put_contents($logDir . '/error.log', $errorLine, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Özel veya detaylı hata logu yazmak için yardımcı metot (5xx Guardian & 404 Guardian için)
     */
    public static function logError(string $reason, array $context = []): void
    {
        $logDir = self::getLogDirectory();
        $fullDate = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? ' | CONTEXT: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        
        $errorEntry = sprintf(
            "[%s] [ERROR] %s%s\n",
            $fullDate,
            $reason,
            $contextJson
        );

        @file_put_contents($logDir . '/error.logs', $errorEntry, FILE_APPEND | LOCK_EX);
        @file_put_contents($logDir . '/error.log', $errorEntry, FILE_APPEND | LOCK_EX);
    }
}
