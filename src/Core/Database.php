<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Bağlantı Yöneticisi (PDO Singleton)
 * SQLite tabanlı taşınabilir ve sıfır kurulumlu veritabanı motoru sağlar.
 */
class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $dbDir = dirname(__DIR__, 2) . '/data';
            $dbPath = $dbDir . '/alumni.sqlite';

            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }

            try {
                self::$instance = new PDO("sqlite:" . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Tablo şemasını başlat
                self::initSchema(self::$instance);
            } catch (PDOException $e) {
                http_response_code(500);
                echo "Veritabanı bağlantı hatası: " . $e->getMessage();
                exit;
            }
        }

        return self::$instance;
    }

    private static function initSchema(PDO $pdo): void
    {
        // 1. Users tablosunu oluştur
        $createTableSql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT,
            graduationYear INTEGER,
            department TEXT,
            company TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($createTableSql);

        // 2. Eğer tablo boşsa, eski data/users.json'dan veya varsayılan mezunlardan içe aktar (Seed)
        $count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count === 0) {
            self::seedInitialUsers($pdo);
        }
    }

    private static function seedInitialUsers(PDO $pdo): void
    {
        $jsonFile = dirname(__DIR__, 2) . '/data/users.json';
        $initialUsers = [];

        if (file_exists($jsonFile)) {
            $initialUsers = json_decode(file_get_contents($jsonFile), true) ?: [];
        }

        if (empty($initialUsers)) {
            $initialUsers = [
                ["name" => "Elif Kaya", "email" => "elif.kaya@alumni.iu.edu.tr", "graduationYear" => 2024, "department" => "Yönetim Bilişim Sistemleri", "company" => "Tech Solutions"],
                ["name" => "Mert Aydın", "email" => "mert.aydin@alumni.iu.edu.tr", "graduationYear" => 2021, "department" => "Yönetim Bilişim Sistemleri", "company" => "Finansbank"],
                ["name" => "Battal Buğra Karataş", "email" => "bugrakaratas@alumni.iu.edu.tr", "graduationYear" => 2027, "department" => "Yönetim Bilişim Sistemleri", "company" => "Akadal Technology"]
            ];
        }

        $stmt = $pdo->prepare("INSERT INTO users (name, email, graduationYear, department, company) VALUES (:name, :email, :graduationYear, :department, :company)");

        foreach ($initialUsers as $u) {
            $stmt->execute([
                ':name'           => $u['name'] ?? 'İsimsiz',
                ':email'          => $u['email'] ?? null,
                ':graduationYear' => isset($u['graduationYear']) ? (int)$u['graduationYear'] : null,
                ':department'     => $u['department'] ?? null,
                ':company'        => $u['company'] ?? null
            ]);
        }
    }
}
