-- Script SQL untuk memperbarui kolom role di tabel users
-- Jalankan ini jika migration tidak bisa dijalankan via artisan

-- Backup data role yang ada
CREATE TEMPORARY TABLE temp_users_backup AS 
SELECT id, role FROM users;

-- Update kolom role untuk mendukung 'piket'
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'piket') DEFAULT 'guru';

-- Insert atau update user piket
INSERT INTO users (name, email, password, role, created_at, updated_at) 
VALUES ('Piket Guru', 'piket@sekolah.com', '$2y$12$/Av61n/qWkz2/MmDtByC6uA0QoemZKksuw.W3FASkn3GIH0LLE2L6', 'piket', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    name = 'Piket Guru',
    role = 'piket',
    updated_at = NOW();

-- Verifikasi data
SELECT id, name, email, role FROM users WHERE role IN ('admin', 'piket');
