-- Migration 008: Job Postings Tablosu Güncellemesi (Hafta 06)
-- Kaynak: ddd.md Bölüm 4.6 & 5
-- Durum: Oluşturuldu / Beklemede (Just created, pending migration per instructor directive)

-- 1. Yeni Sütunların Eklenmesi
ALTER TABLE job_postings ADD COLUMN workplace_type VARCHAR(50) DEFAULT 'Hibrit';
ALTER TABLE job_postings ADD COLUMN experience_level VARCHAR(50) DEFAULT 'Giriş Seviyesi';
ALTER TABLE job_postings ADD COLUMN requirements TEXT;
ALTER TABLE job_postings ADD COLUMN salary_min DECIMAL(10,2);
ALTER TABLE job_postings ADD COLUMN salary_max DECIMAL(10,2);
ALTER TABLE job_postings ADD COLUMN salary_currency VARCHAR(10) DEFAULT 'TRY';
ALTER TABLE job_postings ADD COLUMN contact_email VARCHAR(191);
ALTER TABLE job_postings ADD COLUMN application_url VARCHAR(255);
ALTER TABLE job_postings ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- 2. Sorgu Performansı İndeksleri
CREATE INDEX IF NOT EXISTS idx_job_postings_status ON job_postings(status);
CREATE INDEX IF NOT EXISTS idx_job_postings_company ON job_postings(company_name);
