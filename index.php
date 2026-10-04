<?php
/**
 * Alumni Tracking System - Router & REST API
 * 26-27 Web Programming - Week 02 & Week 03
 * Assoc. Prof. Dr. Emre Akadal · Istanbul University
 */

// Global HTTP Ayarları
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($rawUri, PHP_URL_PATH);

// CORS başlıkları (API çağrıları ve Swagger için)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Preflight OPTIONS isteği geldiyse hemen 200 dön
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// -------------------------------------------------------------
// YOL (PATH) NORMALİZASYONU
// -------------------------------------------------------------
$trimmedRaw = trim($path, '/');

// 1) http://localhost/Alumni GET -> direkt "ok" desin (Hafta 2 Kuralı)
if (strcasecmp($trimmedRaw, 'Alumni') === 0 && $method === 'GET') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "ok";
    exit;
}

// Alt klasördeyse (/Alumni veya /alumni) kırp
if (stripos($path, '/Alumni') === 0) {
    $path = substr($path, strlen('/Alumni'));
}

$path = '/' . trim($path, '/');
if ($path === '//') {
    $path = '/';
}

// -------------------------------------------------------------
// VERİ YARDIMCI FONKSİYONLARI (JSON Dosya Depolama - No DB Yet)
// -------------------------------------------------------------
function getUsersData(): array {
    $file = __DIR__ . '/data/users.json';
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

function saveUsersData(array $users): bool {
    $file = __DIR__ . '/data/users.json';
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    return (bool)file_put_contents($file, json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function sendJsonResponse(int $statusCode, $data): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonInput(): ?array {
    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        return null;
    }
    return json_decode($raw, true);
}

// =============================================================
// HAFTA 03: REST API & CRUD (/api/...)
// =============================================================

// 1) GET /api/health -> JSON Sağlık Kontrolü
if ($path === '/api/health' && $method === 'GET') {
    sendJsonResponse(200, [
        "status" => "healthy",
        "service" => "Alumni Tracking System API",
        "timestamp" => date('c'),
        "version" => "1.0.0"
    ]);
}

// 2) OpenAPI 3.0 Şeması (JSON)
if ($path === '/api/openapi.json' && $method === 'GET') {
    sendJsonResponse(200, [
        "openapi" => "3.0.0",
        "info" => [
            "title" => "Alumni Tracking System API",
            "description" => "YBSB3001 Web Programming - Week 03 CRUD & REST Endpoints",
            "version" => "1.0.0"
        ],
        "servers" => [
            ["url" => "/", "description" => "Mevcut Sunucu"]
        ],
        "paths" => [
            "/api/health" => [
                "get" => [
                    "summary" => "Sistem sağlık kontrolü",
                    "responses" => [
                        "200" => ["description" => "Sistem çalışıyor"]
                    ]
                ]
            ],
            "/api/users" => [
                "get" => [
                    "summary" => "Tüm kullanıcıları listele (Read All)",
                    "responses" => [
                        "200" => ["description" => "Kullanıcı listesi"]
                    ]
                ],
                "post" => [
                    "summary" => "Yeni kullanıcı oluştur (Create)",
                    "requestBody" => [
                        "required" => true,
                        "content" => [
                            "application/json" => [
                                "schema" => [
                                    "type" => "object",
                                    "required" => ["name"],
                                    "properties" => [
                                        "name" => ["type" => "string", "example" => "Elif Kaya"],
                                        "email" => ["type" => "string", "example" => "elif.kaya@alumni.iu.edu.tr"],
                                        "graduationYear" => ["type" => "integer", "example" => 2024],
                                        "department" => ["type" => "string", "example" => "Yönetim Bilişim Sistemleri"],
                                        "company" => ["type" => "string", "example" => "Tech Solutions"]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "responses" => [
                        "201" => ["description" => "Kullanıcı başarıyla oluşturuldu"],
                        "400" => ["description" => "Geçersiz istek"]
                    ]
                ]
            ],
            "/api/users/{id}" => [
                "get" => [
                    "summary" => "ID ile tek kullanıcı getir (Read One)",
                    "parameters" => [
                        ["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"]]
                    ],
                    "responses" => [
                        "200" => ["description" => "Kullanıcı bulundu"],
                        "404" => ["description" => "Kullanıcı bulunamadı"]
                    ]
                ],
                "put" => [
                    "summary" => "Kullanıcıyı tamamen güncelle (Full Update)",
                    "parameters" => [
                        ["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"]]
                    ],
                    "requestBody" => [
                        "required" => true,
                        "content" => [
                            "application/json" => [
                                "schema" => [
                                    "type" => "object",
                                    "required" => ["name"],
                                    "properties" => [
                                        "name" => ["type" => "string", "example" => "Elif Kaya Demir"],
                                        "email" => ["type" => "string", "example" => "elif.demir@alumni.iu.edu.tr"],
                                        "graduationYear" => ["type" => "integer", "example" => 2024],
                                        "department" => ["type" => "string", "example" => "Yönetim Bilişim Sistemleri"],
                                        "company" => ["type" => "string", "example" => "Senior Tech"]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "responses" => [
                        "200" => ["description" => "Kullanıcı güncellendi"],
                        "404" => ["description" => "Kullanıcı bulunamadı"]
                    ]
                ],
                "patch" => [
                    "summary" => "Kullanıcı alanını kısmen güncelle (Partial Update)",
                    "parameters" => [
                        ["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"]]
                    ],
                    "requestBody" => [
                        "required" => true,
                        "content" => [
                            "application/json" => [
                                "schema" => [
                                    "type" => "object",
                                    "properties" => [
                                        "company" => ["type" => "string", "example" => "Yeni Şirket A.Ş."]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "responses" => [
                        "200" => ["description" => "Kullanıcı güncellendi"],
                        "404" => ["description" => "Kullanıcı bulunamadı"]
                    ]
                ],
                "delete" => [
                    "summary" => "Kullanıcıyı sil (Delete)",
                    "parameters" => [
                        ["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"]]
                    ],
                    "responses" => [
                        "200" => ["description" => "Kullanıcı silindi"],
                        "404" => ["description" => "Kullanıcı bulunamadı"]
                    ]
                ]
            ]
        ]
    ]);
}

// /swagger yazılırsa otomatik olarak /api/swagger adresine yönlendir
if ($path === '/swagger' || $path === '/swagger/') {
    header('Location: /api/swagger', true, 301);
    exit;
}

// 7) GET /api/swagger -> Swagger UI Arayüzü
if ($path === '/api/swagger' || $path === '/api/swagger/') {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Alumni API - Swagger UI</title>
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css" />
        <link rel="icon" type="image/png" href="https://unpkg.com/swagger-ui-dist@5.11.0/favicon-32x32.png" />
        <style>
            html { box-sizing: border-box; overflow-y: scroll; }
            *, *:before, *:after { box-sizing: inherit; }
            body { margin: 0; background: #fafafa; font-family: sans-serif; }
            .swagger-custom-header {
                background: #1f2937;
                color: white;
                padding: 14px 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .swagger-custom-header a {
                color: #60a5fa;
                text-decoration: none;
                font-size: 14px;
                border: 1px solid #374151;
                padding: 6px 12px;
                border-radius: 6px;
            }
            .swagger-custom-header a:hover { background: #374151; color: white; }
        </style>
    </head>
    <body>
        <div class="swagger-custom-header">
            <strong>🎓 Alumni Tracking System — Week 03 API & Swagger UI</strong>
            <div>
                <a href="/">← Ana Sayfaya Dön</a>
                <a href="/api/health" target="_blank">Health Check ↗</a>
                <a href="/api/users" target="_blank">Users JSON ↗</a>
            </div>
        </div>
        <div id="swagger-ui"></div>
        <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" charset="UTF-8"></script>
        <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-standalone-preset.js" charset="UTF-8"></script>
        <script>
            window.onload = function() {
                // Dinamik olarak /api/openapi.json yolunu belirle
                const openApiUrl = window.location.pathname.replace(/\/swagger\/?$/, '/openapi.json');
                window.ui = SwaggerUIBundle({
                    url: openApiUrl,
                    dom_id: '#swagger-ui',
                    deepLinking: true,
                    presets: [
                        SwaggerUIBundle.presets.apis,
                        SwaggerUIStandalonePreset
                    ],
                    layout: "StandaloneLayout"
                });
            };
        </script>
    </body>
    </html>
    <?php
    exit;
}

// 4) GET /api/users -> Tüm kullanıcıları listele (Read All)
if ($path === '/api/users' && $method === 'GET') {
    $users = getUsersData();
    sendJsonResponse(200, $users);
}

// 3) POST /api/users -> Yeni kullanıcı ekle (Create)
if ($path === '/api/users' && $method === 'POST') {
    $input = getJsonInput();
    if (!$input || empty(trim($input['name'] ?? ''))) {
        sendJsonResponse(400, [
            "error" => "Geçersiz istek. 'name' alanı zorunludur.",
            "example" => [
                "name" => "Elif Kaya",
                "graduationYear" => 2024,
                "email" => "elif.kaya@alumni.iu.edu.tr",
                "department" => "Yönetim Bilişim Sistemleri",
                "company" => "Tech Corp"
            ]
        ]);
    }

    $users = getUsersData();
    
    // Yeni benzersiz ID üret
    $maxId = 0;
    foreach ($users as $u) {
        if (isset($u['id']) && $u['id'] > $maxId) {
            $maxId = $u['id'];
        }
    }
    $newId = $maxId + 1;

    $newUser = [
        "id" => $newId,
        "name" => trim($input['name']),
        "email" => trim($input['email'] ?? ""),
        "graduationYear" => isset($input['graduationYear']) ? (int)$input['graduationYear'] : null,
        "department" => trim($input['department'] ?? ""),
        "company" => trim($input['company'] ?? "")
    ];

    $users[] = $newUser;
    saveUsersData($users);

    // Slide'da gösterildiği gibi HTTP 201 Created döner
    sendJsonResponse(201, $newUser);
}

// ID'ye bağlı işlemler: /api/users/{id}
if (preg_match('#^/api/users/([0-9]+)$#', $path, $matches)) {
    $userId = (int)$matches[1];
    $users = getUsersData();

    // Kullanıcıyı bul
    $foundIndex = -1;
    $foundUser = null;
    foreach ($users as $index => $u) {
        if (isset($u['id']) && $u['id'] === $userId) {
            $foundIndex = $index;
            $foundUser = $u;
            break;
        }
    }

    // 4b) GET /api/users/{id} -> Tek kullanıcıyı getir (Read One)
    if ($method === 'GET') {
        if ($foundIndex === -1) {
            sendJsonResponse(404, ["error" => "ID {$userId} numaralı kullanıcı bulunamadı."]);
        }
        sendJsonResponse(200, $foundUser);
    }

    // 5) PUT /api/users/{id} -> Kullanıcıyı tamamen güncelle (Full Update)
    if ($method === 'PUT') {
        if ($foundIndex === -1) {
            sendJsonResponse(404, ["error" => "ID {$userId} numaralı kullanıcı bulunamadı."]);
        }
        $input = getJsonInput();
        if (!$input || empty(trim($input['name'] ?? ''))) {
            sendJsonResponse(400, ["error" => "Geçersiz istek. 'name' alanı zorunludur."]);
        }

        $users[$foundIndex] = [
            "id" => $userId,
            "name" => trim($input['name']),
            "email" => trim($input['email'] ?? ""),
            "graduationYear" => isset($input['graduationYear']) ? (int)$input['graduationYear'] : null,
            "department" => trim($input['department'] ?? ""),
            "company" => trim($input['company'] ?? "")
        ];
        saveUsersData($users);
        sendJsonResponse(200, $users[$foundIndex]);
    }

    // 5) PATCH /api/users/{id} -> Kullanıcı alanlarını kısmen güncelle (Partial Update)
    if ($method === 'PATCH') {
        if ($foundIndex === -1) {
            sendJsonResponse(404, ["error" => "ID {$userId} numaralı kullanıcı bulunamadı."]);
        }
        $input = getJsonInput();
        if (!$input) {
            sendJsonResponse(400, ["error" => "Güncellenecek geçerli bir JSON gövdesi gönderiniz."]);
        }

        if (isset($input['name'])) $users[$foundIndex]['name'] = trim($input['name']);
        if (isset($input['email'])) $users[$foundIndex]['email'] = trim($input['email']);
        if (isset($input['graduationYear'])) $users[$foundIndex]['graduationYear'] = (int)$input['graduationYear'];
        if (isset($input['department'])) $users[$foundIndex]['department'] = trim($input['department']);
        if (isset($input['company'])) $users[$foundIndex]['company'] = trim($input['company']);

        saveUsersData($users);
        sendJsonResponse(200, $users[$foundIndex]);
    }

    // 6) DELETE /api/users/{id} -> Kullanıcıyı sil (Delete)
    if ($method === 'DELETE') {
        if ($foundIndex === -1) {
            sendJsonResponse(404, ["error" => "ID {$userId} numaralı kullanıcı bulunamadı."]);
        }
        $deleted = $users[$foundIndex];
        array_splice($users, $foundIndex, 1);
        saveUsersData($users);
        sendJsonResponse(200, [
            "message" => "ID {$userId} numaralı kullanıcı başarıyla silindi.",
            "deletedUser" => $deleted
        ]);
    }
}

// =============================================================
// HAFTA 02: TEMEL ROTALAR (Geriye Dönük Uyumluluk)
// =============================================================

// Sadece GET metoduna izin ver (Week 2 rotaları için)
if ($method === 'GET') {
    // 2) /hello GET -> "Hello World"
    if ($path === '/hello') {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Hello World";
        exit;
    }

    // 3) /hello/{name} GET -> "Hello {name}!"
    if (preg_match('#^/hello/([^/]+)$#u', $path, $matches)) {
        header('Content-Type: text/plain; charset=utf-8');
        $name = urldecode($matches[1]);
        echo "Hello " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "!";
        exit;
    }

    // 4) /sum/{a}/{b} GET -> Toplam
    if (preg_match('#^/sum/([^/]+)/([^/]+)$#', $path, $matches)) {
        header('Content-Type: text/plain; charset=utf-8');
        $num1 = $matches[1];
        $num2 = $matches[2];
        if (is_numeric($num1) && is_numeric($num2)) {
            echo ($num1 + $num2);
        } else {
            http_response_code(400);
            echo "Hata: Gecerli iki sayi giriniz.";
        }
        exit;
    }

    // /about GET -> Temporary About Page
    if ($path === '/about') {
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <title>Temporary About Page - Alumni Tracking System</title>
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
                                    ℹ️ Temporary About Page
                                </span>
                                <a href="/" class="btn btn-sm btn-outline-secondary">← Ana Sayfaya Dön</a>
                            </div>
                            <h1 class="display-6 fw-bold text-dark mb-3">Hakkında (About)</h1>
                            <p class="lead text-muted mb-4">
                                Bu sayfa, <strong>Mezun Takip Sistemi (Alumni Tracking System)</strong> projesi için hazırlanmış geçici hakkında sayfasıdır.
                            </p>
                            <div class="card bg-light border-0 p-4 rounded-3 mb-4">
                                <h5 class="fw-bold mb-3">📌 Proje Bilgileri:</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><strong>Ders:</strong> YBSB3001 · Web Programming</li>
                                    <li class="mb-2"><strong>Öğretim Üyesi:</strong> Doç. Dr. Emre Akadal</li>
                                    <li class="mb-2"><strong>Geliştirici:</strong> Buğra Karataş</li>
                                    <li><strong>API Dokümantasyonu:</strong> <a href="/api/swagger" class="text-decoration-none">Swagger UI (/api/swagger)</a></li>
                                </ul>
                            </div>
                            <div class="text-center">
                                <a href="/" class="btn btn-primary px-4 py-2">Ana Sayfaya Dön ➔</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    // 5) / GET -> Temporary Main Page (Hafta 2 + Hafta 3 Entegre)
    if ($path === '/' || $path === '') {
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Alumni Tracking System</title>
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
                                    Week 02 & Week 03 Ready
                                </span>
                            </div>
                            <h1 class="display-6 fw-bold text-dark mb-2">Alumni Tracking System</h1>
                            <p class="lead text-muted mb-4">
                                Mezun Takip Sistemi projesi kontrol paneli. Aşağıda hem 2. Haftanın temel GET rotalarını hem de 3. Haftanın <strong>CRUD REST API</strong> uç noktalarını test edebilirsiniz:
                            </p>

                            <!-- Week 3 Banner -->
                            <div class="alert alert-primary d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" role="alert">
                                <div>
                                    <h6 class="fw-bold mb-1">🚀 Week 03: Swagger UI Canlı Dokümantasyon</h6>
                                    <small class="text-muted">Tüm CRUD metodlarını (GET, POST, PUT, PATCH, DELETE) tarayıcıdan denemek için Swagger arayüzünü açın.</small>
                                </div>
                                <a href="/api/swagger" class="btn btn-primary fw-semibold px-3 py-2" target="_blank">
                                    Swagger UI Aç ➔
                                </a>
                            </div>

                            <!-- Endpoints Table -->
                            <h5 class="fw-bold mb-3">📋 Aktif API ve Web Uç Noktaları</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Hafta</th>
                                            <th>Metod</th>
                                            <th>Uç Nokta (Route)</th>
                                            <th>Açıklama / İşlev</th>
                                            <th class="text-center">İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-secondary">Week 3</span></td>
                                            <td><span class="badge bg-success">GET</span></td>
                                            <td><span class="endpoint-badge">/api/swagger</span></td>
                                            <td>Swagger UI Dokümantasyon Arayüzü</td>
                                            <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-primary" target="_blank">Aç ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary">Week 3</span></td>
                                            <td><span class="badge bg-success">GET</span></td>
                                            <td><span class="endpoint-badge">/api/health</span></td>
                                            <td>Sistem Sağlık Durumu (JSON)</td>
                                            <td class="text-center"><a href="/api/health" class="btn btn-sm btn-outline-primary" target="_blank">Aç ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary">Week 3</span></td>
                                            <td><span class="badge bg-success">GET</span></td>
                                            <td><span class="endpoint-badge">/api/users</span></td>
                                            <td>Tüm Mezunları Listele (JSON)</td>
                                            <td class="text-center"><a href="/api/users" class="btn btn-sm btn-outline-primary" target="_blank">Aç ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary">Week 3</span></td>
                                            <td><span class="badge bg-info text-dark">POST</span></td>
                                            <td><span class="endpoint-badge">/api/users</span></td>
                                            <td>Yeni Mezun Ekle (JSON Body)</td>
                                            <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-info" target="_blank">Swagger'da Dene ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary">Week 3</span></td>
                                            <td><span class="badge bg-warning text-dark">PUT / PATCH</span></td>
                                            <td><span class="endpoint-badge">/api/users/{id}</span></td>
                                            <td>Mezun Bilgilerini Güncelle</td>
                                            <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-warning" target="_blank">Swagger'da Dene ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary">Week 3</span></td>
                                            <td><span class="badge bg-danger">DELETE</span></td>
                                            <td><span class="endpoint-badge">/api/users/{id}</span></td>
                                            <td>Mezun Kaydını Sil</td>
                                            <td class="text-center"><a href="/api/swagger" class="btn btn-sm btn-outline-danger" target="_blank">Swagger'da Dene ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary-subtle text-secondary">Week 2</span></td>
                                            <td><span class="badge bg-success">GET</span></td>
                                            <td><span class="endpoint-badge">/hello</span></td>
                                            <td>"Hello World"</td>
                                            <td class="text-center"><a href="/hello" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary-subtle text-secondary">Week 2</span></td>
                                            <td><span class="badge bg-success">GET</span></td>
                                            <td><span class="endpoint-badge">/hello/Buğra</span></td>
                                            <td>"Hello Buğra!"</td>
                                            <td class="text-center"><a href="/hello/Bu%C4%9Fra" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary-subtle text-secondary">Week 2</span></td>
                                            <td><span class="badge bg-success">GET</span></td>
                                            <td><span class="endpoint-badge">/sum/15/27</span></td>
                                            <td>Toplam İşlemi (42)</td>
                                            <td class="text-center"><a href="/sum/15/27" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary-subtle text-secondary">Week 2</span></td>
                                            <td><span class="badge bg-success">GET</span></td>
                                            <td><span class="endpoint-badge">/about</span></td>
                                            <td>Temporary About Page</td>
                                            <td class="text-center"><a href="/about" class="btn btn-sm btn-outline-secondary" target="_blank">Aç ↗</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                                <span>Geliştirici: <strong>Buğra Karataş</strong> · GitHub: <a href="https://github.com/tegmenbugo/alumni" target="_blank" class="text-decoration-none">tegmenbugo/alumni</a></span>
                                <a href="/api/swagger" class="btn btn-sm btn-dark">Swagger UI ➔</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Eşleşmeyen rotalar için 404 JSON veya HTML
if (strpos($path, '/api/') === 0) {
    sendJsonResponse(404, ["error" => "Endpoint bulunamadi: " . $path]);
} else {
    http_response_code(404);
    echo "404 - Sayfa Bulunamadı: " . htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
}
