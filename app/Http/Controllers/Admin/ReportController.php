<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman laporan dan hasilnya berdasarkan filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua guru untuk dropdown filter
        $teachers = User::where('role', 'guru')->orderBy('name')->get();

        // Mulai query builder untuk absensi (tabel dan summary)
        $query = Attendance::query()->with(['user', 'recorder']);

        // Terapkan filter tanggal jika ada
        if ($request->filled('start_date')) {
            $query->whereDate('attendance_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('attendance_date', '<=', $request->end_date);
        }

        // Terapkan filter guru jika ada
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Buat query terpisah untuk rekapitulasi (summary)
        $summaryQuery = clone $query;
        $summary = $summaryQuery
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Ambil data detail absensi dengan paginasi
        $attendances = $query->latest('attendance_date')->paginate(15);

        // --- Proses data untuk grafik (Chart.js) ---
        $chartQuery = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->select('users.name as teacher_name', 'attendances.status', DB::raw('count(*) as total'))
            ->where('users.role', 'guru');

        // Terapkan filter tanggal jika ada
        if ($request->filled('start_date')) {
            $chartQuery->whereDate('attendances.attendance_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $chartQuery->whereDate('attendances.attendance_date', '<=', $request->end_date);
        }
        // Terapkan filter guru jika ada
        if ($request->filled('user_id')) {
            $chartQuery->where('attendances.user_id', $request->user_id);
        }

        $attendanceData = $chartQuery->groupBy('users.name', 'attendances.status')->get();
        $teacherNames = $attendanceData->pluck('teacher_name')->unique()->values();
        $chartData = [
            'labels' => $teacherNames,
            'datasets' => [
                'hadir' => [],
                'sakit' => [],
                'izin' => [],
                'alpa' => [],
            ]
        ];
        foreach ($teacherNames as $teacher) {
            $chartData['datasets']['hadir'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'hadir')->first()->total ?? 0;
            $chartData['datasets']['sakit'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'sakit')->first()->total ?? 0;
            $chartData['datasets']['izin'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'izin')->first()->total ?? 0;
            $chartData['datasets']['alpa'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'alpa')->first()->total ?? 0;
        }

        // Kirim semua data ke view
        return view('admin.reports.index', compact('teachers', 'attendances', 'summary', 'chartData'));
    }
}
