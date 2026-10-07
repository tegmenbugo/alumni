<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mezun Listesi - Alumni Tracking System (MVC)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .hero-card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="/" class="btn btn-outline-secondary">← Ana Sayfaya Dön</a>
                    <a href="/api/swagger" class="btn btn-outline-primary" target="_blank">Swagger API Arayüzü ↗</a>
                </div>

                <div class="card hero-card p-4 p-md-5 bg-white mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                    🌐 GET /users & POST /users
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                    🏛️ Controller: UserController (Web View Layer)
                                </span>
                            </div>
                            <h2 class="fw-bold mb-0">Mezun Yönetim Arayüzü (Alumni Directory)</h2>
                            <p class="text-muted small mb-0 mt-1">Bu sayfa JSON/API yerine doğrudan HTML kullanıcı arayüzü sunan View katmanıdır.</p>
                        </div>
                        <button class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            + Modal ile Ekle
                        </button>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                            <span class="fs-4 me-2">🎉</span>
                            <div>
                                <strong>İşlem Başarılı!</strong> <?= htmlspecialchars($message) ?>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                    <?php elseif (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                            <span class="fs-4 me-2">🎉</span>
                            <div>
                                <strong>İşlem Başarılı!</strong> Yeni mezun kaydı <code>POST /users</code> üzerinden veritabanına eklendi.
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Inline Yeni Mezun Ekleme Formu (View Layer - POST /users) -->
                    <div class="card bg-light border-0 rounded-3 p-4 mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success me-2">POST /users</span>
                            <h5 class="fw-bold mb-0">Yeni Mezun Ekle (HTML Web Formu)</h5>
                        </div>
                        <form action="/users" method="POST" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Ad Soyad *</label>
                                <input type="text" name="name" class="form-control" placeholder="Örn: Ayşe Demir" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">E-Posta</label>
                                <input type="email" name="email" class="form-control" placeholder="ayse.demir@alumni.iu.edu.tr">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Mezuniyet Yılı</label>
                                <input type="number" name="graduationYear" class="form-control" placeholder="2025" value="2025">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Bölüm</label>
                                <input type="text" name="department" class="form-control" placeholder="Yönetim Bilişim Sistemleri">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Çalıştığı Şirket</label>
                                <input type="text" name="company" class="form-control" placeholder="Örn: Trendyol">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4 fw-semibold">
                                    ➕ Mezun Kaydet (POST /users)
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- User Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Ad Soyad</th>
                                    <th>E-Posta</th>
                                    <th>Mezuniyet</th>
                                    <th>Bölüm</th>
                                    <th>Şirket</th>
                                    <th class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($u['id']) ?></strong></td>
                                            <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                                            <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($u['graduationYear'] ?? '-') ?></span></td>
                                            <td><?= htmlspecialchars($u['department'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($u['company'] ?? '-') ?></td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/users/<?= $u['id'] ?>" class="btn btn-outline-primary">Profil</a>
                                                    <a href="/users/<?= $u['id'] ?>/edit" class="btn btn-outline-warning">Düzenle</a>
                                                    <form action="/users/<?= $u['id'] ?>/delete" method="POST" class="d-inline"
                                                          onsubmit="return confirm('<?= htmlspecialchars($u['name']) ?> isimli mezunu silmek istediğinize emin misiniz?');">
                                                        <button type="submit" class="btn btn-outline-danger">Sil</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Henüz kayıtlı mezun bulunmuyor.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yeni Mezun Ekleme Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/users" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="addUserModalLabel">Yeni Mezun Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ad Soyad *</label>
                            <input type="text" name="name" class="form-control" placeholder="Örn: Ayşe Demir" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-Posta</label>
                            <input type="email" name="email" class="form-control" placeholder="ayse.demir@alumni.iu.edu.tr">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mezuniyet Yılı</label>
                            <input type="number" name="graduationYear" class="form-control" placeholder="2025">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bölüm</label>
                            <input type="text" name="department" class="form-control" placeholder="Yönetim Bilişim Sistemleri">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Çalıştığı Şirket</label>
                            <input type="text" name="company" class="form-control" placeholder="Örn: Trendyol">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-success">Kaydet (UserController::store)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
