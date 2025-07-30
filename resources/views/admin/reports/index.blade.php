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
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-center" id="summary-container">
                        @include('admin.reports.partials.summary-cards', ['summary' => $summary])
                    </div>

                    {{-- Tabel Persentase Kehadiran per Guru --}}
                    <div id="teacher-stats-container">
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
                                    <th class="py-2 px-4 border-b text-left cursor-pointer hover:bg-gray-300 transition-colors" data-sort="name">
                                        <div class="flex items-center">
                                            Nama Guru
                                            <span class="sort-icon ml-1">↕️</span>
                                        </div>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center cursor-pointer hover:bg-gray-300 transition-colors" data-sort="hadir">
                                        <div class="flex items-center justify-center">
                                            Hadir
                                            <span class="sort-icon ml-1">↕️</span>
                                        </div>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center cursor-pointer hover:bg-gray-300 transition-colors" data-sort="sakit">
                                        <div class="flex items-center justify-center">
                                            Sakit
                                            <span class="sort-icon ml-1">↕️</span>
                                        </div>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center cursor-pointer hover:bg-gray-300 transition-colors" data-sort="izin">
                                        <div class="flex items-center justify-center">
                                            Izin
                                            <span class="sort-icon ml-1">↕️</span>
                                        </div>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center cursor-pointer hover:bg-gray-300 transition-colors" data-sort="alpa">
                                        <div class="flex items-center justify-center">
                                            Alpa
                                            <span class="sort-icon ml-1">↕️</span>
                                        </div>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center cursor-pointer hover:bg-gray-300 transition-colors" data-sort="total">
                                        <div class="flex items-center justify-center">
                                            Total Absensi
                                            <span class="sort-icon ml-1">↕️</span>
                                        </div>
                                    </th>
                                    <th class="py-2 px-4 border-b text-center cursor-pointer hover:bg-gray-300 transition-colors" data-sort="percentage">
                                        <div class="flex items-center justify-center">
                                            Persentase Kehadiran
                                            <span class="sort-icon ml-1">↕️</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="teacherStatsBody">
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
                    </div>

                    {{-- Tabel Detail Laporan --}}
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Absensi</h3>
                    <div class="overflow-x-auto" id="attendance-table-container">
                        @include('admin.reports.partials.attendance-table', ['attendances' => $attendances])
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

    {{-- Script untuk Chart.js dan Table Sorting --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData);
            const teacherStatsData = @json($teacherAttendanceStats);

            // Chart.js code
            if (chartData.labels.length === 0) {
                const canvas = document.getElementById('attendanceChart');
                canvas.style.display = 'none';
                const noDataMessage = document.createElement('p');
                noDataMessage.textContent = 'Tidak ada data untuk ditampilkan pada rentang tanggal yang dipilih.';
                noDataMessage.className = 'text-center text-gray-500 py-16';
                canvas.parentNode.appendChild(noDataMessage);
            } else {
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
            }

            // Table sorting functionality
            let currentSort = { field: null, direction: 'asc' };
            let originalData = [...teacherStatsData];

            function getPercentageClass(percentage) {
                if (percentage >= 90) return 'bg-green-100 text-green-800';
                if (percentage >= 80) return 'bg-yellow-100 text-yellow-800';
                if (percentage >= 70) return 'bg-orange-100 text-orange-800';
                return 'bg-red-100 text-red-800';
            }

            function renderTable(data) {
                const tbody = document.getElementById('teacherStatsBody');
                if (!tbody) return;

                tbody.innerHTML = data.map(stat => `
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b font-medium">${stat.name}</td>
                        <td class="py-2 px-4 border-b text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                ${stat.hadir}
                            </span>
                        </td>
                        <td class="py-2 px-4 border-b text-center">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                ${stat.sakit}
                            </span>
                        </td>
                        <td class="py-2 px-4 border-b text-center">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                ${stat.izin}
                            </span>
                        </td>
                        <td class="py-2 px-4 border-b text-center">
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                ${stat.alpa}
                            </span>
                        </td>
                        <td class="py-2 px-4 border-b text-center">${stat.total}</td>
                        <td class="py-2 px-4 border-b text-center">
                            <span class="px-2 py-1 rounded-full text-sm font-medium ${getPercentageClass(stat.percentage)}">
                                ${stat.percentage}%
                            </span>
                        </td>
                    </tr>
                `).join('');
            }

            function sortTable(field) {
                if (currentSort.field === field) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.field = field;
                    currentSort.direction = 'asc';
                }

                const sortedData = [...originalData].sort((a, b) => {
                    let aValue = a[field];
                    let bValue = b[field];

                    if (field === 'name') {
                        aValue = aValue.toLowerCase();
                        bValue = bValue.toLowerCase();
                        return currentSort.direction === 'asc' 
                            ? aValue.localeCompare(bValue)
                            : bValue.localeCompare(aValue);
                    } else {
                        return currentSort.direction === 'asc' 
                            ? aValue - bValue 
                            : bValue - aValue;
                    }
                });

                renderTable(sortedData);
                updateSortIcons();
            }

            function updateSortIcons() {
                document.querySelectorAll('[data-sort]').forEach(header => {
                    const field = header.getAttribute('data-sort');
                    const icon = header.querySelector('.sort-icon');
                    
                    if (currentSort.field === field) {
                        icon.textContent = currentSort.direction === 'asc' ? '↑' : '↓';
                        header.classList.add('bg-gray-300');
                    } else {
                        icon.textContent = '↕️';
                        header.classList.remove('bg-gray-300');
                    }
                });
            }

            // Add click event listeners to sortable headers
            document.querySelectorAll('[data-sort]').forEach(header => {
                header.addEventListener('click', function() {
                    const field = this.getAttribute('data-sort');
                    sortTable(field);
                    
                    // Scroll to table smoothly
                    document.getElementById('teacherStatsTable').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });

            // AJAX Filter and Pagination functionality
            function loadReportData(url, scrollToElement = null) {
                // Show loading state
                showLoadingState();

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update attendance table
                        if (data.data.attendances_html) {
                            document.getElementById('attendance-table-container').innerHTML = data.data.attendances_html;
                            setupPaginationListeners();
                        }

                        // Update summary
                        if (data.data.summary_html) {
                            document.getElementById('summary-container').innerHTML = data.data.summary_html;
                        }

                        // Update chart
                        if (data.data.chartData && window.attendanceChart) {
                            updateChart(data.data.chartData);
                        }

                        // Update teacher stats table
                        if (data.data.teacherAttendanceStats) {
                            updateTeacherStats(data.data.teacherAttendanceStats);
                        }

                        // Scroll to element if specified
                        if (scrollToElement) {
                            document.getElementById(scrollToElement).scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    // Hide loading state
                    hideLoadingState();
                });
            }

            function showLoadingState() {
                // Add loading overlay or spinner
                const containers = ['attendance-table-container', 'summary-container', 'teacher-stats-container'];
                containers.forEach(containerId => {
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.style.opacity = '0.5';
                        container.style.pointerEvents = 'none';
                    }
                });
            }

            function hideLoadingState() {
                // Remove loading overlay or spinner
                const containers = ['attendance-table-container', 'summary-container', 'teacher-stats-container'];
                containers.forEach(containerId => {
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                    }
                });
            }

            function updateChart(chartData) {
                if (window.attendanceChart) {
                    window.attendanceChart.data.labels = chartData.labels;
                    window.attendanceChart.data.datasets[0].data = chartData.datasets.hadir;
                    window.attendanceChart.data.datasets[1].data = chartData.datasets.sakit;
                    window.attendanceChart.data.datasets[2].data = chartData.datasets.izin;
                    window.attendanceChart.data.datasets[3].data = chartData.datasets.alpa;
                    window.attendanceChart.update();
                }
            }

            function updateTeacherStats(teacherStats) {
                // Update the original data for sorting
                originalData = [...teacherStats];
                // Re-render the table with new data
                renderTable(teacherStats);
            }

            function setupPaginationListeners() {
                // Setup pagination links to use AJAX
                document.querySelectorAll('#pagination-container a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.href;
                        loadReportData(url, 'attendance-table-container');
                    });
                });
            }

            function setupFilterListeners() {
                // Setup form submission to use AJAX
                const filterForm = document.querySelector('form[action*="reports"]');
                if (filterForm) {
                    filterForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        const url = new URL(this.action);
                        
                        // Clear existing params
                        url.search = '';
                        
                        // Add form data to URL
                        formData.forEach((value, key) => {
                            if (value) {
                                url.searchParams.append(key, value);
                            }
                        });
                        
                        loadReportData(url.toString());
                    });

                    // Setup checkbox change events for real-time filtering
                    const statusCheckboxes = filterForm.querySelectorAll('input[name="status[]"]');
                    let filterTimeout;
                    statusCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            // Small delay to allow multiple checkboxes to be changed
                            clearTimeout(filterTimeout);
                            filterTimeout = setTimeout(() => {
                                filterForm.dispatchEvent(new Event('submit'));
                            }, 300);
                        });
                    });

                    // Setup other input change events
                    const otherInputs = filterForm.querySelectorAll('input[type="date"], select');
                    otherInputs.forEach(input => {
                        input.addEventListener('change', function() {
                            filterForm.dispatchEvent(new Event('submit'));
                        });
                    });
                }
            }

            // Initialize listeners
            setupPaginationListeners();
            setupFilterListeners();

            // Store chart reference globally for updates
            if (typeof attendanceChart !== 'undefined') {
                window.attendanceChart = attendanceChart;
            }
        });
    </script>
    @endpush
</x-app-layout>
