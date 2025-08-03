# Perbaikan Error Relasi - Role Piket

## Error yang Terjadi
```
Illuminate\Database\Eloquent\RelationNotFoundException
Call to undefined relationship [teacher] on model [App\Models\Schedule].
```

## Penyebab
Di `PiketController.php` menggunakan relasi `teacher` padahal model `Schedule` menggunakan relasi `user`.

## Perbaikan yang Dilakukan

### 1. Fix PiketController.php
**Sebelum:**
```php
$schedules = Schedule::with(['subject', 'teacher', 'classroom', 'timeSlot'])
```

**Sesudah:**
```php
$schedules = Schedule::with(['subject', 'user', 'classroom', 'timeSlot'])
```

### 2. Fix View piket/schedules/index.blade.php
**Sebelum:**
```php
{{ $schedule->teacher->name }}
```

**Sesudah:**
```php
{{ $schedule->user->name }}
```

## Relasi Model Schedule yang Benar
```php
public function timeSlot() { return $this->belongsTo(TimeSlot::class); }
public function user() { return $this->belongsTo(User::class); }
public function subject() { return $this->belongsTo(Subject::class); }
public function classroom() { return $this->belongsTo(Classroom::class); }
```

## Status
✅ **Error telah diperbaiki**
✅ Login piket seharusnya sudah bisa mengakses halaman jadwal
✅ Semua relasi menggunakan nama yang konsisten
