<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mezun Düzenle · <?= htmlspecialchars($user['name']) ?></title>
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
        .form-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            padding: 35px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="/">
                <span style="font-size: 1.6rem;">🎓</span>
                <span style="color: var(--primary-navy); font-size: 1.1rem;">Alumni Tracking</span>
            </a>
            <div class="ms-auto d-flex gap-2">
                <a href="/users/<?= $user['id'] ?>" class="btn btn-outline-secondary btn-sm">← Profile Dön</a>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-warning-subtle text-warning border px-3 py-1 rounded-pill mb-1">
                                ✏️ Güncelleme Ekranı
                            </span>
                            <h2 class="h4 fw-bold mb-0 text-dark">Mezun Kaydını Güncelle</h2>
                            <small class="text-muted">Kayıt No: #<?= htmlspecialchars((string)$user['id']) ?></small>
                        </div>
                        <a href="/users/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Profil Detayı</a>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            🎉 <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/users/<?= $user['id'] ?>/update" method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Ad Soyad *</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">E-Posta Adresi</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Mezuniyet Yılı</label>
                            <input type="number" name="graduationYear" class="form-control"
                                   value="<?= htmlspecialchars((string)($user['graduationYear'] ?? '')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Bölüm</label>
                            <input type="text" name="department" class="form-control"
                                   value="<?= htmlspecialchars($user['department'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Şirket / Kurum</label>
                            <input type="text" name="company" class="form-control"
                                   value="<?= htmlspecialchars($user['company'] ?? '') ?>">
                        </div>

                        <div class="col-12 d-flex justify-content-between pt-3 border-top mt-4">
                            <a href="/users" class="btn btn-secondary px-3">İptal</a>
                            <button type="submit" class="btn btn-warning fw-semibold px-4">
                                💾 Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>

                    <!-- Silme Alanı -->
                    <div class="alert alert-danger bg-danger-subtle border-danger-subtle mt-5 d-flex justify-content-between align-items-center mb-0">
                        <div>
                            <strong class="d-block text-danger">⚠️ Tehlikeli Bölge</strong>
                            <small class="text-danger-emphasis">Bu mezun kaydını sistemden kalıcı olarak siler.</small>
                        </div>
                        <form action="/users/<?= $user['id'] ?>/delete" method="POST"
                              onsubmit="return confirm('<?= htmlspecialchars($user['name']) ?> kaydını silmek istediğinize emin misiniz?');">
                            <button type="submit" class="btn btn-danger btn-sm">
                                🗑️ Mezunu Sil
                            </button>
                        </form>
                    </div>
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
