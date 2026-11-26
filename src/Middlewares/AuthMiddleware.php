<?php
namespace App\Middlewares;

use App\Core\Response;
use App\Core\View;

/**
 * AuthMiddleware (Yetkilendirme ve Erişim Kontrol Bekçisi - The Bouncer)
 * ddd.md Bölüm 8 (Auth & RBAC) kurallarına göre kullanıcı kimlik doğrulaması
 * ve rol bazlı erişim denetimlerini (RBAC) merkezi olarak yönetir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 09 (Görev 4)
 */
class AuthMiddleware
{
    /**
     * Oturumu güvenli şekilde başlatır.
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }

    /**
     * Mevcut oturum açmış kullanıcı verisini döner.
     */
    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Oturum açmış kullanıcının ID'sini döner.
     */
    public static function id(): ?int
    {
        $u = self::user();
        return $u !== null ? (int)$u['id'] : null;
    }

    /**
     * Oturum açmış kullanıcının sistem rolünü döner ('guest', 'student', 'alumnus', 'faculty', 'admin').
     */
    public static function role(): string
    {
        $u = self::user();
        return $u['role'] ?? 'guest';
    }

    /**
     * Kullanıcının oturum açıp açmadığını denetler.
     */
    public static function isAuthenticated(): bool
    {
        return self::user() !== null;
    }

    /**
     * Ziyaretçi (oturum açmamış) kontrolü.
     */
    public static function isGuest(): bool
    {
        return !self::isAuthenticated();
    }

    /**
     * Kullanıcının belirtilen rollerden birine sahip olup olmadığını kontrol eder.
     *
     * @param string|array $roles Tek rol veya rol dizisi
     */
    public static function hasRole(string|array $roles): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        $rolesArray = is_array($roles) ? $roles : [$roles];
        return in_array(self::role(), $rolesArray, true);
    }

    /**
     * Kimlik doğrulaması zorunlu kılar (Oturum açmamışsa 401 veya login yönlendirmesi).
     */
    public static function requireAuth(bool $isApi = false): array
    {
        if (!self::isAuthenticated()) {
            if ($isApi || self::isJsonRequest()) {
                http_response_code(401);
                Response::json([
                    'error'     => 'Kimlik Doğrulama Gerekli (401 Unauthorized)',
                    'message'   => 'Bu işlemi gerçekleştirmek için sisteme giriş yapmalısınız.',
                    'login_url' => '/login'
                ], 401);
                exit;
            }

            self::startSession();
            $_SESSION['flash_error'] = '⚠️ Bu sayfaya erişmek veya işlem yapmak için lütfen önce giriş yapınız.';
            $target = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: /login?from=' . urlencode($target));
            exit;
        }

        return self::user();
    }

    /**
     * Ortak 403 Forbidden yanıtı (API için JSON, Web için 403.php View).
     */
    public static function denyForbidden(
        string $reason,
        array $allowedRoles = [],
        bool $isApi = false,
        string $errorTitle = 'Yetkisiz Erişim (403 Forbidden)'
    ): void {
        $currentRole = self::role();

        if ($isApi || self::isJsonRequest()) {
            http_response_code(403);
            Response::json([
                'error'         => $errorTitle,
                'message'       => $reason,
                'current_role'  => $currentRole,
                'allowed_roles' => $allowedRoles
            ], 403);
            exit;
        }

        http_response_code(403);
        View::render('errors/403', [
            'reason'       => $reason,
            'currentRole'  => $currentRole,
            'allowedRoles' => $allowedRoles
        ]);
        exit;
    }

    /**
     * Belirli rolleri zorunlu kılar (RBAC Guard).
     *
     * @param array $allowedRoles İzin verilen roller listesi örn: ['alumnus', 'faculty', 'admin']
     */
    public static function requireRole(array $allowedRoles, bool $isApi = false): array
    {
        $user = self::requireAuth($isApi);

        if (!in_array($user['role'], $allowedRoles, true)) {
            $currentRole = $user['role'];
            $allowedStr = implode(', ', $allowedRoles);
            self::denyForbidden(
                "Bu sayfaya veya işleme erişim yetkiniz bulunmamaktadır. İzin verilen roller: [{$allowedStr}]. Mevcut rolünüz: {$currentRole}.",
                $allowedRoles,
                $isApi
            );
        }

        return $user;
    }

    /**
     * İş/Staj ilanı yayınlama yetki kontrolü (ddd.md Bölüm 8.3 & 8.4)
     * İlan yayınlama yetkisi: Yalnızca 'alumnus', 'faculty' ve 'admin' rollerine aittir.
     * Öğrenciler ('student') ve misafirler ('guest') kesinlikle ilan açamaz!
     */
    public static function canPostJob(?array $user = null): bool
    {
        $user = $user ?? self::user();
        if (!$user) {
            return false;
        }

        // Hesap aktif olmalı
        if (($user['status'] ?? 'active') !== 'active') {
            return false;
        }

        // İzin verilen roller
        return in_array($user['role'] ?? '', ['alumnus', 'faculty', 'admin'], true);
    }

    /**
     * İlan yayınlama yetkisini zorunlu kılar (Öğrenci ise 403 döner).
     */
    public static function requireCanPostJob(bool $isApi = false): array
    {
        $user = self::requireAuth($isApi);

        if (!self::canPostJob($user)) {
            $role = $user['role'];
            $msg = ($role === 'student')
                ? '🚫 Öğrenci (student) rolündeki kullanıcılar iş veya staj ilanı yayınlayamaz. İlan açma yetkisi ddd.md Bölüm 8 gereği yalnızca Mezun (alumnus), Akademisyen (faculty) ve Sistem Yöneticilerine (admin) aittir.'
                : '🚫 Hesabınız iş ilanı yayınlamak için yetkili değil veya askıya alınmış.';

            self::denyForbidden(
                $msg,
                ['alumnus', 'faculty', 'admin'],
                $isApi,
                'Yetkisiz Rol (403 Forbidden - Role Not Allowed to Post Job)'
            );
        }

        return $user;
    }

    /**
     * İlanı düzenleme, silme veya gelen başvuruları inceleme yetkisi (Sahiplik veya admin).
     */
    public static function canManageJob(array $job, ?array $user = null): bool
    {
        $user = $user ?? self::user();
        if (!$user) {
            return false;
        }

        if (($user['role'] ?? '') === 'admin') {
            return true;
        }

        return (int)($job['posted_by_user_id'] ?? 0) === (int)$user['id']
            && in_array($user['role'] ?? '', ['alumnus', 'faculty'], true);
    }

    /**
     * İlan yönetim yetkisini (Sahiplik / Admin) zorunlu kılar.
     */
    public static function requireCanManageJob(array $job, bool $isApi = false): array
    {
        $user = self::requireAuth($isApi);

        if (!self::canManageJob($job, $user)) {
            self::denyForbidden(
                '🚫 Bu ilan üzerinde işlem yapma (güncelleme, silme veya başvuruları görme) yetkiniz yok. Yalnızca ilanı yayınlayan kullanıcı veya Sistem Yöneticisi (admin) yetkilidir.',
                ['owner (alumnus/faculty)', 'admin'],
                $isApi,
                'Yetkisiz Kaynak Sahipliği (403 Forbidden - Ownership Required)'
            );
        }

        return $user;
    }

    /**
     * İlana başvuru yapabilme yetkisi ve çıkar çatışması (Conflict of Interest) kontrolü.
     * - Yalnızca 'student' ve 'alumnus' başvurabilir ('faculty' ve 'admin' başvuramaz).
     * - Kullanıcı kendi yayınladığı ilana başvuru yapamaz.
     */
    public static function canApplyJob(array $job, ?array $user = null): bool
    {
        $user = $user ?? self::user();
        if (!$user) {
            return false;
        }

        // Başvurabilecek roller: student veya alumnus
        if (!in_array($user['role'] ?? '', ['student', 'alumnus'], true)) {
            return false;
        }

        // Çıkar çatışması engeli: Kendi ilanına başvuramaz
        $postedBy = (int)($job['posted_by_user_id'] ?? 0);
        if ($postedBy > 0 && $postedBy === (int)$user['id']) {
            return false;
        }

        return true;
    }

    /**
     * İlana başvuru yetkisini zorunlu kılar.
     */
    public static function requireCanApplyJob(array $job, bool $isApi = false): array
    {
        $user = self::requireAuth($isApi);

        if (!self::canApplyJob($job, $user)) {
            $role = $user['role'];
            $isOwnJob = ((int)($job['posted_by_user_id'] ?? 0) === (int)$user['id']);
            $reason = $isOwnJob
                ? '🚫 Kendi yayınladığınız iş ilanına başvuru yapamazsınız (Conflict of Interest - ddd.md Bölüm 8.4).'
                : "🚫 '{$role}' rolündeki kullanıcılar iş ilanlarına başvuru yapamaz. Başvuru yetkisi yalnızca Öğrenci (student) ve Mezun (alumnus) rollerine aittir.";

            self::denyForbidden(
                $reason,
                ['student', 'alumnus'],
                $isApi,
                $isOwnJob ? 'Çıkar Çatışması (403 Forbidden - Conflict of Interest)' : 'Yetkisiz Rol (403 Forbidden)'
            );
        }

        return $user;
    }

    /**
     * Kullanıcı profili düzenleme yetki kontrolü (Sahiplik: auth.id === target.id VEYA admin).
     */
    public static function canEditUser(int $targetUserId, ?array $user = null): bool
    {
        $user = $user ?? self::user();
        if (!$user) {
            return false;
        }

        if (($user['role'] ?? '') === 'admin') {
            return true;
        }

        return (int)$user['id'] === $targetUserId;
    }

    /**
     * Kullanıcı profili düzenleme yetkisini zorunlu kılar.
     */
    public static function requireCanEditUser(int $targetUserId, bool $isApi = false): array
    {
        $user = self::requireAuth($isApi);

        if (!self::canEditUser($targetUserId, $user)) {
            self::denyForbidden(
                '🚫 Sahiplik Kuralı (Ownership): Yalnızca kendi profilinizi düzenleyebilirsiniz. Başka kullanıcıların profilini yalnızca Sistem Yöneticisi (admin) düzenleyebilir.',
                ['owner', 'admin'],
                $isApi,
                'Yetkisiz Profil Düzenleme (403 Forbidden - Ownership Required)'
            );
        }

        return $user;
    }

    /**
     * DİNAMİK NAVİGASYON MENÜSÜ (Hafta 09 - Görev 6: Dynamic Navigation by User Role)
     * Oturum açan kullanıcının rolüne göre ('guest', 'student', 'alumnus', 'faculty', 'admin')
     * yalnızca erişim yetkisi olduğu menü öğelerini döndürür.
     */
    public static function getNavigationMenu(): array
    {
        $user = self::user();
        $role = $user['role'] ?? 'guest';

        $items = [
            [
                'id'     => 'home',
                'label'  => 'Ana Sayfa',
                'icon'   => '🏠',
                'href'   => '/',
                'roles'  => ['guest', 'student', 'alumnus', 'faculty', 'admin'],
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'users',
                'label'  => 'Mezunlar',
                'icon'   => '👥',
                'href'   => '/users',
                'roles'  => ['guest', 'student', 'alumnus', 'faculty', 'admin'],
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'jobs',
                'label'  => 'Kariyer & İlanlar',
                'icon'   => '💼',
                'href'   => '/jobs',
                'roles'  => ['guest', 'student', 'alumnus', 'faculty', 'admin'],
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'dashboard',
                'label'  => 'Panelim',
                'icon'   => '📊',
                'href'   => '/dashboard',
                'roles'  => ['student', 'alumnus', 'faculty', 'admin'], // Sadece giriş yapanlar
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'my_applications',
                'label'  => 'Başvurularım',
                'icon'   => '📥',
                'href'   => '/my-applications',
                'roles'  => ['student', 'alumnus'], // Başvuru yapabilen roller
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'my_jobs',
                'label'  => 'İlanlarım',
                'icon'   => '📋',
                'href'   => '/my-jobs',
                'roles'  => ['alumnus', 'faculty', 'admin'], // İlan açabilen roller (öğrenci göremez)
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'post_job',
                'label'  => 'İlan Yayınla',
                'icon'   => '➕',
                'href'   => '/jobs?new=1',
                'roles'  => ['alumnus', 'faculty', 'admin'], // student ve guest göremez!
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'profile',
                'label'  => 'Profilim',
                'icon'   => '👤',
                'href'   => $user ? ('/users/' . (int)$user['id']) : '/login',
                'roles'  => ['student', 'alumnus', 'faculty', 'admin'], // Sadece giriş yapmış kullanıcılar
                'target' => '_self',
                'badge'  => null,
            ],
            [
                'id'     => 'admin',
                'label'  => 'Admin Paneli',
                'icon'   => '🛡️',
                'href'   => '/admin',
                'roles'  => ['admin'], // Yalnızca Sistem Yöneticisi görür!
                'target' => '_self',
                'badge'  => 'ADMIN',
            ],
            [
                'id'     => 'migrations',
                'label'  => 'Migrasyonlar',
                'icon'   => '⚙️',
                'href'   => '/api/migrations',
                'roles'  => ['admin'], // Yalnızca Sistem Yöneticisi görür!
                'target' => '_blank',
                'badge'  => null,
            ],
            [
                'id'     => 'swagger',
                'label'  => 'Swagger API',
                'icon'   => '📚',
                'href'   => '/api/swagger',
                'roles'  => ['alumnus', 'faculty', 'admin'], // Geliştirici & yetkili roller
                'target' => '_blank',
                'badge'  => null,
            ],
            [
                'id'     => 'about',
                'label'  => 'Hakkında',
                'icon'   => 'ℹ️',
                'href'   => '/about',
                'roles'  => ['guest', 'student', 'alumnus', 'faculty', 'admin'],
                'target' => '_self',
                'badge'  => null,
            ],
        ];

        return array_values(array_filter($items, function (array $item) use ($role) {
            return in_array($role, $item['roles'], true);
        }));
    }

    /**
     * İsteğin JSON tabanlı bir API çağrısı olup olmadığını algılar.
     */
    private static function isJsonRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_starts_with($uri, '/api/')) {
            return true;
        }

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json');
    }
}
