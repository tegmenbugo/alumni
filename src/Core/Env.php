<?php
namespace App\Core;

/**
 * Ortam Değişkenleri Yöneticisi (.env Loader)
 * Harici Composer kütüphanesi olmadan native PHP ile .env dosyasını parse eder.
 */
class Env
{
    private static bool $loaded = false;

    /**
     * .env dosyasını okur ve $_ENV, $_SERVER ve putenv() üzerine kaydeder.
     */
    public static function load(?string $path = null): void
    {
        if (self::$loaded) {
            return;
        }

        $baseDir = dirname(__DIR__, 2);
        $envFile = $path ?? ($baseDir . '/.env');

        // .env yoksa .example.env dosyasını fallback olarak oku
        if (!file_exists($envFile)) {
            $exampleFile = $baseDir . '/.example.env';
            if (file_exists($exampleFile)) {
                $envFile = $exampleFile;
            } else {
                self::$loaded = true;
                return;
            }
        }

        $lines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            self::$loaded = true;
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Boş satırları ve yorum satırlarını atla
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Baştaki ve sondaki çift/tek tırnakları temizle
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                // Sistem ortamında zaten varsa ezme
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                    putenv("{$key}={$value}");
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Ortam değişkenini getirir, yoksa varsayılan değeri döner.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        if (array_key_exists($key, $_ENV)) {
            return self::castValue($_ENV[$key]);
        }

        if (array_key_exists($key, $_SERVER)) {
            return self::castValue($_SERVER[$key]);
        }

        $val = getenv($key);
        if ($val !== false) {
            return self::castValue($val);
        }

        return $default;
    }

    private static function castValue(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $lower = strtolower($value);
        if ($lower === 'true') {
            return true;
        }
        if ($lower === 'false') {
            return false;
        }
        if ($lower === 'null') {
            return null;
        }

        return $value;
    }
}
