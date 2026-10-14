-- Migration 006: Job Postings ve Job Applications Tabloları (Kariyer Fırsatları)
-- Kaynak: ddd.md Bölüm 4.6 & 5
CREATE TABLE IF NOT EXISTS job_postings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    posted_by_user_id INTEGER NOT NULL,
    title VARCHAR(150) NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    employment_type VARCHAR(50) DEFAULT 'Tam Zamanlı',
    location VARCHAR(100) DEFAULT 'İstanbul / Hibrit',
    description TEXT,
    status VARCHAR(30) DEFAULT 'open',
    deadline DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS job_applications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    job_posting_id INTEGER NOT NULL,
    applicant_user_id INTEGER NOT NULL,
    status VARCHAR(30) DEFAULT 'applied',
    cover_letter TEXT,
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_posting_id) REFERENCES job_postings(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(job_posting_id, applicant_user_id)
);

CREATE INDEX IF NOT EXISTS idx_job_postings_user ON job_postings(posted_by_user_id);
CREATE INDEX IF NOT EXISTS idx_job_apps_posting ON job_applications(job_posting_id);
CREATE INDEX IF NOT EXISTS idx_job_apps_user ON job_applications(applicant_user_id);
