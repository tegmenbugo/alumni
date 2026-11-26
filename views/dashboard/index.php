<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Kişisel Panelim · Alumni Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #1e3a8a;
            --accent-blue: #2563eb;
            --light-bg: #f8fafc;
            --card-border: #e2e8f0;
        }
        body {
            background-color: var(--light-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
        }
        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .dash-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            padding: 40px 0 60px;
            border-radius: 0 0 24px 24px;
            margin-bottom: -30px;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }
    </style>
</head>
<body>

    <?php $navbarId = 'dashNav'; include __DIR__ . '/../partials/navbar.php'; ?>

    <?php
        $role = $user['role'] ?? 'alumnus';
        $roleTitle = match ($role) {
            'admin'   => '👑 Sistem Yöneticisi Çalışma Alanı',
            'faculty' => '🏫 Akademisyen Kariyer & İlan Alanı',
            'alumnus' => '🎓 Mezun Kariyer & Ağ Alanı',
            'student' => '📚 Öğrenci Staj & Kariyer Alanı',
            default   => '👤 Kullanıcı Paneli',
        };
    ?>

    <header class="dash-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-2">
                        <?= $roleTitle ?> · Rol: <?= htmlspecialchars(strtoupper($role)) ?>
                    </span>
                    <h1 class="h2 fw-bold mb-1">Hoş Geldiniz, <?= htmlspecialchars($user['name']) ?></h1>
                    <p class="mb-0 opacity-75 small">
                        📚 <?= htmlspecialchars($user['department'] ?? 'Yönetim Bilişim Sistemleri') ?> ·
                        🏢 <?= htmlspecialchars($user['company'] ?? 'İstanbul Üniversitesi') ?>
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="/users/<?= (int)$user['id'] ?>/edit" class="btn btn-light btn-sm fw-semibold text-primary px-3">
                        ✏️ Profilimi Düzenle
                    </a>
                    <?php if ($role === 'admin'): ?>
                        <a href="/admin" class="btn btn-danger btn-sm fw-semibold px-3">
                            🛡️ Admin Paneli
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container pb-5" style="position: relative; z-index: 2;">
        <!-- Rol Bazlı Hızlı İstatistikler -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-custom p-4 h-100">
                    <div class="small text-muted fw-semibold">🔐 Hesap & Rol Yetkiniz</div>
                    <div class="h4 fw-bold text-primary mt-2 mb-1"><?= htmlspecialchars(strtoupper($role)) ?></div>
                    <div class="small text-muted">
                        <?php if ($canPostJob && $canApplyJob): ?>
                            ✅ İlan açabilir ve ilanlara başvurabilirsiniz.
                        <?php elseif ($canPostJob): ?>
                            ✅ İlan yayınlayabilir ve aday başvurularını yönetebilirsiniz.
                        <?php elseif ($canApplyJob): ?>
                            ✅ Aktif staj ve iş ilanlarına başvurabilirsiniz. (İlan açma kapalı)
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($canPostJob): ?>
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100">
                        <div class="small text-muted fw-semibold">📋 Yayınladığım İlanlar</div>
                        <div class="h4 fw-bold text-success mt-2 mb-1"><?= count($myJobs) ?> İlan · <?= (int)$incomingCount ?> Başvuru</div>
                        <div class="mt-2">
                            <a href="/my-jobs" class="btn btn-sm btn-outline-success fw-semibold">İlanlarımı & Adayları Yönet →</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($canApplyJob): ?>
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100">
                        <div class="small text-muted fw-semibold">📥 Başvurularım</div>
                        <div class="h4 fw-bold text-info mt-2 mb-1"><?= count($myApplications) ?> Aktif Başvuru</div>
                        <div class="mt-2">
                            <a href="/my-applications" class="btn btn-sm btn-outline-info fw-semibold">Başvuru Durumlarımı Gör →</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-md-4">
                <div class="card card-custom p-4 h-100">
                    <div class="small text-muted fw-semibold">👤 Kişisel Profil Bilgisi</div>
                    <div class="h5 fw-bold text-dark mt-2 mb-1"><?= htmlspecialchars($user['email'] ?? '—') ?></div>
                    <div class="small text-muted mb-2">Mezuniyet / Dönem: <?= htmlspecialchars((string)($user['graduationYear'] ?? '—')) ?></div>
                    <a href="/users/<?= (int)$user['id'] ?>" class="btn btn-sm btn-outline-primary fw-semibold">Profil Kartıma Git →</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Sol Blok: Role Göre Kişisel Özet -->
            <div class="col-lg-7">
                <?php if ($canApplyJob): ?>
                    <div class="card card-custom p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">📥 Son Başvurularım</h5>
                            <a href="/my-applications" class="small text-decoration-none fw-semibold">Tümünü Gör →</a>
                        </div>
                        <?php if (!empty($myApplications)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($myApplications, 0, 5) as $app): ?>
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="/jobs/<?= (int)$app['job_posting_id'] ?>" class="fw-bold text-decoration-none text-dark">
                                                <?= htmlspecialchars($app['job_title'] ?? ('İlan #' . $app['job_posting_id'])) ?>
                                            </a>
                                            <div class="small text-muted">🏢 <?= htmlspecialchars($app['job_company'] ?? '') ?></div>
                                        </div>
                                        <span class="badge bg-info-subtle text-info-emphasis border"><?= htmlspecialchars($app['status'] ?? 'applied') ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Henüz bir ilana başvuru yapmadınız. <a href="/jobs">İlanları keşfedin →</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($canPostJob): ?>
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">📋 Yayındaki İlanlarım</h5>
                            <a href="/my-jobs" class="small text-decoration-none fw-semibold">Tümünü Yönet →</a>
                        </div>
                        <?php if (!empty($myJobs)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($myJobs, 0, 5) as $mj): ?>
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="/jobs/<?= (int)$mj['id'] ?>" class="fw-bold text-decoration-none text-dark">
                                                <?= htmlspecialchars($mj['title']) ?>
                                            </a>
                                            <div class="small text-muted">🏢 <?= htmlspecialchars($mj['company_name']) ?> · <?= (int)$mj['applicant_count'] ?> Başvuru</div>
                                        </div>
                                        <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($mj['status'] ?? 'active') ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Henüz ilan yayınlamadınız. <a href="/jobs?new=1">İlk ilanınızı yayınlayın →</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sağ Blok: Güncel Açık İlanlar -->
            <div class="col-lg-5">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">💼 Güncel Kariyer Fırsatları</h5>
                        <a href="/jobs" class="small text-decoration-none fw-semibold">Portal →</a>
                    </div>
                    <?php if (!empty($openJobs)): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($openJobs as $oj): ?>
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <a href="/jobs/<?= (int)$oj['id'] ?>" class="fw-bold text-decoration-none text-dark">
                                            <?= htmlspecialchars($oj['title']) ?>
                                        </a>
                                        <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($oj['employment_type'] ?? 'Tam Zamanlı') ?></span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        🏢 <?= htmlspecialchars($oj['company_name']) ?> · 📍 <?= htmlspecialchars($oj['location'] ?? 'İstanbul') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">Şu an aktif ilan bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-top py-4 text-muted small text-center">
        <div class="container">
            Alumni Tracking System · Kişisel Kullanıcı Çalışma Alanı · İstanbul Üniversitesi YBS
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
