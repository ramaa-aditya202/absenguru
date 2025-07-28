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

        // Mulai query builder untuk absensi
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
        // Clone query agar filter tetap sama tapi tanpa paginasi
        $summaryQuery = clone $query;
        $summary = $summaryQuery
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Ambil data detail absensi dengan paginasi
        $attendances = $query->latest('attendance_date')->paginate(15);

        // Kirim semua data yang dibutuhkan ke view
        return view('admin.reports.index', compact('teachers', 'attendances', 'summary'));
    }

    /**
     * Menampilkan rekapitulasi kehadiran dalam bentuk grafik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function chart(Request $request)
    {
        // 1. Mulai query builder
        $query = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->select('users.name as teacher_name', 'attendances.status', DB::raw('count(*) as total'))
            ->where('users.role', 'guru'); // Hanya ambil data guru

        // 2. Terapkan filter tanggal jika ada
        if ($request->filled('start_date')) {
            $query->whereDate('attendances.attendance_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('attendances.attendance_date', '<=', $request->end_date);
        }

        // 3. Eksekusi query setelah semua filter ditambahkan
        $attendanceData = $query->groupBy('users.name', 'attendances.status')->get();

        // 4. Proses data mentah menjadi format yang siap digunakan Chart.js
        $teachers = $attendanceData->pluck('teacher_name')->unique()->values();
        
        $chartData = [
            'labels' => $teachers,
            'datasets' => [
                'hadir' => [],
                'sakit' => [],
                'izin' => [],
                'alpa' => [],
            ]
        ];

        // 5. Isi dataset dengan data yang sesuai
        foreach ($teachers as $teacher) {
            $chartData['datasets']['hadir'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'hadir')->first()->total ?? 0;
            $chartData['datasets']['sakit'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'sakit')->first()->total ?? 0;
            $chartData['datasets']['izin'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'izin')->first()->total ?? 0;
            $chartData['datasets']['alpa'][] = $attendanceData->where('teacher_name', $teacher)->where('status', 'alpa')->first()->total ?? 0;
        }

        // 6. Kirim data yang sudah diformat ke view
        return view('admin.reports.chart', compact('chartData'));
    }
}
