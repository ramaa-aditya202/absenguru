<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Judul dinamis berdasarkan role --}}
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

                    {{-- Tampilkan view yang sesuai dengan role --}}
                    @if(Auth::user()->role == 'admin')
                        {{-- ================================= --}}
                        {{-- Tampilan untuk Pengguna Admin --}}
                        {{-- ================================= --}}
                        <div class="overflow-x-auto">
                           <p class="text-gray-600 mb-4">Dashboard absensi untuk hari: <strong>{{ $currentDate }}</strong></p>
                           <table class="min-w-full bg-white border">
                               <thead class="bg-gray-200">
                                   <tr>
                                       <th class="py-2 px-4 border-b">Jam Pelajaran</th>
                                       <th class="py-2 px-4 border-b">Mata Pelajaran</th>
                                       <th class="py-2 px-4 border-b">Guru</th>
                                       <th class="py-2 px-4 border-b">Kelas</th>
                                       <th class="py-2 px-4 border-b">Status & Keterangan</th>
                                   </tr>
                               </thead>
                               <tbody>
                                   @forelse ($schedules as $schedule)
                                       <tr class="hover:bg-gray-50">
										   <td class="py-2 px-4 border-b text-center">{{ \Carbon\Carbon::parse($schedule->timeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->timeSlot->end_time)->format('H:i') }}</td>                                           <td class="py-2 px-4 border-b">{{ $schedule->subject->name }}</td>
                                           <td class="py-2 px-4 border-b">{{ $schedule->user->name }}</td>
                                           <td class="py-2 px-4 border-b text-center">{{ $schedule->classroom->name }}</td>
                                           <td class="py-2 px-4 border-b align-top">
                                                {{-- Cek hak akses menggunakan Gate 'perform-attendance' --}}
                                                @can('perform-attendance')
                                                    <form action="{{ route('attendance.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                        <div class="space-y-2">
                                                            {{-- Dropdown Status --}}
                                                            <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                                                <option value="" @if(is_null($schedule->attendance_status)) selected @endif>Pilih Status</option>
                                                                <option value="hadir" @if($schedule->attendance_status == 'hadir') selected @endif>Hadir</option>
                                                                <option value="sakit" @if($schedule->attendance_status == 'sakit') selected @endif>Sakit</option>
                                                                <option value="izin" @if($schedule->attendance_status == 'izin') selected @endif>Izin</option>
                                                                <option value="alpa" @if($schedule->attendance_status == 'alpa') selected @endif>Alpa</option>
                                                            </select>
                                                            {{-- Input Keterangan --}}
                                                            <input type="text" name="remarks" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" placeholder="Keterangan (opsional)">
                                                            {{-- Tombol Simpan --}}
                                                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Simpan</button>
                                                        </div>
                                                    </form>
                                                @else
                                                    {{-- Tampilan jika bukan admin --}}
                                                     @if($schedule->attendance_status)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            @switch($schedule->attendance_status)
                                                                @case('hadir') bg-green-100 text-green-800 @break
                                                                @case('sakit') bg-yellow-100 text-yellow-800 @break
                                                                @case('izin') bg-blue-100 text-blue-800 @break
                                                                @case('alpa') bg-red-100 text-red-800 @break
                                                            @endswitch">
                                                            {{ ucfirst($schedule->attendance_status) }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500 text-sm">Belum Diabsen</span>
                                                    @endif
                                                @endcan
                                           </td>
                                       </tr>
                                   @empty
                                       <tr>
                                           <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">
                                               Tidak ada jadwal pelajaran untuk hari ini.
                                           </td>
                                       </tr>
                                   @endforelse
                               </tbody>
                           </table>
                       </div>
                    @else
                        {{-- ================================= --}}
                        {{-- Tampilan untuk Pengguna Guru --}}
                        {{-- ================================= --}}
                        @include('teacher-dashboard')
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
