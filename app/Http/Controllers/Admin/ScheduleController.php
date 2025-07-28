<?php
// app/Http/Controllers/Admin/ScheduleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua jadwal.
     */
    public function index()
    {
        // Eager load relasi untuk menghindari N+1 query problem
        $schedules = Schedule::with(['user', 'subject', 'classroom'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(10);
            
        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat jadwal baru.
     */
    public function create()
    {
        // Ambil semua data yang dibutuhkan untuk dropdown di form
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.schedules.create', compact('teachers', 'subjects', 'classrooms'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan jadwal baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Buat jadwal baru
        Schedule::create($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit jadwal.
     */
    public function edit(Schedule $schedule)
    {
        // Ambil semua data yang dibutuhkan untuk dropdown di form edit
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.schedules.edit', compact('schedule', 'teachers', 'subjects', 'classrooms'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui data jadwal di database.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Update jadwal
        $schedule->update($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data jadwal dari database.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}