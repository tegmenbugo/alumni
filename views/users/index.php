<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👥 Mezunlar Portalı · Alumni Tracking System</title>
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
        .user-card:hover {
            transform: translateY(-3px);
            border-color: #93c5fd;
            box-shadow: 0 8px 25px rgba(37,99,235,0.08);
        }
        .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid #bfdbfe;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="userNav">
                <ul class="navbar-nav me-auto ms-3">
                    <li class="nav-item"><a class="nav-link" href="/">🏠 Ana Sayfa</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold text-primary" href="/users">👥 Mezunlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/jobs">💼 İlanlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/api/swagger" target="_blank">📚 Swagger UI</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">ℹ️ Hakkında</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        ➕ Yeni Mezun Ekle
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
                        🏛️ İstanbul Üniversitesi YBS Mezun Veritabanı
                    </span>
                    <h1 class="h2 fw-bold mb-1">Mezunlar Portföyü & Ağ Rehberi</h1>
                    <p class="mb-0 opacity-75 small">Kayıtlı mezunların sektör, şirket ve mezuniyet yıllarına göre detaylı listesi.</p>
                </div>
                <div>
                    <button class="btn btn-light fw-bold text-primary px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        + Yeni Mezun Kaydı
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="container mb-5" style="position: relative; z-index: 2;">
        <!-- Bildirim Alanı -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
                <span class="fs-4 me-2">🎉</span>
                <div class="flex-grow-1">
                    <strong>İşlem Başarılı!</strong> <?= htmlspecialchars($message) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        <?php endif; ?>

        <!-- Arama ve Filtreleme Kartı -->
        <div class="card card-custom p-3 mb-4">
            <form action="/users" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">🔍</span>
                        <input type="text" name="q" class="form-control border-start-0" 
                               placeholder="İsim, şirket, bölüm veya mezuniyet yılı ara..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="newest" <?= (($_GET['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>En Yeni Mezunlar</option>
                        <option value="name_asc" <?= (($_GET['sort'] ?? '') === 'name_asc') ? 'selected' : '' ?>>İsim (A - Z)</option>
                        <option value="year_desc" <?= (($_GET['sort'] ?? '') === 'year_desc') ? 'selected' : '' ?>>Mezuniyet Yılı (Azalan)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Filtrele</button>
                </div>
                <div class="col-md-2 text-end">
                    <span class="badge bg-light text-dark border p-2 w-100">
                        Toplam: <strong><?= count($users) ?></strong> Mezun
                    </span>
                </div>
            </form>
        </div>

        <!-- Mezunlar Tablo / Kart Listesi -->
        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary text-uppercase tracking-wider">
                            <th class="ps-4">Mezun</th>
                            <th>Bölüm</th>
                            <th>Güncel Şirket / Kurum</th>
                            <th>Mezuniyet Yılı</th>
                            <th>Durum</th>
                            <th class="text-end pe-4">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle">
                                                <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <a href="/users/<?= $user['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?= htmlspecialchars($user['name']) ?>
                                                </a>
                                                <div class="small text-muted">
                                                    <?= htmlspecialchars($user['email'] ?? 'E-posta eklenmedi') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="small fw-semibold text-secondary">
                                            <?= htmlspecialchars($user['department'] ?? 'YBS') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($user['company'])): ?>
                                            <span class="badge bg-light text-dark border">
                                                🏢 <?= htmlspecialchars($user['company']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                            🎓 <?= htmlspecialchars((string)($user['graduationYear'] ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">
                                            ● <?= htmlspecialchars($user['status'] ?? 'active') ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/users/<?= $user['id'] ?>" class="btn btn-outline-primary" title="Profili Gör">
                                                👁️ Profil
                                            </a>
                                            <a href="/users/<?= $user['id'] ?>/edit" class="btn btn-outline-warning" title="Düzenle">
                                                ✏️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="fs-1 mb-2">🔍</div>
                                    <h6>Kriterlere uygun mezun bulunamadı.</h6>
                                    <p class="small mb-3">Arama filtrenizi temizleyebilir veya yeni mezun ekleyebilirsiniz.</p>
                                    <a href="/users" class="btn btn-sm btn-outline-secondary">Filtreleri Sıfırla</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Yeni Mezun Ekleme Modalı (DESIGN.md Standartlarında) -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="/users" method="POST">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold" id="addUserModalLabel">➕ Yeni Mezun Kaydı Ekle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body p-4 row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Ad Soyad *</label>
                            <input type="text" name="name" class="form-control" placeholder="Örn: Mehmet Can" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">E-Posta</label>
                            <input type="email" name="email" class="form-control" placeholder="mehmet.can@alumni.iu.edu.tr">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mezuniyet Yılı</label>
                            <input type="number" name="graduationYear" class="form-control" placeholder="2024" min="1950" max="2040" value="2025">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Bölüm</label>
                            <input type="text" name="department" class="form-control" value="Yönetim Bilişim Sistemleri">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Şirket / Kurum</label>
                            <input type="text" name="company" class="form-control" placeholder="Örn: Getir">
                        </div>
                    </div>
                    <div class="modal-footer p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Kaydet (POST /users)</button>
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
