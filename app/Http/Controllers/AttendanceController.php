<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttendanceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan dashboard yang sesuai dengan role pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        // Tampilan untuk Admin
        if ($user->role === 'admin') {
            $today = Carbon::now()->dayOfWeekIso;

            // =================================================================
            // PERBAIKAN DI SINI: Gunakan JOIN untuk sorting berdasarkan TimeSlot
            // =================================================================
            $schedulesToday = Schedule::with(['user', 'subject', 'classroom', 'timeSlot'])
                ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
                ->where('schedules.day_of_week', $today)
                ->orderBy('time_slots.start_time', 'asc')
                ->select('schedules.*') // Penting: Pilih semua kolom dari schedules
                ->get();
            
            // Ambil data absensi hari ini
            $attendancesToday = Attendance::where('attendance_date', Carbon::today())->get();

            // Gabungkan data absensi (status & keterangan) ke dalam jadwal
            $schedulesToday->map(function ($schedule) use ($attendancesToday) {
                $attendanceRecord = $attendancesToday->firstWhere('schedule_id', $schedule->id);
                $schedule->attendance_status = $attendanceRecord->status ?? null;
                $schedule->attendance_remarks = $attendanceRecord->remarks ?? null;
                return $schedule;
            });

            return view('dashboard', [
                'schedules' => $schedulesToday,
                'currentDate' => Carbon::now()->translatedFormat('l, d F Y')
            ]);
        }

        // Tampilan untuk Guru
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

        // Fallback
        return view('dashboard');
    }

    /**
     * Menyimpan atau memperbarui data absensi.
     */
    public function store(Request $request)
    {
    	$this->authorize('perform-attendance');
    
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'status' => 'required|in:hadir,sakit,izin,alpa',
            'remarks' => 'nullable|string|max:255',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        Attendance::updateOrCreate(
            [
                'attendance_date' => Carbon::today(),
                'schedule_id' => $request->schedule_id,
            ],
            [
                'user_id' => $schedule->user_id,
                'status' => $request->status,
                'remarks' => $request->remarks,
                'recorded_by' => Auth::id(),
            ]
        );

        return back()->with('success', 'Absensi berhasil disimpan!');
    }
}