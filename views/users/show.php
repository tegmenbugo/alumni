<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['name']) ?> · Mezun Profili</title>
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
        .profile-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 50px 0 70px;
            border-radius: 0 0 24px 24px;
        }
        .profile-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            margin-top: -50px;
            padding: 35px;
        }
        .big-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 800;
            border: 3px solid #bfdbfe;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
        }
    </style>
</head>
<body>

    <!-- Dinamik Rol Tabanlı Üst Navbar -->
    <?php
        $navbarId = 'userShowNav';
        $extraActions = '<a href="/users" class="btn btn-outline-secondary btn-sm">← Mezunlar Listesi</a>';
        if (\App\Middlewares\AuthMiddleware::canEditUser((int)$user['id'])) {
            $extraActions .= ' <a href="/users/' . (int)$user['id'] . '/edit" class="btn btn-warning btn-sm fw-semibold">✏️ Düzenle</a>';
        }
        include __DIR__ . '/../partials/navbar.php';
    ?>

    <!-- Header Bölümü -->
    <div class="profile-header text-center">
        <div class="container">
            <span class="badge bg-white bg-opacity-25 text-white mb-2 px-3 py-1 rounded-pill">
                Mezun Kimlik No: #<?= htmlspecialchars((string)$user['id']) ?>
            </span>
            <h1 class="h3 fw-bold mb-0">İstanbul Üniversitesi Mezun Profili</h1>
        </div>
    </div>

    <main class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="profile-card">
                    <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom flex-wrap">
                        <div class="big-avatar">
                            <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h2 class="h3 fw-bold mb-0 text-dark"><?= htmlspecialchars($user['name']) ?></h2>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                    🛡️ <?= htmlspecialchars($user['role'] ?? 'alumnus') ?>
                                </span>
                                <span class="badge bg-success-subtle text-success">
                                    ● <?= htmlspecialchars($user['status'] ?? 'active') ?>
                                </span>
                                <span class="badge bg-primary-subtle text-primary">
                                    🎓 <?= htmlspecialchars((string)($user['graduationYear'] ?? 'Mezun')) ?>
                                </span>
                            </div>
                            <div class="text-muted mt-1">
                                🏢 <?= htmlspecialchars($user['company'] ?? 'Kurum belirtilmedi') ?> · 
                                📚 <?= htmlspecialchars($user['department'] ?? 'YBS') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Detay Bilgi Kutuları -->
                    <h5 class="fw-bold mb-3 text-secondary">📋 Akademik ve İletişim Bilgileri</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <small class="text-muted d-block mb-1">E-Posta Adresi</small>
                                <strong class="text-dark">
                                    <?= htmlspecialchars($user['email'] ?? 'Kayıtlı e-posta yok') ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <small class="text-muted d-block mb-1">Mezuniyet Yılı</small>
                                <strong class="text-primary">
                                    <?= htmlspecialchars((string)($user['graduationYear'] ?? 'Belirtilmedi')) ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <small class="text-muted d-block mb-1">Bölüm / Fakülte</small>
                                <strong class="text-dark">
                                    <?= htmlspecialchars($user['department'] ?? 'YBS') ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <small class="text-muted d-block mb-1">Mevcut Şirket / Pozisyon</small>
                                <strong class="text-dark">
                                    <?= htmlspecialchars($user['company'] ?? 'Serbest / Belirtilmedi') ?>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Aksiyon Barı -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                        <a href="/users" class="btn btn-outline-secondary">
                            ← Listeye Geri Dön
                        </a>
                        <div class="d-flex gap-2">
                            <a href="/users/<?= $user['id'] ?>/edit" class="btn btn-warning fw-semibold">
                                ✏️ Bilgileri Düzenle
                            </a>
                            <form action="/users/<?= $user['id'] ?>/delete" method="POST"
                                  onsubmit="return confirm('<?= htmlspecialchars($user['name']) ?> isimli mezun kaydını silmek istediğinize emin misiniz?');">
                                <button type="submit" class="btn btn-outline-danger">
                                    🗑️ Sil
                                </button>
                            </form>
                        </div>
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
