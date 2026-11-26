<?php
use App\Middlewares\AuthMiddleware;

$menuItems    = AuthMiddleware::getNavigationMenu();
$currentPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$navbarId     = $navbarId ?? 'dynamicNavbar';
$extraActions = $extraActions ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="/">
            <span style="font-size: 1.65rem;">🎓</span>
            <div>
                <div class="lh-1" style="font-size: 1.1rem; color: #1e3a8a;">Alumni Tracking</div>
                <small class="text-muted d-none d-sm-block" style="font-size: 0.7rem;">İstanbul Üniversitesi YBS</small>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#<?= htmlspecialchars($navbarId) ?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="<?= htmlspecialchars($navbarId) ?>">
            <ul class="navbar-nav me-auto ms-lg-3 mb-2 mb-lg-0">
                <?php foreach ($menuItems as $item): ?>
                    <?php
                        $hrefPath = parse_url($item['href'], PHP_URL_PATH) ?: '/';
                        $isActive = ($item['id'] !== 'post_job') && (
                            ($hrefPath === '/' && $currentPath === '/') ||
                            ($hrefPath !== '/' && str_starts_with($currentPath, $hrefPath))
                        );
                    ?>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold <?= $isActive ? 'active text-primary' : 'text-dark' ?>"
                           href="<?= htmlspecialchars($item['href']) ?>"
                           target="<?= htmlspecialchars($item['target']) ?>">
                            <?= $item['icon'] ?> <?= htmlspecialchars($item['label']) ?>
                            <?php if (!empty($item['badge'])): ?>
                                <span class="badge bg-danger ms-1" style="font-size: 0.65rem;"><?= htmlspecialchars($item['badge']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <?php if ($extraActions !== ''): ?>
                    <?= $extraActions ?>
                <?php endif; ?>
                <?php include __DIR__ . '/auth_nav.php'; ?>
            </div>
        </div>
    </div>
</nav>
