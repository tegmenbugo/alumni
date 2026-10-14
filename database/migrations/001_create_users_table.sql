-- Migration 001: Users Tablosu
-- Kaynak: ddd.md Bölüm 4.1 & 5
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(191) UNIQUE,
    graduationYear INTEGER,
    department VARCHAR(150) DEFAULT 'Yönetim Bilişim Sistemleri',
    company VARCHAR(150),
    role VARCHAR(50) DEFAULT 'alumnus',
    status VARCHAR(30) DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_grad_year ON users(graduationYear);
