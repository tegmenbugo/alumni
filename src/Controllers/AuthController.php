<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\View;
use App\Core\Response;
use App\Middlewares\AuthMiddleware;

/**
 * AuthController (Kimlik Doğrulama ve Kayıt Denetleyicisi)
 * ddd.md Bölüm 8 (Auth & RBAC) kurallarına uygun kullanıcı kaydı, giriş, çıkış
 * (Web & REST API) işlemlerini yönetir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 09
 */
class AuthController
{
    // =========================================================================
    // KAYIT İŞLEMLERİ (REGISTRATION) - Hafta 09 Görev 2
    // =========================================================================

    /**
     * GET /register - Kayıt Ol (Register) sayfasını görüntüler.
     */
    public function showRegister(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        View::render('auth/register', [
            'errors'         => [],
            'old'            => [],
            'registeredUser' => null,
            'message'        => null,
        ]);
    }

    /**
     * POST /register - Web formundan gelen yeni kullanıcı kaydını işler.
     */
    public function register(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $input = $_POST;
        $errors = User::validateRegistration($input, true);

        if (!empty($errors)) {
            // Ayrıcalık yükseltme girişimi (admin/faculty) varsa 403, diğer validation hatalarında 422 dön
            $attemptedRole = trim($input['role'] ?? '');
            $statusCode = in_array($attemptedRole, ['admin', 'faculty'], true) ? 403 : 422;
            http_response_code($statusCode);

            View::render('auth/register', [
                'errors'         => $errors,
                'old'            => $input,
                'registeredUser' => null,
                'message'        => '⚠️ Kayıt tamamlanamadı. Lütfen formdaki hatalı alanları kontrol ediniz.',
            ]);
            return;
        }

        $newUser = User::register($input);

        // Oturum bilgisini güvenli şekilde kaydet (password_hash asla eklenmez)
        $_SESSION['user'] = [
            'id'     => $newUser['id'],
            'name'   => $newUser['name'],
            'email'  => $newUser['email'],
            'role'   => $newUser['role'],
            'status' => $newUser['status'],
        ];

        http_response_code(201);
        View::render('auth/register', [
            'errors'         => [],
            'old'            => [],
            'registeredUser' => $newUser,
            'message'        => "🎉 Hoş geldiniz {$newUser['name']}! Hesabınız '{$newUser['role']}' rolüyle başarıyla oluşturuldu.",
        ]);
    }

    /**
     * POST /api/auth/register - JSON REST API üzerinden kullanıcı kaydı (Postman / Swagger testleri için)
     */
    public function apiRegister(): void
    {
        $raw = file_get_contents('php://input');
        if (trim($raw) === '') {
            Response::json([
                'error'   => 'Boş istek gövdesi (Empty request body)',
                'message' => 'Kayıt işlemi için JSON formatında veri gönderilmelidir.'
            ], 400);
            return;
        }

        $data = json_decode($raw, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            Response::json([
                'error'   => 'Geçersiz JSON sözdizimi',
                'message' => json_last_error_msg()
            ], 400);
            return;
        }

        $errors = User::validateRegistration($data, false);
        if (!empty($errors)) {
            $attemptedRole = trim($data['role'] ?? '');
            $statusCode = in_array($attemptedRole, ['admin', 'faculty'], true) ? 403 : 422;

            Response::json([
                'error'   => $statusCode === 403 ? 'Yetkisiz Rol Seçimi (Forbidden Role Escalation)' : 'Doğrulama Hatası (Validation Failed)',
                'message' => 'Kayıt verileri ddd.md Bölüm 8 yetkilendirme ve doğrulama kurallarına uymuyor.',
                'errors'  => $errors
            ], $statusCode);
            return;
        }

        $newUser = User::register($data);
        Response::json([
            'message' => 'Kullanıcı kaydı başarıyla oluşturuldu.',
            'user'    => $newUser
        ], 201);
    }

    // =========================================================================
    // GİRİŞ İŞLEMLERİ (LOGIN) - Hafta 09 Görev 3
    // =========================================================================

    /**
     * GET /login - Giriş Yap sayfasını görüntüler.
     */
    public function showLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $success = null;
        if (!empty($_GET['logout'])) {
            $success = 'Oturumunuz başarıyla kapatıldı. Tekrar giriş yapabilirsiniz.';
        }

        View::render('auth/login', [
            'error'       => null,
            'success'     => $success,
            'old'         => [],
            'currentUser' => AuthMiddleware::user(),
        ]);
    }

    /**
     * POST /login - Web formundan gelen e-posta ve parola ile kullanıcı girişini işler.
     *
     * Kimlik Doğrulama Akışı:
     * 1. E-posta ve parolayı al
     * 2. User::authenticate() ile Bcrypt doğrulaması yap
     * 3. Hesap durumunu kontrol et (active, pending, suspended)
     * 4. Oturum bilgilerini $_SESSION['user'] içine yaz
     * 5. Redirect target varsa oraya, yoksa / adresine yönlendir
     */
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $email    = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        // 1. Boşluk Kontrolü
        if ($email === '' || $password === '') {
            http_response_code(422);
            View::render('auth/login', [
                'error'   => 'E-posta ve parola alanları zorunludur.',
                'success' => null,
                'old'     => ['email' => $email],
            ]);
            return;
        }

        // 2. Kimlik Doğrulama (Bcrypt)
        $user = User::authenticate($email, $password);

        if (!$user) {
            http_response_code(401);
            View::render('auth/login', [
                'error'   => 'Geçersiz e-posta adresi veya parola. Lütfen bilgilerinizi kontrol ediniz.',
                'success' => null,
                'old'     => ['email' => $email],
            ]);
            return;
        }

        // 3. Hesap Durumu Kontrolü (ddd.md Bölüm 8.4 - Status Enforcement)
        $status = $user['status'] ?? 'active';
        if ($status === 'suspended') {
            http_response_code(403);
            View::render('auth/login', [
                'error'   => '🚫 Hesabınız yönetici tarafından askıya alınmıştır (suspended). Destek için sistem yöneticisiyle iletişime geçiniz.',
                'success' => null,
                'old'     => ['email' => $email],
            ]);
            return;
        }

        if ($status === 'pending') {
            http_response_code(403);
            View::render('auth/login', [
                'error'   => '⏳ Hesabınız henüz onaylanmamıştır (pending). Lütfen e-posta doğrulamasını tamamlayınız veya yönetici onayını bekleyiniz.',
                'success' => null,
                'old'     => ['email' => $email],
            ]);
            return;
        }

        // 4. Oturum Bilgilerini Kaydet (password_hash asla eklenmez)
        $_SESSION['user'] = [
            'id'     => (int)$user['id'],
            'name'   => $user['name'],
            'email'  => $user['email'],
            'role'   => $user['role'],
            'status' => $user['status'],
        ];

        // Oturum sabitleme saldırısına karşı Session ID yenile
        session_regenerate_id(true);

        // 5. Yönlendirme: form'dan gelen redirect_to veya / ana sayfa
        $redirectTo = trim($_POST['redirect_to'] ?? '');
        if ($redirectTo === '' || str_starts_with($redirectTo, '//') || str_starts_with($redirectTo, 'http')) {
            $redirectTo = '/';
        }

        header('Location: ' . $redirectTo);
        exit;
    }

    // =========================================================================
    // ÇIKIŞ İŞLEMLERİ (LOGOUT) - Hafta 09 Görev 3
    // =========================================================================

    /**
     * GET /logout - Kullanıcı oturumunu sonlandırır ve ana sayfaya yönlendirir.
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        // Oturum verilerini temizle
        unset($_SESSION['user']);
        $_SESSION = [];

        // Session çerezini sil
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        // Session'ı yok et
        session_destroy();

        // Kullanıcıya çıkış bilgilendirmesi ile login sayfasına yönlendir
        header('Location: /login?logout=1');
        exit;
    }

    // =========================================================================
    // REST API GİRİŞ / ÇIKIŞ (API LOGIN & LOGOUT) - Hafta 09
    // =========================================================================

    /**
     * POST /api/auth/login - JSON REST API üzerinden giriş (Postman / Swagger testleri için)
     */
    public function apiLogin(): void
    {
        $raw = file_get_contents('php://input');
        if (trim($raw) === '') {
            Response::json([
                'error'   => 'Boş istek gövdesi (Empty request body)',
                'message' => 'Giriş işlemi için JSON formatında e-posta ve parola gönderilmelidir.'
            ], 400);
            return;
        }

        $data = json_decode($raw, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            Response::json([
                'error'   => 'Geçersiz JSON sözdizimi',
                'message' => json_last_error_msg()
            ], 400);
            return;
        }

        $email    = trim($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');

        if ($email === '' || $password === '') {
            Response::json([
                'error'   => 'Eksik Kimlik Bilgisi (Missing Credentials)',
                'message' => 'E-posta (email) ve parola (password) alanları zorunludur.'
            ], 422);
            return;
        }

        $user = User::authenticate($email, $password);
        if (!$user) {
            Response::json([
                'error'   => 'Kimlik Doğrulama Başarısız (401 Unauthorized)',
                'message' => 'Geçersiz e-posta adresi veya parola.'
            ], 401);
            return;
        }

        // Hesap durumu kontrolü
        $status = $user['status'] ?? 'active';
        if (in_array($status, ['suspended', 'pending'], true)) {
            $msg = $status === 'suspended'
                ? 'Hesap askıya alınmıştır (suspended). Tüm işlemler kilitlidir.'
                : 'Hesap henüz onaylanmamıştır (pending). Lütfen doğrulamayı tamamlayınız.';
            Response::json([
                'error'   => 'Hesap Erişim Engeli (403 Forbidden)',
                'message' => $msg,
                'status'  => $status
            ], 403);
            return;
        }

        // API tarafında da session başlat (Postman cookie jar kullanıyorsa)
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['user'] = [
            'id'     => (int)$user['id'],
            'name'   => $user['name'],
            'email'  => $user['email'],
            'role'   => $user['role'],
            'status' => $user['status'],
        ];
        session_regenerate_id(true);

        Response::json([
            'message'    => "Giriş başarılı. Hoş geldiniz {$user['name']}!",
            'user'       => $user,
            'session_id' => session_id()
        ], 200);
    }

    /**
     * POST /api/auth/logout - JSON REST API üzerinden çıkış
     */
    public function apiLogout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $wasLoggedIn = isset($_SESSION['user']);
        $userName = $_SESSION['user']['name'] ?? 'Bilinmeyen';

        unset($_SESSION['user']);
        $_SESSION = [];
        session_destroy();

        if ($wasLoggedIn) {
            Response::json([
                'message' => "Oturum başarıyla sonlandırıldı. Güle güle {$userName}!"
            ], 200);
        } else {
            Response::json([
                'message' => 'Zaten oturum açılmamış durumdasınız.'
            ], 200);
        }
    }

    /**
     * GET /api/auth/me - Oturum açmış mevcut kullanıcının profil ve rol bilgisini döner.
     */
    public function apiMe(): void
    {
        $user = AuthMiddleware::requireAuth(true);

        Response::json([
            'authenticated' => true,
            'user'          => $user,
            'permissions'   => [
                'can_post_job' => AuthMiddleware::canPostJob($user),
                'can_apply'    => in_array($user['role'] ?? '', ['student', 'alumnus'], true),
                'is_admin'     => ($user['role'] ?? '') === 'admin',
            ]
        ], 200);
    }
}
