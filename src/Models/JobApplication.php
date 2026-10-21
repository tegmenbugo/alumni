<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * JobApplication Model (MVC - Model Katmanı)
 * İş ilanlarına yapılan başvuruları ve durumlarını yönetir.
 *
 * İlişkiler:
 *   - BelongsTo: JobPosting (job_posting_id -> job_postings.id)
 *   - BelongsTo: User (applicant_user_id -> users.id)
 */
class JobApplication
{
    private static function getDb(): PDO
    {
        return Database::connect();
    }

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
            SELECT u.id, u.name, u.email, u.department, u.graduationYear
            FROM users u
            INNER JOIN job_applications ja ON u.id = ja.applicant_user_id
            WHERE ja.id = :id
        ");
        $stmt->execute([':id' => $applicationId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT * FROM job_applications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            INSERT INTO job_applications (job_posting_id, applicant_user_id, status, cover_letter)
            VALUES (:job_posting_id, :applicant_user_id, :status, :cover_letter)
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
}
