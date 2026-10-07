<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mezun Düzenle - <?= htmlspecialchars($user['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .hero-card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="/users" class="btn btn-outline-secondary">← Mezun Listesine Dön</a>
                    <a href="/users/<?= $user['id'] ?>" class="btn btn-outline-primary">Profili Görüntüle</a>
                </div>

                <div class="card hero-card p-4 p-md-5 bg-white">
                    <div class="mb-4">
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">
                                ✏️ POST /users/<?= $user['id'] ?>/update
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                🏛️ UserController::update() (View Layer)
                            </span>
                        </div>
                        <h2 class="fw-bold mb-1">Mezun Bilgilerini Düzenle (UPDATE)</h2>
                        <p class="text-muted small mb-0">
                            Mezun <strong>#<?= htmlspecialchars($user['id']) ?></strong> — <code>POST /users/<?= $user['id'] ?>/update → UserController::update()</code>
                        </p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                            <span class="fs-4 me-2">✅</span>
                            <div><?= htmlspecialchars($message) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Güncelleme Formu -->
                    <form action="/users/<?= $user['id'] ?>/update" method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ad Soyad *</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">E-Posta</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mezuniyet Yılı</label>
                            <input type="number" name="graduationYear" class="form-control"
                                   value="<?= htmlspecialchars($user['graduationYear'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bölüm</label>
                            <input type="text" name="department" class="form-control"
                                   value="<?= htmlspecialchars($user['department'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Çalıştığı Şirket</label>
                            <input type="text" name="company" class="form-control"
                                   value="<?= htmlspecialchars($user['company'] ?? '') ?>">
                        </div>
                        <div class="col-12 d-flex justify-content-between mt-3">
                            <a href="/users" class="btn btn-secondary">İptal</a>
                            <button type="submit" class="btn btn-warning fw-semibold px-4">
                                💾 Kaydet (UserController::update)
                            </button>
                        </div>
                    </form>

                    <!-- Silme Bölümü -->
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Tehlikeli Bölge (DELETE)</h6>
                            <small class="text-muted">Bu işlem geri alınamaz. <code>POST /users/<?= $user['id'] ?>/delete → UserController::destroy()</code></small>
                        </div>
                        <form action="/users/<?= $user['id'] ?>/delete" method="POST"
                              onsubmit="return confirm('<?= htmlspecialchars($user['name']) ?> isimli mezunu silmek istediğinize emin misiniz?');">
                            <button type="submit" class="btn btn-outline-danger">🗑️ Mezunu Sil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
