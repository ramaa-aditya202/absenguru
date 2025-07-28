<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal dengan paginasi.
     */
    public function index()
    {
        // PERBAIKAN: Gunakan join untuk sorting dan paginate untuk paginasi
        $schedules = Schedule::with(['user', 'subject', 'classroom', 'timeSlot'])
            ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
            ->orderBy('schedules.day_of_week', 'asc')
            ->orderBy('time_slots.start_time', 'asc')
            ->select('schedules.*') // Hindari ambiguitas kolom
            ->paginate(10); // Gunakan paginate() bukan get()
            
        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Menampilkan form untuk membuat jadwal baru.
     */
    public function create()
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();
        $timeSlots = TimeSlot::orderBy('lesson_number')->get();

        return view('admin.schedules.create', compact('teachers', 'subjects', 'classrooms', 'timeSlots'));
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
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

    /**
     * Menampilkan form untuk mengedit jadwal.
     */
    public function edit(Schedule $schedule)
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();
        $timeSlots = TimeSlot::orderBy('lesson_number')->get();

        return view('admin.schedules.edit', compact('schedule', 'teachers', 'subjects', 'classrooms', 'timeSlots'));
    }

    /**
     * Memperbarui data jadwal di database.
     */
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

    /**
     * Menghapus data jadwal dari database.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}