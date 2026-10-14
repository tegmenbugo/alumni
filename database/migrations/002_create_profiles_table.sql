-- Migration 002: Profiles Tablosu (1:1 Genişletilmiş Profil)
-- Kaynak: ddd.md Bölüm 4.2 & 5
CREATE TABLE IF NOT EXISTS profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE NOT NULL,
    phone VARCHAR(30),
    avatar_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    github_url VARCHAR(255),
    bio TEXT,
    city VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Türkiye',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_profiles_user ON profiles(user_id);
