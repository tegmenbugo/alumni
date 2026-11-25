<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\View;
use App\Core\Response;

/**
 * AuthController (Kimlik Doğrulama ve Kayıt Denetleyicisi)
 * ddd.md Bölüm 8 (Auth & RBAC) kurallarına uygun kullanıcı kaydı (Web & REST API) işlemlerini yönetir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 09
 */
class AuthController
{
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
}
