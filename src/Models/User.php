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
     * Satır tiplerini normalize eder (password_hash asla dönülmez).
     */
    private static function formatRow(array $row): array
    {
        $row['id'] = (int)$row['id'];
        if (isset($row['graduationYear']) && $row['graduationYear'] !== null) {
            $row['graduationYear'] = (int)$row['graduationYear'];
        }
        $row['role'] = $row['role'] ?? 'alumnus';
        $row['status'] = $row['status'] ?? 'active';
        unset($row['password_hash']);
        return $row;
    }

    /**
     * READ ALL (C[R]UD) - Tüm kullanıcıları veritabanından getirir.
     */
    public static function all(): array
    {
        $db = self::getDb();
        $stmt = $db->query("SELECT id, name, email, graduationYear, department, company, role, status, created_at FROM users ORDER BY id ASC");
        $results = $stmt->fetchAll();

        return array_map([self::class, 'formatRow'], $results);
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
            SELECT id, name, email, graduationYear, department, company, role, status, created_at 
            FROM users 
            WHERE name LIKE :t OR company LIKE :t OR department LIKE :t OR email LIKE :t
            ORDER BY id ASC
        ");
        $stmt->execute([':t' => $term]);
        $results = $stmt->fetchAll();

        return array_map([self::class, 'formatRow'], $results);
    }

    /**
     * READ ONE (C[R]UD) - ID'ye göre tek bir kullanıcıyı getirir.
     */
    public static function find(int $id): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT id, name, email, graduationYear, department, company, role, status, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        return self::formatRow($user);
    }

    /**
     * E-posta adresine göre kullanıcı arar (password_hash hariç).
     */
    public static function findByEmail(string $email): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT id, name, email, graduationYear, department, company, role, status, created_at FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1");
        $stmt->execute([':email' => trim($email)]);
        $user = $stmt->fetch();

        return $user ? self::formatRow($user) : null;
    }

    // =========================================================================
    // KİMLİK DOĞRULAMA & KAYIT (AUTHENTICATION & REGISTRATION) - Hafta 09
    // =========================================================================

    /**
     * Kayıt işlemi öncesinde users tablosunda password_hash sütununun varlığını doğrular.
     * Migrasyon 010 henüz uygulanmadıysa sadece password_hash sütununu ve 010 kaydını güvenle ekler.
     */
    public static function ensureAuthColumns(): void
    {
        $db = self::getDb();
        $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $hasColumn = false;

        if ($driver === 'sqlite') {
            $cols = $db->query("PRAGMA table_info(users)")->fetchAll();
            foreach ($cols as $col) {
                if (($col['name'] ?? '') === 'password_hash') {
                    $hasColumn = true;
                    break;
                }
            }
        } else {
            $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password_hash'");
            $hasColumn = (bool)$stmt->fetch();
        }

        if (!$hasColumn) {
            $db->exec("ALTER TABLE users ADD COLUMN password_hash VARCHAR(255)");
            $db->exec("CREATE INDEX IF NOT EXISTS idx_users_role ON users(role)");
            $db->exec("CREATE INDEX IF NOT EXISTS idx_users_status ON users(status)");

            // Migrasyon tablosu mevcutsa 010'u kayıt altına al
            try {
                $stmt = $db->prepare("INSERT OR IGNORE INTO migrations (migration) VALUES (:m)");
                $stmt->execute([':m' => '010_add_password_hash_to_users_table.sql']);
            } catch (\Exception $e) {
                // migrations tablosu yoksa sessizce geç
            }
        }
    }

    /**
     * Kayıt formundan veya API'den gelen verileri ddd.md RBAC ve güvenlik kurallarına göre doğrular.
     */
    public static function validateRegistration(array $data, bool $requireTerms = true): array
    {
        $errors = [];

        // 1. Ad Soyad
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $errors['name'] = 'Ad ve Soyad alanı zorunludur.';
        } elseif (mb_strlen($name) < 2) {
            $errors['name'] = 'Ad Soyad en az 2 karakter olmalıdır.';
        } elseif (mb_strlen($name) > 150) {
            $errors['name'] = 'Ad Soyad en fazla 150 karakter olabilir.';
        }

        // 2. E-Posta (Zorunlu + Format + Benzersizlik)
        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $errors['email'] = 'E-posta adresi zorunludur.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geçerli bir e-posta adresi giriniz (örn: ad.soyad@alumni.iu.edu.tr).';
        } elseif (self::findByEmail($email) !== null) {
            $errors['email'] = 'Bu e-posta adresi sistemde zaten kayıtlıdır.';
        }

        // 3. Rol Denetimi (ddd.md Bölüm 8.4 - Anti-Privilege Escalation)
        $role = trim($data['role'] ?? 'alumnus');
        $allowedSelfRoles = ['alumnus', 'student'];
        if (!in_array($role, $allowedSelfRoles, true)) {
            $errors['role'] = 'Kayıt formundan yalnızca Mezun (alumnus) veya Öğrenci (student) rolü seçilebilir. Admin veya Akademisyen rolleri dışarıdan atanamaz.';
        }

        // 4. Bölüm
        $department = trim($data['department'] ?? '');
        if ($department === '') {
            $errors['department'] = 'Bölüm bilgisi zorunludur.';
        } elseif (mb_strlen($department) > 150) {
            $errors['department'] = 'Bölüm adı en fazla 150 karakter olabilir.';
        }

        // 5. Mezuniyet / Beklenen Mezuniyet Yılı
        $yearRaw = trim((string)($data['graduationYear'] ?? ''));
        if ($role === 'alumnus' && $yearRaw === '') {
            $errors['graduationYear'] = 'Mezun rolü için mezuniyet yılı zorunludur.';
        } elseif ($yearRaw !== '') {
            if (!is_numeric($yearRaw) || (int)$yearRaw < 1950 || (int)$yearRaw > 2040) {
                $errors['graduationYear'] = 'Mezuniyet yılı 1950 ile 2040 arasında geçerli bir yıl olmalıdır.';
            }
        }

        // 6. Şirket (Opsiyonel)
        $company = trim($data['company'] ?? '');
        if ($company !== '' && mb_strlen($company) > 150) {
            $errors['company'] = 'Şirket / Kurum adı en fazla 150 karakter olabilir.';
        }

        // 7. Parola Güçlülük Kontrolü
        $password = (string)($data['password'] ?? '');
        if ($password === '') {
            $errors['password'] = 'Parola alanı zorunludur.';
        } elseif (mb_strlen($password) < 6) {
            $errors['password'] = 'Parola en az 6 karakter uzunluğunda olmalıdır.';
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Parola güvenlik kuralı gereği en az 1 harf ve 1 rakam içermelidir.';
        }

        // 8. Parola Tekrarı
        if ($requireTerms || array_key_exists('password_confirm', $data)) {
            $passwordConfirm = (string)($data['password_confirm'] ?? '');
            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Girilen parolalar birbiriyle eşleşmiyor.';
            }
        }

        // 9. Kullanım Koşulları Onayı
        if ($requireTerms && empty($data['terms'])) {
            $errors['terms'] = 'Devam etmek için kullanım koşullarını ve rol yetkilendirme kurallarını onaylamalısınız.';
        }

        return $errors;
    }

    /**
     * Yeni kullanıcı kaydı oluşturur (Parola Bcrypt ile hashlenir).
     */
    public static function register(array $data): array
    {
        self::ensureAuthColumns();

        $db = self::getDb();
        $role = in_array($data['role'] ?? 'alumnus', ['alumnus', 'student'], true)
            ? $data['role']
            : 'alumnus';

        $stmt = $db->prepare("
            INSERT INTO users (name, email, password_hash, graduationYear, department, company, role, status) 
            VALUES (:name, :email, :password_hash, :graduationYear, :department, :company, :role, :status)
        ");

        $stmt->execute([
            ':name'           => trim($data['name']),
            ':email'          => strtolower(trim($data['email'])),
            ':password_hash'  => password_hash((string)$data['password'], PASSWORD_BCRYPT),
            ':graduationYear' => isset($data['graduationYear']) && $data['graduationYear'] !== '' ? (int)$data['graduationYear'] : null,
            ':department'     => trim($data['department'] ?? 'Yönetim Bilişim Sistemleri'),
            ':company'        => isset($data['company']) && trim($data['company']) !== '' ? trim($data['company']) : null,
            ':role'           => $role,
            ':status'         => 'active',
        ]);

        $newId = (int)$db->lastInsertId();
        return self::find($newId);
    }

    /**
     * E-posta ve parolayı doğrular (Authentication - Hafta 09 Görev 3).
     * Başarılı ise kullanıcı bilgilerini (password_hash hariç) döner; aksi halde null döner.
     * Seed/demo hesaplar için kolaylık: password_hash boşsa ve 'Alumni2026!' girilmişse otomatik hashleyip kaydeder.
     */
    public static function authenticate(string $email, string $password): ?array
    {
        self::ensureAuthColumns();

        $email = strtolower(trim($email));
        $password = (string)$password;

        if ($email === '' || $password === '') {
            return null;
        }

        $db = self::getDb();
        $stmt = $db->prepare("
            SELECT id, name, email, password_hash, graduationYear, department, company, role, status, created_at 
            FROM users 
            WHERE LOWER(email) = :email 
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        $storedHash = (string)($user['password_hash'] ?? '');

        // 1. Standart Bcrypt Doğrulaması
        if ($storedHash !== '' && password_verify($password, $storedHash)) {
            return self::formatRow($user);
        }

        // 2. Tohum / Demo Hesaplar İçin Kolaylaştırıcı Geri Dönüş (Alumni2026! veya Secret123)
        if ($storedHash === '' && ($password === 'Alumni2026!' || $password === 'Secret123')) {
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            $upStmt = $db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
            $upStmt->execute([':hash' => $newHash, ':id' => (int)$user['id']]);
            $user['password_hash'] = $newHash;
            return self::formatRow($user);
        }

        return null;
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
