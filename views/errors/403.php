<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Yetkisiz Erişim · Alumni Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #0A2540;
            --accent-cyan: #00D4B2;
            --danger-coral: #ef4444;
        }
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding: 20px;
        }
        .error-card {
            max-width: 650px;
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .error-header {
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .error-code {
            font-size: 5.5rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -2px;
            text-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .error-body {
            padding: 35px 30px;
        }
        .role-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .role-student { background: #e0f2fe; color: #0369a1; }
        .role-alumnus { background: #dcfce7; color: #15803d; }
        .role-faculty { background: #fef3c7; color: #b45309; }
        .role-admin { background: #fce7f3; color: #be185d; }
        .role-guest { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>
    <div class="error-card">
        <!-- Başlık & İkon Alanı -->
        <div class="error-header">
            <div class="error-code">403</div>
            <h2 class="h4 fw-bold mt-2 mb-0">🚫 Yetkisiz Erişim / Erişim Engellendi</h2>
            <p class="small opacity-75 mb-0 mt-1">AuthMiddleware · RBAC Güvenlik Katmanı</p>
        </div>

        <!-- Gövde & Açıklama Alanı -->
        <div class="error-body">
            <div class="alert alert-danger d-flex align-items-center gap-3 mb-4 rounded-3 border-danger-subtle">
                <span style="font-size: 1.8rem;">🛡️</span>
                <div>
                    <div class="fw-bold">Bu İşlem İçin Yetkiniz Bulunmamaktadır</div>
                    <div class="small opacity-90 mt-1">
                        <?= htmlspecialchars($reason ?? 'Bu kaynağa veya özelliğe erişim izniniz bulunmuyor.') ?>
                    </div>
                </div>
            </div>

            <!-- Rol Karşılaştırması -->
            <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <span class="text-muted small fw-semibold">Mevcut Rolünüz:</span>
                    <?php 
                        $curr = $currentRole ?? 'guest';
                        $class = match($curr) {
                            'student' => 'role-student',
                            'alumnus' => 'role-alumnus',
                            'faculty' => 'role-faculty',
                            'admin'   => 'role-admin',
                            default   => 'role-guest'
                        };
                    ?>
                    <span class="role-pill <?= $class ?>">
                        👤 <?= htmlspecialchars(strtoupper($curr)) ?>
                    </span>
                </div>

                <?php if (!empty($allowedRoles)): ?>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-muted small fw-semibold">Gerekli Yetkili Roller:</span>
                        <div class="d-flex gap-1 flex-wrap">
                            <?php foreach ($allowedRoles as $r): ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($r) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Bilgilendirme Notu -->
            <div class="text-muted small mb-4">
                💡 <strong>Örnek Yetki Kuralı (ddd.md Bölüm 8):</strong> Öğrenci (student) rolündeki kullanıcılar sistemdeki kariyer fırsatlarını inceleyip başvurabilirler; ancak kendi adlarına yeni iş veya staj ilanı yayınlayamazlar. İlan yayınlama yetkisi Mezun (alumnus), Akademisyen (faculty) ve Sistem Yöneticilerine (admin) aittir.
            </div>

            <!-- Navigasyon Butonları -->
            <div class="d-flex gap-2 flex-wrap justify-content-between pt-2 border-top">
                <a href="/jobs" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                    💼 İlanlar Portalı
                </a>
                <div class="d-flex gap-2">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="/logout" class="btn btn-outline-secondary btn-sm px-3">
                            🚪 Farklı Hesapla Giriş Yap
                        </a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary btn-sm px-3 fw-semibold">
                            🔑 Giriş Yap
                        </a>
                    <?php endif; ?>
                    <a href="/" class="btn btn-dark btn-sm px-3 fw-semibold">
                        🏠 Ana Sayfa
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
