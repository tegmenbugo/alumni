<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📥 Başvurularım · Alumni Tracking System</title>
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

    <?php $navbarId = 'myAppsNav'; include __DIR__ . '/../partials/navbar.php'; ?>

    <header class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-2">
                        👤 <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)
                    </span>
                    <h1 class="h2 fw-bold mb-1">📥 İş & Staj Başvurularım</h1>
                    <p class="mb-0 opacity-75 small">Yaptığınız tüm kariyer başvurularının güncel değerlendirme aşamalarını buradan takip edebilirsiniz.</p>
                </div>
                <a href="/jobs" class="btn btn-light btn-sm fw-bold text-primary px-3">💼 Yeni İlanları Keşfet</a>
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

        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th class="ps-4">İlan Başlığı & Şirket</th>
                            <th>Lokasyon & Şekil</th>
                            <th>Başvuru Tarihi</th>
                            <th>Durum</th>
                            <th class="text-end pe-4">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <?php
                                $st = $app['status'] ?? 'applied';
                                $stClass = match ($st) {
                                    'accepted'  => 'bg-success-subtle text-success',
                                    'interview' => 'bg-primary-subtle text-primary',
                                    'reviewing' => 'bg-warning-subtle text-warning-emphasis',
                                    'rejected', 'withdrawn' => 'bg-danger-subtle text-danger',
                                    default     => 'bg-info-subtle text-info-emphasis',
                                };
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <a href="/jobs/<?= (int)$app['job_posting_id'] ?>" class="fw-bold text-decoration-none text-dark">
                                        <?= htmlspecialchars($app['job_title'] ?? ('İlan #' . $app['job_posting_id'])) ?>
                                    </a>
                                    <div class="small text-muted">🏢 <?= htmlspecialchars($app['job_company'] ?? '—') ?></div>
                                    <?php if (!empty($app['cover_letter'])): ?>
                                        <div class="small text-secondary fst-italic mt-1">"<?= htmlspecialchars($app['cover_letter']) ?>"</div>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <div>📍 <?= htmlspecialchars($app['job_location'] ?? '—') ?></div>
                                    <div class="text-muted"><?= htmlspecialchars($app['job_type'] ?? '—') ?></div>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($app['applied_at'] ?? '—') ?></td>
                                <td><span class="badge <?= $stClass ?> px-3 py-2"><?= htmlspecialchars($st) ?></span></td>
                                <td class="text-end pe-4">
                                    <?php if ($st !== 'withdrawn' && $st !== 'rejected' && $st !== 'accepted'): ?>
                                        <form action="/my-applications/<?= (int)$app['id'] ?>/withdraw" method="POST" onsubmit="return confirm('Bu başvuruyu geri çekmek istediğinize emin misiniz?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Geri Çek</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="/jobs/<?= (int)$app['job_posting_id'] ?>" class="btn btn-sm btn-outline-secondary">İlanı Gör</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="fs-2 mb-2">📭</div>
                                    Henüz hiçbir iş veya staj ilanına başvurmadınız.
                                    <div class="mt-2"><a href="/jobs" class="btn btn-sm btn-primary">İlanlara Göz At</a></div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-white border-top py-4 text-muted small text-center">
        <div class="container">
            Alumni Tracking System · Başvurularım · İstanbul Üniversitesi YBS
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
