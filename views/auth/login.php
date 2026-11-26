<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔑 Giriş Yap · Alumni Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1e3a8a;
            --color-accent: #2563eb;
            --color-gold: #f59e0b;
            --color-success: #10b981;
            --bg-app: #f8fafc;
            --bg-surface: #ffffff;
            --border-subtle: #e2e8f0;
        }
        body {
            background-color: var(--bg-app);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
        }
        .navbar-custom {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-subtle);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .login-wrapper {
            max-width: 980px;
            margin: 48px auto 60px;
        }
        .brand-panel {
            background: linear-gradient(145deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 40px;
            height: 100%;
            box-shadow: 0 12px 30px rgba(30, 58, 138, 0.18);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-panel {
            background: var(--bg-surface);
            border-radius: 20px;
            border: 1px solid var(--border-subtle);
            padding: 40px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }
        .form-control:focus {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }
        .demo-account {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .demo-account:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.1rem;
            color: #94a3b8;
            z-index: 5;
            background: none;
            border: none;
            padding: 0;
        }
        .password-toggle:hover { color: #475569; }
    </style>
</head>
<body>

    <!-- Dinamik Rol Tabanlı Üst Navbar -->
    <?php
        $navbarId = 'loginNav';
        $extraActions = '';
        include __DIR__ . '/../partials/navbar.php';
    ?>

    <main class="container login-wrapper">
        <div class="row g-4 align-items-stretch">
            <!-- Sol Panel: Marka & Demo Hesaplar -->
            <div class="col-lg-5">
                <div class="brand-panel">
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-3 align-self-start">
                        🏛️ İstanbul Üniversitesi YBS Ağı
                    </span>
                    <h1 class="h3 fw-bold mb-3">Sisteme Giriş Yapın</h1>
                    <p class="small opacity-90 mb-4">
                        Kariyer fırsatlarını keşfetmek, iş ilanlarına başvurmak veya kendi ilanlarınızı yönetmek için hesabınızla giriş yapın.
                    </p>

                    <h6 class="fw-bold text-uppercase small opacity-75 mb-3">🧪 Demo Hesaplar (Tek Tıkla Doldur)</h6>

                    <div class="demo-account mb-2" onclick="fillDemo('bugrakaratas@alumni.iu.edu.tr', 'Alumni2026!')">
                        <div class="fw-bold small">👑 Battal Buğra Karataş <span class="badge bg-danger bg-opacity-75 ms-1">admin</span></div>
                        <div class="small opacity-75">bugrakaratas@alumni.iu.edu.tr · Admin Paneli & tam yetki</div>
                    </div>
                    <div class="demo-account mb-2" onclick="fillDemo('elif.kaya@alumni.iu.edu.tr', 'Alumni2026!')">
                        <div class="fw-bold small">🎓 Elif Kaya <span class="badge bg-success bg-opacity-75 ms-1">alumnus</span></div>
                        <div class="small opacity-75">elif.kaya@alumni.iu.edu.tr · İlan açabilir & başvurabilir</div>
                    </div>
                    <div class="demo-account mb-2" onclick="fillDemo('ayse.yilmaz@alumni.iu.edu.tr', 'Alumni2026!')">
                        <div class="fw-bold small">📚 Ayşe Yılmaz <span class="badge bg-info bg-opacity-75 ms-1">student</span></div>
                        <div class="small opacity-75">ayse.yilmaz@alumni.iu.edu.tr · Sadece başvuru (ilan açamaz)</div>
                    </div>
                    <div class="demo-account mb-2" onclick="fillDemo('dr.aydin@iu.edu.tr', 'Alumni2026!')">
                        <div class="fw-bold small">🏫 Dr. Ahmet Aydın <span class="badge bg-warning text-dark bg-opacity-75 ms-1">faculty</span></div>
                        <div class="small opacity-75">dr.aydin@iu.edu.tr · İlan açabilir, akademik referans</div>
                    </div>

                    <div class="mt-4 pt-3 border-top border-light border-opacity-25">
                        <div class="small opacity-75">
                            💡 Hesabınız yok mu? <a href="/register" class="text-white fw-bold text-decoration-underline">Ücretsiz Kayıt Olun →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Panel: Giriş Formu -->
            <div class="col-lg-7">
                <div class="form-panel">
                    <div class="mb-4">
                        <h2 class="h4 fw-bold mb-1">🔑 Giriş Yap</h2>
                        <p class="text-muted small mb-0">E-posta adresiniz ve parolanız ile sisteme giriş yapın.</p>
                    </div>

                    <!-- Flash Mesajı: Hata -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3" role="alert">
                            🚫 <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Flash Mesajı: Başarı -->
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
                            ✅ <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Flash Mesajı: Zaten Oturum Açık -->
                    <?php if (!empty($currentUser)): ?>
                        <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 rounded-3">
                            <div class="small">
                                👤 Şu an <strong><?= htmlspecialchars($currentUser['name']) ?></strong>
                                (<span class="badge bg-primary"><?= htmlspecialchars($currentUser['role']) ?></span>) olarak oturumunuz açık.
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/jobs" class="btn btn-sm btn-primary fw-semibold">💼 İlanlar</a>
                                <a href="/logout" class="btn btn-sm btn-outline-danger fw-semibold">🚪 Çıkış Yap</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Flash Mesajı: Session Redirect Uyarısı -->
                    <?php
                        if (session_status() === PHP_SESSION_NONE) { @session_start(); }
                        $flashError = $_SESSION['flash_error'] ?? null;
                        if ($flashError) { unset($_SESSION['flash_error']); }
                    ?>
                    <?php if ($flashError): ?>
                        <div class="alert alert-warning alert-dismissible fade show mb-4 rounded-3" role="alert">
                            <?= htmlspecialchars($flashError) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/login" id="loginForm">
                        <!-- E-Posta -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">📧 E-Posta Adresi</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg"
                                   placeholder="ornek@alumni.iu.edu.tr"
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                   required autofocus>
                        </div>

                        <!-- Parola -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">🔒 Parola</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="password" class="form-control form-control-lg"
                                       placeholder="Parolanızı giriniz"
                                       required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword()" title="Parolayı Göster/Gizle">
                                    👁️
                                </button>
                            </div>
                        </div>

                        <!-- Hidden: Redirect Target -->
                        <?php $from = $_GET['from'] ?? ''; ?>
                        <?php if ($from): ?>
                            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($from) ?>">
                        <?php endif; ?>

                        <!-- Giriş Butonu -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold mb-3" id="loginBtn">
                            🔑 Giriş Yap
                        </button>

                        <!-- Alt Linkler -->
                        <div class="text-center">
                            <span class="text-muted small">Hesabınız yok mu?</span>
                            <a href="/register" class="fw-semibold text-decoration-none small ms-1">Kayıt Ol →</a>
                        </div>
                    </form>

                    <!-- Rol Yetki Bilgilendirmesi -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold small text-muted mb-3">🔐 Giriş Sonrası Rol Yetkileriniz (ddd.md Bölüm 8)</h6>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="border rounded-3 p-3 bg-light h-100">
                                    <div class="fw-bold small mb-1">🎓 Mezun (alumnus)</div>
                                    <div class="small text-muted">İş ilanı yayınlayabilir, ilanlara başvurabilir, kendi profilini düzenleyebilir.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded-3 p-3 bg-light h-100">
                                    <div class="fw-bold small mb-1">📚 Öğrenci (student)</div>
                                    <div class="small text-muted">İlanlara başvurabilir, mezun ağını inceleyebilir. İlan <strong>açamaz</strong>.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 text-muted small text-center">
        <div class="container">
            Alumni Tracking System · İstanbul Üniversitesi İktisat Fakültesi YBS · 26-27 Web Programming
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Demo hesap doldurma
        function fillDemo(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('email').focus();
        }

        // Parola göster/gizle
        function togglePassword() {
            const pw = document.getElementById('password');
            pw.type = pw.type === 'password' ? 'text' : 'password';
        }

        // Form gönderildiğinde butonu devre dışı bırak
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '⏳ Giriş yapılıyor...';
        });
    </script>
</body>
</html>
