<?php
namespace App\Core;

use PDO;
use Exception;

/**
 * Veritabanı Migrasyon Yöneticisi (Migration Runner)
 * ddd.md şemasına göre SQL migrasyon dosyalarını sırayla çalıştırır.
 * SQLite ve MySQL uyumludur.
 */
class Migration
{
    private static function initMigrationTable(PDO $pdo): void
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        }

        $pdo->exec($sql);
    }

    public static function getAppliedMigrations(PDO $pdo): array
    {
        self::initMigrationTable($pdo);
        $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public static function getMigrationFiles(): array
    {
        $dir = dirname(__DIR__, 2) . '/database/migrations';
        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/*.sql');
        sort($files);
        return $files;
    }

    /**
     * Bekleyen tüm migrasyonları çalıştırır.
     * @return array Çalıştırılan migrasyonların listesi
     */
    public static function run(PDO $pdo): array
    {
        self::initMigrationTable($pdo);
        $applied = self::getAppliedMigrations($pdo);
        $files = self::getMigrationFiles();
        $executed = [];
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        foreach ($files as $filePath) {
            $migrationName = basename($filePath);

            if (in_array($migrationName, $applied, true)) {
                continue;
            }

            $sqlContent = file_get_contents($filePath);
            if (empty(trim($sqlContent))) {
                continue;
            }

            // MySQL ve SQLite arasındaki küçük sözdizimi farklarını uyarla
            if ($driver === 'mysql') {
                $sqlContent = str_replace('AUTOINCREMENT', 'AUTO_INCREMENT', $sqlContent);
                $sqlContent = str_replace('INSERT OR IGNORE', 'INSERT IGNORE', $sqlContent);
            }

            // Sorguları çalıştır
            try {
                $pdo->beginTransaction();

                // SQL ifadelerini noktalı virgülle ayırıp tek tek çalıştır
                $statements = array_filter(array_map('trim', explode(';', $sqlContent)));
                foreach ($statements as $stmtSql) {
                    if (!empty($stmtSql)) {
                        $pdo->exec($stmtSql);
                    }
                }

                // Migrasyon kaydını tabloya ekle
                $insertStmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
                $insertStmt->execute([':migration' => $migrationName]);

                $pdo->commit();
                $executed[] = $migrationName;
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw new Exception("Migrasyon hatası ({$migrationName}): " . $e->getMessage());
            }
        }

        return $executed;
    }

    /**
     * Tüm migrasyonların durum bilgisini döner.
     */
    public static function getStatus(PDO $pdo): array
    {
        self::initMigrationTable($pdo);
        $applied = self::getAppliedMigrations($pdo);
        $files = self::getMigrationFiles();
        $status = [];

        foreach ($files as $filePath) {
            $name = basename($filePath);
            $status[] = [
                'migration' => $name,
                'status'    => in_array($name, $applied, true) ? 'applied' : 'pending'
            ];
        }

        return $status;
    }
}
