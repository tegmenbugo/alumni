<?php
namespace Database\Seeders;

use App\Core\Database;
use App\Models\User;
use Database\Factories\UserFactory;
use PDO;

/**
 * DatabaseSeeder (Veritabanı Tohumlama Yöneticisi)
 * ddd.md şemasına göre sistemi zengin başlangıç ve factory verileriyle doldurur.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 05 (Görev 6)
 */
class DatabaseSeeder
{
    public static function run(?PDO $pdo = null): array
    {
        $db = $pdo ?? Database::connect();
        $seededInfo = [];

        // 1. Çekirdek Mezunlar (Baseline Users)
        $baselineUsers = [
            [
                'name'           => 'Elif Kaya',
                'email'          => 'elif.kaya@alumni.iu.edu.tr',
                'graduationYear' => 2024,
                'department'     => 'Yönetim Bilişim Sistemleri',
                'company'        => 'Trendyol'
            ],
            [
                'name'           => 'Mert Aydın',
                'email'          => 'mert.aydin@alumni.iu.edu.tr',
                'graduationYear' => 2021,
                'department'     => 'Yönetim Bilişim Sistemleri',
                'company'        => 'QNB Finansbank'
            ],
            [
                'name'           => 'Zeynep Arslan',
                'email'          => 'zeynep.arslan@alumni.iu.edu.tr',
                'graduationYear' => 2019,
                'department'     => 'İktisat',
                'company'        => 'Hepsiburada'
            ],
            [
                'name'           => 'Can Öztürk',
                'email'          => 'can.ozturk@alumni.iu.edu.tr',
                'graduationYear' => 2023,
                'department'     => 'İşletme',
                'company'        => 'McKinsey & Company'
            ],
            [
                'name'           => 'Battal Buğra Karataş',
                'email'          => 'bugrakaratas@alumni.iu.edu.tr',
                'graduationYear' => 2027,
                'department'     => 'Yönetim Bilişim Sistemleri',
                'company'        => 'Akadal Technology'
            ]
        ];

        // Mevcut kullanıcı sayısını kontrol et
        $existingCount = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();

        if ($existingCount < 5) {
            foreach ($baselineUsers as $u) {
                // E-posta varsa tekrar ekleme
                $check = $db->prepare("SELECT id FROM users WHERE email = :email");
                $check->execute([':email' => $u['email']]);
                if (!$check->fetch()) {
                    User::create($u);
                }
            }
        }

        // 2. Factory ile Ekstra 15 Dinamik Mezun Üret (Görev 6)
        // Böylece ekranlar verilerle dolup taşar (20+ Mezun)
        $currentTotal = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($currentTotal < 20) {
            $needed = 20 - $currentTotal;
            UserFactory::createMany($needed);
            $seededInfo[] = "UserFactory ile {$needed} yeni mezun üretildi.";
        }

        // 3. Yetenekleri Tohumla (Skills)
        $skills = [
            ['PHP 8.3 & OOP MVC', 'Yazılım'],
            ['Docker & DevOps', 'DevOps'],
            ['SQL & Relational Databases', 'Veri'],
            ['RESTful API & Swagger', 'Yazılım'],
            ['Power BI & Veri Analitiği', 'Veri'],
            ['Python & Machine Learning', 'Yapay Zeka'],
            ['Cloud Architecture (AWS/GCP)', 'Bulut'],
            ['Agile & Scrum Yönetimi', 'Yönetim']
        ];

        $stmtSkill = $db->prepare("INSERT OR IGNORE INTO skills (name, category) VALUES (:name, :category)");
        foreach ($skills as $s) {
            $stmtSkill->execute([':name' => $s[0], ':category' => $s[1]]);
        }

        $seededInfo[] = "Skills kataloğu tohumlandı.";

        // 4. İlk 5 Mezun İçin Biyografi & Profil Tohumla
        $profiles = [
            [1, 'İstanbul', 'Türkiye', 'İÜ YBS 2024 mezunu. Trendyol bünyesinde Backend Developer.', 'https://linkedin.com/in/elif-kaya'],
            [2, 'İstanbul', 'Türkiye', 'Finans ve bankacılık sektöründe Kıdemli Veri Analisti.', 'https://linkedin.com/in/mert-aydin'],
            [3, 'İstanbul', 'Türkiye', 'E-Ticaret ve Büyüme Pazarlaması Yöneticisi.', 'https://linkedin.com/in/zeynep-arslan'],
            [4, 'Londra', 'İngiltere', 'Yönetim danışmanı ve strateji lideri.', 'https://linkedin.com/in/can-ozturk'],
            [5, 'İstanbul', 'Türkiye', 'İÜ YBS Öğrencisi ve Web Geliştirici.', 'https://linkedin.com/in/bugrakaratas']
        ];

        $stmtProf = $db->prepare("INSERT OR IGNORE INTO profiles (user_id, city, country, bio, linkedin_url) VALUES (?, ?, ?, ?, ?)");
        foreach ($profiles as $p) {
            $stmtProf->execute($p);
        }

        // 5. İş İlanlarını Tohumla (Job Postings - Hafta 06 Görev 6)
        try {
            $jobsCount = (int)$db->query("SELECT COUNT(*) FROM job_postings")->fetchColumn();
            if ($jobsCount < 6 && class_exists(\Database\Factories\JobPostingFactory::class)) {
                $neededJobs = 6 - $jobsCount;
                \Database\Factories\JobPostingFactory::createMany($neededJobs);
                $seededInfo[] = "JobPostingFactory ile {$neededJobs} yeni iş/staj ilanı üretildi.";
            }

            // 6. Örnek Başvuru Tohumla (Job Applications)
            $appsCount = (int)$db->query("SELECT COUNT(*) FROM job_applications")->fetchColumn();
            if ($appsCount === 0) {
                $stmtApp = $db->prepare("
                    INSERT OR IGNORE INTO job_applications (job_posting_id, applicant_user_id, status, cover_letter)
                    VALUES (?, ?, ?, ?)
                ");
                $stmtApp->execute([1, 5, 'applied', 'İÜ YBS öğrencisiyim. Backend geliştirme ve PHP konusunda projelerim bulunmaktadır.']);
                $stmtApp->execute([2, 4, 'reviewing', 'Veri analitiği ve SQL tecrübelerimle ilana başvurmak istiyorum.']);
                $seededInfo[] = "Örnek iş başvuruları tohumlandı.";
            }
        } catch (\Exception $e) {
            $seededInfo[] = "İlan tohumlama notu: " . $e->getMessage();
        }

        $totalUsers = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalJobs = 0;
        try {
            $totalJobs = (int)$db->query("SELECT COUNT(*) FROM job_postings")->fetchColumn();
        } catch (\Exception $e) {}

        $seededInfo[] = "Toplam mezun sayısı: {$totalUsers}, Toplam iş ilanı: {$totalJobs}";

        return [
            'status'     => 'success',
            'totalUsers' => $totalUsers,
            'totalJobs'  => $totalJobs,
            'details'    => $seededInfo
        ];
    }
}
