<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;

// TAMBAHKAN USE STATEMENT UNTUK CONTROLLER ADMIN DI SINI
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BulkScheduleController;
use App\Http\Controllers\Piket\PiketController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [AttendanceController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/attendance', [AttendanceController::class, 'store'])
    ->middleware(['auth'])->name('attendance.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // LETAKKAN GRUP ROUTE ADMIN DI SINI, DI DALAM MIDDLEWARE 'auth'
    Route::middleware(['admin'])->name('admin.')->prefix('admin')->group(function () {
        Route::get('schedules/bulk-create', [BulkScheduleController::class, 'show'])->name('schedules.bulk-create');
        Route::post('schedules/bulk-store', [BulkScheduleController::class, 'store'])->name('schedules.bulk-store');
        Route::resource('teachers', TeacherController::class);
        Route::resource('subjects', SubjectController::class);
        Route::resource('classrooms', ClassroomController::class);
        Route::resource('schedules', ScheduleController::class);
    });

    // ROUTE UNTUK ADMIN DAN PIKET (akses report)
    Route::middleware(['admin-or-piket'])->name('admin.')->prefix('admin')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // ROUTE KHUSUS PIKET (akses read-only ke data master)
    Route::middleware(['piket'])->name('piket.')->prefix('piket')->group(function () {
        Route::get('teachers', [PiketController::class, 'teachers'])->name('teachers.index');
        Route::get('subjects', [PiketController::class, 'subjects'])->name('subjects.index');
        Route::get('classrooms', [PiketController::class, 'classrooms'])->name('classrooms.index');
        Route::get('schedules', [PiketController::class, 'schedules'])->name('schedules.index');
    });
});

require __DIR__.'/auth.php';