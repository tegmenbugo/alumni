-- Migration 007: Sentetik Tohum Veri Kümesi (Seed Data)
-- Kaynak: ddd.md Bölüm 6

-- 1. Users Başlangıç Kayıtları
INSERT OR IGNORE INTO users (id, name, email, graduationYear, department, company, role, status) VALUES
(1, 'Elif Kaya', 'elif.kaya@alumni.iu.edu.tr', 2024, 'Yönetim Bilişim Sistemleri', 'Trendyol', 'alumnus', 'active'),
(2, 'Mert Aydın', 'mert.aydin@alumni.iu.edu.tr', 2021, 'Yönetim Bilişim Sistemleri', 'QNB Finansbank', 'alumnus', 'active'),
(3, 'Zeynep Arslan', 'zeynep.arslan@alumni.iu.edu.tr', 2019, 'İktisat', 'Hepsiburada', 'alumnus', 'active'),
(4, 'Can Öztürk', 'can.ozturk@alumni.iu.edu.tr', 2023, 'İşletme', 'McKinsey & Company', 'alumnus', 'active'),
(5, 'Battal Buğra Karataş', 'bugrakaratas@alumni.iu.edu.tr', 2027, 'Yönetim Bilişim Sistemleri', 'Akadal Technology', 'student', 'active');

-- 2. Skills Başlangıç Kayıtları
INSERT OR IGNORE INTO skills (id, name, category) VALUES
(1, 'PHP 8.3 & OOP MVC', 'Yazılım'),
(2, 'Docker & DevOps', 'DevOps'),
(3, 'SQL & Relational Databases', 'Veri'),
(4, 'RESTful API & Swagger', 'Yazılım'),
(5, 'Business Intelligence & Power BI', 'Veri'),
(6, 'Python & Machine Learning', 'Yapay Zeka');

-- 3. User Skills Eşleşmeleri
INSERT OR IGNORE INTO user_skills (user_id, skill_id, proficiency_level) VALUES
(1, 1, 'İleri'),
(1, 4, 'İleri'),
(2, 3, 'Uzman'),
(2, 5, 'İleri'),
(3, 5, 'Orta'),
(4, 5, 'İleri'),
(5, 1, 'İleri'),
(5, 2, 'İleri'),
(5, 3, 'İleri');

-- 4. Örnek Profiller
INSERT OR IGNORE INTO profiles (user_id, city, country, bio, linkedin_url, github_url) VALUES
(1, 'İstanbul', 'Türkiye', 'İÜ YBS 2024 mezunu. Trendyol bünyesinde Backend Developer.', 'https://linkedin.com/in/elif-kaya', 'https://github.com/elifkaya'),
(2, 'İstanbul', 'Türkiye', 'Finans ve bankacılık sektöründe Kıdemli Veri Analisti.', 'https://linkedin.com/in/mert-aydin', 'https://github.com/mertaydin'),
(5, 'İstanbul', 'Türkiye', 'İÜ YBS Öğrencisi ve Yazılım Geliştirici.', 'https://linkedin.com/in/bugrakaratas', 'https://github.com/tegmenbugo');

-- 5. Örnek İş İlanı
INSERT OR IGNORE INTO job_postings (id, posted_by_user_id, title, company_name, employment_type, location, description, status) VALUES
(1, 1, 'Junior Backend Developer (PHP & MySQL)', 'Trendyol', 'Tam Zamanlı', 'İstanbul / Hibrit', 'Mezunlarımız ve son sınıf öğrencilerimiz için backend pozisyonu.', 'open');
