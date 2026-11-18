<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💼 Kariyer & İlanlar · Alumni Tracking System</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .page-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            padding: 45px 0 55px;
            border-radius: 0 0 24px 24px;
            margin-bottom: -30px;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }
        .job-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: all 0.25s ease;
        }
        .job-card:hover {
            transform: translateY(-4px);
            border-color: #93c5fd;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.08);
        }
    </style>
</head>
<body>

    <!-- Ortak Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="/">
                <span style="font-size: 1.6rem;">🎓</span>
                <span style="color: var(--primary-navy); font-size: 1.1rem;">Alumni Tracking</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#jobsNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="jobsNav">
                <ul class="navbar-nav me-auto ms-3">
                    <li class="nav-item"><a class="nav-link" href="/">🏠 Ana Sayfa</a></li>
                    <li class="nav-item"><a class="nav-link" href="/users">👥 Mezunlar</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold text-primary" href="/jobs">💼 Kariyer & İlanlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/api/swagger" target="_blank">📚 Swagger UI</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">ℹ️ Hakkında</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#newJobModal">
                        + Yeni İlan Yayınla
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sayfa Başlığı -->
    <header class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white mb-2 px-3 py-1 rounded-pill">
                        💼 İstanbul Üniversitesi Mezun Kariyer Merkezi
                    </span>
                    <h1 class="h2 fw-bold mb-1">İş & Staj İlanları Portalı</h1>
                    <p class="mb-0 opacity-75 small">Mezunlarımızın ve partner teknoloji şirketlerinin güncel kariyer fırsatları.</p>
                </div>
                <div>
                    <button class="btn btn-light fw-bold text-primary px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newJobModal">
                        + Yeni İlan Oluştur
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="container mb-5" style="position: relative; z-index: 2;">
        <!-- Bildirim Mesajı -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
                <span class="fs-4 me-2">🎉</span>
                <div class="flex-grow-1">
                    <?= htmlspecialchars($message) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Arama ve Filtreleme Kartı -->
        <div class="card card-custom p-3 mb-4">
            <form action="/jobs" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">🔍</span>
                        <input type="text" name="q" class="form-control border-start-0" 
                               placeholder="Pozisyon, şirket veya anahtar kelime..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">Tüm Çalışma Şekilleri</option>
                        <option value="Tam Zamanlı" <?= (($_GET['type'] ?? '') === 'Tam Zamanlı') ? 'selected' : '' ?>>Tam Zamanlı</option>
                        <option value="Yarı Zamanlı" <?= (($_GET['type'] ?? '') === 'Yarı Zamanlı') ? 'selected' : '' ?>>Yarı Zamanlı</option>
                        <option value="Staj" <?= (($_GET['type'] ?? '') === 'Staj') ? 'selected' : '' ?>>Staj</option>
                        <option value="Freelance" <?= (($_GET['type'] ?? '') === 'Freelance') ? 'selected' : '' ?>>Freelance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="workplace" class="form-select">
                        <option value="">Tüm Ortamlar</option>
                        <option value="Hibrit" <?= (($_GET['workplace'] ?? '') === 'Hibrit') ? 'selected' : '' ?>>Hibrit</option>
                        <option value="Uzaktan / Remote" <?= (($_GET['workplace'] ?? '') === 'Uzaktan / Remote') ? 'selected' : '' ?>>Uzaktan</option>
                        <option value="Ofisten" <?= (($_GET['workplace'] ?? '') === 'Ofisten') ? 'selected' : '' ?>>Ofisten</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Filtrele</button>
                </div>
            </form>
        </div>

        <!-- İlan Kartları Grid -->
        <div class="row g-4">
            <?php if (!empty($jobs)): ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="job-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-light text-dark border">
                                        🏢 <?= htmlspecialchars($job['company_name']) ?>
                                    </span>
                                    <span class="badge bg-success-subtle text-success">
                                        <?= htmlspecialchars($job['employment_type'] ?? 'Tam Zamanlı') ?>
                                    </span>
                                </div>
                                <h5 class="fw-bold mb-2">
                                    <a href="/jobs/<?= $job['id'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($job['title']) ?>
                                    </a>
                                </h5>
                                <p class="text-muted small mb-2">
                                    📍 <?= htmlspecialchars($job['location'] ?? 'İstanbul, Türkiye') ?> · 
                                    <?= htmlspecialchars($job['workplace_type'] ?? 'Hibrit') ?>
                                </p>
                                <p class="text-secondary small mb-3 text-truncate" style="max-height: 48px; overflow: hidden;">
                                    <?= htmlspecialchars($job['description'] ?? 'Detaylı ilan açıklaması için tıklayın.') ?>
                                </p>
                            </div>

                            <div class="pt-3 border-top mt-auto">
                                <?php if (!empty($job['salary_min'])): ?>
                                    <div class="small mb-2">
                                        <span class="text-muted">Maaş:</span>
                                        <strong class="text-success">
                                            <?= number_format($job['salary_min']) ?> - <?= number_format($job['salary_max'] ?? $job['salary_min']) ?> <?= htmlspecialchars($job['salary_currency'] ?? 'TRY') ?>
                                        </strong>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">#<?= htmlspecialchars((string)$job['id']) ?></small>
                                    <a href="/jobs/<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                        İlan Detayı & Başvur ➔
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card card-custom p-5 text-center text-muted">
                        <div class="fs-1 mb-2">💼</div>
                        <h5>Henüz bu kriterlere uygun ilan bulunmuyor.</h5>
                        <p class="small mb-3">Filtreleri temizleyebilir veya yeni bir iş ilanı yayınlayabilirsiniz.</p>
                        <a href="/jobs" class="btn btn-sm btn-outline-secondary">Tüm İlanları Göster</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Yeni İlan Modalı (DESIGN.md Kuralları & 120 Karakter Kontrolü) -->
    <div class="modal fade" id="newJobModal" tabindex="-1" aria-labelledby="newJobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="/jobs" method="POST">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold" id="newJobModalLabel">💼 Yeni Kariyer / Staj İlanı Yayınla</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Pozisyon Başlığı * (Maks 120 Karakter)</label>
                            <input type="text" name="title" class="form-control" placeholder="Örn: Junior Backend Developer" maxlength="120" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Şirket Adı * (Maks 120 Karakter)</label>
                            <input type="text" name="company_name" class="form-control" placeholder="Örn: Trendyol" maxlength="120" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Yayınlayan Mezun *</label>
                            <select name="posted_by_user_id" class="form-select">
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['company'] ?? 'Mezun') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Çalışma Şekli</label>
                            <select name="employment_type" class="form-select">
                                <option value="Tam Zamanlı">Tam Zamanlı</option>
                                <option value="Yarı Zamanlı">Yarı Zamanlı</option>
                                <option value="Staj">Staj</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Ortam Modeli</label>
                            <select name="workplace_type" class="form-select">
                                <option value="Hibrit">Hibrit</option>
                                <option value="Uzaktan / Remote">Uzaktan</option>
                                <option value="Ofisten">Ofisten</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Şehir / Lokasyon (Opsiyonel)</label>
                            <input type="text" name="location" class="form-control" placeholder="Örn: İstanbul, Türkiye" maxlength="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Min Maaş (TRY)</label>
                            <input type="number" name="salary_min" class="form-control" placeholder="45000" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Max Maaş (TRY)</label>
                            <input type="number" name="salary_max" class="form-control" placeholder="65000" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">İş Tanımı & Sorumluluklar (Opsiyonel)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Pozisyon detayları..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Aranan Nitelikler & Yetkinlikler (Opsiyonel)</label>
                            <textarea name="requirements" class="form-control" rows="2" placeholder="PHP, Docker, SQL bilgisi..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">İlanı Yayınla (POST /jobs)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ortak Footer -->
    <footer class="bg-white border-top py-4 text-muted small text-center">
        <div class="container">
            Alumni Tracking System · İstanbul Üniversitesi İktisat Fakültesi YBS · 26-27 Web Programming
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
