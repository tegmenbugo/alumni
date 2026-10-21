-- Migration 009: Job Applications Tablosu Güncellemesi (Hafta 06)
-- Kaynak: ddd.md Bölüm 4.7 & 5
-- Durum: Oluşturuldu / Beklemede (Just created, pending migration per instructor directive)

-- 1. Başvuru Süreci Yeni Sütunlarının Eklenmesi
ALTER TABLE job_applications ADD COLUMN resume_url VARCHAR(255);
ALTER TABLE job_applications ADD COLUMN portfolio_url VARCHAR(255);
ALTER TABLE job_applications ADD COLUMN notes TEXT;
ALTER TABLE job_applications ADD COLUMN interview_date DATETIME;
ALTER TABLE job_applications ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- 2. Sorgu İndeksi
CREATE INDEX IF NOT EXISTS idx_job_apps_status ON job_applications(status);
