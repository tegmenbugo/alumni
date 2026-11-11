<?php
namespace App\Middlewares;

use App\Core\Response;

/**
 * NotFoundGuardianMiddleware (404 Guardian)
 * Sistemde eşleşmeyen istekleri yakalayarak sadece kuru bir "404" yerine
 * hatanın somut gerekçesini (reason) ve akıllı çözüm önerilerini sunan ara katman.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 07 (Görev 4)
 */
class NotFoundGuardianMiddleware
{
    /**
     * Eşleşmeyen rotayı analiz eder ve gerekçeli 404 yanıtı üretir.
     *
     * @param string $path            İstenen URL yolu
     * @param string $method          Kullanılan HTTP metodu
     * @param array  $registeredRoutes Sistemde tanımlı tüm rotalar [['method'=>'GET', 'path'=>'/...'], ...]
     */
    public static function handle(string $path, string $method, array $registeredRoutes): void
    {
        $analysis = self::analyzeReason($path, $method, $registeredRoutes);

        // Görev 6: 404 Guardian hatayı ve gerekçesini logs/error.logs dosyasına kaydeder
        LoggerMiddleware::logError("404 NOT FOUND: {$analysis['reason']}", [
            'path'        => $path,
            'method'      => $method,
            'category'    => $analysis['category'],
            'suggestions' => $analysis['suggestions']
        ]);

        http_response_code(404);

        // API isteği mi (veya Accept: application/json) kontrolü
        $isApi = str_starts_with($path, '/api/') 
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if ($isApi) {
            Response::json([
                'status'           => 404,
                'error'            => 'Not Found',
                'guardian'         => 'NotFoundGuardianMiddleware',
                'requested_path'   => $path,
                'requested_method' => $method,
                'reason'           => $analysis['reason'],
                'reason_category'  => $analysis['category'],
                'suggestions'      => $analysis['suggestions'],
                'allowed_methods'  => $analysis['allowed_methods'] ?? null,
                'timestamp'        => date('Y-m-d H:i:s'),
                'documentation'    => 'http://localhost:8000/api/swagger'
            ], 404);
            return;
        }

        // Web Arayüzü için Zengin HTML 404 Guardian Görünümü
        self::renderHtmlView($path, $method, $analysis);
        exit;
    }

    /**
     * Rota bulunamama sebebini ve akıllı önerileri tespit eder.
     */
    private static function analyzeReason(string $path, string $method, array $registeredRoutes): array
    {
        $cleanPath = '/' . trim($path, '/');
        if ($cleanPath === '//') $cleanPath = '/';

        // 1. Durum: Aynı yol başka bir HTTP metodu ile var mı? (Method Mismatch)
        $allowedMethodsForPath = [];
        foreach ($registeredRoutes as $route) {
            $routePath = $route['path'];
            // Basit veya regex desen kontrolü
            $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '[^/]+', $routePath);
            if (preg_match('#^' . $pattern . '$#u', $cleanPath)) {
                $allowedMethodsForPath[] = $route['method'];
            }
        }

        if (!empty($allowedMethodsForPath)) {
            return [
                'category'        => 'METHOD_NOT_ALLOWED_ON_ENDPOINT',
                'reason'          => "Bu URL yolu ('{$cleanPath}') sistemde mevcuttur; ancak '{$method}' HTTP metodu desteklenmemektedir.",
                'suggestions'     => ["Desteklenen HTTP metotları: " . implode(', ', array_unique($allowedMethodsForPath))],
                'allowed_methods' => array_unique($allowedMethodsForPath)
            ];
        }

        // 2. Durum: Benzer rotalar (Typo / Levenshtein Benzerlik Taraması)
        $uniquePaths = array_values(array_unique(array_column($registeredRoutes, 'path')));
        $suggestions = [];

        foreach ($uniquePaths as $candidate) {
            // Parametresiz temiz yol
            $staticCandidate = preg_replace('#/\{[a-zA-Z0-9_]+\}#', '', $candidate);
            $lev = levenshtein($cleanPath, $staticCandidate);

            // Benzerlik skoru veya içerme kontrolü
            if ($lev <= 4 || str_contains($candidate, trim($cleanPath, '/')) || str_contains($cleanPath, trim($staticCandidate, '/'))) {
                $suggestions[] = $candidate;
            }
        }
        $suggestions = array_slice(array_unique($suggestions), 0, 4);

        if (!empty($suggestions)) {
            return [
                'category'    => 'TYPO_OR_SIMILAR_ROUTE',
                'reason'      => "İstenen rota ('{$cleanPath}') sistemde kayıtlı değildir. Bir yazım hatası yapmış olabilirsiniz.",
                'suggestions' => $suggestions
            ];
        }

        // 3. Durum: Desteklenmeyen API sürümü veya ad alanı
        if (preg_match('#^/v[0-9]+/#', $cleanPath)) {
            return [
                'category'    => 'UNSUPPORTED_API_VERSION',
                'reason'      => "Talep edilen API sürümü sistemde aktif değildir. Mezun Takip Sistemi tüm REST servislerini '/api/*' altında sunmaktadır.",
                'suggestions' => ['/api/users', '/api/jobs', '/api/applications', '/api/swagger']
            ];
        }

        // 4. Durum: Genel Bilinmeyen Rota
        return [
            'category'    => 'UNKNOWN_ENDPOINT',
            'reason'      => "Bu adrese ('{$cleanPath}') ait hiçbir yönlendirme kuralı veya denetleyici (controller) tanımlanmamıştır.",
            'suggestions' => ['/', '/users', '/jobs', '/api/swagger']
        ];
    }

    /**
     * Web tarayıcıları için kullanıcı dostu Bootstrap 404 sayfası çizer.
     */
    private static function renderHtmlView(string $path, string $method, array $analysis): void
    {
        $safePath = htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
        $safeMethod = htmlspecialchars($method, ENT_QUOTES, 'UTF-8');
        $safeReason = htmlspecialchars($analysis['reason'], ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($analysis['category'], ENT_QUOTES, 'UTF-8');
        $time = date('H:i:s d.m.Y');

        echo <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Sayfa Bulunamadı · 404 Guardian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .guardian-card { max-width: 680px; margin: 60px auto; border-radius: 16px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card guardian-card shadow-sm p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold mb-2">
                    🛡️ 404 Guardian Middleware
                </span>
                <h1 class="display-5 fw-bold text-dark mt-2">Sayfa Bulunamadı</h1>
                <p class="text-muted">İstediğiniz kaynak sunucuda mevcut değil veya taşınmış olabilir.</p>
            </div>

            <div class="alert alert-warning border-warning-subtle" role="alert">
                <h6 class="alert-heading fw-bold mb-2">🔍 Hatanın Gerekçesi (Reason):</h6>
                <p class="mb-1">{$safeReason}</p>
                <hr class="my-2">
                <div class="small text-secondary">
                    <strong>İstenen Yol:</strong> <code>{$safePath}</code> | 
                    <strong>Metot:</strong> <span class="badge bg-secondary">{$safeMethod}</span> | 
                    <strong>Kategori:</strong> <code>{$category}</code>
                </div>
            </div>

HTML;

        if (!empty($analysis['suggestions'])) {
            echo '<div class="mb-4"><h6 class="fw-semibold text-secondary">💡 Önerilen Geçerli Rotalar:</h6><div class="d-flex flex-wrap gap-2">';
            foreach ($analysis['suggestions'] as $sug) {
                $cleanSug = htmlspecialchars($sug, ENT_QUOTES, 'UTF-8');
                if (str_starts_with($sug, '/')) {
                    echo "<a href=\"{$cleanSug}\" class=\"btn btn-sm btn-outline-primary\">{$cleanSug}</a>";
                } else {
                    echo "<span class=\"badge bg-light text-dark border p-2\">{$cleanSug}</span>";
                }
            }
            echo '</div></div>';
        }

        echo <<<HTML
            <div class="d-flex justify-content-between align-items-center pt-3 border-top text-muted small">
                <span>Zaman: {$time}</span>
                <div class="d-flex gap-2">
                    <a href="/" class="btn btn-primary btn-sm">🏠 Ana Sayfa</a>
                    <a href="/jobs" class="btn btn-outline-secondary btn-sm">💼 İlanlar</a>
                    <a href="/api/swagger" class="btn btn-outline-dark btn-sm">📚 Swagger UI</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
