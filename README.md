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

## 🛠️ Teknoloji Tercihleri ve Savunması (Stack Justification)

*Ders sözleşmesi gereğince seçilen teknolojiler ve zayıf yönleri:*

### 1. Backend: PHP 8.3 (Native & OOP)
* **Neden Seçildi?** Web'in doğal dili olarak sıfır ek bağımlılıkla request-response döngüsünü, HTTP oturumlarını (session) ve routing mantığını en şeffaf şekilde yönetmeyi sağlar.
* **Zayıf Olduğu Yön:** Asenkron I/O (event loop) ve CPU-yoğun uzun süreli arka plan iş parçacıklarını Node.js veya Go kadar doğal desteklemez; her istek tipik olarak yeni bir proses yaşam döngüsünde çalışır.

### 2. Veritabanı: MySQL / MariaDB (Relational)
* **Neden Seçildi?** Mezun profilleri, iş ilanları ve yetkilendirme modelleri arasındaki katı ilişkiler (Foreign Keys) ve ACID işlem güvenliği için en uygun çözümdür.
* **Zayıf Olduğu Yön:** Yatayda ölçekleme (horizontal scaling / sharding) ve esnek şemasız veri yapıları (NoSQL) gerektiren durumlarda yapılandırması karmaşıktır.

### 3. Yapay Zeka Asistanı: Antigravity
* **Kullanılan Asistan:** Antigravity (Google DeepMind)
* **Kullanım Kapsamı:** Mimari tasarım, kod yazımı ve hata ayıklama süreçlerinde ders kurallarına uygun eşlikçi geliştirici olarak kullanılmaktadır.

---

## 👥 Kullanıcı Rolleri ve Temel Özellikler

### 1. 🎓 Mezun & Öğrenci Modülü
* **Kayıt ve Profil:** Bölüm, mezuniyet yılı, biyografi, sosyal medya, profil fotoğrafı ve CV yükleme.
* **Kariyer Bilgisi:** Çalışma durumu (Özel Sektör, Kamu, Akademik, Yüksek Lisans, İş Arıyor), şirket adı ve unvanı.
* **Mezun Ağı (Networking):** Bölüm, mezuniyet yılı, şehir ve çalışma alanına göre filtreleme ve arama.
* **İlanlar & Duyurular:** İş/staj ilanlarını ve üniversite duyurularını görüntüleme.

### 2. 🛡️ Yönetici (Admin) Modülü
* **Mezun Onaylama/Denetleme:** Kaydolan mezunların doğrulanması ve yönetimi.
* **İlan & Duyuru Yönetimi:** İlanları onaylama, düzenleme ve silme.
* **İstatistik & Raporlama:** Mezunların sektör dağılımı, istihdam oranı ve bölüm bazlı grafiksel raporlar.

---

## 📡 Hafta 03: REST API & Swagger UI

Sistem, veritabanı öncesi aşamada (`data/users.json`) üzerinden tam kapsamlı bir CRUD REST API sunmaktadır:

* **Swagger UI Dokümantasyonu:** `http://localhost:8000/api/swagger`
* **OpenAPI 3.0 Şeması:** `http://localhost:8000/api/openapi.json`

| Metod | Uç Nokta (Endpoint) | Açıklama |
| :--- | :--- | :--- |
| `GET` | `/api/health` | Servis sağlık kontrolü (JSON) |
| `GET` | `/api/users` | Tüm kullanıcıları listeleme (Read All) |
| `POST` | `/api/users` | Yeni kullanıcı oluşturma (Create - 201 Created) |
| `GET` | `/api/users/{id}` | ID ile kullanıcı getirme (Read One) |
| `PUT` | `/api/users/{id}` | Kullanıcıyı tamamen güncelleme (Full Update) |
| `PATCH` | `/api/users/{id}` | Belirli alanları güncelleme (Partial Update) |
| `DELETE` | `/api/users/{id}` | Kullanıcıyı silme (Delete) |

---

## 🗂️ Proje Dizin Yapısı

```text
alumni/
├── data/
│   └── users.json       # JSON tabanlı veri deposu (Database-less persistence)
├── Dockerfile           # Konteyner imaj tanımı (PHP 8.3 + Apache + mod_rewrite)
├── docker-compose.yml   # Tek komutla ayağa kaldırma yapılandırması
├── index.php            # Merkezi HTTP yönlendirici ve REST API
├── .htaccess            # Apache URL yeniden yazma kuralları
├── .gitignore           # Git takip dışı dosyalar
└── README.md            # Proje dokümantasyonu ve sözleşmesi
```

---

## 🗓️ Dönem Yol Haritası (Fourteen Weeks Roadmap)

* [x] **Week 01:** Project inception & fundamentals, repository setup, stack selection & justification.
* [x] **Week 02:** Routing — the doors of the system (GET endpoints: `/Alumni`, `/hello`, `/hello/{name}`, `/sum/{n1}/{n2}`, `/about`, `/`).
* [x] **Week 03:** HTTP methods & CRUD on `/api/users` (GET, POST, PUT, PATCH, DELETE) + Swagger UI at `/api/swagger`.
* [ ] **Week 04:** MVC architecture.
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
