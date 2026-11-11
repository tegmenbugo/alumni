<?php
namespace App\Middlewares;

use App\Core\Response;
use Throwable;

/**
 * ServerErrorGuardianMiddleware (5xx Guardian)
 * Sunucuda oluşabilecek 500'lü (500, 502, 503) tüm istisnaları, veritabanı çökmelerini
 * ve çalışma zamanı (runtime) fatal hatalarını yakalayan, gerekçeli (reason) hata üreten
 * ve hatanın tam dökümünü logs/error.logs dosyasına kaydeden koruyucu ara katman.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 07 (Görev 5 & 6)
 */
class ServerErrorGuardianMiddleware
{
    private static bool $registered = false;

    /**
     * Global Exception ve Error işleyicilerini sisteme kaydeder.
     */
    public static function register(): void
    {
        if (self::$registered) {
            return;
        }
        self::$registered = true;

        // 1. Yakalanmamış tüm Exception / Throwable hatalarını yakala
        set_exception_handler(function (Throwable $e) {
            self::handleException($e);
        });

        // 2. Standart PHP hatalarını ErrorException'a çevir (Fatal/Warning ayrımı)
        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        // 3. Script kapanışında Fatal Error (E_ERROR, E_PARSE vb.) kontrolü
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
                self::handleFatal($error['message'], $error['file'], $error['line']);
            }
        });
    }

    /**
     * Throwable türündeki tüm 500 hatalarını ele alır.
     */
    public static function handleException(Throwable $e, int $statusCode = 500): void
    {
        $errorId = 'ERR-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
        $analysis = self::analyzeReason($e);

        // logs/error.logs dosyasına tam stack trace ile kaydet
        self::logToFile($errorId, $analysis, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

        self::renderResponse($statusCode, $errorId, $analysis, $e->getMessage());
    }

    /**
     * Fatal PHP hatalarını yakalayıp 500 gerekçesine dönüştürür.
     */
    public static function handleFatal(string $message, string $file, int $line): void
    {
        $errorId = 'FATAL-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
        $analysis = [
            'category' => 'FATAL_CORE_ERROR',
            'reason'   => 'PHP çekirdeğinde kritik bir çalışma zamanı (Fatal Error) oluştu.'
        ];

        self::logToFile($errorId, $analysis, $message, $file, $line, '');
        self::renderResponse(500, $errorId, $analysis, $message);
    }

    /**
     * Hatanın kaynağını analiz ederek kullanıcıya sunulacak gerekçeyi (reason) belirler.
     */
    private static function analyzeReason(Throwable $e): array
    {
        $class = get_class($e);
        $msg = $e->getMessage();

        // 1. Veritabanı ve PDO Hataları
        if ($e instanceof \PDOException || str_contains($class, 'PDO') || str_contains(strtolower($msg), 'sql')) {
            return [
                'category' => 'DATABASE_ENGINE_ERROR',
                'reason'   => 'Veritabanı motoru sorguyu yürütemedi veya bağlantı kesintisi yaşandı. (Database Integrity / Connectivity Failure)'
            ];
        }

        // 2. Bellek veya Kaynak Aşımı Hataları
        if (str_contains(strtolower($msg), 'memory') || str_contains(strtolower($msg), 'maximum execution time')) {
            return [
                'category' => 'RESOURCE_EXHAUSTION',
                'reason'   => 'Sunucu işlem zamanı veya bellek sınırı aşıldı.'
            ];
        }

        // 3. Eksik Sınıf / Metot / Dosya Hataları
        if ($e instanceof \Error && (str_contains($msg, 'Call to undefined') || str_contains($msg, 'Class not found'))) {
            return [
                'category' => 'APPLICATION_LOGIC_ERROR',
                'reason'   => 'Uygulama kodu içinde tanımlanmamış bir sınıf veya fonksiyon çağrıldı.'
            ];
        }

        // 4. Genel 500 İstisnası
        return [
            'category' => 'UNHANDLED_SERVER_EXCEPTION',
            'reason'   => 'Sunucu tarafında işlenemeyen beklenmeyen bir iç istisna (Unhandled Internal Exception) meydana geldi.'
        ];
    }

    /**
     * logs/error.logs ve logs/error.log dosyasına detaylı teknik döküm yazar.
     */
    private static function logToFile(string $errorId, array $analysis, string $message, string $file, int $line, string $trace): void
    {
        $logDir = LoggerMiddleware::getLogDirectory();
        $date = date('Y-m-d H:i:s');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $entry = sprintf(
            "================================================================================\n" .
            "[%s] [5xx GUARDIAN] ID: %s | STATUS: 500\n" .
            "CLIENT IP: %s | METHOD: %s | ROUTE: %s\n" .
            "CATEGORY: %s\n" .
            "REASON: %s\n" .
            "MESSAGE: %s\n" .
            "FILE: %s:%d\n" .
            "STACK TRACE:\n%s\n" .
            "================================================================================\n\n",
            $date,
            $errorId,
            $ip,
            $method,
            $uri,
            $analysis['category'],
            $analysis['reason'],
            $message,
            $file,
            $line,
            $trace
        );

        @file_put_contents($logDir . '/error.logs', $entry, FILE_APPEND | LOCK_EX);
        @file_put_contents($logDir . '/error.log', $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * İstemciye (API için JSON, Tarayıcı için HTML) gerekçeli 500 yanıtı döner.
     */
    private static function renderResponse(int $statusCode, string $errorId, array $analysis, string $rawMessage): void
    {
        http_response_code($statusCode);

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $isApi = str_starts_with($uri, '/api/') 
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        // Geliştirme ortamında (APP_DEBUG=true) detaylı mesajı göster, değilse gizle
        $debug = getenv('APP_DEBUG') === 'true' || getenv('APP_DEBUG') === '1';

        if ($isApi) {
            $response = [
                'status'          => $statusCode,
                'error'           => 'Internal Server Error',
                'guardian'        => 'ServerErrorGuardianMiddleware (5xx Guardian)',
                'error_id'        => $errorId,
                'reason'          => $analysis['reason'],
                'reason_category' => $analysis['category'],
                'message'         => 'Sunucu bu isteği işlerken beklenmeyen bir hatayla karşılaştı. Detaylar sistem günlüğüne (logs/error.logs) kaydedildi.',
                'timestamp'       => date('Y-m-d H:i:s'),
                'support_note'    => "Lütfen hata referans numarasını ({$errorId}) sistem yöneticisine iletiniz."
            ];

            if ($debug) {
                $response['debug_detail'] = $rawMessage;
            }

            Response::json($response, $statusCode);
            exit;
        }

        // Web Tarayıcısı için Zengin 5xx Guardian Sayfası
        self::renderHtmlView($statusCode, $errorId, $analysis, $debug ? $rawMessage : null);
        exit;
    }

    /**
     * Web arayüzü için Bootstrap 5xx Guardian görünümü
     */
    private static function renderHtmlView(int $statusCode, string $errorId, array $analysis, ?string $debugDetail): void
    {
        $safeErrorId = htmlspecialchars($errorId, ENT_QUOTES, 'UTF-8');
        $safeReason = htmlspecialchars($analysis['reason'], ENT_QUOTES, 'UTF-8');
        $safeCategory = htmlspecialchars($analysis['category'], ENT_QUOTES, 'UTF-8');
        $time = date('H:i:s d.m.Y');

        $debugHtml = '';
        if ($debugDetail !== null) {
            $safeDebug = htmlspecialchars($debugDetail, ENT_QUOTES, 'UTF-8');
            $debugHtml = <<<HTML
            <div class="mt-3 p-3 bg-dark text-danger font-monospace small rounded">
                <strong>Hata Detayı (APP_DEBUG=true):</strong><br>{$safeDebug}
            </div>
HTML;
        }

        echo <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Sunucu Hatası · 5xx Guardian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .guardian-card { max-width: 680px; margin: 60px auto; border-radius: 16px; border: 1px solid #fee2e2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card guardian-card shadow-sm p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-semibold mb-2">
                    🛡️ 5xx Guardian Middleware
                </span>
                <h1 class="display-5 fw-bold text-dark mt-2">500 - Sunucu Hatası</h1>
                <p class="text-muted">İşleminiz gerçekleştirilirken beklenmeyen bir sunucu hatası oluştu.</p>
            </div>

            <div class="alert alert-danger border-danger-subtle" role="alert">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="alert-heading fw-bold mb-0">🚨 Hatanın Gerekçesi (Reason):</h6>
                    <span class="badge bg-danger-subtle text-danger">Takip ID: {$safeErrorId}</span>
                </div>
                <p class="mb-1">{$safeReason}</p>
                <hr class="my-2">
                <div class="small text-secondary">
                    <strong>Hata Kategorisi:</strong> <code>{$safeCategory}</code> | 
                    <strong>Kayıt Yeri:</strong> <code>logs/error.logs</code>
                </div>
                {$debugHtml}
            </div>

            <div class="p-3 bg-light rounded small text-secondary mb-4">
                💡 <strong>Geliştirici Notu:</strong> Hatanın tüm teknik ayrıntıları ve çağrı yığını (stack trace) <code>logs/error.logs</code> dosyasına güvenli biçimde arşivlenmiştir.
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top text-muted small">
                <span>Zaman: {$time}</span>
                <div class="d-flex gap-2">
                    <a href="/" class="btn btn-primary btn-sm">🏠 Ana Sayfa</a>
                    <a href="/jobs" class="btn btn-outline-secondary btn-sm">💼 İlanlar</a>
                    <a href="javascript:location.reload()" class="btn btn-outline-danger btn-sm">🔄 Yeniden Dene</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
