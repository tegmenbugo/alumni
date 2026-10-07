<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Tracking System - MVC Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .hero-card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .endpoint-badge { font-family: monospace; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Hero Card -->
                <div class="card hero-card p-4 p-md-5 mb-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                            🎓 YBSB3001 · Web Programming
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                            🏛️ MVC Architecture Active (Week 04)
                        </span>
                    </div>
                    <h1 class="display-6 fw-bold text-dark mb-2">Alumni Tracking System</h1>
                    <p class="lead text-muted mb-4">
                        Mezun Takip Sistemi — <strong>Model-View-Controller (MVC)</strong> mimarisi ile yeniden yapılandırılmış kontrol paneli.
                    </p>

                    <!-- MVC Banner -->
                    <div class="alert alert-success d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" role="alert">
                        <div>
                            <h6 class="fw-bold mb-1">🏛️ Hafta 04: Saf PHP MVC Mimarisi Devrede</h6>
                            <small class="text-muted">Tüm rotalar merkezi <strong>Router</strong> üzerinden <strong>Controllers</strong>, <strong>Models</strong> ve <strong>Views</strong> katmanlarına ayrıştırıldı.</small>
                        </div>
                        <a href="/api/swagger" class="btn btn-success fw-semibold px-3 py-2" target="_blank">
                            Swagger UI ➔
                        </a>
                    </div>

                    <!-- Endpoints Table -->
                    <h5 class="fw-bold mb-3">📋 Aktif MVC Rotaları ve API Uç Noktaları</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>Katman</th>
                                    <th>Metod</th>
                                    <th>Uç Nokta (Route)</th>
                                    <th>İlgili Controller & Metod</th>
                                    <th class="text-center">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-info text-dark">Swagger</span></td>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><span class="endpoint-badge">/api/swagger</span></td>
                                    <td><code>ApiController::swagger()</code></td>
                                    <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-primary" target="_blank">Aç ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">API</span></td>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><span class="endpoint-badge">/api/health</span></td>
                                    <td><code>ApiController::health()</code></td>
                                    <td class="text-center"><a href="/api/health" class="btn btn-sm btn-outline-primary" target="_blank">Aç ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">API</span></td>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><span class="endpoint-badge">/api/users</span></td>
                                    <td><code>UserController::index()</code></td>
                                    <td class="text-center"><a href="/api/users" class="btn btn-sm btn-outline-primary" target="_blank">Aç ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">API</span></td>
                                    <td><span class="badge bg-info text-dark">POST</span></td>
                                    <td><span class="endpoint-badge">/api/users</span></td>
                                    <td><code>UserController::store()</code></td>
                                    <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-info" target="_blank">Swagger'da Dene ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">API</span></td>
                                    <td><span class="badge bg-warning text-dark">PUT / PATCH</span></td>
                                    <td><span class="endpoint-badge">/api/users/{id}</span></td>
                                    <td><code>UserController::update() / patch()</code></td>
                                    <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-warning" target="_blank">Swagger'da Dene ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">API</span></td>
                                    <td><span class="badge bg-danger">DELETE</span></td>
                                    <td><span class="endpoint-badge">/api/users/{id}</span></td>
                                    <td><code>UserController::destroy()</code></td>
                                    <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-danger" target="_blank">Swagger'da Dene ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Web</span></td>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><span class="endpoint-badge">/hello</span></td>
                                    <td><code>HomeController::hello()</code></td>
                                    <td class="text-center"><a href="/hello" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Web</span></td>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><span class="endpoint-badge">/hello/Buğra</span></td>
                                    <td><code>HomeController::helloName()</code></td>
                                    <td class="text-center"><a href="/hello/Bu%C4%9Fra" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Web</span></td>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><span class="endpoint-badge">/sum/15/27</span></td>
                                    <td><code>HomeController::sum()</code></td>
                                    <td class="text-center"><a href="/sum/15/27" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Web</span></td>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><span class="endpoint-badge">/about</span></td>
                                    <td><code>HomeController::about()</code></td>
                                    <td class="text-center"><a href="/about" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                        <span>Geliştirici: <strong>Buğra Karataş</strong> · GitHub: <a href="https://github.com/tegmenbugo/alumni" target="_blank" class="text-decoration-none">tegmenbugo/alumni</a></span>
                        <a href="/about" class="text-decoration-none">Hakkında Sayfası ➔</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
