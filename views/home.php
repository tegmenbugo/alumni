<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎓 Alumni Tracking System · İstanbul Üniversitesi Mezun Takip Portalı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-blue: #1e3a8a;
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border-bottom: 1px solid var(--card-border);
        }
        .hero-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 80px 0 90px;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.15);
        }
        .hero-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            color: #ffffff;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .feature-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 30px;
            border: 1px solid var(--card-border);
            height: 100%;
            transition: all 0.25s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #93c5fd;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.08);
        }
        .feature-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 20px;
        }
        .dev-console {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid var(--card-border);
            padding: 25px;
        }
        .endpoint-row {
            font-family: monospace;
            font-size: 0.85rem;
        }
        footer {
            background: #ffffff;
            border-top: 1px solid var(--card-border);
            padding: 40px 0;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- 1. DİNAMİK ROL TABANLI ÜST NAVİGASYON (NAVBAR) -->
    <?php
        $navbarId = 'mainNav';
        $extraActions = '';
        include __DIR__ . '/partials/navbar.php';
    ?>

    <!-- 2. HERO SECTION (KARŞILAMA ALANI) -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="hero-badge mb-3">
                        🏛️ 26-27 Web Programming Dönem Projesi · Doç. Dr. Emre Akadal
                    </div>
                    <h1 class="display-4 fw-bold mb-3">
                        İstanbul Üniversitesi Mezunları <br class="d-none d-md-block">Tek Bir Çatıda Buluşuyor
                    </h1>
                    <p class="lead opacity-90 mb-4 px-md-5">
                        Kariyer yolculuğunu paylaş, mezunlar ağına katıl, staj ve iş fırsatlarını keşfet. 
                        Öğrenciler ile sektör profesyonelleri arasındaki en güçlü köprü.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="/register" class="btn btn-light btn-lg px-4 py-2 fw-bold text-primary shadow-sm">
                            📝 Hemen Kayıt Ol
                        </a>
                        <a href="/users" class="btn btn-outline-light btn-lg px-4 py-2 fw-semibold">
                            👥 Mezunları Keşfet
                        </a>
                        <a href="/jobs" class="btn btn-outline-light btn-lg px-4 py-2 fw-semibold">
                            💼 Kariyer Fırsatları ➔
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. CANLI İSTATİSTİK SAYAÇLARI (STATS) -->
    <section class="container" style="margin-top: -45px;">
        <div class="row g-3 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="h2 fw-bold text-primary mb-1">20+</div>
                    <div class="text-muted small fw-semibold">Kayıtlı Mezun</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="h2 fw-bold text-success mb-1">6+</div>
                    <div class="text-muted small fw-semibold">Aktif İş & Staj İlanı</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="h2 fw-bold text-warning mb-1">1950-2026</div>
                    <div class="text-muted small fw-semibold">Mezuniyet Yelpazesi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="h2 fw-bold text-info mb-1">%100</div>
                    <div class="text-muted small fw-semibold">Docker & SQLite Kalıcı</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. TEMEL SİSTEM ÖZELLİKLERİ (FEATURES) -->
    <section class="container py-5 mt-3">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase tracking-wider">Platform Modülleri</h6>
            <h2 class="fw-bold">Alumni Tracking Sistemi Neler Sunar?</h2>
            <p class="text-muted">MVC mimarisi ve ilişkisel veritabanı altyapısıyla geliştirilen temel işlevler</p>
        </div>

        <div class="row g-4">
            <!-- Kart 1: Mezun Veritabanı -->
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon bg-primary-subtle text-primary">
                        👥
                    </div>
                    <h5 class="fw-bold mb-2">Mezun Portföyü & Ağ</h5>
                    <p class="text-muted small mb-4">
                        İstanbul Üniversitesi mezunlarının çalıştığı şirketler, departmanlar, unvanlar ve mezuniyet yıllarına göre tam listesi ve profil detayları.
                    </p>
                    <a href="/users" class="btn btn-sm btn-outline-primary fw-semibold">Mezunlar Sayfası ➔</a>
                </div>
            </div>

            <!-- Kart 2: Kariyer & İlanlar -->
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon bg-success-subtle text-success">
                        💼
                    </div>
                    <h5 class="fw-bold mb-2">İş & Staj İlanları</h5>
                    <p class="text-muted small mb-4">
                        Mezunların ve şirketlerin açtığı hibrit, uzaktan veya ofisten stajyer ve uzman pozisyonları. Maaş aralığı ve deneyim filtreleme.
                    </p>
                    <a href="/jobs" class="btn btn-sm btn-outline-success fw-semibold">İlanları İncele ➔</a>
                </div>
            </div>

            <!-- Kart 3: REST API & Güvenlik -->
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon bg-info-subtle text-info">
                        🛡️
                    </div>
                    <h5 class="fw-bold mb-2">Hata Bekçileri & API</h5>
                    <p class="text-muted small mb-4">
                        Swagger UI ile tam senkronize OpenAPI 3.0 dokümantasyonu, Logger Middleware, 404 Guardian ve 5xx Guardian koruma kalkanları.
                    </p>
                    <a href="/api/swagger" class="btn btn-sm btn-outline-info fw-semibold" target="_blank">Swagger Dokümanı ↗</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. GELİŞTİRİCİ & SİSTEM KONSOLU (DEVELOPER DRAWER) -->
    <section class="container pb-5">
        <div class="dev-console shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1">🛠️ Geliştirici & Mimari Kontrol Paneli</h5>
                    <small class="text-muted">Saf PHP 8.3 Nesne Yönelimli MVC Omurgası ve Canlı Uç Noktalar</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="/api/health" class="btn btn-sm btn-outline-success" target="_blank">Sağlık Kontrolü (200 OK)</a>
                    <a href="/api/migrations" class="btn btn-sm btn-outline-primary" target="_blank">Migrasyon Tablosu</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle border mb-0">
                    <thead class="table-light">
                        <tr class="endpoint-row">
                            <th>Tür</th>
                            <th>Metod</th>
                            <th>Uç Nokta</th>
                            <th>İşlev & Denetleyici</th>
                            <th class="text-end">Test</th>
                        </tr>
                    </thead>
                    <tbody class="endpoint-row">
                        <tr>
                            <td><span class="badge bg-success">Web UI</span></td>
                            <td><span class="badge bg-success">GET</span></td>
                            <td><code>/users</code></td>
                            <td>Mezun listesi ve CRUD HTML arayüzü</td>
                            <td class="text-end"><a href="/users" class="btn btn-xs btn-outline-secondary py-0">Git</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success">Web UI</span></td>
                            <td><span class="badge bg-success">GET</span></td>
                            <td><code>/jobs</code></td>
                            <td>İş ve staj portalı arayüzü (Hafta 06)</td>
                            <td class="text-end"><a href="/jobs" class="btn btn-xs btn-outline-secondary py-0">Git</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-primary">REST API</span></td>
                            <td><span class="badge bg-success">GET</span></td>
                            <td><code>/api/jobs</code></td>
                            <td>Doğrudan iş ilanları JSON servisi</td>
                            <td class="text-end"><a href="/api/jobs" class="btn btn-xs btn-outline-secondary py-0" target="_blank">JSON</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-primary">REST API</span></td>
                            <td><span class="badge bg-info text-dark">NESTED</span></td>
                            <td><code>/api/jobs/1/applications</code></td>
                            <td>İlana gelen başvurular (Nested Route)</td>
                            <td class="text-end"><a href="/api/jobs/1/applications" class="btn btn-xs btn-outline-secondary py-0" target="_blank">JSON</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info text-dark">Hafta 09</span></td>
                            <td><span class="badge bg-success">GET / POST</span></td>
                            <td><code>/register</code></td>
                            <td><code>AuthController::showRegister() / register()</code> (Kayıt & RBAC)</td>
                            <td class="text-end"><a href="/register" class="btn btn-xs btn-outline-primary py-0">Kayıt Ol</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">Admin Panel</span></td>
                            <td><span class="badge bg-success">GET / POST</span></td>
                            <td><code>/admin</code></td>
                            <td><code>AdminController::index()</code> (Yönetici Paneli & Dinamik Menü)</td>
                            <td class="text-end"><a href="/admin" class="btn btn-xs btn-outline-danger py-0">Panel</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info text-dark">Hafta 09</span></td>
                            <td><span class="badge bg-success">GET / POST</span></td>
                            <td><code>/login</code> & <code>/logout</code></td>
                            <td><code>AuthController::login() / logout()</code> (Oturum & RBAC)</td>
                            <td class="text-end"><a href="/login" class="btn btn-xs btn-outline-primary py-0">Giriş Yap</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info text-dark">Hafta 09</span></td>
                            <td><span class="badge bg-primary">POST API</span></td>
                            <td><code>/api/auth/login</code></td>
                            <td><code>AuthController::apiLogin()</code> & <code>/api/auth/me</code></td>
                            <td class="text-end"><a href="/api/swagger" class="btn btn-xs btn-outline-info py-0" target="_blank">Swagger</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info text-dark">Hafta 09</span></td>
                            <td><span class="badge bg-primary">POST API</span></td>
                            <td><code>/api/auth/register</code></td>
                            <td><code>AuthController::apiRegister()</code> (JSON Kayıt & Rol Koruması)</td>
                            <td class="text-end"><a href="/api/swagger" class="btn btn-xs btn-outline-info py-0" target="_blank">Swagger</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning text-dark">Hafta 07</span></td>
                            <td><span class="badge bg-danger">404 Test</span></td>
                            <td><code>/gecersiz-rota</code></td>
                            <td>404 Guardian & Generic 404 Redirect Testi</td>
                            <td class="text-end"><a href="/gecersiz-rota" class="btn btn-xs btn-outline-danger py-0">Dene</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning text-dark">Hafta 07</span></td>
                            <td><span class="badge bg-danger">500 Test</span></td>
                            <td><code>/api/test-500</code></td>
                            <td>5xx Guardian & logs/error.logs Testi</td>
                            <td class="text-end"><a href="/api/test-500" class="btn btn-xs btn-outline-danger py-0" target="_blank">Dene</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 6. ALT BİLGİ (FOOTER) -->
    <footer>
        <div class="container text-center text-md-start">
            <div class="row gy-3">
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-1">🎓 Alumni Tracking System</h6>
                    <small>İstanbul Üniversitesi İktisat Fakültesi Yönetim Bilişim Sistemleri Bölümü</small>
                    <div class="small mt-1">Ders: <strong>YBSB3001 Web Programming</strong> · Doç. Dr. Emre Akadal</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Geliştirici: <strong>Battal Buğra Karataş</strong> (<a href="https://github.com/tegmenbugo" target="_blank" class="text-decoration-none">@tegmenbugo</a>)</small>
                    <div class="small mt-1">
                        <a href="https://github.com/tegmenbugo/alumni" target="_blank" class="text-decoration-none me-3">GitHub Repo ↗</a>
                        <a href="/api/swagger" target="_blank" class="text-decoration-none me-3">Swagger UI</a>
                        <a href="/about" class="text-decoration-none">Hakkında</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
