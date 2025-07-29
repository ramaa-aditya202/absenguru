@php
    // Array helper untuk nama hari
    $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    // Fungsi helper untuk membuat link sorting
    function sortable_link($column, $title, $currentSort, $currentDirection) {
        $newDirection = ($currentSort == $column && $currentDirection == 'asc') ? 'desc' : 'asc';
        $icon = '';
        if ($currentSort == $column) {
            $icon = $currentDirection == 'asc' ? '&#9650;' : '&#9660;'; // Panah atas atau bawah
        }
        return '<a href="' . route('admin.schedules.index', ['sort' => $column, 'direction' => $newDirection]) . '">' . $title . ' ' . $icon . '</a>';
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manajemen Jadwal') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- ======================================================= --}}
                    {{-- BAGIAN YANG DIPERBARUI DENGAN DUA TOMBOL BARU --}}
                    {{-- ======================================================= --}}
                    <div class="mb-4 flex space-x-2">
                        <a href="{{ route('admin.schedules.create') }}">
                            <x-primary-button>{{ __('Tambah Jadwal Satuan') }}</x-primary-button>
                        </a>
                        {{-- TOMBOL BARU --}}
                        <a href="{{ route('admin.schedules.bulk-create') }}">
                            <x-secondary-button>{{ __('Input Jadwal Massal per Kelas') }}</x-secondary-button>
                        </a>
                    </div>
                    
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">{{ session('success') }}</div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">{!! sortable_link('schedules.day_of_week', 'Hari', $sort, $direction) !!}</th>
                                    <th class="py-2 px-4 border-b">{!! sortable_link('time_slots.start_time', 'Jam', $sort, $direction) !!}</th>
                                    <th class="py-2 px-4 border-b">{!! sortable_link('subjects.name', 'Mata Pelajaran', $sort, $direction) !!}</th>
                                    <th class="py-2 px-4 border-b">{!! sortable_link('users.name', 'Guru', $sort, $direction) !!}</th>
                                    <th class="py-2 px-4 border-b">{!! sortable_link('classrooms.name', 'Kelas', $sort, $direction) !!}</th>
                                    <th class="py-2 px-4 border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $schedule)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $days[$schedule->day_of_week] ?? 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($schedule->timeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->timeSlot->end_time)->format('H:i') }}</td>
                                        <td class="py-2 px-4 border-b">{{ $schedule->subject->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $schedule->user->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $schedule->classroom->name }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('admin.schedules.edit', $schedule) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">Edit</a>
                                                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-4 border-b text-center text-gray-500">Tidak ada data jadwal.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">{{ $schedules->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
