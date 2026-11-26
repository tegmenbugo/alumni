<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📝 Kayıt Ol · Alumni Tracking System</title>
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
        .auth-wrapper {
            max-width: 1080px;
            margin: 36px auto 60px;
        }
        .brand-panel {
            background: linear-gradient(145deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 36px;
            height: 100%;
            box-shadow: 0 12px 30px rgba(30, 58, 138, 0.18);
        }
        .form-panel {
            background: var(--bg-surface);
            border-radius: 20px;
            border: 1px solid var(--border-subtle);
            padding: 36px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }
        .role-option {
            cursor: pointer;
            border: 2px solid var(--border-subtle);
            border-radius: 12px;
            padding: 14px;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        .role-option:hover {
            border-color: #93c5fd;
            background: #f8fafc;
        }
        .btn-check:checked + .role-option {
            border-color: var(--color-accent);
            background: #eff6ff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }
        .rbac-card {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            padding: 16px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }
    </style>
</head>
<body>

    <!-- Ortak Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="/">
                <span style="font-size: 1.6rem;">🎓</span>
                <span style="color: var(--color-primary); font-size: 1.1rem;">Alumni Tracking</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#regNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="regNav">
                <ul class="navbar-nav me-auto ms-3">
                    <li class="nav-item"><a class="nav-link" href="/">🏠 Ana Sayfa</a></li>
                    <li class="nav-item"><a class="nav-link" href="/users">👥 Mezunlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/jobs">💼 Kariyer & İlanlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/api/swagger" target="_blank">📚 Swagger UI</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">ℹ️ Hakkında</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <?php include __DIR__ . '/../partials/auth_nav.php'; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="container auth-wrapper">
        <div class="row g-4 align-items-stretch">
            <!-- Sol Panel: Marka & Rol Yetkilendirme Bilgilendirmesi (ddd.md Bölüm 8) -->
            <div class="col-lg-5">
                <div class="brand-panel d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-3">
                            🏛️ İstanbul Üniversitesi YBS Ağı
                        </span>
                        <h1 class="h3 fw-bold mb-3">Mezun ve Öğrenci Ağına Katılın</h1>
                        <p class="small opacity-90 mb-4">
                            Kariyer fırsatlarına erişmek, staj ve iş ilanlarına başvurmak ya da İÜ mezunlarıyla deneyim paylaşmak için hesabınızı oluşturun.
                        </p>

                        <h6 class="fw-bold text-uppercase small opacity-75 mb-3">🔐 Sistem Rol Yetkileri (RBAC)</h6>

                        <div class="rbac-card mb-3">
                            <div class="fw-bold mb-1">🎓 Mezun (Alumnus)</div>
                            <ul class="small mb-0 ps-3 opacity-90">
                                <li>Kendi profilini ve şirket bilgisini yönetebilir</li>
                                <li>Şirketi adına <strong>iş / staj ilanı yayınlayabilir</strong></li>
                                <li>Açtığı ilanlara gelen başvuruları değerlendirir</li>
                                <li>Diğer kariyer fırsatlarına başvurabilir</li>
                            </ul>
                        </div>

                        <div class="rbac-card mb-3">
                            <div class="fw-bold mb-1">📚 Öğrenci (Student)</div>
                            <ul class="small mb-0 ps-3 opacity-90">
                                <li>Mezun rehberini ve kariyer profillerini inceler</li>
                                <li>Aktif iş ve staj ilanlarına <strong>başvurabilir</strong></li>
                                <li>Yaptığı başvuruların durumunu takip edebilir</li>
                                <li><em>(Güvenlik kuralı gereği ilan yayınlayamaz)</em></li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-3 border-top border-white border-opacity-25 small opacity-75">
                        🛡️ Parolalar Bcrypt (<code>password_hash</code>) ile şifrelenir. Yetki yükseltme (admin/faculty) self-register üzerinden kapalıdır.
                    </div>
                </div>
            </div>

            <!-- Sağ Panel: Kayıt Formu / Başarı Ekranı -->
            <div class="col-lg-7">
                <div class="form-panel h-100">
                    <?php if (!empty($registeredUser)): ?>
                        <!-- Kayıt Başarılı Durumu -->
                        <div class="text-center py-3">
                            <div class="mb-3" style="font-size: 3.5rem;">🎉</div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill mb-3">
                                HTTP 201 Created · Kayıt Tamamlandı
                            </span>
                            <h2 class="h3 fw-bold text-dark mb-2">Aramıza Hoş Geldiniz!</h2>
                            <p class="text-muted mb-4"><?= htmlspecialchars($message ?? '') ?></p>

                            <div class="card bg-light border rounded-4 p-4 text-start mb-4">
                                <h6 class="fw-bold text-secondary mb-3">📋 Oluşturulan Hesap Özeti</h6>
                                <div class="row g-2 small">
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block">Kullanıcı ID:</span>
                                        <strong>#<?= htmlspecialchars((string)$registeredUser['id']) ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block">Atanan Rol & Durum:</span>
                                        <span class="badge bg-primary"><?= htmlspecialchars($registeredUser['role']) ?></span>
                                        <span class="badge bg-success"><?= htmlspecialchars($registeredUser['status']) ?></span>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <span class="text-muted d-block">Ad Soyad:</span>
                                        <strong><?= htmlspecialchars($registeredUser['name']) ?></strong>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <span class="text-muted d-block">E-Posta:</span>
                                        <strong><?= htmlspecialchars($registeredUser['email']) ?></strong>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <span class="text-muted d-block">Bölüm:</span>
                                        <strong><?= htmlspecialchars($registeredUser['department'] ?? '-') ?></strong>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <span class="text-muted d-block">Mezuniyet Yılı:</span>
                                        <strong><?= htmlspecialchars((string)($registeredUser['graduationYear'] ?? '-')) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="/users/<?= (int)$registeredUser['id'] ?>" class="btn btn-primary px-4 fw-semibold">
                                    👤 Profilime Git
                                </a>
                                <a href="/jobs" class="btn btn-outline-success px-4 fw-semibold">
                                    💼 İş İlanlarını Gör
                                </a>
                                <a href="/users" class="btn btn-outline-secondary px-3">
                                    👥 Mezun Rehberi
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Kayıt Formu -->
                        <div class="mb-4 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill mb-2">
                                        📝 POST /register · AuthController
                                    </span>
                                    <h2 class="h4 fw-bold mb-0">Yeni Hesap Oluştur (Register)</h2>
                                </div>
                                <a href="/users" class="btn btn-sm btn-outline-secondary">👥 Mezun Listesi</a>
                            </div>
                        </div>

                        <?php if (!empty($message) && !empty($errors)): ?>
                            <div class="alert alert-danger border-danger-subtle mb-4" role="alert">
                                <div class="fw-bold mb-1"><?= htmlspecialchars($message) ?></div>
                                <ul class="mb-0 small ps-3">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="/register" method="POST" class="row g-3" novalidate>
                            <!-- Rol Seçimi (Görsel Kartlar) -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small">
                                    Sistemdeki Rolünüz <span class="text-danger">*</span>
                                </label>
                                <?php $selectedRole = $old['role'] ?? 'alumnus'; ?>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role" id="roleAlumnus" value="alumnus"
                                               <?= ($selectedRole === 'alumnus') ? 'checked' : '' ?> onchange="toggleGradLabel()">
                                        <label class="role-option d-block w-100" for="roleAlumnus">
                                            <div class="fw-bold text-dark">🎓 Mezun (Alumnus)</div>
                                            <small class="text-muted d-block">İlan açabilir & başvurabilir</small>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role" id="roleStudent" value="student"
                                               <?= ($selectedRole === 'student') ? 'checked' : '' ?> onchange="toggleGradLabel()">
                                        <label class="role-option d-block w-100" for="roleStudent">
                                            <div class="fw-bold text-dark">📚 Öğrenci (Student)</div>
                                            <small class="text-muted d-block">Staj & iş ilanlarına başvurur</small>
                                        </label>
                                    </div>
                                </div>
                                <?php if (!empty($errors['role'])): ?>
                                    <div class="text-danger small mt-1"><?= htmlspecialchars($errors['role']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Ad Soyad -->
                            <div class="col-md-6">
                                <label for="regName" class="form-label fw-semibold small">
                                    Ad Soyad <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="regName" name="name"
                                       class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                                       placeholder="Örn: Zeynep Demir"
                                       value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                                <?php if (!empty($errors['name'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- E-Posta -->
                            <div class="col-md-6">
                                <label for="regEmail" class="form-label fw-semibold small">
                                    E-Posta Adresi <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="regEmail" name="email"
                                       class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                       placeholder="ad.soyad@alumni.iu.edu.tr"
                                       value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                                <?php if (!empty($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Bölüm -->
                            <div class="col-md-6">
                                <label for="regDept" class="form-label fw-semibold small">
                                    Akademik Bölüm <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="regDept" name="department" list="deptList"
                                       class="form-control <?= !empty($errors['department']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($old['department'] ?? 'Yönetim Bilişim Sistemleri') ?>" required>
                                <datalist id="deptList">
                                    <option value="Yönetim Bilişim Sistemleri">
                                    <option value="İktisat">
                                    <option value="İşletme">
                                    <option value="Ekonometri">
                                    <option value="Maliye">
                                    <option value="Bilgisayar Mühendisliği">
                                </datalist>
                                <?php if (!empty($errors['department'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['department']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Mezuniyet Yılı -->
                            <div class="col-md-6">
                                <label for="regGradYear" id="gradYearLabel" class="form-label fw-semibold small">
                                    Mezuniyet Yılı <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="regGradYear" name="graduationYear"
                                       class="form-control <?= !empty($errors['graduationYear']) ? 'is-invalid' : '' ?>"
                                       min="1950" max="2040" placeholder="2025"
                                       value="<?= htmlspecialchars((string)($old['graduationYear'] ?? '2025')) ?>">
                                <?php if (!empty($errors['graduationYear'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['graduationYear']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Şirket (Opsiyonel) -->
                            <div class="col-12">
                                <label for="regCompany" class="form-label fw-semibold small">
                                    Çalıştığı Şirket / Kurum <span class="text-muted fw-normal">(Opsiyonel)</span>
                                </label>
                                <input type="text" id="regCompany" name="company"
                                       class="form-control <?= !empty($errors['company']) ? 'is-invalid' : '' ?>"
                                       placeholder="Örn: Trendyol, Getir, QNB Finansbank..."
                                       value="<?= htmlspecialchars($old['company'] ?? '') ?>">
                                <?php if (!empty($errors['company'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['company']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Parola -->
                            <div class="col-md-6">
                                <label for="regPassword" class="form-label fw-semibold small">
                                    Parola <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" id="regPassword" name="password"
                                           class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                                           placeholder="En az 6 karakter (harf + rakam)" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('regPassword', this)">👁️</button>
                                    <?php if (!empty($errors['password'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Parola Tekrar -->
                            <div class="col-md-6">
                                <label for="regPasswordConfirm" class="form-label fw-semibold small">
                                    Parola Tekrar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" id="regPasswordConfirm" name="password_confirm"
                                           class="form-control <?= !empty($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                           placeholder="Parolayı tekrar giriniz" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('regPasswordConfirm', this)">👁️</button>
                                    <?php if (!empty($errors['password_confirm'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password_confirm']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Koşullar Onayı -->
                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input <?= !empty($errors['terms']) ? 'is-invalid' : '' ?>"
                                           type="checkbox" value="1" id="regTerms" name="terms"
                                           <?= !empty($old['terms']) ? 'checked' : '' ?> required>
                                    <label class="form-check-label small text-secondary" for="regTerms">
                                        Sistemdeki rol yetkilendirme kurallarını (<code>ddd.md</code> Bölüm 8) ve veri gizliliği ilkelerini okudum, kabul ediyorum. <span class="text-danger">*</span>
                                    </label>
                                    <?php if (!empty($errors['terms'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['terms']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Gönder Butonu -->
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                    🎓 Hesabı Oluştur ve Kayıt Ol (POST /register)
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
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
    <script>
        function togglePass(inputId, btn) {
            const el = document.getElementById(inputId);
            if (!el) return;
            if (el.type === 'password') {
                el.type = 'text';
                btn.textContent = '🙈';
            } else {
                el.type = 'password';
                btn.textContent = '👁️';
            }
        }

        function toggleGradLabel() {
            const isStudent = document.getElementById('roleStudent')?.checked;
            const label = document.getElementById('gradYearLabel');
            if (!label) return;
            label.innerHTML = isStudent
                ? 'Beklenen Mezuniyet Yılı <span class="text-muted fw-normal">(Opsiyonel)</span>'
                : 'Mezuniyet Yılı <span class="text-danger">*</span>';
        }
        toggleGradLabel();
    </script>
</body>
</html>
