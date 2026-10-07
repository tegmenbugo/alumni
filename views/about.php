<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkında - Alumni Tracking System (MVC)</title>
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
                <div class="card hero-card p-4 p-md-5 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2 rounded-pill">
                            ℹ️ MVC View: about.php
                        </span>
                        <a href="/" class="btn btn-sm btn-outline-secondary">← Ana Sayfaya Dön</a>
                    </div>
                    <h1 class="display-6 fw-bold text-dark mb-3">Hakkında (About)</h1>
                    <p class="lead text-muted mb-4">
                        Bu sayfa, <strong>Mezun Takip Sistemi (Alumni Tracking System)</strong> projesi için MVC mimarisi View katmanında (<code>views/about.php</code>) hazırlanmıştır.
                    </p>
                    
                    <div class="card bg-light border-0 p-4 rounded-3 mb-4">
                        <h5 class="fw-bold mb-3">📌 Proje Bilgileri:</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><strong>Ders:</strong> YBSB3001 · Web Programming (5. Yarıyıl)</li>
                            <li class="mb-2"><strong>Öğretim Üyesi:</strong> Doç. Dr. Emre Akadal</li>
                            <li class="mb-2"><strong>Geliştirici:</strong> Buğra Karataş</li>
                            <li class="mb-2"><strong>Mimari:</strong> Model-View-Controller (Native PHP 8.3 OOP)</li>
                            <li><strong>API Dokümantasyonu:</strong> <a href="/api/swagger" class="text-decoration-none">Swagger UI (/api/swagger)</a></li>
                        </ul>
                    </div>

                    <div class="text-center">
                        <a href="/" class="btn btn-primary px-4 py-2">Ana Sayfayı Ziyaret Et ➔</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
