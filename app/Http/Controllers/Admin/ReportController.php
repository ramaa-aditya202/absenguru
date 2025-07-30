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

        // Buat query terpisah untuk rekapitulasi (summary) - TANPA filter status
        $summaryQuery = Attendance::query();
        
        // Terapkan filter tanggal dan guru yang sama seperti detail, tapi TIDAK filter status
        if ($request->filled('start_date')) {
            $summaryQuery->whereDate('attendance_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $summaryQuery->whereDate('attendance_date', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $summaryQuery->where('user_id', $request->user_id);
        }
        
        $summary = $summaryQuery
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Terapkan filter status HANYA untuk detail absensi
        if ($request->filled('status')) {
            $query->whereIn('status', $request->status);
        }

        // Ambil data detail absensi dengan paginasi
        $attendances = $query->latest('attendance_date')->paginate(15);

        // --- Proses data untuk grafik dan statistik guru (TANPA filter status) ---
        // Query terpisah untuk statistik guru yang tidak terpengaruh filter status
        $statsQuery = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->select('users.name as teacher_name', 'attendances.status', DB::raw('count(*) as total'))
            ->where('users.role', 'guru');

        // Terapkan filter tanggal jika ada
        if ($request->filled('start_date')) {
            $statsQuery->whereDate('attendances.attendance_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $statsQuery->whereDate('attendances.attendance_date', '<=', $request->end_date);
        }
        // Terapkan filter guru jika ada
        if ($request->filled('user_id')) {
            $statsQuery->where('attendances.user_id', $request->user_id);
        }
        // TIDAK menerapkan filter status untuk statistik yang akurat

        $attendanceData = $statsQuery->groupBy('users.name', 'attendances.status')->get();
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

        // --- Hitung persentase kehadiran per guru ---
        $teacherAttendanceStats = [];
        foreach ($teacherNames as $teacher) {
            $hadirCount = $attendanceData->where('teacher_name', $teacher)->where('status', 'hadir')->first()->total ?? 0;
            $sakitCount = $attendanceData->where('teacher_name', $teacher)->where('status', 'sakit')->first()->total ?? 0;
            $izinCount = $attendanceData->where('teacher_name', $teacher)->where('status', 'izin')->first()->total ?? 0;
            $alpaCount = $attendanceData->where('teacher_name', $teacher)->where('status', 'alpa')->first()->total ?? 0;
            $totalCount = $hadirCount + $sakitCount + $izinCount + $alpaCount;
            
            $percentage = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100, 1) : 0;
            
            $teacherAttendanceStats[] = [
                'name' => $teacher,
                'hadir' => $hadirCount,
                'sakit' => $sakitCount,
                'izin' => $izinCount,
                'alpa' => $alpaCount,
                'total' => $totalCount,
                'percentage' => $percentage
            ];
        }

        // Sorting untuk tabel persentase kehadiran
        if ($request->filled('sort_stats')) {
            $sortField = $request->sort_stats;
            $sortDirection = $request->get('sort_direction', 'asc');
            
            usort($teacherAttendanceStats, function($a, $b) use ($sortField, $sortDirection) {
                $aValue = $a[$sortField];
                $bValue = $b[$sortField];
                
                if ($sortField === 'name') {
                    $result = strcasecmp($aValue, $bValue);
                } else {
                    $result = $aValue <=> $bValue;
                }
                
                return $sortDirection === 'desc' ? -$result : $result;
            });
        }

        // Sorting untuk tabel persentase kehadiran
        $sortBy = $request->get('sort_stats', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['name', 'hadir', 'sakit', 'izin', 'alpa', 'total', 'percentage'])) {
            usort($teacherAttendanceStats, function($a, $b) use ($sortBy, $sortDirection) {
                if ($sortDirection === 'desc') {
                    return $b[$sortBy] <=> $a[$sortBy];
                }
                return $a[$sortBy] <=> $b[$sortBy];
            });
        }

        // Kirim semua data ke view
        if ($request->ajax() && $request->has('sort_stats')) {
            // Jika ini adalah request AJAX untuk sorting, hanya kirim data tabel
            return response()->json([
                'success' => true,
                'html' => view('admin.reports.partials.teacher-stats-table', compact('teacherAttendanceStats', 'sortBy', 'sortDirection'))->render()
            ]);
        }
        
        return view('admin.reports.index', compact('teachers', 'attendances', 'summary', 'chartData', 'teacherAttendanceStats', 'sortBy', 'sortDirection'));
    }
}
