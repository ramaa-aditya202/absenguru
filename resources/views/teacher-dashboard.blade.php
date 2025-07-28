{{-- resources/views/teacher-dashboard.blade.php --}}
@php
    $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
@endphp

<div class="space-y-6">
    <!-- Welcome Message -->
    <div>
        <h2 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="text-gray-600">Berikut adalah ringkasan jadwal dan kehadiran Anda.</p>
    </div>

    <!-- Rekapitulasi Kehadiran -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Rekapitulasi Kehadiran Anda</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="p-4 bg-green-100 rounded-lg">
                <div class="text-3xl font-bold text-green-800">{{ $summary['hadir'] ?? 0 }}</div>
                <div class="text-sm text-green-600">Hadir</div>
            </div>
            <div class="p-4 bg-yellow-100 rounded-lg">
                <div class="text-3xl font-bold text-yellow-800">{{ $summary['sakit'] ?? 0 }}</div>
                <div class="text-sm text-yellow-600">Sakit</div>
            </div>
            <div class="p-4 bg-blue-100 rounded-lg">
                <div class="text-3xl font-bold text-blue-800">{{ $summary['izin'] ?? 0 }}</div>
                <div class="text-sm text-blue-600">Izin</div>
            </div>
            <div class="p-4 bg-red-100 rounded-lg">
                <div class="text-3xl font-bold text-red-800">{{ $summary['alpa'] ?? 0 }}</div>
                <div class="text-sm text-red-600">Alpa</div>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar Seminggu -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Jadwal Mengajar Anda Minggu Ini</h3>
        <div class="bg-white border rounded-lg p-4">
            @forelse($schedules as $day => $daySchedules)
                <div class="mb-4">
                    <p class="font-bold text-gray-700">{{ $days[$day] }}</p>
                    <ul class="list-disc list-inside ml-4 text-gray-600">
                        @foreach($daySchedules as $schedule)
                            <li>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>:
                                {{ $schedule->subject->name }} di kelas {{ $schedule->classroom->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-gray-500">Anda tidak memiliki jadwal mengajar.</p>
            @endforelse
        </div>
    </div>

    <!-- Riwayat Absensi Terakhir -->
    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">10 Riwayat Kehadiran Terakhir</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border-b">Tanggal</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('d F Y') }}</td>
                            <td class="py-2 px-4 border-b text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($attendance->status)
                                        @case('hadir') bg-green-100 text-green-800 @break
                                        @case('sakit') bg-yellow-100 text-yellow-800 @break
                                        @case('izin') bg-blue-100 text-blue-800 @break
                                        @case('alpa') bg-red-100 text-red-800 @break
                                    @endswitch">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b">{{ $attendance->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 px-4 border-b text-center text-gray-500">
                                Belum ada riwayat kehadiran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>