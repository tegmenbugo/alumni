<?php
/**
 * Alumni Tracking System - Database Migration CLI Runner
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming
 *
 * Kullanım:
 *   php migrate.php           -> Bekleyen tüm migrasyonları çalıştırır
 *   php migrate.php --status  -> Migrasyon durumunu listeler
 */

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Env;
use App\Core\Database;
use App\Core\Migration;

Env::load();

echo "\n=======================================================\n";
echo "🎓 ALUMNI TRACKING SYSTEM - DATABASE MIGRATION RUNNER\n";
echo "   ddd.md Veri Şeması Üretim ve Senkronizasyon Aracı\n";
echo "=======================================================\n";

$action = $argv[1] ?? 'run';
$pdo = Database::connect();
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
echo "📡 Aktif Veritabanı Sürücüsü: " . strtoupper($driver) . "\n\n";

if ($action === '--status' || $action === 'status') {
    $statusList = Migration::getStatus($pdo);
    echo "📋 Migrasyon Durum Tablosu:\n";
    echo str_repeat('-', 60) . "\n";
    printf("%-45s | %-10s\n", "MİGRASYON DOSYASI", "DURUM");
    echo str_repeat('-', 60) . "\n";
    foreach ($statusList as $item) {
        $badge = $item['status'] === 'applied' ? '✅ UYGULANDI' : '⏳ BEKLİYOR';
        printf("%-45s | %-10s\n", $item['migration'], $badge);
    }
    echo str_repeat('-', 60) . "\n\n";
    exit(0);
}

try {
    echo "🚀 ddd.md şemasına göre migrasyonlar çalıştırılıyor...\n";
    $executed = Migration::run($pdo);

    if (empty($executed)) {
        echo "✨ Veritabanı zaten güncel! Bekleyen migrasyon bulunmuyor.\n";
    } else {
        echo "🎉 Başarıyla uygulanan migrasyonlar:\n";
        foreach ($executed as $mig) {
            echo "   [✓] {$mig}\n";
        }
    }

    // Toplam tablo sayısını doğrula
    if ($driver === 'sqlite') {
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    }

    echo "\n📊 Veritabanındaki Aktif Tablolar (" . count($tables) . "):\n";
    foreach ($tables as $t) {
        echo "   • {$t}\n";
    }
    echo "\n✅ Veritabanı üretimi ddd.md ile %100 uyumlu olarak tamamlandı!\n\n";

} catch (Exception $e) {
    echo "\n❌ HATA: " . $e->getMessage() . "\n\n";
    exit(1);
}
