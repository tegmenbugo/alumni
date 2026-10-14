<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Bağlantı Yöneticisi (PDO Singleton)
 * SQLite ve MySQL destekli, .env ortam değişkenleriyle yapılandırılan,
 * ddd.md migrasyonlarını otomatik yöneten veritabanı çekirdeği.
 */
class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            Env::load();

            $connection = Env::get('DB_CONNECTION', 'sqlite');

            if ($connection === 'mysql') {
                self::$instance = self::connectMysql();
            } else {
                self::$instance = self::connectSqlite();
            }

            // ddd.md şemasına göre tüm tabloları ve sentetik verileri migrasyonla üret
            try {
                Migration::run(self::$instance);
            } catch (\Exception $e) {
                error_log("Otomatik migrasyon uyarısı: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    private static function connectSqlite(): PDO
    {
        $dbPath = Env::get('DB_DATABASE', 'data/alumni.sqlite');

        // Göreceli yolları tam dosya yoluna çevir
        if (!str_starts_with($dbPath, '/')) {
            $dbPath = dirname(__DIR__, 2) . '/' . ltrim($dbPath, '/');
        }

        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            @mkdir($dbDir, 0777, true);
        }

        try {
            $pdo = new PDO("sqlite:" . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec("PRAGMA foreign_keys = ON;");
            return $pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "SQLite veritabanı bağlantı hatası: " . $e->getMessage();
            exit;
        }
    }

    private static function connectMysql(): PDO
    {
        $host = Env::get('DB_HOST', 'db');
        $port = Env::get('DB_PORT', '3306');
        $database = Env::get('DB_DATABASE_NAME', 'alumni_db');
        $username = Env::get('DB_USERNAME', 'alumni_user');
        $password = Env::get('DB_PASSWORD', 'alumni_secret_password');

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            error_log("MySQL bağlantısı kurulamadı ({$host}:{$port}), SQLite yedeğine dönülüyor: " . $e->getMessage());
            return self::connectSqlite();
        }
    }
}
