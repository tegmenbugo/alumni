<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛡️ Admin Paneli · Alumni Tracking System</title>
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
            color: #0f172a;
        }
        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .admin-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
            color: #ffffff;
            padding: 40px 0 60px;
            border-radius: 0 0 24px 24px;
            margin-bottom: -32px;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            padding: 20px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
        }
        .log-box {
            background: #0f172a;
            color: #e2e8f0;
            font-family: "SF Mono", Menlo, Consolas, monospace;
            font-size: 0.78rem;
            border-radius: 12px;
            padding: 16px;
            max-height: 280px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

    <!-- Dinamik Rol Tabanlı Üst Menü -->
    <?php $navbarId = 'adminNav'; include __DIR__ . '/../partials/navbar.php'; ?>

    <!-- Admin Başlık Alanı -->
    <header class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-danger px-3 py-1 rounded-pill mb-2">
                        👑 Sistem Yöneticisi Kontrol Merkezi (RBAC: admin)
                    </span>
                    <h1 class="h2 fw-bold mb-1">🛡️ Alumni Tracking Admin Paneli</h1>
                    <p class="mb-0 opacity-75 small">
                        Hoş geldiniz <strong><?= htmlspecialchars($adminUser['name']) ?></strong> · Kullanıcı rolleri, iş ilanları, başvurular ve sistem loglarını buradan yönetebilirsiniz.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light fw-bold text-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#adminAddUserModal">
                        ➕ Rol Atamalı Kullanıcı Ekle
                    </button>
                    <a href="/api/migrations" target="_blank" class="btn btn-outline-light btn-sm px-3">
                        ⚙️ Migrasyon Durumu
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container pb-5" style="position: relative; z-index: 2;">
        <!-- Flash Bildirimler -->
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 rounded-3" role="alert">
                <?= htmlspecialchars($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 rounded-3" role="alert">
                <?= htmlspecialchars($flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- KPI Özet Kartları -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="kpi-card">
                    <div class="text-muted small fw-semibold">👥 Toplam Kullanıcı</div>
                    <div class="display-6 fw-bold text-primary mt-1"><?= count($users) ?></div>
                    <div class="small text-muted mt-1">
                        👑 <?= $roleStats['admin'] ?? 0 ?> Admin · 🎓 <?= $roleStats['alumnus'] ?? 0 ?> Mezun · 📚 <?= $roleStats['student'] ?? 0 ?> Öğrenci
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card">
                    <div class="text-muted small fw-semibold">💼 İş & Staj İlanları</div>
                    <div class="display-6 fw-bold text-success mt-1"><?= count($jobs) ?></div>
                    <div class="small text-muted mt-1">Aktif, taslak ve kapalı tüm ilanlar</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card">
                    <div class="text-muted small fw-semibold">📥 Gelen Başvurular</div>
                    <div class="display-6 fw-bold text-info mt-1"><?= count($applications) ?></div>
                    <div class="small text-muted mt-1">Tüm aday değerlendirme kayıtları</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card">
                    <div class="text-muted small fw-semibold">🔐 Hesap Durumları</div>
                    <div class="display-6 fw-bold text-warning mt-1"><?= $statusStats['active'] ?? 0 ?></div>
                    <div class="small text-muted mt-1">
                        Aktif · ⏳ <?= $statusStats['pending'] ?? 0 ?> Bekleyen · 🚫 <?= $statusStats['suspended'] ?? 0 ?> Askıda
                    </div>
                </div>
            </div>
        </div>

        <!-- Sekmeler (Tabs) -->
        <ul class="nav nav-pills mb-4 gap-2" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="users-tab" data-bs-toggle="pill" data-bs-target="#users-pane" type="button" role="tab">
                    👥 Kullanıcı & Rol Yönetimi (<?= count($users) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="jobs-tab" data-bs-toggle="pill" data-bs-target="#jobs-pane" type="button" role="tab">
                    💼 İlan Moderasyonu (<?= count($jobs) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="apps-tab" data-bs-toggle="pill" data-bs-target="#apps-pane" type="button" role="tab">
                    📥 Başvuru Yönetimi (<?= count($applications) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="logs-tab" data-bs-toggle="pill" data-bs-target="#logs-pane" type="button" role="tab">
                    📜 Hata Günlükleri (error.logs)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            <!-- 1. KULLANICILAR VE ROL YÖNETİMİ -->
            <div class="tab-pane fade show active" id="users-pane" role="tabpanel">
                <div class="card card-custom overflow-hidden">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0">Kullanıcılar, Roller ve Hesap Durumları</h5>
                            <small class="text-muted">Buradan herhangi bir kullanıcıya <code>admin</code>, <code>faculty</code>, <code>alumnus</code> veya <code>student</code> rolü atayabilir, hesabı askıya alabilirsiniz.</small>
                        </div>
                        <button class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#adminAddUserModal">
                            ➕ Yeni Kullanıcı Ekle
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Ad Soyad & E-Posta</th>
                                    <th>Bölüm / Şirket</th>
                                    <th>Rol & Durum Güncelle</th>
                                    <th class="text-end pe-3">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <?php $isSelf = ((int)$u['id'] === (int)$adminUser['id']); ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted">#<?= (int)$u['id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= htmlspecialchars($u['name']) ?>
                                                <?php if ($isSelf): ?>
                                                    <span class="badge bg-primary ms-1">Siz</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted"><?= htmlspecialchars($u['email'] ?? '—') ?></div>
                                        </td>
                                        <td class="small">
                                            <div><?= htmlspecialchars($u['department'] ?? '—') ?></div>
                                            <div class="text-muted"><?= htmlspecialchars($u['company'] ?? '—') ?> (<?= htmlspecialchars((string)($u['graduationYear'] ?? '—')) ?>)</div>
                                        </td>
                                        <td>
                                            <form action="/admin/users/<?= (int)$u['id'] ?>/role" method="POST" class="d-flex gap-2 align-items-center flex-wrap">
                                                <select name="role" class="form-select form-select-sm" style="width: 130px;">
                                                    <option value="student" <?= ($u['role'] === 'student') ? 'selected' : '' ?>>📚 student</option>
                                                    <option value="alumnus" <?= ($u['role'] === 'alumnus') ? 'selected' : '' ?>>🎓 alumnus</option>
                                                    <option value="faculty" <?= ($u['role'] === 'faculty') ? 'selected' : '' ?>>🏫 faculty</option>
                                                    <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : '' ?>>👑 admin</option>
                                                </select>
                                                <select name="status" class="form-select form-select-sm" style="width: 125px;">
                                                    <option value="active" <?= ($u['status'] === 'active') ? 'selected' : '' ?>>🟢 active</option>
                                                    <option value="pending" <?= ($u['status'] === 'pending') ? 'selected' : '' ?>>⏳ pending</option>
                                                    <option value="suspended" <?= ($u['status'] === 'suspended') ? 'selected' : '' ?>>🚫 suspended</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold">Kaydet</button>
                                            </form>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="d-inline-flex gap-1">
                                                <a href="/users/<?= (int)$u['id'] ?>/edit" class="btn btn-sm btn-outline-secondary" title="Detaylı Düzenle">✏️</a>
                                                <?php if (!$isSelf): ?>
                                                    <form action="/admin/users/<?= (int)$u['id'] ?>/delete" method="POST" onsubmit="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinize emin misiniz?');">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">🗑️</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. İŞ İLANLARI MODERASYONU -->
            <div class="tab-pane fade" id="jobs-pane" role="tabpanel">
                <div class="card card-custom overflow-hidden">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0">İş ve Staj İlanları Moderasyonu</h5>
                            <small class="text-muted">Tüm ilanların yayın durumunu (active, closed, draft, archived) değiştirebilir veya uygunsuz ilanları silebilirsiniz.</small>
                        </div>
                        <a href="/jobs?new=1" class="btn btn-outline-primary btn-sm fw-semibold">+ Yeni İlan Oluştur</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Pozisyon & Şirket</th>
                                    <th>Lokasyon & Tür</th>
                                    <th>Durum Güncelle</th>
                                    <th class="text-end pe-3">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jobs as $j): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted">#<?= (int)$j['id'] ?></td>
                                        <td>
                                            <a href="/jobs/<?= (int)$j['id'] ?>" class="fw-bold text-decoration-none text-dark">
                                                <?= htmlspecialchars($j['title']) ?>
                                            </a>
                                            <div class="small text-muted">🏢 <?= htmlspecialchars($j['company_name']) ?> · Sahip ID: #<?= (int)$j['posted_by_user_id'] ?></div>
                                        </td>
                                        <td class="small">
                                            <div>📍 <?= htmlspecialchars($j['location'] ?? '—') ?></div>
                                            <div class="text-muted"><?= htmlspecialchars($j['employment_type'] ?? '—') ?> · <?= htmlspecialchars($j['workplace_type'] ?? '—') ?></div>
                                        </td>
                                        <td>
                                            <form action="/admin/jobs/<?= (int)$j['id'] ?>/status" method="POST" class="d-flex gap-2 align-items-center">
                                                <select name="status" class="form-select form-select-sm" style="width: 135px;">
                                                    <option value="active" <?= (($j['status'] ?? '') === 'active') ? 'selected' : '' ?>>🟢 active</option>
                                                    <option value="closed" <?= (($j['status'] ?? '') === 'closed') ? 'selected' : '' ?>>🔴 closed</option>
                                                    <option value="draft" <?= (($j['status'] ?? '') === 'draft') ? 'selected' : '' ?>>📝 draft</option>
                                                    <option value="archived" <?= (($j['status'] ?? '') === 'archived') ? 'selected' : '' ?>>📦 archived</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold">Güncelle</button>
                                            </form>
                                        </td>
                                        <td class="text-end pe-3">
                                            <form action="/admin/jobs/<?= (int)$j['id'] ?>/delete" method="POST" onsubmit="return confirm('Bu ilanı kalıcı olarak silmek istediğinize emin misiniz?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Sil</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($jobs)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">Sistemde henüz iş ilanı bulunmuyor.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. BAŞVURU YÖNETİMİ -->
            <div class="tab-pane fade" id="apps-pane" role="tabpanel">
                <div class="card card-custom overflow-hidden">
                    <div class="p-3 bg-white border-bottom">
                        <h5 class="fw-bold mb-0">Başvuru Durumu Yönetimi</h5>
                        <small class="text-muted">Tüm iş ve staj başvurularının aşamasını (applied, reviewing, interview, accepted, rejected) yönetin.</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Aday & İlan</th>
                                    <th>Tarih</th>
                                    <th>Durum Güncelle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted">#<?= (int)$app['id'] ?></td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($app['applicant_name'] ?? ('Aday #' . $app['applicant_user_id'])) ?></div>
                                            <div class="small text-muted">
                                                İlan: <a href="/jobs/<?= (int)$app['job_posting_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($app['job_title'] ?? ('#' . $app['job_posting_id'])) ?></a>
                                                (<?= htmlspecialchars($app['job_company'] ?? $app['company_name'] ?? '') ?>)
                                            </div>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($app['applied_at'] ?? '—') ?></td>
                                        <td>
                                            <form action="/admin/applications/<?= (int)$app['id'] ?>/status" method="POST" class="d-flex gap-2 align-items-center">
                                                <select name="status" class="form-select form-select-sm" style="width: 150px;">
                                                    <?php foreach (['applied', 'reviewing', 'interview', 'accepted', 'rejected', 'withdrawn'] as $st): ?>
                                                        <option value="<?= $st ?>" <?= (($app['status'] ?? '') === $st) ? 'selected' : '' ?>><?= $st ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold">Kaydet</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($applications)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Sistemde henüz başvuru kaydı bulunmuyor.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. HATA GÜNLÜKLERİ -->
            <div class="tab-pane fade" id="logs-pane" role="tabpanel">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0">📜 Son Sistem Hata Günlükleri (<code>logs/error.logs</code>)</h5>
                            <small class="text-muted">4xx ve 5xx Guardian katmanlarının kaydettiği son 15 olay.</small>
                        </div>
                        <a href="/api/health" target="_blank" class="btn btn-outline-success btn-sm">🟢 Sağlık Kontrolü</a>
                    </div>
                    <div class="log-box">
                        <?php if (!empty($errorLogs)): ?>
                            <?php foreach ($errorLogs as $line): ?>
                                <div class="mb-1 border-bottom border-secondary border-opacity-25 pb-1"><?= htmlspecialchars($line) ?></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-success">✅ Henüz kaydedilmiş 4xx/5xx hata kaydı bulunmuyor.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Admin Yeni Kullanıcı Modalı -->
    <div class="modal fade" id="adminAddUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="/admin/users" method="POST">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold">➕ Rol Atamalı Yeni Kullanıcı Ekle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Ad Soyad *</label>
                            <input type="text" name="name" class="form-control" required placeholder="Örn: Dr. Zeynep Kaya">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">E-Posta Adresi *</label>
                            <input type="email" name="email" class="form-control" required placeholder="ornek@iu.edu.tr">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Sistem Rolü</label>
                            <select name="role" class="form-select">
                                <option value="alumnus">🎓 alumnus</option>
                                <option value="student">📚 student</option>
                                <option value="faculty">🏫 faculty</option>
                                <option value="admin">👑 admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Hesap Durumu</label>
                            <select name="status" class="form-select">
                                <option value="active">🟢 active</option>
                                <option value="pending">⏳ pending</option>
                                <option value="suspended">🚫 suspended</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Bölüm</label>
                            <input type="text" name="department" class="form-control" value="Yönetim Bilişim Sistemleri">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Mezuniyet Yılı</label>
                            <input type="number" name="graduationYear" class="form-control" placeholder="2025">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Başlangıç Parolası (Opsiyonel, boşsa Alumni2026!)</label>
                            <input type="text" name="password" class="form-control" placeholder="Alumni2026!">
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-semibold">Kullanıcıyı Oluştur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-white border-top py-4 text-muted small text-center">
        <div class="container">
            Alumni Tracking System · Admin Control Center · İstanbul Üniversitesi YBS
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // URL hash'e göre ilgili sekmeyi (#jobs-tab, #apps-tab, #logs-tab) otomatik aç
        document.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash;
            if (hash) {
                const trigger = document.querySelector(`button[id="${hash.substring(1)}"]`);
                if (trigger) {
                    new bootstrap.Tab(trigger).show();
                }
            }
        });
    </script>
</body>
</html>
