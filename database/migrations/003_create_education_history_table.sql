-- Migration 003: Education History Tablosu (1:N Akademik Geçmiş)
-- Kaynak: ddd.md Bölüm 4.3 & 5
CREATE TABLE IF NOT EXISTS education_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    degree VARCHAR(50) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    faculty VARCHAR(150),
    department VARCHAR(150) NOT NULL,
    start_year INTEGER NOT NULL,
    end_year INTEGER,
    gpa DECIMAL(3,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_education_user ON education_history(user_id);
