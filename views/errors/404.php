<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Sayfa Bulunamadı · Alumni Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta http-equiv="refresh" content="7;url=/">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
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
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -2px;
            text-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .error-body {
            padding: 35px 30px;
        }
        .countdown-badge {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <!-- Başlık & İkon Alanı -->
        <div class="error-header">
            <div class="error-code">404</div>
            <h2 class="h4 fw-bold mt-2 mb-1">Aradığınız Sayfa Mezun Olmuş Olabilir!</h2>
            <p class="mb-0 opacity-75 small">Talep edilen web sayfası bulunamadı veya taşındı.</p>
        </div>

        <!-- Gövde Alanı -->
        <div class="error-body">
            <!-- Gerekçe Bildirimi -->
            <div class="alert alert-warning border-warning-subtle d-flex align-items-start gap-3 mb-4" role="alert">
                <span style="font-size: 1.8rem; line-height: 1;">🛡️</span>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold mb-1">404 Guardian Gerekçesi (Reason):</h6>
                    <p class="mb-1 small text-dark">
                        <?= !empty($reason) ? htmlspecialchars($reason, ENT_QUOTES, 'UTF-8') : 'Bu adrese ait hiçbir yönlendirme kuralı veya sayfa bulunmamaktadır.' ?>
                    </p>
                    <?php if (!empty($path)): ?>
                        <div class="small text-muted mt-2 pt-2 border-top">
                            <strong>Geçersiz Yol (Invalid Path):</strong> <code><?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?></code>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Otomatik Yönlendirme (Redirect Notification) -->
            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4 border">
                <div class="d-flex align-items-center gap-2">
                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    <span class="small text-secondary">
                        <strong id="timer">6</strong> saniye içinde ana sayfaya otomatik yönlendiriliyorsunuz...
                    </span>
                </div>
                <button type="button" id="stopTimerBtn" class="btn btn-sm btn-outline-secondary" onclick="stopCountdown()">
                    Durdur
                </button>
            </div>

            <!-- Hızlı Geçiş Butonları -->
            <h6 class="fw-bold text-dark mb-3">🧭 Geçerli Sayfalara Göz Atın:</h6>
            <div class="row g-2 mb-4">
                <div class="col-6 col-sm-3">
                    <a href="/" class="btn btn-primary w-100 py-2 small fw-semibold">
                        🏠 Ana Sayfa
                    </a>
                </div>
                <div class="col-6 col-sm-3">
                    <a href="/jobs" class="btn btn-outline-primary w-100 py-2 small fw-semibold">
                        💼 İlanlar
                    </a>
                </div>
                <div class="col-6 col-sm-3">
                    <a href="/users" class="btn btn-outline-primary w-100 py-2 small fw-semibold">
                        👥 Mezunlar
                    </a>
                </div>
                <div class="col-6 col-sm-3">
                    <a href="/api/swagger" class="btn btn-outline-dark w-100 py-2 small fw-semibold">
                        📚 Swagger
                    </a>
                </div>
            </div>

            <!-- Alt Bilgi -->
            <div class="text-center pt-3 border-top text-muted small">
                Alumni Tracking System · YBSB3001 Web Programming · Hafta 07 (Görev 7)
            </div>
        </div>
    </div>

    <script>
        let seconds = 6;
        const timerEl = document.getElementById('timer');
        const stopBtn = document.getElementById('stopTimerBtn');
        let countdownInterval;

        countdownInterval = setInterval(() => {
            seconds--;
            if (timerEl) timerEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                window.location.href = '/';
            }
        }, 1000);

        function stopCountdown() {
            clearInterval(countdownInterval);
            if (stopBtn) {
                stopBtn.textContent = 'Durduruldu';
                stopBtn.disabled = true;
                stopBtn.classList.replace('btn-outline-secondary', 'btn-secondary');
            }
        }
    </script>
</body>
</html>
