# Implementasi Role Piket - AbsenGuru

## Overview
Role piket telah berhasil diimplementasikan dengan fitur-fitur berikut:

### 1. Middleware yang Sudah Ada
- `IsAdmin`: Untuk akses admin penuh
- `IsPiket`: Untuk akses khusus piket
- `IsAdminOrPiket`: Untuk akses gabungan admin dan piket

### 2. Routes yang Telah Dikonfigurasi
```php
// Routes untuk admin (akses penuh - CRUD)
Route::middleware(['admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::resource('teachers', TeacherController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('classrooms', ClassroomController::class);
    Route::resource('schedules', ScheduleController::class);
});

// Routes untuk admin dan piket (akses report)
Route::middleware(['admin-or-piket'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});

// Routes khusus piket (akses read-only)
Route::middleware(['piket'])->name('piket.')->prefix('piket')->group(function () {
    Route::get('teachers', [PiketController::class, 'teachers'])->name('teachers.index');
    Route::get('subjects', [PiketController::class, 'subjects'])->name('subjects.index');
    Route::get('classrooms', [PiketController::class, 'classrooms'])->name('classrooms.index');
    Route::get('schedules', [PiketController::class, 'schedules'])->name('schedules.index');
});
```

### 3. Controller Piket
`app/Http/Controllers/Piket/PiketController.php`
- Menyediakan akses read-only ke data guru, mata pelajaran, kelas, dan jadwal
- Menggunakan pagination untuk performa yang baik

### 4. Views Read-Only
Dibuat views khusus untuk role piket di `resources/views/piket/`:
- `teachers/index.blade.php` - Tampilan data guru (read-only)
- `subjects/index.blade.php` - Tampilan mata pelajaran (read-only)
- `classrooms/index.blade.php` - Tampilan kelas (read-only)
- `schedules/index.blade.php` - Tampilan jadwal (read-only)

### 5. Navigasi yang Telah Diperbarui
File `resources/views/layouts/navigation.blade.php` telah diperbarui untuk:
- Menampilkan menu "Data Sekolah" untuk role piket
- Menyediakan link ke views read-only
- Tetap memberikan akses ke laporan absensi

### 6. Akses Dashboard
`AttendanceController.php` sudah mendukung role piket untuk:
- Input absensi (fungsi utama piket)
- Melihat jadwal hari ini
- Menggunakan filter dan sorting

### 7. User Seeder
Telah ditambahkan user dengan role piket:
```php
User::updateOrCreate(
    ['email' => 'piket@sekolah.com'],
    [
        'name' => 'Piket Guru',
        'password' => Hash::make('password'),
        'role' => 'piket',
    ]
);
```

## Perbedaan Akses Role

### Admin
- ✅ CRUD penuh untuk guru, mata pelajaran, kelas, jadwal
- ✅ Input absensi
- ✅ Laporan dan grafik
- ✅ Manajemen data lengkap

### Piket
- ❌ Tidak bisa tambah/edit/hapus guru, mata pelajaran, kelas, jadwal
- ✅ Melihat data guru, mata pelajaran, kelas, jadwal (read-only)
- ✅ Input absensi (fungsi utama)
- ✅ Laporan dan grafik
- ✅ Dashboard dengan filter dan sorting

### Guru
- ❌ Tidak ada akses admin
- ✅ Melihat jadwal pribadi
- ✅ Riwayat absensi pribadi

## Testing Manual

### Login sebagai Piket
1. Login dengan: `piket@sekolah.com` / `password`
2. Cek navigasi menampilkan menu "Data Sekolah"
3. Test akses ke:
   - `/piket/teachers` - Lihat data guru
   - `/piket/subjects` - Lihat mata pelajaran  
   - `/piket/classrooms` - Lihat kelas
   - `/piket/schedules` - Lihat jadwal
   - `/admin/reports` - Laporan (akses bersama admin)
4. Test input absensi di dashboard utama
5. Verifikasi tidak ada tombol tambah/edit/hapus di semua halaman

### Error Handling
Semua middleware akan mengarahkan ke halaman 403 jika role tidak sesuai.

## Status Implementasi
✅ **SELESAI** - Role piket telah diimplementasikan sepenuhnya sesuai permintaan:
- Fungsi dan fitur sama dengan admin
- Perbedaan: tidak bisa menambah/mengurangi data guru, mata pelajaran, kelas, jadwal
- Tetap bisa input absen di dashboard utama
- Akses read-only ke semua data master
