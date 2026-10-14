-- Migration 004: Work Experiences Tablosu (1:N Kariyer ve Deneyim)
-- Kaynak: ddd.md Bölüm 4.4 & 5
CREATE TABLE IF NOT EXISTS work_experiences (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    employment_type VARCHAR(50) DEFAULT 'Tam Zamanlı',
    location VARCHAR(100),
    start_date DATE NOT NULL,
    end_date DATE,
    is_current BOOLEAN DEFAULT 0,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_experience_user ON work_experiences(user_id);
