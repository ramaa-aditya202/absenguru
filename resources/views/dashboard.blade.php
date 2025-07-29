@php
    // Fungsi helper untuk membuat link sorting, hanya didefinisikan jika user adalah admin
    if (Auth::check() && Auth::user()->role == 'admin') {
        function sortable_dashboard_link($column, $title, $currentSort, $currentDirection) {
            $newDirection = ($currentSort == $column && $currentDirection == 'asc') ? 'desc' : 'asc';
            // Gabungkan parameter filter yang ada dengan parameter sorting baru
            $queryParams = array_merge(request()->except('sort', 'direction'), ['sort' => $column, 'direction' => $newDirection]);
            return '<a href="' . route('dashboard', $queryParams) . '" class="inline-flex items-center">
                        '.$title.'
                        <x-sort-icon :sort="$currentSort" :direction="$currentDirection" :field="$column" />
                    </a>';
        }
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::user()->role == 'admin')
                {{ __('Dashboard Absensi Guru') }}
            @else
                {{ __('Dashboard Anda') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(Auth::user()->role == 'admin')
                        {{-- Tampilan untuk Pengguna Admin --}}

                        <!-- Panel Filter Lanjutan -->
                        <div class="mb-6 p-4 bg-gray-50 border rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Jadwal</h3>
                            <form method="GET" action="{{ route('dashboard') }}">
                                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                    <!-- Filter Tanggal -->
                                    <div>
                                        <x-input-label for="date" :value="__('Tanggal')" />
                                        <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="$selectedDateValue" />
                                    </div>
                                    <!-- Filter Jam Pelajaran -->
                                    <div>
                                        <x-input-label for="time_slot_id" :value="__('Jam Pelajaran')" />
                                        <select name="time_slot_id" id="time_slot_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="">Semua Jam</option>
                                            @foreach($timeSlots as $slot)
                                                <option value="{{ $slot->id }}" @if(request('time_slot_id') == $slot->id) selected @endif>Jam ke-{{ $slot->lesson_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Filter Guru -->
                                    <div>
                                        <x-input-label for="user_id" :value="__('Guru')" />
                                        <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="">Semua Guru</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" @if(request('user_id') == $teacher->id) selected @endif>{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Filter Kelas -->
                                    <div>
                                        <x-input-label for="classroom_id" :value="__('Kelas')" />
                                        <select name="classroom_id" id="classroom_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="">Semua Kelas</option>
                                            @foreach($classrooms as $classroom)
                                                <option value="{{ $classroom->id }}" @if(request('classroom_id') == $classroom->id) selected @endif>{{ $classroom->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Tombol Aksi -->
                                    <div class="flex items-end space-x-2">
                                        <x-primary-button class="w-full justify-center">Terapkan</x-primary-button>
                                        <a href="{{ route('dashboard') }}" class="w-full">
                                            <x-secondary-button type="button" class="w-full justify-center">Reset</x-secondary-button>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Tabel Absensi -->
                        <div class="overflow-x-auto">
                           <p class="text-gray-600 mb-4">Menampilkan jadwal untuk: <strong>{{ $currentDate }}</strong></p>
                           <table class="min-w-full bg-white border">
                               <thead class="bg-gray-200">
                                   <tr>
                                       <th class="py-2 px-4 border-b">{!! sortable_dashboard_link('time_slots.start_time', 'Jam', $sort, $direction) !!}</th>
                                       <th class="py-2 px-4 border-b">{!! sortable_dashboard_link('subjects.name', 'Mata Pelajaran', $sort, $direction) !!}</th>
                                       <th class="py-2 px-4 border-b">{!! sortable_dashboard_link('users.name', 'Guru', $sort, $direction) !!}</th>
                                       <th class="py-2 px-4 border-b">{!! sortable_dashboard_link('classrooms.name', 'Kelas', $sort, $direction) !!}</th>
                                       <th class="py-2 px-4 border-b">Status & Keterangan</th>
                                   </tr>
                               </thead>
                               <tbody>
                                   @forelse ($schedules as $schedule)
                                       <tr class="hover:bg-gray-50">
                                           <td class="py-2 px-4 border-b text-center">{{ \Carbon\Carbon::parse($schedule->timeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->timeSlot->end_time)->format('H:i') }}</td>
                                           <td class="py-2 px-4 border-b">{{ $schedule->subject->name }}</td>
                                           <td class="py-2 px-4 border-b">{{ $schedule->user->name }}</td>
                                           <td class="py-2 px-4 border-b text-center">{{ $schedule->classroom->name }}</td>
                                           <td class="py-2 px-4 border-b align-top">
                                                @can('perform-attendance')
                                                    <form action="{{ route('attendance.store') }}" method="POST" class="attendance-form">
                                                        @csrf
                                                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                        <input type="hidden" name="attendance_date" value="{{ $selectedDateValue }}">
                                                        <div class="space-y-2">
                                                            <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                                                <option value="" @if(is_null($schedule->attendance_status)) selected @endif>Pilih Status</option>
                                                                <option value="hadir" @if($schedule->attendance_status == 'hadir') selected @endif>Hadir</option>
                                                                <option value="sakit" @if($schedule->attendance_status == 'sakit') selected @endif>Sakit</option>
                                                                <option value="izin" @if($schedule->attendance_status == 'izin') selected @endif>Izin</option>
                                                                <option value="alpa" @if($schedule->attendance_status == 'alpa') selected @endif>Alpa</option>
                                                            </select>
                                                            <input type="text" name="remarks" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Keterangan" value="{{ $schedule->attendance_remarks }}">
                                                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm transition duration-150 ease-in-out">Simpan</button>
                                                        </div>
                                                    </form>
                                                @else
                                                     {{-- Tampilan jika bukan admin --}}
                                                @endcan
                                           </td>
                                       </tr>
                                   @empty
                                       <tr>
                                           <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">
                                               Tidak ada jadwal yang cocok dengan filter yang diterapkan.
                                           </td>
                                       </tr>
                                   @endforelse
                               </tbody>
                           </table>
                       </div>
                    @else
                        {{-- Tampilan untuk Guru --}}
                        @include('teacher-dashboard')
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hanya jalankan script jika user adalah admin
            if (document.querySelector('.attendance-form')) {
                const forms = document.querySelectorAll('.attendance-form');

                forms.forEach(form => {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const button = form.querySelector('button[type="submit"]');
                        const originalButtonText = button.textContent;
                        const originalButtonClass = button.className;

                        button.textContent = 'Menyimpan...';
                        button.disabled = true;

                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers.get('content-type'));
                            
                            if (!response.ok) {
                                // Jika response tidak OK, coba parse sebagai JSON untuk error details
                                return response.text().then(text => {
                                    console.log('Error response text:', text);
                                    try {
                                        const json = JSON.parse(text);
                                        throw json;
                                    } catch (e) {
                                        throw new Error('Server error: ' + response.status);
                                    }
                                });
                            }
                            
                            // Cek apakah response adalah JSON
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json();
                            } else {
                                // Jika bukan JSON, mungkin redirect atau HTML
                                return response.text().then(text => {
                                    console.log('Non-JSON response:', text);
                                    throw new Error('Server returned non-JSON response');
                                });
                            }
                        })
                        .then(data => {
                            console.log('Success response:', data);
                            // Pastikan response mengandung success: true
                            if (data && data.success === true) {
                                button.textContent = 'Tersimpan';
                                button.className = 'w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded text-sm transition duration-150 ease-in-out';
                                button.style.backgroundColor = '#10b981'; // Hijau yang lebih jelas
                                
                                setTimeout(() => {
                                    button.textContent = originalButtonText;
                                    button.className = originalButtonClass;
                                    button.style.backgroundColor = ''; // Reset inline style
                                    button.disabled = false;
                                }, 2000);
                            } else {
                                throw new Error(data && data.message ? data.message : 'Gagal menyimpan data.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            
                            // Tampilkan error message yang lebih spesifik
                            let errorMessage = 'Gagal!';
                            if (error.message) {
                                errorMessage = error.message.includes('Server') ? 'Server Error!' : 'Gagal!';
                                console.log('Detailed error:', error.message);
                            }
                            
                            button.textContent = errorMessage;
                            button.className = 'w-full px-4 py-2 bg-red-500 text-white rounded text-sm transition duration-150 ease-in-out';

                            setTimeout(() => {
                                button.textContent = originalButtonText;
                                button.className = originalButtonClass;
                                button.disabled = false;
                            }, 3000);
                        });
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>