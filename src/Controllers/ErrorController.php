<?php
namespace App\Controllers;

use App\Core\View;

/**
 * ErrorController
 * Sistem genelindeki hata sayfalarını ve geçersiz istek yönlendirmelerini yönetir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 07 (Görev 7)
 */
class ErrorController
{
    /**
     * GET /404 - Generic 404 Sayfası
     * Geçersiz web isteklerinin yönlendirildiği merkezi hata ekranı.
     */
    public function notFound(): void
    {
        http_response_code(404);

        $path = $_GET['from'] ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $reason = $_GET['reason'] ?? 'Talep edilen web sayfası bulunamadı veya taşındı.';

        View::render('errors/404', [
            'path'   => $path,
            'reason' => $reason
        ]);
    }
}
