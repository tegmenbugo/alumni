<?php
/**
 * Alumni Tracking System - Database Seeder CLI Runner
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 05 (Görev 6)
 *
 * Kullanım:
 *   php seed.php
 */

spl_autoload_register(function ($class) {
    // App\...
    if (str_starts_with($class, 'App\\')) {
        $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
    // Database\...
    if (str_starts_with($class, 'Database\\')) {
        $file = __DIR__ . '/database/' . str_replace('\\', '/', substr($class, 9)) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

use App\Core\Env;
use App\Core\Database;
use Database\Seeders\DatabaseSeeder;

Env::load();

echo "\n=======================================================\n";
echo "🌱 ALUMNI TRACKING SYSTEM - DATABASE SEEDER (Görev 6)\n";
echo "   UserFactory ve Sentetik Veri Üretim Aracı\n";
echo "=======================================================\n";

try {
    $pdo = Database::connect();
    echo "📡 Veritabanı bağlantısı kuruldu.\n";
    echo "⚙️ UserFactory ile veriler üretiliyor ve tohumlanıyor...\n";

    $result = DatabaseSeeder::run($pdo);

    echo "🎉 Tohumlama Başarılı!\n";
    foreach ($result['details'] as $msg) {
        echo "   [✓] {$msg}\n";
    }
    echo "📊 Veritabanındaki Toplam Mezun Sayısı: " . $result['totalUsers'] . "\n";
    echo "✅ Artık CRUD ekranlarınız (http://localhost:8000/users) verilerle dolup taşıyor!\n\n";

} catch (Exception $e) {
    echo "\n❌ HATA: " . $e->getMessage() . "\n\n";
    exit(1);
}
