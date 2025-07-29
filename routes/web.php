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
    	Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
		Route::get('reports/chart', [ReportController::class, 'chart'])->name('reports.chart');


    });
});

require __DIR__.'/auth.php';