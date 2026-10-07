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
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-2">
                                👥 Web Controller: UserController::index()
                            </span>
                            <h2 class="fw-bold mb-0">Mezun Rehberi (Alumni Directory)</h2>
                        </div>
                        <button class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            + Yeni Mezun Ekle
                        </button>
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
                                                    <form action="/users/<?= $u['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
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
