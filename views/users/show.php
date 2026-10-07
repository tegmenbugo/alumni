<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mezun Detayı - <?= htmlspecialchars($user['name']) ?></title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="/users" class="btn btn-outline-secondary">← Mezun Listesine Dön</a>
                </div>

                <div class="card hero-card p-4 p-md-5 bg-white">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3 align-self-start">
                        👤 Web Controller: UserController::show()
                    </span>
                    <h2 class="display-6 fw-bold text-dark mb-1"><?= htmlspecialchars($user['name']) ?></h2>
                    <p class="text-muted mb-4">Mezun Kayıt No: <strong>#<?= htmlspecialchars($user['id']) ?></strong></p>

                    <div class="card bg-light border-0 p-4 rounded-3 mb-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <small class="text-muted d-block">E-Posta:</small>
                                <strong><?= htmlspecialchars($user['email'] ?? 'Belirtilmedi') ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Mezuniyet Yılı:</small>
                                <strong><?= htmlspecialchars($user['graduationYear'] ?? 'Belirtilmedi') ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Bölüm:</small>
                                <strong><?= htmlspecialchars($user['department'] ?? 'Belirtilmedi') ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Çalıştığı Şirket:</small>
                                <strong><?= htmlspecialchars($user['company'] ?? 'Belirtilmedi') ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/users" class="btn btn-secondary">← Geri Dön</a>
                        <form action="/users/<?= $user['id'] ?>/delete" method="POST" onsubmit="return confirm('Bu mezunu silmek istediğinize emin misiniz?');">
                            <button type="submit" class="btn btn-danger">Mezunu Sil (UserController::destroy)</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
