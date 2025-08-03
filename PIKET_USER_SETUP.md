# Panduan Mengatasi Error Role Piket

## Masalah
Error: `Data truncated for column 'role' at row 1` saat menjalankan seeder untuk user piket.

## Penyebab
Kolom `role` di database mungkin belum diupdate untuk mendukung nilai 'piket' dalam enum.

## Solusi (Pilih salah satu)

### Solusi 1: Menggunakan Artisan Command (Direkomendasikan)
```bash
php artisan user:create-piket
```

Atau dengan parameter custom:
```bash
php artisan user:create-piket --email=piket@sekolah.com --name="Petugas Piket" --password=password123
```

### Solusi 2: Menggunakan Migration
1. Jalankan migration baru:
```bash
php artisan migrate
```

2. Kemudian jalankan seeder:
```bash
php artisan db:seed --class=UserSeeder
```

### Solusi 3: Manual SQL (Jika artisan tidak tersedia)
1. Buka MySQL/MariaDB console
2. Jalankan SQL dari file: `database/sql/update_role_enum.sql`

### Solusi 4: Menggunakan Tinker
```bash
php artisan tinker
```

Kemudian jalankan:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Piket Guru',
    'email' => 'piket@sekolah.com', 
    'password' => Hash::make('password'),
    'role' => 'piket'
]);
```

### Solusi 5: Update Database Manual
Jika masih error, update database secara manual:

```sql
-- Update enum untuk mendukung piket
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'piket') DEFAULT 'guru';

-- Insert user piket
INSERT INTO users (name, email, password, role, created_at, updated_at) 
VALUES ('Piket Guru', 'piket@sekolah.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'piket', NOW(), NOW());
```

## Verifikasi
Setelah user piket berhasil dibuat, verifikasi dengan:

1. **Cek di database:**
```sql
SELECT id, name, email, role FROM users WHERE role = 'piket';
```

2. **Test login:**
- Email: `piket@sekolah.com`
- Password: `password`

3. **Test navigasi:**
- Seharusnya muncul menu "Data Sekolah"
- Dapat akses halaman read-only

## Status File yang Telah Dibuat

✅ `app/Console/Commands/CreatePiketUser.php` - Command untuk membuat user piket
✅ `database/migrations/2025_08_04_015500_update_users_role_enum.php` - Migration update enum
✅ `database/sql/update_role_enum.sql` - Script SQL manual
✅ `database/seeders/UserSeeder.php` - Seeder dengan fallback
✅ Semua implementasi role piket sudah selesai

## Catatan
Jika semua solusi di atas tidak berhasil, kemungkinan ada masalah dengan versi MySQL/MariaDB yang tidak mendukung perubahan enum secara langsung. Dalam kasus ini, perlu drop dan recreate table atau menggunakan migration yang lebih kompleks.
