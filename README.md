# 🎓 Alumni Tracking System (Mezun Takip Sistemi)

> **26-27 Web Programming Dersi Dönem Projesi**  
> **Ders:** YBSB3001 · Web Programming · Doç. Dr. Emre Akadal · İstanbul Üniversitesi İktisat Fakültesi YBS  
> **Repo:** [github.com/tegmenbugo/alumni](https://github.com/tegmenbugo/alumni)

---

## 📌 Proje Amacı ve Kapsamı
Bu sistem, üniversite mezunları ile mevcut öğrenciler ve yönetim arasındaki iletişimi güçlendiren, mezunların kariyer yolculuklarını (kurum, pozisyon, sektör, iletişim) takip eden ve staj/iş fırsatlarını bir araya getiren web tabanlı bir Mezun Takip Platformudur.

---

## 🚀 Hızlı Başlangıç (One-Command Execution)

Dersin temel kuralı uyarınca sistem tek bir komutla ayağa kalkmaktadır:

```bash
docker compose up
```

Komut çalıştıktan sonra tarayıcınızdan şu adrese gidebilirsiniz:
👉 **`http://localhost:8000`** *(veya `http://localhost`)*

*(Alternatif olarak yerel PHP ortamında çalıştırmak için: `php -S localhost:8000 index.php`)*

---

## 🏛️ Hafta 04: MVC (Model-View-Controller) Mimarisi

Dersin 4. haftası kapsamında uygulama, harici bir framework'ün "büyülü" arka planına sığınmadan; web programlamanın temel yapı taşlarını ve sorumlulukların ayrışması (Separation of Concerns) ilkesini en şeffaf şekilde ortaya koyan **Saf (Native) PHP 8.3 Nesne Yönelimli MVC Mimarisi** ile yeniden yapılandırılmıştır.

### 🔄 MVC İstek Yaşam Döngüsü (Request Lifecycle)

```mermaid
sequenceDiagram
    autonumber
    actor Kullanıcı as Tarayıcı / İstemci
    participant FC as Front Controller (index.php)
    participant Router as Core\Router
    participant Controller as Controllers
    participant Model as Models\User
    participant DB as data/users.json
    participant View as Views / Response

    Kullanıcı->>FC: HTTP İsteği (GET/POST/PUT/PATCH/DELETE)
    FC->>Router: İsteği Yönlendir (dispatch)
    Router->>Controller: Eşleşen Metodu Çağır (Controller Action)
    alt Veri Gerektiren İstek (CRUD)
        Controller->>Model: Veri İsteği (all, find, create, update, delete)
        Model->>DB: JSON Oku / Yaz
        DB-->>Model: Ham Veri
        Model-->>Controller: İşlenmiş Veri / Obje
        Controller->>View: JSON Yanıtı Hazırla (Core\Response::json)
    else HTML Sayfa İsteği (Home / About / Swagger)
        Controller->>View: Şablonu Yükle (Core\View::render)
    end
    View-->>Kullanıcı: HTTP 200/201/404 Yanıtı (HTML / JSON)
```

---

### 📂 MVC Dizin ve Dosya Yapısı (Directories, Folders & Files)

```text
alumni/
├── data/                               # [VERİ KATMANI]
│   └── users.json                      # Veritabanı öncesi aşamada kalıcı JSON veri deposu
│
├── src/                                # [UYGULAMA ÇEKİRDEĞİ & İŞ MANTIĞI]
│   ├── Core/                           # Çekirdek MVC Altyapı Sınıfları
│   │   ├── Router.php                  # Dinamik regex rota çözümleyici ve metod dağıtıcı
│   │   ├── Response.php                # Standart JSON, Text ve Redirect HTTP yanıt üreticisi
│   │   └── View.php                    # views/ dizinindeki HTML şablonlarını veriyle birleştiren renderer
│   │
│   ├── Models/                         # [MODEL KATMANI (M)]
│   │   └── User.php                    # Kullanıcı ve mezun verilerinin doğrulanması, CRUD operasyonları
│   │
│   └── Controllers/                    # [CONTROLLER KATMANI (C)]
│       ├── HomeController.php          # Web sayfaları (/, /hello, /sum, /about, /Alumni)
│       ├── UserController.php          # REST API CRUD mantığı (/api/users - GET, POST, PUT, PATCH, DELETE)
│       └── ApiController.php           # Sistem sağlığı (/api/health) ve Swagger/OpenAPI yönetimi
│
├── views/                              # [VIEW KATMANI (V)]
│   ├── home.php                        # Ana kontrol paneli ve uç nokta test arayüzü
│   ├── about.php                       # Proje hakkında sayfası
│   └── swagger.php                     # Swagger UI canlı dokümantasyon sayfası
│
├── Dockerfile                          # Konteyner ortam tanımı (PHP 8.3 + Apache + mod_rewrite)
├── docker-compose.yml                  # Tek komutla ayağa kaldırma orkestrasyonu
├── index.php                           # [FRONT CONTROLLER] Tüm istekleri karşılayan tek giriş noktası
├── .htaccess                           # Apache URL rewrite motoru (Tüm trafiği index.php'ye yönlendirir)
├── .gitignore                          # Sürüm kontrolü dışı dosyalar
└── README.md                           # Proje mimarisi, sözleşmesi ve dokümantasyonu
```

---

### 🧩 MVC Katmanlarının Sorumlulukları

| Katman | Konum | Sorumluluk ve Görev Tanımı |
| :--- | :--- | :--- |
| **Front Controller** | `index.php` | Sistemin tek kapısıdır. PSR-4 standartlarında otomatik yükleyiciyi (Autoloader) başlatır, CORS ayarlarını yapar, rotaları tanımlar ve isteği `Router`'a iletir. |
| **Model (M)** | `src/Models/User.php` | Veri kaynağıyla (`data/users.json`) konuşan yegane katmandır. Veri ekleme, arama, filtreleme, güncelleme ve silme iş kurallarını kapsüller. Controller verinin nerede ve nasıl saklandığını bilmez. |
| **View (V)** | `views/*.php` | Kullanıcının gördüğü sunum katmanıdır. `Core\View` sınıfı üzerinden çağrılır. Controller'dan aktarılan verileri modern HTML5 ve Bootstrap 5 bileşenleriyle görselleştirir. |
| **Controller (C)** | `src/Controllers/*.php` | Model ile View arasındaki köprüdür. HTTP isteklerini yakalar, girdi doğrulamalarını (validation) yapar, ilgili Model metodunu tetikler ve sonucu `Response::json()` veya `View::render()` ile istemciye döndürür. |
| **Core Engine** | `src/Core/*` | MVC omurgasını oluşturan `Router`, `Response` ve `View` motorudur. Harici kütüphane bağımlılığı olmaksızın RESTful rotaları ve HTTP durum kodlarını yönetir. |

---

## 🛠️ Teknoloji Tercihleri ve Savunması (Stack Justification)

*Ders sözleşmesi gereğince seçilen teknolojiler ve zayıf yönleri:*

### 1. Backend: PHP 8.3 (Native & OOP MVC)
* **Neden Seçildi?** Web'in doğal dili olarak sıfır ek bağımlılıkla request-response döngüsünü, HTTP oturumlarını (session) ve routing mantığını en şeffaf şekilde yönetmeyi sağlar.
* **Zayıf Olduğu Yön:** Asenkron I/O (event loop) ve CPU-yoğun uzun süreli arka plan iş parçacıklarını Node.js veya Go kadar doğal desteklemez; her istek tipik olarak yeni bir proses yaşam döngüsünde çalışır.

### 2. Veritabanı: MySQL / MariaDB (Relational)
* **Neden Seçildi?** Mezun profilleri, iş ilanları ve yetkilendirme modelleri arasındaki katı ilişkiler (Foreign Keys) ve ACID işlem güvenliği için en uygun çözümdür.
* **Zayıf Olduğu Yön:** Yatayda ölçekleme (horizontal scaling / sharding) ve esnek şemasız veri yapıları (NoSQL) gerektiren durumlarda yapılandırması karmaşıktır.

### 3. Yapay Zeka Asistanı: Antigravity
* **Kullanılan Asistan:** Antigravity (Google DeepMind)
* **Kullanım Kapsamı:** Mimari tasarım, kod yazımı ve hata ayıklama süreçlerinde ders kurallarına uygun eşlikçi geliştirici olarak kullanılmaktadır.

---

## 📡 Aktif REST API & Swagger UI

Sistem, `data/users.json` modeli üzerinden tam kapsamlı bir CRUD REST API sunmaktadır:

* **Swagger UI Dokümantasyonu:** `http://localhost:8000/api/swagger`
* **OpenAPI 3.0 Şeması:** `http://localhost:8000/api/openapi.json`

| Metod | Uç Nokta (Endpoint) | İlgili Controller & Metod | Açıklama |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/health` | `ApiController::health()` | Servis sağlık kontrolü (JSON) |
| `GET` | `/api/users` | `UserController::index()` | Tüm kullanıcıları listeleme (Read All) |
| `POST` | `/api/users` | `UserController::store()` | Yeni kullanıcı oluşturma (Create - 201 Created) |
| `GET` | `/api/users/{id}` | `UserController::show($id)` | ID ile kullanıcı getirme (Read One) |
| `PUT` | `/api/users/{id}` | `UserController::update($id)` | Kullanıcıyı tamamen güncelleme (Full Update) |
| `PATCH` | `/api/users/{id}` | `UserController::patch($id)` | Belirli alanları güncelleme (Partial Update) |
| `DELETE` | `/api/users/{id}` | `UserController::destroy($id)` | Kullanıcıyı silme (Delete) |

---

## 🗓️ Dönem Yol Haritası (Fourteen Weeks Roadmap)

* [x] **Week 01:** Project inception & fundamentals, repository setup, stack selection & justification.
* [x] **Week 02:** Routing — the doors of the system (GET endpoints: `/Alumni`, `/hello`, `/hello/{name}`, `/sum/{n1}/{n2}`, `/about`, `/`).
* [x] **Week 03:** HTTP methods & CRUD on `/api/users` (GET, POST, PUT, PATCH, DELETE) + Swagger UI at `/api/swagger`.
* [x] **Week 04:** MVC architecture (Decoupled Front Controller, Router, Controllers, Models, Views).
* [ ] **Week 05:** Database & ORM.
* [ ] **Week 06:** Database integration.
* [ ] **Week 07:** Relational data & advanced routing.
* [ ] **Week 08:** Midterm — code review.
* [ ] **Week 09:** Middleware — the bouncer.
* [ ] **Week 10:** Views & front-end integration.
* [ ] **Week 11:** Authentication & authorization.
* [ ] **Week 12:** Service layers.
* [ ] **Week 13:** Deployment & CI/CD.
* [ ] **Week 14:** Final presentations — system handover.
