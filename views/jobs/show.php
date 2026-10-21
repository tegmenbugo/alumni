<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> - <?= htmlspecialchars($job['company_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .hero-card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <!-- Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="/jobs" class="btn btn-outline-secondary">← Tüm İlanlara Dön</a>
                    <a href="/users" class="btn btn-outline-primary">👥 Mezun Rehberi</a>
                </div>

                <!-- İlan Ana Kartı -->
                <div class="card hero-card p-4 p-md-5 bg-white mb-4">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                            <span class="fs-4 me-2">🎉</span>
                            <div><?= htmlspecialchars($message) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-2">
                                🏢 <?= htmlspecialchars($job['company_name']) ?>
                            </span>
                            <h2 class="fw-bold mb-1"><?= htmlspecialchars($job['title']) ?></h2>
                            <p class="text-muted small mb-0">
                                📍 <?= htmlspecialchars($job['location'] ?? 'İstanbul') ?> · 
                                💼 <?= htmlspecialchars($job['employment_type'] ?? 'Tam Zamanlı') ?> · 
                                🏠 <?= htmlspecialchars($job['workplace_type'] ?? 'Hibrit') ?> · 
                                🎯 <?= htmlspecialchars($job['experience_level'] ?? 'Giriş Seviyesi') ?>
                            </p>
                        </div>
                        <button class="btn btn-success fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#applyModal">
                            ✍️ Hemen Başvur
                        </button>
                    </div>

                    <?php if (!empty($job['salary_min'])): ?>
                        <div class="alert alert-light border d-flex align-items-center justify-content-between p-3 rounded-3 mb-4">
                            <div>
                                <small class="text-muted d-block">Maaş Skalası:</small>
                                <strong class="fs-5 text-success">
                                    <?= number_format($job['salary_min']) ?> - <?= number_format($job['salary_max'] ?? $job['salary_min']) ?> <?= htmlspecialchars($job['salary_currency'] ?? 'TRY') ?>
                                </strong>
                            </div>
                            <?php if (!empty($job['deadline'])): ?>
                                <div class="text-end">
                                    <small class="text-muted d-block">Son Başvuru:</small>
                                    <strong class="text-dark"><?= htmlspecialchars($job['deadline']) ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- İlan Detayları -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark border-bottom pb-2">📋 Pozisyon Açıklaması</h5>
                        <p class="text-secondary leading-relaxed" style="white-space: pre-line;">
                            <?= htmlspecialchars($job['description']) ?>
                        </p>
                    </div>

                    <?php if (!empty($job['requirements'])): ?>
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark border-bottom pb-2">🎯 Aranan Nitelikler & Yetkinlikler</h5>
                            <p class="text-secondary" style="white-space: pre-line;">
                                <?= htmlspecialchars($job['requirements']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- İlan Sahibi Bilgisi -->
                    <div class="card bg-light border-0 p-3 rounded-3 mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <small class="text-muted d-block">İlanı Yayınlayan Mezunumuz:</small>
                                <strong><?= htmlspecialchars($job['posted_by_name'] ?? 'Mezun Üye') ?></strong>
                                <span class="text-muted small ms-1">(<?= htmlspecialchars($job['poster_company'] ?? '') ?>)</span>
                            </div>
                            <?php if (!empty($job['posted_by_user_id'])): ?>
                                <a href="/users/<?= $job['posted_by_user_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    Mezun Profilini Gör ➔
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Gelen Başvurular (İlişkisel / Nested Veri) -->
                    <div class="border-top pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                📥 Bu İlana Yapılan Başvurular 
                                <span class="badge bg-secondary"><?= count($applications) ?></span>
                            </h5>
                            <span class="badge bg-info-subtle text-info border">
                                Nested Route: /jobs/<?= $job['id'] ?>/applications
                            </span>
                        </div>

                        <?php if (!empty($applications)): ?>
                            <div class="list-group">
                                <?php foreach ($applications as $app): ?>
                                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start p-3">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <strong class="text-dark"><?= htmlspecialchars($app['applicant_name']) ?></strong>
                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($app['applicant_department'] ?? 'Öğrenci') ?></span>
                                                <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($app['status']) ?></span>
                                            </div>
                                            <p class="text-muted small mb-1">
                                                ✉️ <?= htmlspecialchars($app['applicant_email'] ?? '') ?> · 
                                                📅 <?= htmlspecialchars($app['applied_at'] ?? '') ?>
                                            </p>
                                            <?php if (!empty($app['cover_letter'])): ?>
                                                <small class="text-secondary d-block fst-italic">
                                                    "<?= htmlspecialchars($app['cover_letter']) ?>"
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-3 text-center text-muted bg-light rounded-3">
                                <small>Bu ilana henüz başvuru yapılmadı. İlk başvuruyu siz yapabilirsiniz!</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Başvuru Yap Modalı -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/jobs/<?= $job['id'] ?>/apply" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="applyModalLabel">✍️ İlana Başvuru Yap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Başvuran Mezun / Öğrenci Seçiniz *</label>
                            <select name="applicant_user_id" class="form-select" required>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['department'] ?? 'YBS') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ön Yazı / Motivasyon Notu</label>
                            <textarea name="cover_letter" class="form-control" rows="3" placeholder="Neden bu pozisyona uygunsunuz? Yetkinliklerinizden bahsedin..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-success fw-bold">Başvuruyu Gönder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
