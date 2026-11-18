# 🎨 Alumni Tracking System — Brand Identity & Design Guidelines (`brand.md`)

> **26-27 Web Programming Dersi Dönem Projesi · Marka Kimliği ve Görsel Tasarım Sözleşmesi**  
> **Ders:** YBSB3001 · Web Programming · Doç. Dr. Emre Akadal · İstanbul Üniversitesi İktisat Fakültesi YBS  
> **Geliştirici:** Battal Buğra Karataş ([@tegmenbugo](https://github.com/tegmenbugo))  
> **Ana Dokümantasyon:** [README.md](./README.md) · **Veri Şeması:** [ddd.md](./ddd.md) · **AI Tasarım Sistemi:** [DESIGN.md](./DESIGN.md)

---

## 📌 1. Marka ve Tasarım Dokümantasyonunun Amacı

Bu doküman (**`brand.md`**), Mezun Takip Sistemi'nin (Alumni Tracking System) marka kimliğini, kurumsal renk paletini, tipografi hiyerarşisini, görsel dilini ve UI bileşen standartlarını belirleyen resmi marka rehberidir.

Doç. Dr. Emre Akadal'ın ders standartları ve [VoltAgent/awesome-design-md](https://github.com/VoltAgent/awesome-design-md) açık kaynak tasarım şartnamesi uyarınca:
1. **Yapay Zeka ve Geliştirici Tasarım Sözleşmesi:** Sistemde yeni bir sayfa, bileşen veya arayüz (View) geliştirileceği zaman geliştirici veya yapay zeka ajanı, bu dokümandaki ve [DESIGN.md](./DESIGN.md) dosyasındaki tasarım tokenlarını temel alır.
2. **Kurumsal & Modern Hibrit Kimlik:** İstanbul Üniversitesi'nin köklü akademik ciddiyeti (Derin Lacivert & Altın Vurgusu) ile modern teknoloji şirketlerinin temiz ve ferah ürün dili (Vercel/Linear tarzı nötr arayüzler) harmanlanmıştır.
3. **README Entegrasyonu:** Bu dosya, sistemin ana dokümantasyonu olan [README.md](./README.md) ile karşılıklı çapraz referanslı (cross-referenced) çalışır.

---

## 🏛️ 2. Marka Felsefesi ve Görsel Kimlik (Brand Atmosphere)

* **Marka Adı:** Alumni Tracking System (İstanbul Üniversitesi Mezun Takip Portalı)
* **Karakter & Ton:** Güvenilir, akademik, çağdaş, erişilebilir, profesyonel.
* **Görsel Yoğunluk (Density):** Balanced (Ferah boşluklar, 16px-24px padding'ler, okunaklı kart yapıları).
* **UI Felsefesi:** İşlev odaklı sadelik. Gereksiz süslemeler yerine yüksek okunabilirlik, tutarlı kenar yuvarlaklıkları (border-radius: 12px-20px) ve yumuşak gölgeler (soft elevation).

---

## 🎨 3. Renk Paleti ve Tasarım Tokenları (Color Palette & Tokens)

Sistemde kullanılan tüm renkler anlamsal (semantic) rollere ayrılmıştır:

| Rol / Token | HEX Kodu | RGB | Kullanım Alanı ve Anlamı |
| :--- | :---: | :---: | :--- |
| `--brand-primary` | `#1e3a8a` | `rgb(30, 58, 138)` | **IU Deep Navy:** Birincil kurumsal renk; Navbar, Hero başlıkları, resmi logolar. |
| `--brand-accent` | `#2563eb` | `rgb(37, 99, 235)` | **Royal Blue:** Aksiyon rengi; Birincil butonlar, linkler, aktif durumlar. |
| `--brand-gold` | `#f59e0b` | `rgb(245, 158, 11)` | **Alumni Gold:** Başarı, mezuniyet yılı vurgusu, öne çıkan etiketler. |
| `--brand-success` | `#10b981` | `rgb(16, 185, 129)` | **Emerald Green:** Aktif ilanlar, başarılı işlemler, 200/201 HTTP durumları. |
| `--brand-danger` | `#ef4444` | `rgb(239, 68, 68)` | **Rose Red:** 404/500 Guardian uyarıları, silme butonları, hata mesajları. |
| `--brand-warning` | `#f97316` | `rgb(249, 115, 22)` | **Warm Orange:** 422 Doğrulama hataları, beklemedeki başvurular. |
| `--surface-bg` | `#f8fafc` | `rgb(248, 250, 252)` | **Slate 50:** Genel sayfa arka planı. Gözü yormayan soğuk nötr zemin. |
| `--surface-card` | `#ffffff` | `rgb(255, 255, 255)` | **Pure White:** Kart, modal ve tablo zeminleri. |
| `--text-main` | `#0f172a` | `rgb(15, 23, 42)` | **Slate 900:** Ana başlık ve gövde metinleri. |
| `--text-muted` | `#64748b` | `rgb(100, 116, 139)` | **Slate 500:** İkincil açıklamalar, meta etiketler, tarih bilgileri. |
| `--border-color` | `#e2e8f0` | `rgb(226, 232, 240)` | **Slate 200:** Kart kenarlıkları, tablo ayırıcı çizgileri. |

---

## 🔤 4. Tipografi Hiyerarşisi (Typography Hierarchy)

* **Ana Yazı Tipi (Body & UI):**  
  `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`  
  *(Yerel sistem fontları kullanılarak sıfır harici gecikme ile anında render sağlanır.)*
* **Kod & API Yazı Tipi (Monospace):**  
  `"SF Mono", Menlo, Consolas, "Liberation Mono", monospace`  
  *(HTTP metotları, uç noktalar, JSON verileri ve parametreler için.)*

### Tipografi Ölçeği:
* **Display / Hero:** `2.5rem` – `3.0rem` (40px – 48px), `font-weight: 800`, line-height: 1.15
* **H1 / Sayfa Başlığı:** `2.0rem` (32px), `font-weight: 700`, line-height: 1.25
* **H2 / Bölüm Başlığı:** `1.5rem` (24px), `font-weight: 700`
* **H3 / Kart Başlığı:** `1.25rem` (20px), `font-weight: 600`
* **Gövde Metni (Body):** `1.0rem` (16px), `font-weight: 400`, line-height: 1.5
* **Küçük Metin / Meta:** `0.875rem` (14px), `font-weight: 500`
* **Etiket / Rozet:** `0.75rem` (12px), `font-weight: 600`, text-transform: uppercase

---

## 🧱 5. Bileşen Standartları (UI Component System)

### 5.1. Butonlar (Buttons)
* **Primary Buton:** Mavi zemin (`#2563eb`), beyaz yazı, `border-radius: 10px`, hover'da yumuşak koyulaşma (`#1d4ed8`).
* **Secondary Buton:** Slate gri sınır (`#cbd5e1`), koyu metin (`#334155`), hover'da hafif gri arka plan (`#f1f5f9`).
* **Success Buton:** Yeşil zemin (`#10b981`), beyaz metin, form gönderme ve ilan açma işlemleri için.
* **Danger Buton:** Kırmızı sınır ve zemin, silme veya iptal aksiyonları için.

### 5.2. Kartlar (Cards)
* Zemin: `#ffffff`
* Kenarlık: `1px solid #e2e8f0`
* Köşe Yuvarlaklığı: `border-radius: 16px`
* Gölgelendirme: `box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04)`
* Hover Etkisi: `transform: translateY(-4px)`, gölgede hafif artış.

### 5.3. Formlar ve Girdiler (Forms & Validation States)
* Kenarlık: `1px solid #cbd5e1`, odaklanmada (focus): `#2563eb` halkası (`box-shadow: 0 0 0 3px rgba(37,99,235,0.15)`).
* Hatalı Durum (Invalid): `border-color: #ef4444`, altında açık kırmızı hata mesajı kutusu.
* Zorunlu Alan Belirteci: Kırmızı yıldız (`<span class="text-danger">*</span>`).

---

## 🚫 6. Tasarımda Yapılacaklar ve Yapılmayacaklar (Do's & Don'ts)

| Yapılması Gerekenler (DO) ✅ | Kesinlikle Yapılmaması Gerekenler (DON'T) ❌ |
| :--- | :--- |
| Tüm renkleri CSS değişkenleri veya Bootstrap tema sınıflarıyla kullanın. | HTML içine rastgele hex kodları (`style="color:#3412aa"`) serpiştirmeyin. |
| İlan başlıklarını 120 karakter sınırına uygun tasarlayın (taşma kontrolü: `text-truncate`). | Uzun metinlerin kartların dışına taşmasına izin vermeyin. |
| Mobil cihazlar için responsive (`col-12 col-md-6`) ızgara yapısı kurun. | Sabit piksel genişlikleri (`width: 900px`) tanımlamayın. |
| Başarılı ve hatalı durumlar için kullanıcıya her zaman net geri bildirim rozetleri verin. | Gerekçesiz veya düz metin hata sayfaları basmayın. |
| Tüm harici bağlantıları (`target="_blank" rel="noopener noreferrer"`) olarak açın. | Güvensiz dış link yönlendirmeleri yapmayın. |

---

## 🔗 7. Awesome-Design-MD Şartname Referansı

Bu tasarım rehberi, GitHub üzerindeki [VoltAgent/awesome-design-md](https://github.com/VoltAgent/awesome-design-md) standartlarına uygun olarak modellenmiştir. Yapay zeka ajanlarının proje arayüzlerini otomatik inşa ederken başvuracağı teknik token dökümü için lütfen [DESIGN.md](./DESIGN.md) dosyasını inceleyiniz.
