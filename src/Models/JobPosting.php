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
            ':posted_by_user_id' => (int)$data['posted_by_user_id'],
            ':title'              => trim($data['title']),
            ':company_name'       => trim($data['company_name']),
            ':employment_type'    => $data['employment_type'] ?? 'Tam Zamanlı',
            ':workplace_type'     => $data['workplace_type'] ?? 'Hibrit',
            ':location'           => $data['location'] ?? 'İstanbul, Türkiye',
            ':experience_level'   => $data['experience_level'] ?? 'Giriş Seviyesi',
            ':salary_min'         => !empty($data['salary_min']) ? (float)$data['salary_min'] : null,
            ':salary_max'         => !empty($data['salary_max']) ? (float)$data['salary_max'] : null,
            ':salary_currency'    => $data['salary_currency'] ?? 'TRY',
            ':description'        => trim($data['description']),
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
