<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkında · Alumni Tracking System</title>
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
        .about-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            padding: 40px;
        }
    </style>
</head>
<body>

    <!-- Ortak Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="/">
                <span style="font-size: 1.6rem;">🎓</span>
                <span style="color: var(--primary-navy); font-size: 1.1rem;">Alumni Tracking</span>
            </a>
            <div class="ms-auto d-flex gap-2 align-items-center flex-wrap">
                <a href="/" class="btn btn-outline-secondary btn-sm">🏠 Ana Sayfa</a>
                <a href="/api/swagger" class="btn btn-outline-primary btn-sm" target="_blank">Swagger UI ↗</a>
                <?php include __DIR__ . '/partials/auth_nav.php'; ?>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="about-card">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <span style="font-size: 2.5rem;">🏛️</span>
                        <div>
                            <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill mb-1">
                                YBSB3001 Web Programming
                            </span>
                            <h2 class="h3 fw-bold mb-0 text-dark">Proje Hakkında & Mimari Savunma</h2>
                        </div>
                    </div>

                    <p class="lead text-secondary mb-4">
                        <strong>Mezun Takip Sistemi (Alumni Tracking System)</strong>, İstanbul Üniversitesi İktisat Fakültesi Yönetim Bilişim Sistemleri Bölümü 2026-2027 Güz dönemi dönem projesi olarak geliştirilmiştir.
                    </p>

                    <!-- Bilgi Izgarası -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">Öğretim Üyesi</small>
                                <strong class="text-dark">Doç. Dr. Emre Akadal</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">Geliştirici & Öğrenci</small>
                                <strong class="text-primary">Battal Buğra Karataş (@tegmenbugo)</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">Mimari Model</small>
                                <strong class="text-dark">Saf (Native) PHP 8.3 OOP MVC</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block">Kalıcı Depolama</small>
                                <strong class="text-success">SQLite / MySQL (Volume Mount)</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Canlı Sözleşmeler -->
                    <h5 class="fw-bold text-dark mb-3">📜 Proje Yaşayan Sözleşmeleri (Living Contracts):</h5>
                    <div class="list-group mb-4">
                        <a href="https://github.com/tegmenbugo/alumni/blob/main/ddd.md" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h6 class="mb-1 fw-bold text-primary">🗄️ ddd.md — Data Design Documentation</h6>
                                <small class="text-muted">ERD diyagramı, DDL tabloları ve 1-N / N-M veri ilişkileri sözleşmesi.</small>
                            </div>
                            <span>↗</span>
                        </a>
                        <a href="https://github.com/tegmenbugo/alumni/blob/main/brand.md" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h6 class="mb-1 fw-bold text-primary">🎨 brand.md — Marka Kimliği & Renk Paleti</h6>
                                <small class="text-muted">İstanbul Üniversitesi kurumsal kimliği ve görsel dil standartları.</small>
                            </div>
                            <span>↗</span>
                        </a>
                        <a href="https://github.com/tegmenbugo/alumni/blob/main/DESIGN.md" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h6 class="mb-1 fw-bold text-primary">📐 DESIGN.md — VoltAgent Standardında AI Tasarım Sistemi</h6>
                                <small class="text-muted">Agent-readable CSS değişkenleri, tipografi ve bileşen spesifikasyonu.</small>
                            </div>
                            <span>↗</span>
                        </a>
                    </div>

                    <div class="text-center pt-3 border-top">
                        <a href="/" class="btn btn-primary px-4 fw-semibold">🏠 Ana Sayfaya Dön</a>
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
