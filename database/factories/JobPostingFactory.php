<?php
namespace Database\Factories;

use App\Models\JobPosting;
use App\Models\User;

/**
 * JobPostingFactory (İş ve Staj İlanı Üretim Fabrikası)
 * ddd.md şemasına uygun gerçekçi iş/staj ilanları üretir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 06 (Görev 6)
 */
class JobPostingFactory
{
    private static array $titles = [
        'Junior Backend Developer (PHP & MySQL)',
        'Data Analyst / BI Uzmanı',
        'Cloud & DevOps Engineer (Docker / AWS)',
        'Junior Product Manager',
        'Frontend Developer (Vue / React)',
        'İş Analisti (ERP & Fintek)',
        'Machine Learning & Python Stajyeri',
        'Siber Güvenlik Analisti',
        'Yönetim Bilişim Sistemleri Stajyeri',
        'Full Stack Web Developer'
    ];

    private static array $companies = [
        'Trendyol', 'Getir', 'Hepsiburada', 'QNB Finansbank', 'Garanti BBVA',
        'Yapı Kredi', 'Türkiye İş Bankası', 'Google Türkiye', 'Amazon AWS',
        'Baykar Teknoloji', 'Aselsan', 'McKinsey & Company', 'PwC Türkiye'
    ];

    private static array $employmentTypes = [
        'Tam Zamanlı', 'Tam Zamanlı', 'Tam Zamanlı', 'Yarı Zamanlı', 'Stajyer'
    ];

    private static array $workplaceTypes = [
        'Hibrit', 'Hibrit', 'Uzaktan / Remote', 'Ofisten'
    ];

    private static array $locations = [
        'İstanbul (Maslak)', 'İstanbul (Levent)', 'İstanbul / Hibrit', 
        'Ankara (ODTÜ Teknokent)', 'Uzaktan / Remote (Türkiye Geneli)', 'İzmir'
    ];

    private static array $experienceLevels = [
        'Stajyer', 'Giriş Seviyesi', 'Giriş Seviyesi', 'Orta Seviye'
    ];

    /**
     * Tek bir sentetik ilan verisi üretir.
     */
    public static function make(array $overrides = []): array
    {
        // İlanı yayınlayacak rastgele bir mezun kullanıcı bul veya varsayılan 1
        $users = User::all();
        $randomUserId = !empty($users) ? $users[array_rand($users)]['id'] : 1;

        $title = self::$titles[array_rand(self::$titles)];
        $company = self::$companies[array_rand(self::$companies)];

        $minSalary = mt_rand(35, 70) * 1000;
        $maxSalary = $minSalary + (mt_rand(10, 30) * 1000);

        $data = [
            'posted_by_user_id' => $randomUserId,
            'title'              => $title,
            'company_name'       => $company,
            'employment_type'    => self::$employmentTypes[array_rand(self::$employmentTypes)],
            'workplace_type'     => self::$workplaceTypes[array_rand(self::$workplaceTypes)],
            'location'           => self::$locations[array_rand(self::$locations)],
            'experience_level'   => self::$experienceLevels[array_rand(self::$experienceLevels)],
            'salary_min'         => $minSalary,
            'salary_max'         => $maxSalary,
            'salary_currency'    => 'TRY',
            'description'        => "{$company} bünyesinde {$title} pozisyonunda görevlendirilmek üzere mezun veya son sınıf öğrencisi takım arkadaşları arıyoruz. Modern yazılım geliştirme pratikleri, takım çalışması ve sürekli öğrenme odaklı çalışma ortamı sunuyoruz.",
            'requirements'       => "• Üniversitelerin Yönetim Bilişim Sistemleri veya ilgili bölümlerinden mezun / son sınıf\n• Temel veritabanı ve web mimarileri bilgisi\n• Git ve Docker araçlarına aşinalık\n• Analitik düşünme ve problem çözme becerisi",
            'contact_email'      => 'kariyer@' . strtolower(str_replace([' ', '&'], '', $company)) . '.com',
            'application_url'    => 'https://kariyer.' . strtolower(str_replace([' ', '&'], '', $company)) . '.com',
            'status'             => 'active',
            'deadline'           => date('Y-m-d', strtotime('+' . mt_rand(15, 60) . ' days'))
        ];

        return array_merge($data, $overrides);
    }

    /**
     * İlan üretir ve veritabanına ekler.
     */
    public static function create(array $overrides = []): array
    {
        $data = self::make($overrides);
        return JobPosting::create($data);
    }

    /**
     * Çoklu ilan üretir.
     */
    public static function createMany(int $count = 5, array $overrides = []): array
    {
        $created = [];
        for ($i = 0; $i < $count; $i++) {
            $created[] = self::create($overrides);
        }
        return $created;
    }
}
