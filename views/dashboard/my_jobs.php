<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📋 İlanlarım & Gelen Başvurular · Alumni Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #1e3a8a;
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
        }
        .page-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            padding: 36px 0 54px;
            border-radius: 0 0 24px 24px;
            margin-bottom: -28px;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }
    </style>
</head>
<body>

    <?php $navbarId = 'myJobsNav'; include __DIR__ . '/../partials/navbar.php'; ?>

    <header class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-2">
                        👤 <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)
                    </span>
                    <h1 class="h2 fw-bold mb-1">📋 Yayınladığım İlanlar & Aday Değerlendirme</h1>
                    <p class="mb-0 opacity-75 small">Kendi açtığınız ilanların yayın durumunu yönetin ve aday başvurularını değerlendirin.</p>
                </div>
                <a href="/jobs?new=1" class="btn btn-light btn-sm fw-bold text-primary px-3">+ Yeni İlan Yayınla</a>
            </div>
        </div>
    </header>

    <main class="container pb-5" style="position: relative; z-index: 2;">
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm">
                <?= htmlspecialchars($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 shadow-sm">
                <?= htmlspecialchars($flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($myJobs)): ?>
            <div class="d-flex flex-column gap-4">
                <?php foreach ($myJobs as $job): ?>
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 pb-3 border-bottom">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="fw-bold mb-0">
                                        <a href="/jobs/<?= (int)$job['id'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($job['title']) ?>
                                        </a>
                                    </h5>
                                    <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($job['employment_type'] ?? 'Tam Zamanlı') ?></span>
                                </div>
                                <div class="small text-muted">
                                    🏢 <?= htmlspecialchars($job['company_name']) ?> · 📍 <?= htmlspecialchars($job['location'] ?? 'İstanbul') ?> ·
                                    📥 <strong><?= count($job['applications']) ?></strong> Başvuru
                                </div>
                            </div>
                            <form action="/my-jobs/<?= (int)$job['id'] ?>/status" method="POST" class="d-flex gap-2 align-items-center">
                                <select name="status" class="form-select form-select-sm" style="width: 130px;">
                                    <option value="active" <?= (($job['status'] ?? '') === 'active') ? 'selected' : '' ?>>🟢 active</option>
                                    <option value="closed" <?= (($job['status'] ?? '') === 'closed') ? 'selected' : '' ?>>🔴 closed</option>
                                    <option value="draft" <?= (($job['status'] ?? '') === 'draft') ? 'selected' : '' ?>>📝 draft</option>
                                    <option value="archived" <?= (($job['status'] ?? '') === 'archived') ? 'selected' : '' ?>>📦 archived</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold">Durumu Kaydet</button>
                            </form>
                        </div>

                        <!-- Bu İlana Gelen Başvurular -->
                        <div class="pt-3">
                            <h6 class="small fw-bold text-muted text-uppercase mb-2">Gelen Başvurular</h6>
                            <?php if (!empty($job['applications'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Aday</th>
                                                <th>Ön Yazı</th>
                                                <th>Değerlendirme Aşaması</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($job['applications'] as $app): ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold"><?= htmlspecialchars($app['applicant_name'] ?? ('Aday #' . $app['applicant_user_id'])) ?></div>
                                                        <div class="small text-muted"><?= htmlspecialchars($app['applicant_email'] ?? '') ?></div>
                                                    </td>
                                                    <td class="small text-secondary"><?= htmlspecialchars($app['cover_letter'] ?? '—') ?></td>
                                                    <td>
                                                        <form action="/my-jobs/<?= (int)$job['id'] ?>/applications/<?= (int)$app['id'] ?>/status" method="POST" class="d-flex gap-2 align-items-center">
                                                            <select name="status" class="form-select form-select-sm" style="width: 145px;">
                                                                <?php foreach (['applied', 'reviewing', 'interview', 'accepted', 'rejected'] as $st): ?>
                                                                    <option value="<?= $st ?>" <?= (($app['status'] ?? '') === $st) ? 'selected' : '' ?>><?= $st ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-outline-success">Güncelle</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Bu ilana henüz başvuru gelmedi.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card card-custom p-5 text-center text-muted">
                <div class="fs-1 mb-2">💼</div>
                <h5>Henüz yayınladığınız bir iş ilanı bulunmuyor.</h5>
                <p class="small mb-3">Mezun ve akademisyen hesabınızla kariyer fırsatları oluşturabilirsiniz.</p>
                <div><a href="/jobs?new=1" class="btn btn-primary btn-sm px-4">+ İlk İlanımı Yayınla</a></div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-white border-top py-4 text-muted small text-center">
        <div class="container">
            Alumni Tracking System · İlanlarım · İstanbul Üniversitesi YBS
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
