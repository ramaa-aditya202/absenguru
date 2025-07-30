{{-- resources/views/admin/reports/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Bagian Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Laporan</h3>
                    <form method="GET" action="{{ route('admin.reports.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            {{-- Filter Tanggal Mulai --}}
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="request('start_date')" />
                            </div>
                            {{-- Filter Tanggal Selesai --}}
                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="request('end_date')" />
                            </div>
                            {{-- Filter Guru --}}
                            <div>
                                <x-input-label for="user_id" :value="__('Guru')" />
                                <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Semua Guru</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" @if(request('user_id') == $teacher->id) selected @endif>{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Filter Status --}}
                            <div>
                                <x-input-label :value="__('Status')" />
                                <div class="mt-1 space-y-2">
                                    @php
                                        $statuses = ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpa' => 'Alpa'];
                                        $selectedStatuses = request('status', []);
                                    @endphp
                                    @foreach($statuses as $value => $label)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="status[]" value="{{ $value }}" 
                                                @if(in_array($value, $selectedStatuses)) checked @endif
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            {{-- Tombol Filter --}}
                            <div class="flex items-end space-x-2">
                                <x-primary-button>
                                    {{ __('Terapkan Filter') }}
                                </x-primary-button>
                                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bagian Hasil Laporan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Tabel Rekapitulasi --}}
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rekapitulasi</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-center">
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

                    {{-- Tabel Persentase Kehadiran per Guru --}}
                    @if(!empty($teacherAttendanceStats))
                    <h3 class="text-lg font-medium text-gray-900 mb-2 mt-8">Persentase Kehadiran per Guru</h3>
                    @if(request('sort_stats'))
                        <p class="text-sm text-gray-600 mb-4">
                            Diurutkan berdasarkan: <strong>{{ ucfirst(str_replace('_', ' ', request('sort_stats'))) }}</strong> 
                            ({{ request('sort_direction') == 'asc' ? 'Ascending' : 'Descending' }})
                            <a href="{{ route('admin.reports.index', array_diff_key(request()->all(), ['sort_stats' => '', 'sort_direction' => ''])) }}" class="ml-2 text-blue-600 hover:text-blue-800">Reset Sorting</a>
                        </p>
                    @endif
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">
                                        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['sort_stats' => 'name', 'sort_direction' => (request('sort_stats') == 'name' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center hover:text-blue-600">
                                            Nama Guru
                                            @if(request('sort_stats') == 'name')
                                                @if(request('sort_direction') == 'asc')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['sort_stats' => 'hadir', 'sort_direction' => (request('sort_stats') == 'hadir' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center justify-center hover:text-blue-600">
                                            Hadir
                                            @if(request('sort_stats') == 'hadir')
                                                @if(request('sort_direction') == 'asc')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['sort_stats' => 'sakit', 'sort_direction' => (request('sort_stats') == 'sakit' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center justify-center hover:text-blue-600">
                                            Sakit
                                            @if(request('sort_stats') == 'sakit')
                                                @if(request('sort_direction') == 'asc')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['sort_stats' => 'izin', 'sort_direction' => (request('sort_stats') == 'izin' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center justify-center hover:text-blue-600">
                                            Izin
                                            @if(request('sort_stats') == 'izin')
                                                @if(request('sort_direction') == 'asc')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['sort_stats' => 'alpa', 'sort_direction' => (request('sort_stats') == 'alpa' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center justify-center hover:text-blue-600">
                                            Alpa
                                            @if(request('sort_stats') == 'alpa')
                                                @if(request('sort_direction') == 'asc')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['sort_stats' => 'total', 'sort_direction' => (request('sort_stats') == 'total' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center justify-center hover:text-blue-600">
                                            Total Absensi
                                            @if(request('sort_stats') == 'total')
                                                @if(request('sort_direction') == 'asc')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['sort_stats' => 'percentage', 'sort_direction' => (request('sort_stats') == 'percentage' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center justify-center hover:text-blue-600">
                                            Persentase Kehadiran
                                            @if(request('sort_stats') == 'percentage')
                                                @if(request('sort_direction') == 'asc')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teacherAttendanceStats as $stat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b font-medium">{{ $stat['name'] }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                                {{ $stat['hadir'] }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                                {{ $stat['sakit'] }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                                {{ $stat['izin'] }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                                {{ $stat['alpa'] }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center font-medium">{{ $stat['total'] }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="px-2 py-1 rounded-full text-sm font-medium
                                                @if($stat['percentage'] >= 90) bg-green-100 text-green-800
                                                @elseif($stat['percentage'] >= 80) bg-yellow-100 text-yellow-800
                                                @elseif($stat['percentage'] >= 70) bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $stat['percentage'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- Tabel Detail Laporan --}}
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Absensi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">Tanggal</th>
                                    <th class="py-2 px-4 border-b">Guru</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Keterangan</th>
                                    <th class="py-2 px-4 border-b">Diabsen oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('d F Y') }}</td>
                                        <td class="py-2 px-4 border-b">{{ $attendance->user->name }}</td>
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
                                        <td class="py-2 px-4 border-b">{{ $attendance->remarks }}</td>
                                        <td class="py-2 px-4 border-b">{{ $attendance->recorder->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">
                                            Tidak ada data absensi yang cocok dengan filter yang diterapkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Paginasi --}}
                    <div class="mt-4">
                        {{ $attendances->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            {{-- Grafik Kehadiran --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Grafik Rekapitulasi Kehadiran
                        @if(request('start_date') && request('end_date'))
                            <span class="text-base font-normal text-gray-600">
                                (Periode: {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }})
                            </span>
                        @else
                            <span class="text-base font-normal text-gray-600">(Semua Waktu)</span>
                        @endif
                    </h3>
                    <div style="width: 100%; height: 500px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Chart.js --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData);

            // Cek jika tidak ada data untuk ditampilkan
            if (chartData.labels.length === 0) {
                const canvas = document.getElementById('attendanceChart');
                canvas.style.display = 'none';
                const noDataMessage = document.createElement('p');
                noDataMessage.textContent = 'Tidak ada data untuk ditampilkan pada rentang tanggal yang dipilih.';
                noDataMessage.className = 'text-center text-gray-500 py-16';
                canvas.parentNode.appendChild(noDataMessage);
                return;
            }

            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: chartData.datasets.hadir,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Sakit',
                            data: chartData.datasets.sakit,
                            backgroundColor: 'rgba(255, 206, 86, 0.6)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Izin',
                            data: chartData.datasets.izin,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Alpa',
                            data: chartData.datasets.alpa,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        },
                        x: { stacked: false }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Jumlah Kehadiran per Guru'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
