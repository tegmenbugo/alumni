<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * User Model (MVC - Model Katmanı)
 * Veritabanı (Database) bağlantısı üzerinden kullanıcı ve mezun CRUD işlemlerini yürütür.
 */
class User
{
    private static function getDb(): PDO
    {
        return Database::connect();
    }

    /**
     * READ ALL (C[R]UD) - Tüm kullanıcıları veritabanından getirir.
     */
    public static function all(): array
    {
        $db = self::getDb();
        $stmt = $db->query("SELECT id, name, email, graduationYear, department, company, created_at FROM users ORDER BY id ASC");
        $results = $stmt->fetchAll();

        // graduationYear sayısal tipe cast edilsin
        foreach ($results as &$row) {
            if ($row['graduationYear'] !== null) {
                $row['graduationYear'] = (int)$row['graduationYear'];
            }
            $row['id'] = (int)$row['id'];
        }

        return $results;
    }

    /**
     * Toplam kayıt sayısını döner.
     */
    public static function count(): int
    {
        $db = self::getDb();
        return (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    /**
     * İsim, şirket veya bölüme göre arama yapar.
     */
    public static function search(string $keyword): array
    {
        $db = self::getDb();
        $term = '%' . trim($keyword) . '%';
        $stmt = $db->prepare("
            SELECT id, name, email, graduationYear, department, company, created_at 
            FROM users 
            WHERE name LIKE :t OR company LIKE :t OR department LIKE :t OR email LIKE :t
            ORDER BY id ASC
        ");
        $stmt->execute([':t' => $term]);
        $results = $stmt->fetchAll();

        foreach ($results as &$row) {
            if ($row['graduationYear'] !== null) {
                $row['graduationYear'] = (int)$row['graduationYear'];
            }
            $row['id'] = (int)$row['id'];
        }

        return $results;
    }

    /**
     * READ ONE (C[R]UD) - ID'ye göre tek bir kullanıcıyı getirir.
     */
    public static function find(int $id): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT id, name, email, graduationYear, department, company, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        $user['id'] = (int)$user['id'];
        if ($user['graduationYear'] !== null) {
            $user['graduationYear'] = (int)$user['graduationYear'];
        }

        return $user;
    }

    /**
     * CREATE ([C]RUD) - Veritabanına yeni bir kullanıcı ekler.
     */
    public static function create(array $data): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            INSERT INTO users (name, email, graduationYear, department, company) 
            VALUES (:name, :email, :graduationYear, :department, :company)
        ");

        $stmt->execute([
            ':name'           => trim($data['name']),
            ':email'          => isset($data['email']) ? trim($data['email']) : null,
            ':graduationYear' => isset($data['graduationYear']) && $data['graduationYear'] !== '' ? (int)$data['graduationYear'] : null,
            ':department'     => isset($data['department']) ? trim($data['department']) : null,
            ':company'        => isset($data['company']) ? trim($data['company']) : null
        ]);

        $newId = (int)$db->lastInsertId();
        return self::find($newId);
    }

    /**
     * UPDATE (CR[U]D) - Kullanıcı bilgilerini tamamen günceller (Full Update).
     */
    public static function update(int $id, array $data): ?array
    {
        $existing = self::find($id);
        if (!$existing) {
            return null;
        }

        $db = self::getDb();
        $stmt = $db->prepare("
            UPDATE users 
            SET name = :name, email = :email, graduationYear = :graduationYear, department = :department, company = :company 
            WHERE id = :id
        ");

        $stmt->execute([
            ':id'             => $id,
            ':name'           => trim($data['name']),
            ':email'          => isset($data['email']) ? trim($data['email']) : null,
            ':graduationYear' => isset($data['graduationYear']) && $data['graduationYear'] !== '' ? (int)$data['graduationYear'] : null,
            ':department'     => isset($data['department']) ? trim($data['department']) : null,
            ':company'        => isset($data['company']) ? trim($data['company']) : null
        ]);

        return self::find($id);
    }

    /**
     * PATCH (CR[U]D) - Kullanıcının sadece belirtilen alanlarını günceller (Partial Update).
     */
    public static function patch(int $id, array $data): ?array
    {
        $existing = self::find($id);
        if (!$existing) {
            return null;
        }

        $fields = [];
        $params = [':id' => $id];

        if (array_key_exists('name', $data)) {
            $fields[] = "name = :name";
            $params[':name'] = trim($data['name']);
        }
        if (array_key_exists('email', $data)) {
            $fields[] = "email = :email";
            $params[':email'] = trim($data['email']);
        }
        if (array_key_exists('graduationYear', $data)) {
            $fields[] = "graduationYear = :graduationYear";
            $params[':graduationYear'] = $data['graduationYear'] !== null ? (int)$data['graduationYear'] : null;
        }
        if (array_key_exists('department', $data)) {
            $fields[] = "department = :department";
            $params[':department'] = trim($data['department']);
        }
        if (array_key_exists('company', $data)) {
            $fields[] = "company = :company";
            $params[':company'] = trim($data['company']);
        }

        if (empty($fields)) {
            return $existing;
        }

        $db = self::getDb();
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return self::find($id);
    }

    /**
     * DELETE (CRU[D]) - ID'ye sahip kullanıcıyı veritabanından siler.
     */
    public static function delete(int $id): ?array
    {
        $existing = self::find($id);
        if (!$existing) {
            return null;
        }

        $db = self::getDb();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return $existing;
    }

    // =========================================================================
    // İLİŞKİLER (RELATIONSHIPS) - Hafta 06 (Görev 3)
    // =========================================================================

    /**
     * RELATIONSHIP: HasMany JobPosting
     * Bu mezunun/kullanıcının yayınladığı tüm iş ilanlarını getirir.
     */
    public static function jobPostings(int $userId): array
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

    /**
     * RELATIONSHIP: HasMany JobApplication
     * Bu kullanıcının/öğrencinin yaptığı tüm iş başvurularını ve ilan detayını getirir.
     */
    public static function jobApplications(int $userId): array
    {
        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT ja.*, jp.title as job_title, jp.company_name as job_company
            FROM job_applications ja
            INNER JOIN job_postings jp ON ja.job_posting_id = jp.id
            WHERE ja.applicant_user_id = :user_id
            ORDER BY ja.applied_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * RELATIONSHIP: HasOne Profile
     * Kullanıcının detaylı biyografi ve profil bilgilerini getirir.
     */
    public static function profile(int $userId): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT * FROM profiles WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
