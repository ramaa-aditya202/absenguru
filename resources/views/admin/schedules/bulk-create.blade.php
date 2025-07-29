@php
    $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Jadwal Massal per Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter Pemilihan Kelas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Kelas</h3>
                    <form method="GET" action="{{ route('admin.schedules.bulk-create') }}">
                        <div class="flex items-end space-x-2">
                            <div class="flex-grow">
                                <x-input-label for="classroom_id" :value="__('Kelas')" />
                                <select name="classroom_id" id="classroom_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}" @if($selectedClassroom && $selectedClassroom->id == $classroom->id) selected @endif>
                                            {{ $classroom->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-primary-button>Tampilkan Jadwal</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grid Jadwal (hanya tampil jika kelas sudah dipilih) -->
            @if($selectedClassroom)
            <form method="POST" action="{{ route('admin.schedules.bulk-store') }}">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $selectedClassroom->id }}">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Isi Jadwal untuk Kelas: {{ $selectedClassroom->name }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border text-sm">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-2 border-b">Jam Ke-</th>
                                        @foreach($days as $day_number => $day_name)
                                            <th class="py-2 px-2 border-b">{{ $day_name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeSlots as $timeSlot)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-2 border font-semibold text-center">
                                                <p>Jam ke-{{ $timeSlot->lesson_number }}</p>
                                                <p class="text-xs text-gray-500">({{ \Carbon\Carbon::parse($timeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($timeSlot->end_time)->format('H:i') }})</p>
                                            </td>
                                            @foreach($days as $day_number => $day_name)
                                                @php
                                                    $currentSchedule = $schedules->get($day_number . '-' . $timeSlot->id);
                                                @endphp
                                                <td class="p-1 border">
                                                    <div class="space-y-1">
                                                        {{-- Dropdown Guru --}}
                                                        <select name="schedules[{{ $day_number }}][{{ $timeSlot->id }}][user_id]" class="block w-full border-gray-300 rounded-md shadow-sm text-xs">
                                                            <option value="">-- Guru --</option>
                                                            @foreach($teachers as $teacher)
                                                                <option value="{{ $teacher->id }}" @if($currentSchedule && $currentSchedule->user_id == $teacher->id) selected @endif>
                                                                    {{ $teacher->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{-- Dropdown Mapel --}}
                                                        <select name="schedules[{{ $day_number }}][{{ $timeSlot->id }}][subject_id]" class="block w-full border-gray-300 rounded-md shadow-sm text-xs">
                                                            <option value="">-- Mapel --</option>
                                                            @foreach($subjects as $subject)
                                                                <option value="{{ $subject->id }}" @if($currentSchedule && $currentSchedule->subject_id == $subject->id) selected @endif>
                                                                    {{ $subject->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>Simpan Semua Jadwal</x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>