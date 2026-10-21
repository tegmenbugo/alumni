<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İş ve Staj İlanları - Alumni Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .hero-card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .job-card { border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; border: 1px solid #e9ecef; }
        .job-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <!-- Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <a href="/" class="btn btn-outline-secondary">← Ana Sayfa</a>
                        <a href="/users" class="btn btn-outline-primary">👥 Mezun Rehberi</a>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#newJobModal">
                            + Yeni İlan Yayınla
                        </button>
                        <a href="/api/swagger" class="btn btn-outline-dark" target="_blank">Swagger API ↗</a>
                    </div>
                </div>

                <!-- Başlık ve Durum Kartı -->
                <div class="card hero-card p-4 p-md-5 bg-white mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                💼 Hafta 06: Çoklu Tablo ve İlişkisel Rotalar
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                🏛️ Web UI: JobPostingController::index()
                            </span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill">
                                🔗 Nested: /users/{id}/jobs & /jobs/{id}/applications
                            </span>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">Kariyer & İş/Staj İlanları Portalı</h2>
                    <p class="text-muted small mb-0">
                        Mezunlarımızın ve partner kurumların açtığı kariyer fırsatları. İlanlara doğrudan başvurabilir veya yeni ilan oluşturabilirsiniz.
                    </p>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mt-3 mb-0" role="alert">
                            <span class="fs-4 me-2">🎉</span>
                            <div><?= htmlspecialchars($message) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Arama ve Filtreleme Çubuğu -->
                <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
                    <form action="/jobs" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Pozisyon, şirket veya anahtar kelime ara..." 
                                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">Tüm Çalışma Türleri</option>
                                <option value="Tam Zamanlı" <?= (($_GET['type'] ?? '') === 'Tam Zamanlı') ? 'selected' : '' ?>>Tam Zamanlı</option>
                                <option value="Yarı Zamanlı" <?= (($_GET['type'] ?? '') === 'Yarı Zamanlı') ? 'selected' : '' ?>>Yarı Zamanlı</option>
                                <option value="Stajyer" <?= (($_GET['type'] ?? '') === 'Stajyer') ? 'selected' : '' ?>>Stajyer</option>
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
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">🔍 Filtrele</button>
                            <?php if (!empty($_GET['q']) || !empty($_GET['type']) || !empty($_GET['workplace'])): ?>
                                <a href="/jobs" class="btn btn-outline-secondary">✕</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- İlan Kartları Listesi -->
                <div class="row g-3">
                    <?php if (!empty($jobs)): ?>
                        <?php foreach ($jobs as $job): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card job-card h-100 p-4 bg-white d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($job['company_name']) ?></span>
                                            <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($job['employment_type'] ?? 'Tam Zamanlı') ?></span>
                                        </div>
                                        <h5 class="fw-bold mb-2">
                                            <a href="/jobs/<?= $job['id'] ?>" class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($job['title']) ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-3">
                                            📍 <?= htmlspecialchars($job['location'] ?? 'İstanbul') ?> · 
                                            🏢 <?= htmlspecialchars($job['workplace_type'] ?? 'Hibrit') ?>
                                        </p>
                                        <p class="text-secondary small mb-3 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= htmlspecialchars($job['description']) ?>
                                        </p>
                                    </div>
                                    <div>
                                        <?php if (!empty($job['salary_min'])): ?>
                                            <div class="mb-3 small">
                                                <span class="text-muted">Maaş:</span>
                                                <strong class="text-success">
                                                    <?= number_format($job['salary_min']) ?> - <?= number_format($job['salary_max'] ?? $job['salary_min']) ?> <?= htmlspecialchars($job['salary_currency'] ?? 'TRY') ?>
                                                </strong>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <small class="text-muted">
                                                İlan #<?= htmlspecialchars((string)$job['id']) ?>
                                            </small>
                                            <a href="/jobs/<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                                İlanı Gör & Başvur ➔
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card p-5 text-center text-muted border-0 shadow-sm bg-white rounded-3">
                                <span class="fs-1 mb-2">💼</span>
                                <h5>Henüz kriterlere uygun ilan bulunmuyor.</h5>
                                <p class="small mb-3">İlk ilanı siz yayınlayabilir veya filtreleri temizleyebilirsiniz.</p>
                                <div>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#newJobModal">+ Yeni İlan Ekle</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Yeni İlan Ekleme Modalı -->
    <div class="modal fade" id="newJobModal" tabindex="-1" aria-labelledby="newJobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="/jobs" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="newJobModalLabel">💼 Yeni İş / Staj İlanı Yayınla</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pozisyon Başlığı *</label>
                            <input type="text" name="title" class="form-control" placeholder="Örn: Junior Backend Developer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Şirket Adı *</label>
                            <input type="text" name="company_name" class="form-control" placeholder="Örn: Trendyol" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Yayınlayan Mezun *</label>
                            <select name="posted_by_user_id" class="form-select">
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['company'] ?? 'Mezun') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Çalışma Şekli</label>
                            <select name="employment_type" class="form-select">
                                <option value="Tam Zamanlı">Tam Zamanlı</option>
                                <option value="Yarı Zamanlı">Yarı Zamanlı</option>
                                <option value="Stajyer">Stajyer</option>
                                <option value="Sözleşmeli">Sözleşmeli</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ortam Modeli</label>
                            <select name="workplace_type" class="form-select">
                                <option value="Hibrit">Hibrit</option>
                                <option value="Uzaktan / Remote">Uzaktan</option>
                                <option value="Ofisten">Ofisten</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lokasyon</label>
                            <input type="text" name="location" class="form-control" value="İstanbul / Hibrit">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Min Maaş (Opsiyonel)</label>
                            <input type="number" name="salary_min" class="form-control" placeholder="45000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Max Maaş (Opsiyonel)</label>
                            <input type="number" name="salary_max" class="form-control" placeholder="65000">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">İş Tanımı & Sorumluluklar *</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Pozisyon detayları..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Aranan Nitelikler & Yetkinlikler</label>
                            <textarea name="requirements" class="form-control" rows="2" placeholder="PHP, SQL, Git bilgisi vb..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-success fw-semibold">İlanı Yayınla (POST /jobs)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
