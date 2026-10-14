<?php
namespace Database\Factories;

use App\Models\User;

/**
 * UserFactory (Veri Üretim Fabrikası)
 * ddd.md şemasına uygun sentetik, gerçekçi ve dinamik mezun verileri üretir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 05 (Görev 6)
 */
class UserFactory
{
    private static array $firstNames = [
        'Ahmet', 'Mehmet', 'Elif', 'Zeynep', 'Ayşe', 'Can', 'Burak', 'Selin', 
        'Mert', 'Gamze', 'Emre', 'Büşra', 'Oğuzhan', 'İrem', 'Batuhan', 
        'Ece', 'Tugay', 'Deniz', 'Kaan', 'Ceren', 'Onur', 'Ezgi', 'Yusuf'
    ];

    private static array $lastNames = [
        'Yılmaz', 'Kaya', 'Demir', 'Çelik', 'Şahin', 'Yıldız', 'Öztürk', 
        'Aydın', 'Özdemir', 'Arslan', 'Doğan', 'Kılıç', 'Aslan', 'Çetin', 
        'Kara', 'Koç', 'Kurt', 'Özkan', 'Şimşek', 'Polat', 'Korkmaz'
    ];

    private static array $departments = [
        'Yönetim Bilişim Sistemleri',
        'Yönetim Bilişim Sistemleri',
        'Yönetim Bilişim Sistemleri', // Ağırlıklı YBS
        'İktisat',
        'İşletme',
        'Siyaset Bilimi ve Uluslararası İlişkiler',
        'Maliye',
        'Çalışma Ekonomisi ve Endüstri İlişkileri'
    ];

    private static array $companies = [
        'Trendyol', 'Getir', 'Hepsiburada', 'QNB Finansbank', 'Garanti BBVA',
        'Yapı Kredi', 'Türkiye İş Bankası', 'Akbank', 'Amazon AWS', 'Google',
        'Microsoft', 'McKinsey & Company', 'PwC Türkiye', 'Deloitte', 'KPMG',
        'Baykar Teknoloji', 'Aselsan', 'Türk Hava Yolları', 'Turkcell', 'Vodafone'
    ];

    /**
     * Tek bir sentetik kullanıcı veri dizisi üretir (Veritabanına yazmaz).
     */
    public static function make(array $overrides = []): array
    {
        $first = self::$firstNames[array_rand(self::$firstNames)];
        $last = self::$lastNames[array_rand(self::$lastNames)];
        $name = "{$first} {$last}";

        // E-posta için Türkçe karakterleri temizle
        $slug = strtolower(str_replace(
            ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü', ' '],
            ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u', '.'],
            $name
        ));
        $email = $slug . mt_rand(10, 999) . '@alumni.iu.edu.tr';

        $data = [
            'name'           => $name,
            'email'          => $email,
            'graduationYear' => mt_rand(2016, 2026),
            'department'     => self::$departments[array_rand(self::$departments)],
            'company'        => self::$companies[array_rand(self::$companies)],
            'role'           => 'alumnus',
            'status'         => 'active'
        ];

        return array_merge($data, $overrides);
    }

    /**
     * Tek bir sentetik kullanıcı üretir ve veritabanına kaydeder.
     */
    public static function create(array $overrides = []): array
    {
        $data = self::make($overrides);
        return User::create($data);
    }

    /**
     * Belirtilen adette kullanıcı üretip veritabanına kaydeder.
     * @param int $count Üretilecek kayıt sayısı (Varsayılan 10)
     * @return array Kaydedilen kullanıcı listesi
     */
    public static function createMany(int $count = 10, array $overrides = []): array
    {
        $created = [];
        for ($i = 0; $i < $count; $i++) {
            $created[] = self::create($overrides);
        }
        return $created;
    }
}
