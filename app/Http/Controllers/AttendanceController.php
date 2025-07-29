<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan dashboard yang sesuai dengan role pengguna dan filter.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Tampilan untuk Admin
        if ($user->role === 'admin') {
            // 1. Ambil semua data master untuk dropdown filter
            $filterData = [
                'teachers' => User::where('role', 'guru')->orderBy('name')->get(),
                'subjects' => Subject::orderBy('name')->get(),
                'classrooms' => Classroom::orderBy('name')->get(),
                'timeSlots' => TimeSlot::orderBy('lesson_number')->get(),
            ];

            // 2. Tentukan tanggal yang akan ditampilkan. Default ke hari ini.
            $selectedDate = Carbon::parse($request->input('date', 'today'));
            $dayOfWeek = $selectedDate->dayOfWeekIso;

            // 3. Logika sorting dinamis
            $sort = $request->get('sort', 'time_slots.start_time');
            $direction = $request->get('direction', 'asc');
            $allowedSorts = ['time_slots.start_time', 'subjects.name', 'users.name', 'classrooms.name'];
            if (!in_array($sort, $allowedSorts)) {
                $sort = 'time_slots.start_time';
            }

            // 4. Bangun query dasar
            $query = Schedule::with(['user', 'subject', 'classroom', 'timeSlot'])
                ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
                ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
                ->join('users', 'schedules.user_id', '=', 'users.id')
                ->join('classrooms', 'schedules.classroom_id', '=', 'classrooms.id')
                ->where('schedules.day_of_week', $dayOfWeek);

            // 5. Terapkan filter tambahan secara kondisional
            if ($request->filled('time_slot_id')) {
                $query->where('schedules.time_slot_id', $request->time_slot_id);
            }
            if ($request->filled('subject_id')) {
                $query->where('schedules.subject_id', $request->subject_id);
            }
            if ($request->filled('user_id')) {
                $query->where('schedules.user_id', $request->user_id);
            }
            if ($request->filled('classroom_id')) {
                $query->where('schedules.classroom_id', $request->classroom_id);
            }

            // 6. Terapkan sorting dan eksekusi query
            $schedules = $query->orderBy($sort, $direction)
                ->select('schedules.*')
                ->get();
            
            // 7. Ambil data absensi untuk tanggal yang dipilih
            $attendances = Attendance::whereDate('attendance_date', $selectedDate)->get();

            // 8. Gabungkan data absensi ke dalam jadwal
            $schedules->map(function ($schedule) use ($attendances) {
                $attendanceRecord = $attendances->firstWhere('schedule_id', $schedule->id);
                $schedule->attendance_status = $attendanceRecord->status ?? null;
                $schedule->attendance_remarks = $attendanceRecord->remarks ?? null;
                return $schedule;
            });

            // 9. Kirim semua data yang dibutuhkan ke view
            return view('dashboard', array_merge($filterData, [
                'schedules' => $schedules,
                'currentDate' => $selectedDate->translatedFormat('l, d F Y'),
                'selectedDateValue' => $selectedDate->format('Y-m-d'),
                'sort' => $sort,
                'direction' => $direction,
            ]));
        }

        // Tampilan untuk Guru (tidak berubah)
        if ($user->role === 'guru') {
            $schedules = Schedule::with(['subject', 'classroom', 'timeSlot'])
                ->where('user_id', $user->id)
                ->get()
                ->sortBy(['day_of_week', 'timeSlot.start_time']);
            $summary = Attendance::where('user_id', $user->id)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->get()->pluck('total', 'status');
            $history = Attendance::where('user_id', $user->id)
                ->latest('attendance_date')->take(10)->get();
            return view('dashboard', compact('schedules', 'summary', 'history'));
        }

        return view('dashboard');
    }

    /**
     * Menyimpan atau memperbarui data absensi.
     * Dapat merespons permintaan form biasa atau permintaan AJAX (JSON).
     */
    public function store(Request $request)
    {
    	$this->authorize('perform-attendance');
    
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'status' => 'required|in:hadir,sakit,izin,alpa',
            'remarks' => 'nullable|string|max:255',
            'attendance_date' => 'required|date_format:Y-m-d',
        ]);

        $schedule = Schedule::findOrFail($validated['schedule_id']);

        $attendance = Attendance::updateOrCreate(
            [
                'attendance_date' => $validated['attendance_date'],
                'schedule_id' => $validated['schedule_id'],
            ],
            [
                'user_id' => $schedule->user_id,
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'recorded_by' => Auth::id(),
            ]
        );

        // Logika untuk merespons AJAX
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan!',
                'attendance' => $attendance,
            ]);
        }

        // Fallback untuk non-AJAX
        return back()->with('success', 'Absensi berhasil disimpan!');
    }
}
