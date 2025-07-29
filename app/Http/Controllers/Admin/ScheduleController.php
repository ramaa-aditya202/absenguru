<?php
// File: app/Http/Controllers/Admin/ScheduleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TimeSlot;
use Illuminate\Http\Request; // <-- Pastikan Request di-import

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal dengan sorting dan paginasi dinamis.
     */
    public function index(Request $request)
    {
        // 1. Ambil parameter sorting dari URL, atau gunakan default
        $sort = $request->get('sort', 'schedules.day_of_week');
        $direction = $request->get('direction', 'asc');

        // 2. Daftar kolom yang diizinkan untuk di-sort (untuk keamanan)
        $allowedSorts = [
            'schedules.day_of_week', 'time_slots.start_time',
            'subjects.name', 'users.name', 'classrooms.name'
        ];

        // 3. Jika kolom yang diminta tidak diizinkan, gunakan default
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'schedules.day_of_week';
            $direction = 'asc';
        }

        // 4. Bangun query dengan join ke semua tabel terkait
        $query = Schedule::with(['user', 'subject', 'classroom', 'timeSlot'])
            ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
            ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
            ->join('users', 'schedules.user_id', '=', 'users.id')
            ->join('classrooms', 'schedules.classroom_id', '=', 'classrooms.id');

        // 5. Terapkan sorting secara dinamis
        if ($sort === 'schedules.day_of_week') {
            // Jika sort berdasarkan hari, tambahkan sort berdasarkan jam sebagai secondary sort
            $query->orderBy($sort, $direction)
                  ->orderBy('time_slots.start_time', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        // 6. Eksekusi query dengan paginasi dan sertakan parameter URL
        $schedules = $query->select('schedules.*')->paginate(15)->withQueryString();
            
        // 7. Kirim data ke view
        return view('admin.schedules.index', compact('schedules', 'sort', 'direction'));
    }

    // ... method lainnya (create, store, edit, update, destroy) tidak berubah ...
    
    public function create()
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();
        $timeSlots = TimeSlot::orderBy('lesson_number')->get();
        return view('admin.schedules.create', compact('teachers', 'subjects', 'classrooms', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day_of_week' => 'required|integer|between:1,7',
            'time_slot_id' => 'required|exists:time_slots,id',
        ]);
        Schedule::create($request->all());
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    public function edit(Schedule $schedule)
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();
        $timeSlots = TimeSlot::orderBy('lesson_number')->get();
        return view('admin.schedules.edit', compact('schedule', 'teachers', 'subjects', 'classrooms', 'timeSlots'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day_of_week' => 'required|integer|between:1,7',
            'time_slot_id' => 'required|exists:time_slots,id',
        ]);
        $schedule->update($request->all());
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}