<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> · İlan Detayı & Başvuru</title>
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
            color: #1e293b;
        }
        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
        }
        .job-detail-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 50px 0 70px;
            border-radius: 0 0 24px 24px;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
    </style>
</head>
<body>

    <!-- Dinamik Rol Tabanlı Üst Navbar -->
    <?php
        $navbarId = 'jobShowNav';
        $extraActions = '<a href="/jobs" class="btn btn-outline-secondary btn-sm">← Tüm İlanlar</a>';
        include __DIR__ . '/../partials/navbar.php';
    ?>

    <!-- Header Bölümü -->
    <header class="job-detail-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill">
                            🏢 <?= htmlspecialchars($job['company_name']) ?>
                        </span>
                        <span class="badge bg-success text-white px-3 py-1 rounded-pill">
                            ● <?= htmlspecialchars($job['status'] ?? 'active') ?>
                        </span>
                    </div>
                    <h1 class="h2 fw-bold mb-1"><?= htmlspecialchars($job['title']) ?></h1>
                    <p class="mb-0 opacity-75 small">
                        📍 <?= htmlspecialchars($job['location'] ?? 'İstanbul') ?> · 
                        💼 <?= htmlspecialchars($job['employment_type'] ?? 'Tam Zamanlı') ?> · 
                        🏢 <?= htmlspecialchars($job['workplace_type'] ?? 'Hibrit') ?>
                    </p>
                </div>
            </div>
        </div>
    </header>

    <main class="container mb-5" style="margin-top: -35px; position: relative; z-index: 2;">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                🎉 <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Sol Kolon: İlan Detayları -->
            <div class="col-lg-7">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold mb-3 text-secondary">📋 Pozisyon Tanımı ve Sorumluluklar</h5>
                    <p class="text-secondary lh-lg mb-4" style="white-space: pre-line;">
                        <?= htmlspecialchars($job['description'] ?? 'Detaylı bir iş tanımı girilmedi.') ?>
                    </p>

                    <?php if (!empty($job['requirements'])): ?>
                        <h5 class="fw-bold mb-3 text-secondary">🎯 Aranan Nitelikler & Yetkinlikler</h5>
                        <p class="text-secondary lh-lg mb-4" style="white-space: pre-line;">
                            <?= htmlspecialchars($job['requirements']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="row g-3 p-3 bg-light rounded-3 border mt-2">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Maaş Skalası</small>
                            <strong class="text-success">
                                <?php if (!empty($job['salary_min'])): ?>
                                    <?= number_format($job['salary_min']) ?> - <?= number_format($job['salary_max'] ?? $job['salary_min']) ?> <?= htmlspecialchars($job['salary_currency'] ?? 'TRY') ?>
                                <?php else: ?>
                                    Belirtilmedi / Görüşmede
                                <?php endif; ?>
                            </strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">İlan Referans No</small>
                            <strong class="text-dark">#<?= htmlspecialchars((string)$job['id']) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- İlana Yapılan Başvurular Listesi (Nested) -->
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-secondary">
                            📥 Gelen Başvurular (<?= count($applications) ?>)
                        </h5>
                        <span class="badge bg-primary-subtle text-primary">Nested HasMany</span>
                    </div>

                    <?php if (!empty($applications)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($applications as $app): ?>
                                <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($app['applicant_name'] ?? 'Aday #' . $app['applicant_user_id']) ?>
                                        </div>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($app['applicant_email'] ?? '') ?> · 
                                            Tarih: <?= date('d.m.Y H:i', strtotime($app['applied_at'] ?? 'now')) ?>
                                        </small>
                                        <?php if (!empty($app['cover_letter'])): ?>
                                            <p class="small text-secondary mt-1 mb-0 italic">
                                                "<?= htmlspecialchars($app['cover_letter']) ?>"
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-info-subtle text-info">
                                        <?= htmlspecialchars($app['status'] ?? 'applied') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0 py-3 text-center">
                            Henüz bu ilana bir başvuru yapılmadı. İlk adayı siz olun!
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sağ Kolon: Başvuru Formu Kartı -->
            <div class="col-lg-5">
                <div class="card card-custom p-4 sticky-top" style="top: 85px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fs-4">🚀</span>
                        <h5 class="fw-bold mb-0">Hemen Başvuru Yap</h5>
                    </div>
                    <p class="text-muted small mb-4">
                        Bu ilana mezun profilinizle tek tıkla başvurabilirsiniz.
                    </p>

                    <?php
                        $showAuthUser = \App\Middlewares\AuthMiddleware::user();
                        $canApply = $showAuthUser ? \App\Middlewares\AuthMiddleware::canApplyJob($job, $showAuthUser) : false;
                        $isOwnJob = $showAuthUser && ((int)($job['posted_by_user_id'] ?? 0) === (int)$showAuthUser['id']);
                    ?>

                    <?php if (!$showAuthUser): ?>
                        <div class="alert alert-info small mb-3">
                            🔑 İlana başvurmak için hesabınızla giriş yapmalısınız.
                        </div>
                        <a href="/login?from=/jobs/<?= (int)$job['id'] ?>" class="btn btn-primary w-100 fw-semibold py-2 mb-2">
                            🔑 Giriş Yap ve Başvur
                        </a>
                        <a href="/register" class="btn btn-outline-primary w-100 btn-sm fw-semibold">
                            📝 Hesabınız Yoksa Kayıt Olun
                        </a>
                    <?php else: ?>
                        <?php if ($isOwnJob): ?>
                            <div class="alert alert-warning small mb-3">
                                ⚠️ Kendi yayınladığınız ilana başvuru yapamazsınız (Conflict of Interest).
                            </div>
                        <?php elseif (!$canApply): ?>
                            <div class="alert alert-danger small mb-3">
                                🚫 <strong><?= htmlspecialchars($showAuthUser['role']) ?></strong> rolü ilanlara başvuramaz. Başvurular yalnızca öğrenci ve mezunlara açıktır.
                            </div>
                        <?php endif; ?>

                        <form action="/jobs/<?= $job['id'] ?>/apply" method="POST" class="d-flex flex-column gap-3">
                            <div>
                                <label class="form-label fw-semibold small">Başvuran Hesap (Oturum)</label>
                                <input type="text" class="form-control bg-light" readonly
                                       value="<?= htmlspecialchars($showAuthUser['name'] . ' (' . $showAuthUser['role'] . ')') ?>">
                            </div>
                        <div>
                            <label class="form-label fw-semibold small">Ön Yazı / Motivasyon</label>
                            <textarea name="cover_letter" class="form-control" rows="3" placeholder="Neden bu rol için uygunsunuz?..."></textarea>
                        </div>
                        <div>
                            <label class="form-label fw-semibold small">CV / Özgeçmiş Linki (PDF/Drive)</label>
                            <input type="url" name="resume_url" class="form-control" placeholder="https://drive.google.com/...">
                        </div>
                        <div>
                            <label class="form-label fw-semibold small">Portfolyo / GitHub Linki</label>
                            <input type="url" name="portfolio_url" class="form-control" placeholder="https://github.com/...">
                        </div>
                        <button type="submit" class="btn btn-primary fw-semibold py-2 mt-2">
                            📥 Başvurumu Gönder (POST /apply)
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-top py-4 text-muted small text-center">
        <div class="container">
            Alumni Tracking System · İstanbul Üniversitesi İktisat Fakültesi YBS · 26-27 Web Programming
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
