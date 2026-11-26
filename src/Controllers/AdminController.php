<?php
namespace App\Controllers;

use App\Core\View;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\LoggerMiddleware;
use App\Models\User;
use App\Models\JobPosting;
use App\Models\JobApplication;

/**
 * AdminController (Sistem Yönetim Paneli - Hafta 09 Görev 5)
 * Yalnızca 'admin' rolüne sahip yöneticilerin erişebildiği veri yönetim ve denetim panelidir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming
 */
class AdminController
{
    /**
     * GET /admin - Yönetici Kontrol Paneli (Kullanıcılar, İlanlar, Başvurular, Loglar)
     */
    public function index(): void
    {
        $adminUser = AuthMiddleware::requireRole(['admin'], false);
        User::ensureAuthColumns();

        $users        = User::all();
        $jobs         = JobPosting::all();
        $applications = JobApplication::all();

        // Rol ve durum bazlı özet istatistikler
        $roleStats = ['admin' => 0, 'faculty' => 0, 'alumnus' => 0, 'student' => 0];
        $statusStats = ['active' => 0, 'pending' => 0, 'suspended' => 0];
        foreach ($users as $u) {
            $r = $u['role'] ?? 'alumnus';
            $s = $u['status'] ?? 'active';
            $roleStats[$r] = ($roleStats[$r] ?? 0) + 1;
            $statusStats[$s] = ($statusStats[$s] ?? 0) + 1;
        }

        // Son hata günlüklerini oku
        $errorLogs = [];
        $logFile = LoggerMiddleware::getLogDirectory() . '/error.logs';
        if (file_exists($logFile)) {
            $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $errorLogs = array_reverse(array_slice($lines, -15));
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $flashSuccess = $_SESSION['admin_flash_success'] ?? null;
        $flashError   = $_SESSION['admin_flash_error'] ?? null;
        unset($_SESSION['admin_flash_success'], $_SESSION['admin_flash_error']);

        View::render('admin/index', [
            'adminUser'    => $adminUser,
            'users'        => $users,
            'jobs'         => $jobs,
            'applications' => $applications,
            'roleStats'    => $roleStats,
            'statusStats'  => $statusStats,
            'errorLogs'    => $errorLogs,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ]);
    }

    /**
     * POST /admin/users - Admin panelinden özel rol ve statü ile yeni kullanıcı oluştur
     */
    public function storeUser(): void
    {
        AuthMiddleware::requireRole(['admin'], false);
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['admin_flash_error'] = '⚠️ Geçerli bir Ad Soyad ve E-posta adresi girmelisiniz.';
            header('Location: /admin#users-tab');
            exit;
        }

        if (User::findByEmail($email) !== null) {
            $_SESSION['admin_flash_error'] = "⚠️ '{$email}' adresi sistemde zaten kayıtlı.";
            header('Location: /admin#users-tab');
            exit;
        }

        $created = User::adminCreate($_POST);
        $_SESSION['admin_flash_success'] = "✅ '{$created['name']}' kullanıcısı '{$created['role']}' rolüyle eklendi.";
        header('Location: /admin#users-tab');
        exit;
    }

    /**
     * POST /admin/users/{id}/role - Kullanıcının rolünü ve hesap durumunu güncelle
     */
    public function updateUserRoleStatus(string $id): void
    {
        $adminUser = AuthMiddleware::requireRole(['admin'], false);
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $targetId = (int)$id;
        $newRole   = trim($_POST['role'] ?? '');
        $newStatus = trim($_POST['status'] ?? '');

        // Yönetici kendi kendini kilitlemesin veya admin yetkisini kazara almasın
        if ($targetId === (int)$adminUser['id'] && ($newRole !== 'admin' || $newStatus !== 'active')) {
            $_SESSION['admin_flash_error'] = '🚫 Güvenlik kuralı: Aktif yöneticinin kendi admin rolünü kaldırmasına veya hesabını askıya almasına izin verilmez.';
            header('Location: /admin#users-tab');
            exit;
        }

        $updated = User::updateRoleAndStatus($targetId, $newRole, $newStatus);
        if (!$updated) {
            $_SESSION['admin_flash_error'] = "❌ Kullanıcı bulunamadı (ID: {$targetId}).";
        } else {
            $_SESSION['admin_flash_success'] = "✅ '{$updated['name']}' güncellendi → Rol: {$updated['role']}, Durum: {$updated['status']}.";
        }

        header('Location: /admin#users-tab');
        exit;
    }

    /**
     * POST /admin/users/{id}/delete - Kullanıcıyı sistemden sil
     */
    public function deleteUser(string $id): void
    {
        $adminUser = AuthMiddleware::requireRole(['admin'], false);
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $targetId = (int)$id;
        if ($targetId === (int)$adminUser['id']) {
            $_SESSION['admin_flash_error'] = '🚫 Kendi yönetici hesabınızı panel üzerinden silemezsiniz.';
            header('Location: /admin#users-tab');
            exit;
        }

        $deleted = User::delete($targetId);
        if ($deleted) {
            $_SESSION['admin_flash_success'] = "🗑️ '{$deleted['name']}' (#{$targetId}) kullanıcısı sistemden silindi.";
        } else {
            $_SESSION['admin_flash_error'] = "❌ Silinecek kullanıcı bulunamadı (ID: {$targetId}).";
        }

        header('Location: /admin#users-tab');
        exit;
    }

    /**
     * POST /admin/jobs/{id}/status - İş ilanının yayın durumunu güncelle (active, closed, draft, archived)
     */
    public function updateJobStatus(string $id): void
    {
        AuthMiddleware::requireRole(['admin'], false);
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $jobId = (int)$id;
        $status = trim($_POST['status'] ?? 'active');
        $allowed = ['active', 'closed', 'draft', 'archived'];

        if (!in_array($status, $allowed, true)) {
            $_SESSION['admin_flash_error'] = '⚠️ Geçersiz ilan durumu.';
            header('Location: /admin#jobs-tab');
            exit;
        }

        $updated = JobPosting::update($jobId, ['status' => $status]);
        if ($updated) {
            $_SESSION['admin_flash_success'] = "✅ '{$updated['title']}' (#{$jobId}) ilan durumu '{$status}' olarak güncellendi.";
        } else {
            $_SESSION['admin_flash_error'] = "❌ İlan bulunamadı (ID: {$jobId}).";
        }

        header('Location: /admin#jobs-tab');
        exit;
    }

    /**
     * POST /admin/jobs/{id}/delete - İş ilanını moderasyon kapsamında sil
     */
    public function deleteJob(string $id): void
    {
        AuthMiddleware::requireRole(['admin'], false);
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $jobId = (int)$id;
        $deleted = JobPosting::delete($jobId);
        if ($deleted) {
            $_SESSION['admin_flash_success'] = "🗑️ '{$deleted['title']}' (#{$jobId}) ilanı moderasyon kapsamında silindi.";
        } else {
            $_SESSION['admin_flash_error'] = "❌ İlan bulunamadı (ID: {$jobId}).";
        }

        header('Location: /admin#jobs-tab');
        exit;
    }

    /**
     * POST /admin/applications/{id}/status - İş başvuru durumunu güncelle
     */
    public function updateApplicationStatus(string $id): void
    {
        AuthMiddleware::requireRole(['admin'], false);
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $appId = (int)$id;
        $status = trim($_POST['status'] ?? 'applied');
        $updated = JobApplication::updateStatus($appId, $status);

        if ($updated) {
            $_SESSION['admin_flash_success'] = "✅ Başvuru #{$appId} durumu '{$updated['status']}' olarak güncellendi.";
        } else {
            $_SESSION['admin_flash_error'] = "❌ Başvuru bulunamadı (ID: {$appId}).";
        }

        header('Location: /admin#apps-tab');
        exit;
    }
}
