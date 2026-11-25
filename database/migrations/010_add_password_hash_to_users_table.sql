-- Migration 010: Users Tablosuna Kimlik Doğrulama (Auth) Sütunu Eklenmesi
-- Kaynak: ddd.md Bölüm 4.1, 5 ve 8 (Authentication & RBAC)
-- Amaç: Kayıt (Register) ve parola doğrulama işlemleri için password_hash saklanması

ALTER TABLE users ADD COLUMN password_hash VARCHAR(255);

CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
