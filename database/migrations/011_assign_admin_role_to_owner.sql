-- Migration 011: Proje sahibine Admin rolü atama ve test rol hesapları
-- Hafta 09 - Görev 5 (Admin Panel & Role Assignment)
-- Doç. Dr. Emre Akadal · YBSB3001 Web Programming

-- 1. Proje sahibine (Battal Buğra Karataş) admin rolü ve aktif statü ata
UPDATE users
SET role = 'admin', status = 'active'
WHERE LOWER(email) = 'bugrakaratas@alumni.iu.edu.tr';

-- 2. Eğer proje sahibi veritabanında henüz yoksa admin olarak ekle
INSERT OR IGNORE INTO users (name, email, graduationYear, department, company, role, status)
VALUES ('Battal Buğra Karataş', 'bugrakaratas@alumni.iu.edu.tr', 2027, 'Yönetim Bilişim Sistemleri', 'Akadal Technology', 'admin', 'active');

-- 3. RBAC testleri için örnek Öğrenci (student) ve Akademisyen (faculty) hesapları
INSERT OR IGNORE INTO users (name, email, graduationYear, department, company, role, status)
VALUES ('Ayşe Yılmaz', 'ayse.yilmaz@alumni.iu.edu.tr', 2027, 'Yönetim Bilişim Sistemleri', 'İstanbul Üniversitesi', 'student', 'active');

INSERT OR IGNORE INTO users (name, email, graduationYear, department, company, role, status)
VALUES ('Dr. Ahmet Aydın', 'dr.aydin@iu.edu.tr', 2012, 'Yönetim Bilişim Sistemleri', 'İstanbul Üniversitesi YBS', 'faculty', 'active');
