<?php
namespace App\Core;

/**
 * Görünüm Katmanı Yöneticisi (View Renderer)
 */
class View
{
    public static function render(string $viewName, array $data = []): void
    {
        $viewPath = dirname(__DIR__, 2) . '/views/' . $viewName . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "Görünüm dosyası bulunamadı: " . htmlspecialchars($viewName);
            exit;
        }

        // Verileri değişkenlere dönüştür
        extract($data);

        header('Content-Type: text/html; charset=utf-8');
        require $viewPath;
        exit;
    }
}
