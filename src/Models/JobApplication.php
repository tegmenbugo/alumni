<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * JobApplication Model (MVC - Model Katmanı)
 * İş ilanlarına yapılan başvuruları, aday değerlendirme durumlarını ve ilişkilerini yönetir.
 * 
 * İlişkiler (Relationships):
 *   - BelongsTo: JobPosting (job_posting_id -> job_postings.id)
 *   - BelongsTo: User (applicant_user_id -> users.id)
 */
class JobApplication
{
    private static function getDb(): PDO
    {
        return Database::connect();
    }

    // =========================================================================
    // İLİŞKİLER (RELATIONSHIPS)
    // =========================================================================

    /**
     * RELATIONSHIP: BelongsTo JobPosting
     * Başvurulan iş ilanının detaylarını getirir.
     */
    public static function jobPosting(int $applicationId): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT jp.* 
            FROM job_postings jp
            INNER JOIN job_applications ja ON jp.id = ja.job_posting_id
            WHERE ja.id = :id
        ");
        $stmt->execute([':id' => $applicationId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * RELATIONSHIP: BelongsTo User (Applicant)
     * Başvuru yapan mezun/öğrenci bilgilerini getirir.
     */
    public static function applicant(int $applicationId): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT u.id, u.name, u.email, u.department, u.graduationYear, u.company
            FROM users u
            INNER JOIN job_applications ja ON u.id = ja.applicant_user_id
            WHERE ja.id = :id
        ");
        $stmt->execute([':id' => $applicationId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Belirli bir ilana yapılan tüm başvuruları getirir (Nested Route için).
     */
    public static function forPosting(int $postingId): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT ja.*, u.name as applicant_name, u.email as applicant_email, 
                   u.department as applicant_department, u.graduationYear as applicant_year
            FROM job_applications ja
            INNER JOIN users u ON ja.applicant_user_id = u.id
            WHERE ja.job_posting_id = :posting_id
            ORDER BY ja.applied_at DESC
        ");
        $stmt->execute([':posting_id' => $postingId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Belirli bir kullanıcının yaptığı tüm başvuruları getirir (Nested Route için).
     */
    public static function forUser(int $userId): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT ja.*, jp.title as job_title, jp.company_name as job_company,
                   jp.location as job_location, jp.employment_type as job_type
            FROM job_applications ja
            INNER JOIN job_postings jp ON ja.job_posting_id = jp.id
            WHERE ja.applicant_user_id = :user_id
            ORDER BY ja.applied_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll() ?: [];
    }

    // =========================================================================
    // CRUD METODLARI
    // =========================================================================

    /**
     * Tüm başvuruları listeler (Doğrudan Rota için).
     */
    public static function all(array $filters = []): array
    {
        $db = self::getDb();
        $sql = "
            SELECT ja.*, 
                   jp.title as job_title, jp.company_name as job_company,
                   u.name as applicant_name, u.email as applicant_email
            FROM job_applications ja
            LEFT JOIN job_postings jp ON ja.job_posting_id = jp.id
            LEFT JOIN users u ON ja.applicant_user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND ja.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['job_id'])) {
            $sql .= " AND ja.job_posting_id = :job_id";
            $params[':job_id'] = (int)$filters['job_id'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND ja.applicant_user_id = :user_id";
            $params[':user_id'] = (int)$filters['user_id'];
        }

        $sql .= " ORDER BY ja.applied_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public static function find(int $id): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT ja.*, 
                   jp.title as job_title, jp.company_name as job_company,
                   u.name as applicant_name, u.email as applicant_email
            FROM job_applications ja
            LEFT JOIN job_postings jp ON ja.job_posting_id = jp.id
            LEFT JOIN users u ON ja.applicant_user_id = u.id
            WHERE ja.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            INSERT INTO job_applications (
                job_posting_id, applicant_user_id, status, cover_letter
            ) VALUES (
                :job_posting_id, :applicant_user_id, :status, :cover_letter
            )
        ");

        $stmt->execute([
            ':job_posting_id'    => (int)$data['job_posting_id'],
            ':applicant_user_id' => (int)$data['applicant_user_id'],
            ':status'             => $data['status'] ?? 'applied',
            ':cover_letter'       => $data['cover_letter'] ?? null
        ]);

        $newId = (int)$db->lastInsertId();
        return self::find($newId);
    }

    public static function updateStatus(int $id, string $status): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("UPDATE job_applications SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
        return self::find($id);
    }

    public static function delete(int $id): ?array
    {
        $existing = self::find($id);
        if (!$existing) {
            return null;
        }

        $db = self::getDb();
        $stmt = $db->prepare("DELETE FROM job_applications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $existing;
    }

    public static function count(array $filters = []): int
    {
        $db = self::getDb();
        $sql = "SELECT COUNT(*) FROM job_applications WHERE 1=1";
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
