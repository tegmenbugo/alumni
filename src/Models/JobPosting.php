<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * JobPosting Model (MVC - Model Katmanı)
 * ddd.md ve 008_update_job_postings_table.sql migrasyon şemasına göre
 * kariyer, staj ve iş ilanı işlemlerini ve ilişkilerini yönetir.
 * 
 * İlişkiler (Relationships):
 *   - BelongsTo: User (posted_by_user_id -> users.id)
 *   - HasMany: JobApplication (job_postings.id -> job_applications.job_posting_id)
 */
class JobPosting
{
    private static function getDb(): PDO
    {
        return Database::connect();
    }

    // =========================================================================
    // İLİŞKİLER (RELATIONSHIPS)
    // =========================================================================

    /**
     * RELATIONSHIP: BelongsTo User
     * İlanı yayınlayan mezun/kullanıcı bilgisini getirir.
     */
    public static function user(int $postingId): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT u.id, u.name, u.email, u.department, u.company, u.graduationYear
            FROM users u
            INNER JOIN job_postings jp ON u.id = jp.posted_by_user_id
            WHERE jp.id = :id
        ");
        $stmt->execute([':id' => $postingId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * RELATIONSHIP: HasMany JobApplication
     * İlana yapılan tüm başvuruları ve başvuran kullanıcı bilgilerini getirir.
     */
    public static function applications(int $postingId): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT ja.*, u.name as applicant_name, u.email as applicant_email
            FROM job_applications ja
            INNER JOIN users u ON ja.applicant_user_id = u.id
            WHERE ja.job_posting_id = :id
            ORDER BY ja.applied_at DESC
        ");
        $stmt->execute([':id' => $postingId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * RELATIONSHIP: BelongsTo User (Ters sorgu)
     * Belirli bir kullanıcının açtığı tüm ilanları getirir.
     */
    public static function byUser(int $userId): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT * FROM job_postings 
            WHERE posted_by_user_id = :user_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll() ?: [];
    }

    // =========================================================================
    // İŞ KURALLARI VE VALİDASYON (BUSINESS RULES & VALIDATION) - Hafta 07
    // =========================================================================

    /**
     * İlan verilerini ddd.md iş kurallarına göre doğrular.
     * Hata varsa ['field' => 'Hata mesajı'] şeklinde döner, geçerliyse boş array döner.
     *
     * Kurallar:
     *  - title: Zorunlu, min 3, maks 120 karakter.
     *  - company / company_name: Zorunlu, maks 120 karakter.
     *  - city / location: Opsiyonel, maks 100 karakter.
     *  - description: Opsiyonel.
     *  - salary: salary_min >= 0, salary_max >= 0 ve salary_min <= salary_max.
     *  - contact_email: Opsiyonel, girildiyse geçerli email formatı.
     *  - employment_type: Geçerli enum listesinde olmalı.
     *  - workplace_type: Geçerli enum listesinde olmalı.
     *  - status: ['active', 'closed', 'draft', 'archived'].
     *  - deadline: Opsiyonel, girildiyse YYYY-MM-DD ve geçmiş tarih olamaz.
     */
    public static function validate(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        // 1. title: required, max 120 chars
        if (!$isUpdate || array_key_exists('title', $data)) {
            $title = trim($data['title'] ?? '');
            if ($title === '') {
                $errors['title'] = 'İlan başlığı (title) zorunludur.';
            } elseif (mb_strlen($title) < 3) {
                $errors['title'] = 'İlan başlığı (title) en az 3 karakter olmalıdır.';
            } elseif (mb_strlen($title) > 120) {
                $errors['title'] = 'İlan başlığı (title) en fazla 120 karakter olabilir. (Girilen: ' . mb_strlen($title) . ')';
            }
        }

        // 2. company / company_name: required, max 120 chars
        if (!$isUpdate || array_key_exists('company_name', $data) || array_key_exists('company', $data)) {
            $company = trim($data['company_name'] ?? ($data['company'] ?? ''));
            if ($company === '') {
                $errors['company_name'] = 'Şirket / kurum adı (company_name) zorunludur.';
            } elseif (mb_strlen($company) > 120) {
                $errors['company_name'] = 'Şirket adı en fazla 120 karakter olabilir. (Girilen: ' . mb_strlen($company) . ')';
            }
        }

        // 3. city / location: optional, max 100 chars
        $location = $data['location'] ?? ($data['city'] ?? null);
        if ($location !== null && trim($location) !== '') {
            if (mb_strlen(trim($location)) > 100) {
                $errors['location'] = 'Şehir / lokasyon bilgisi en fazla 100 karakter olabilir.';
            }
        }

        // 4. description: optional (boş bırakılabilir)
        // Kural: description opsiyoneldir.

        // 5. salary_min & salary_max mantıksal kontrolü (Sistem Kuralı)
        $hasMin = isset($data['salary_min']) && $data['salary_min'] !== '' && $data['salary_min'] !== null;
        $hasMax = isset($data['salary_max']) && $data['salary_max'] !== '' && $data['salary_max'] !== null;

        if ($hasMin && (float)$data['salary_min'] < 0) {
            $errors['salary_min'] = 'Asgari maaş (salary_min) 0 veya pozitif bir değer olmalıdır.';
        }
        if ($hasMax && (float)$data['salary_max'] < 0) {
            $errors['salary_max'] = 'Azami maaş (salary_max) 0 veya pozitif bir değer olmalıdır.';
        }
        if ($hasMin && $hasMax && (float)$data['salary_min'] > (float)$data['salary_max']) {
            $errors['salary_range'] = 'Asgari maaş (salary_min), azami maaştan (salary_max) büyük olamaz.';
        }

        // 6. contact_email kontrolü (Sistem Kuralı)
        if (!empty($data['contact_email'])) {
            if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
                $errors['contact_email'] = 'İletişim e-posta adresi (contact_email) geçerli bir e-posta formatında olmalıdır.';
            }
        }

        // 7. application_url kontrolü (Sistem Kuralı)
        if (!empty($data['application_url'])) {
            if (!filter_var($data['application_url'], FILTER_VALIDATE_URL)) {
                $errors['application_url'] = 'Başvuru bağlantısı (application_url) geçerli bir URL formatında olmalıdır (örn: https://...).';
            }
        }

        // 8. employment_type enum kontrolü
        $allowedEmployment = ['Tam Zamanlı', 'Yarı Zamanlı', 'Staj', 'Sözleşmeli', 'Freelance'];
        if (!empty($data['employment_type']) && !in_array($data['employment_type'], $allowedEmployment, true)) {
            $errors['employment_type'] = 'Geçersiz çalışma biçimi. İzin verilenler: ' . implode(', ', $allowedEmployment);
        }

        // 9. workplace_type enum kontrolü
        $allowedWorkplace = ['Ofisten', 'Hibrit', 'Uzaktan / Remote'];
        if (!empty($data['workplace_type']) && !in_array($data['workplace_type'], $allowedWorkplace, true)) {
            $errors['workplace_type'] = 'Geçersiz çalışma ortamı. İzin verilenler: ' . implode(', ', $allowedWorkplace);
        }

        // 10. status enum kontrolü
        $allowedStatus = ['active', 'closed', 'draft', 'archived'];
        if (!empty($data['status']) && !in_array($data['status'], $allowedStatus, true)) {
            $errors['status'] = 'Geçersiz ilan durumu. İzin verilenler: ' . implode(', ', $allowedStatus);
        }

        // 11. deadline tarih kontrolü
        if (!empty($data['deadline'])) {
            $deadlineTime = strtotime($data['deadline']);
            if ($deadlineTime === false) {
                $errors['deadline'] = 'Son başvuru tarihi geçerli bir tarih formatında (YYYY-MM-DD) olmalıdır.';
            } elseif (date('Y-m-d', $deadlineTime) < date('Y-m-d')) {
                $errors['deadline'] = 'Son başvuru tarihi geçmiş bir tarih olamaz.';
            }
        }

        return $errors;
    }

    // =========================================================================
    // CRUD VE SORGULAMA METODLARI
    // =========================================================================

    /**
     * Tüm ilanları getirir (Opsiyonel filtreleme destekli).
     */
    public static function all(array $filters = []): array
    {
        $db = self::getDb();
        $sql = "SELECT jp.*, u.name as posted_by_name, u.company as poster_company 
                FROM job_postings jp
                LEFT JOIN users u ON jp.posted_by_user_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND jp.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['employment_type'])) {
            $sql .= " AND jp.employment_type = :employment_type";
            $params[':employment_type'] = $filters['employment_type'];
        }

        if (!empty($filters['workplace_type'])) {
            $sql .= " AND jp.workplace_type = :workplace_type";
            $params[':workplace_type'] = $filters['workplace_type'];
        }

        if (!empty($filters['company'])) {
            $sql .= " AND jp.company_name LIKE :company";
            $params[':company'] = '%' . trim($filters['company']) . '%';
        }

        if (!empty($filters['q'])) {
            $sql .= " AND (jp.title LIKE :q OR jp.description LIKE :q OR jp.company_name LIKE :q)";
            $params[':q'] = '%' . trim($filters['q']) . '%';
        }

        $sql .= " ORDER BY jp.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Yalnızca aktif (başvuruya açık) ilanları getirir.
     */
    public static function active(): array
    {
        return self::all(['status' => 'active']);
    }

    /**
     * ID'ye göre tekil ilan getirir.
     */
    public static function find(int $id): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT jp.*, u.name as posted_by_name, u.email as poster_email, u.company as poster_company
            FROM job_postings jp
            LEFT JOIN users u ON jp.posted_by_user_id = u.id
            WHERE jp.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Yeni bir iş ilanı oluşturur.
     */
    public static function create(array $data): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            INSERT INTO job_postings (
                posted_by_user_id, title, company_name, employment_type, workplace_type,
                location, experience_level, salary_min, salary_max, salary_currency,
                description, requirements, contact_email, application_url, status, deadline
            ) VALUES (
                :posted_by_user_id, :title, :company_name, :employment_type, :workplace_type,
                :location, :experience_level, :salary_min, :salary_max, :salary_currency,
                :description, :requirements, :contact_email, :application_url, :status, :deadline
            )
        ");

        $stmt->execute([
            ':posted_by_user_id' => (int)($data['posted_by_user_id'] ?? 1),
            ':title'              => trim($data['title']),
            ':company_name'       => trim($data['company_name'] ?? ($data['company'] ?? '')),
            ':employment_type'    => $data['employment_type'] ?? 'Tam Zamanlı',
            ':workplace_type'     => $data['workplace_type'] ?? 'Hibrit',
            ':location'           => $data['location'] ?? ($data['city'] ?? 'İstanbul, Türkiye'),
            ':experience_level'   => $data['experience_level'] ?? 'Giriş Seviyesi',
            ':salary_min'         => !empty($data['salary_min']) ? (float)$data['salary_min'] : null,
            ':salary_max'         => !empty($data['salary_max']) ? (float)$data['salary_max'] : null,
            ':salary_currency'    => $data['salary_currency'] ?? 'TRY',
            ':description'        => isset($data['description']) ? trim($data['description']) : null,
            ':requirements'       => $data['requirements'] ?? null,
            ':contact_email'      => $data['contact_email'] ?? null,
            ':application_url'    => $data['application_url'] ?? null,
            ':status'             => $data['status'] ?? 'active',
            ':deadline'           => $data['deadline'] ?? null
        ]);

        $newId = (int)$db->lastInsertId();
        return self::find($newId);
    }

    /**
     * İlanı günceller.
     */
    public static function update(int $id, array $data): ?array
    {
        $existing = self::find($id);
        if (!$existing) {
            return null;
        }

        $db = self::getDb();
        $stmt = $db->prepare("
            UPDATE job_postings SET
                title = :title,
                company_name = :company_name,
                employment_type = :employment_type,
                workplace_type = :workplace_type,
                location = :location,
                experience_level = :experience_level,
                salary_min = :salary_min,
                salary_max = :salary_max,
                salary_currency = :salary_currency,
                description = :description,
                requirements = :requirements,
                contact_email = :contact_email,
                application_url = :application_url,
                status = :status,
                deadline = :deadline,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $stmt->execute([
            ':id'                 => $id,
            ':title'              => trim($data['title'] ?? $existing['title']),
            ':company_name'       => trim($data['company_name'] ?? $existing['company_name']),
            ':employment_type'    => $data['employment_type'] ?? $existing['employment_type'],
            ':workplace_type'     => $data['workplace_type'] ?? ($existing['workplace_type'] ?? 'Hibrit'),
            ':location'           => $data['location'] ?? $existing['location'],
            ':experience_level'   => $data['experience_level'] ?? ($existing['experience_level'] ?? 'Giriş Seviyesi'),
            ':salary_min'         => isset($data['salary_min']) ? (float)$data['salary_min'] : ($existing['salary_min'] ?? null),
            ':salary_max'         => isset($data['salary_max']) ? (float)$data['salary_max'] : ($existing['salary_max'] ?? null),
            ':salary_currency'    => $data['salary_currency'] ?? ($existing['salary_currency'] ?? 'TRY'),
            ':description'        => trim($data['description'] ?? $existing['description']),
            ':requirements'       => $data['requirements'] ?? ($existing['requirements'] ?? null),
            ':contact_email'      => $data['contact_email'] ?? ($existing['contact_email'] ?? null),
            ':application_url'    => $data['application_url'] ?? ($existing['application_url'] ?? null),
            ':status'             => $data['status'] ?? $existing['status'],
            ':deadline'           => $data['deadline'] ?? ($existing['deadline'] ?? null)
        ]);

        return self::find($id);
    }

    /**
     * İlanı siler.
     */
    public static function delete(int $id): ?array
    {
        $existing = self::find($id);
        if (!$existing) {
            return null;
        }

        $db = self::getDb();
        $stmt = $db->prepare("DELETE FROM job_postings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $existing;
    }

    /**
     * Toplam ilan sayısını döner.
     */
    public static function count(array $filters = []): int
    {
        $db = self::getDb();
        $sql = "SELECT COUNT(*) FROM job_postings WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
