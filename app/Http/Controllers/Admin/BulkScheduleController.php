<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkScheduleController extends Controller
{
    /**
     * Menampilkan halaman input jadwal massal.
     */
    public function show(Request $request)
    {
        // Ambil semua data master yang dibutuhkan untuk dropdown
        $classrooms = Classroom::orderBy('name')->get();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $timeSlots = TimeSlot::orderBy('lesson_number')->get();

        $schedules = collect();
        $selectedClassroom = null;

        // Jika ada kelas yang dipilih dari filter, ambil jadwal yang sudah ada
        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::findOrFail($request->classroom_id);
            $schedules = Schedule::where('classroom_id', $selectedClassroom->id)
                ->get()
                ->keyBy(function ($item) {
                    // Buat key unik: 'hari-jam_ke' (e.g., '1-3')
                    return $item->day_of_week . '-' . $item->time_slot_id;
                });
        }

        return view('admin.schedules.bulk-create', compact(
            'classrooms',
            'teachers',
            'subjects',
            'timeSlots',
            'schedules',
            'selectedClassroom'
        ));
    }

    /**
     * Menyimpan data jadwal massal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'schedules' => 'required|array',
        ]);

        $classroomId = $request->classroom_id;
        $schedulesInput = $request->schedules;

        // Gunakan transaction untuk memastikan semua query berhasil atau tidak sama sekali
        DB::transaction(function () use ($classroomId, $schedulesInput) {
            // 1. Ambil jadwal yang sudah ada untuk kelas ini
            $existingSchedules = Schedule::where('classroom_id', $classroomId)->get();
            
            // 2. Cek apakah ada attendance yang terkait dengan jadwal lama
            $scheduleIdsWithAttendance = DB::table('attendances')
                ->whereIn('schedule_id', $existingSchedules->pluck('id'))
                ->distinct()
                ->pluck('schedule_id')
                ->toArray();

            // 3. Hapus hanya jadwal yang TIDAK memiliki data attendance
            $schedulesToDelete = $existingSchedules->filter(function ($schedule) use ($scheduleIdsWithAttendance) {
                return !in_array($schedule->id, $scheduleIdsWithAttendance);
            });
            
            if ($schedulesToDelete->isNotEmpty()) {
                Schedule::whereIn('id', $schedulesToDelete->pluck('id'))->delete();
            }

            // 4. Loop melalui input dan update/buat jadwal baru
            foreach ($schedulesInput as $day => $timeSlots) {
                foreach ($timeSlots as $timeSlotId => $data) {
                    // Hanya simpan jika guru dan mapel dipilih
                    if (!empty($data['user_id']) && !empty($data['subject_id'])) {
                        
                        // Cari jadwal yang sudah ada berdasarkan kelas, hari, dan jam
                        $existingSchedule = Schedule::where([
                            'classroom_id' => $classroomId,
                            'day_of_week' => $day,
                            'time_slot_id' => $timeSlotId,
                        ])->first();

                        if ($existingSchedule) {
                            // Update jadwal yang sudah ada
                            $existingSchedule->update([
                                'user_id' => $data['user_id'],
                                'subject_id' => $data['subject_id'],
                            ]);
                        } else {
                            // Buat jadwal baru
                            Schedule::create([
                                'classroom_id' => $classroomId,
                                'day_of_week' => $day,
                                'time_slot_id' => $timeSlotId,
                                'user_id' => $data['user_id'],
                                'subject_id' => $data['subject_id'],
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()
            ->route('admin.schedules.bulk-create', ['classroom_id' => $classroomId])
            ->with('success', 'Jadwal berhasil disimpan secara massal! Data absensi yang sudah ada tetap terjaga.');
    }
}