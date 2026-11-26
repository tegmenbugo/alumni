<?php
use App\Middlewares\AuthMiddleware;

$navAuthUser = AuthMiddleware::user();
$navRole = $navAuthUser['role'] ?? 'guest';
$navRoleBadge = match ($navRole) {
    'admin'   => 'bg-danger-subtle text-danger border-danger-subtle',
    'faculty' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
    'alumnus' => 'bg-success-subtle text-success border-success-subtle',
    'student' => 'bg-info-subtle text-info-emphasis border-info-subtle',
    default   => 'bg-secondary-subtle text-secondary',
};
$navRoleIcon = match ($navRole) {
    'admin'   => '👑',
    'faculty' => '🏫',
    'alumnus' => '🎓',
    'student' => '📚',
    default   => '👤',
};
?>
<?php if ($navAuthUser): ?>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="/users/<?= (int)$navAuthUser['id'] ?>" class="text-decoration-none d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill border bg-light text-dark small fw-semibold" title="Profilim">
            <span><?= $navRoleIcon ?> <?= htmlspecialchars($navAuthUser['name']) ?></span>
            <span class="badge border <?= $navRoleBadge ?>"><?= htmlspecialchars($navRole) ?></span>
        </a>
        <a href="/logout" class="btn btn-outline-danger btn-sm px-3 fw-semibold" title="Oturumu Kapat">
            🚪 Çıkış Yap
        </a>
    </div>
<?php else: ?>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="/login" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
            🔑 Giriş Yap
        </a>
        <a href="/register" class="btn btn-primary btn-sm px-3 fw-semibold">
            📝 Kayıt Ol
        </a>
    </div>
<?php endif; ?>
