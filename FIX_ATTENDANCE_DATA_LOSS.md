# Perbaikan: Data Absensi Hilang Saat Input Jadwal Massal

## Masalah yang Terjadi
Ketika melakukan input jadwal secara massal, data absensi yang sudah terekam menghilang semua.

## Penyebab Masalah
Di `BulkScheduleController.php`, sistem melakukan:
```php
// Hapus semua jadwal lama untuk kelas ini
Schedule::where('classroom_id', $classroomId)->delete();
```

Ketika jadwal dihapus, data attendance yang mengacu pada `schedule_id` tersebut menjadi orphan (kehilangan referensi), sehingga tidak tampil lagi di dashboard.

## Solusi yang Diterapkan

### 1. Logika Perlindungan Data Absensi
- ✅ **Cek data absensi** yang terkait dengan jadwal sebelum menghapus
- ✅ **Hanya hapus jadwal** yang TIDAK memiliki data absensi
- ✅ **Update jadwal existing** yang sudah memiliki data absensi
- ✅ **Buat jadwal baru** untuk slot yang kosong

### 2. Algoritma Baru di BulkScheduleController
```php
// 1. Ambil jadwal yang sudah ada
$existingSchedules = Schedule::where('classroom_id', $classroomId)->get();

// 2. Cek mana yang memiliki data absensi
$scheduleIdsWithAttendance = DB::table('attendances')
    ->whereIn('schedule_id', $existingSchedules->pluck('id'))
    ->distinct()
    ->pluck('schedule_id')
    ->toArray();

// 3. Hapus hanya yang TIDAK memiliki absensi
$schedulesToDelete = $existingSchedules->filter(function ($schedule) use ($scheduleIdsWithAttendance) {
    return !in_array($schedule->id, $scheduleIdsWithAttendance);
});

// 4. Update atau buat baru
if ($existingSchedule) {
    $existingSchedule->update([...]);  // Update
} else {
    Schedule::create([...]);           // Buat baru
}
```

### 3. Peningkatan UI
Ditambahkan peringatan di halaman bulk schedule:
- Info box biru yang menjelaskan perlindungan data
- Pesan sukses yang lebih informatif

## Hasil Setelah Perbaikan

### ✅ Yang Dilindungi:
- Data absensi tetap utuh dan tidak hilang
- Riwayat absensi guru tetap tersimpan
- Relasi antara jadwal dan absensi tetap valid

### ✅ Yang Tetap Berfungsi:
- Input jadwal massal tetap bisa dilakukan
- Jadwal lama yang tidak terpakai akan dihapus
- Jadwal baru akan dibuat sesuai input

### ✅ Scenario Penggunaan:
1. **Jadwal baru tanpa absensi** → Akan dibuat baru
2. **Jadwal lama dengan absensi** → Akan diupdate (guru/mapel berubah, absensi tetap)
3. **Jadwal lama tanpa absensi** → Akan dihapus dan diganti
4. **Slot kosong** → Akan dibuat jadwal baru

## Testing yang Disarankan

1. **Test 1: Jadwal dengan absensi**
   - Buat jadwal dan input beberapa absensi
   - Lakukan input jadwal massal
   - Verifikasi data absensi masih ada

2. **Test 2: Jadwal tanpa absensi**
   - Buat jadwal tanpa input absensi
   - Lakukan input jadwal massal
   - Verifikasi jadwal lama terhapus dan diganti

3. **Test 3: Mixed scenario**
   - Sebagian jadwal ada absensi, sebagian tidak
   - Input jadwal massal
   - Verifikasi behavior sesuai expectation

## File yang Dimodifikasi
- ✅ `app/Http/Controllers/Admin/BulkScheduleController.php` - Logic perlindungan data
- ✅ `resources/views/admin/schedules/bulk-create.blade.php` - UI warning

## Status
🎉 **FIXED** - Data absensi sekarang aman dari penghapusan tidak sengaja saat input jadwal massal.
