{{-- resources/views/admin/reports/chart.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grafik Rekapitulasi Kehadiran Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Form Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Grafik</h3>
                    <form method="GET" action="{{ route('admin.reports.chart') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="request('start_date')" />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="request('end_date')" />
                            </div>
                            <div class="flex items-end">
                                <x-primary-button>
                                    {{ __('Terapkan Filter') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Grafik --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Rekapitulasi Kehadiran
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
                const ctx = canvas.getContext('2d');
                canvas.style.display = 'none'; // Sembunyikan canvas
                
                const noDataMessage = document.createElement('p');
                noDataMessage.textContent = 'Tidak ada data untuk ditampilkan pada rentang tanggal yang dipilih.';
                noDataMessage.className = 'text-center text-gray-500 py-16';
                canvas.parentNode.appendChild(noDataMessage); // Tampilkan pesan
                return; // Hentikan eksekusi script
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
