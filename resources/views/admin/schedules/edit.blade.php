{{-- resources/views/admin/schedules/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Jadwal') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Guru -->
                            <div>
                                <x-input-label for="user_id" :value="__('Guru')" />
                                <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Guru</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" @if(old('user_id', $schedule->user_id) == $teacher->id) selected @endif>{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <!-- Mata Pelajaran -->
                            <div>
                                <x-input-label for="subject_id" :value="__('Mata Pelajaran')" />
                                <select name="subject_id" id="subject_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" @if(old('subject_id', $schedule->subject_id) == $subject->id) selected @endif>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
                            </div>

                            <!-- Kelas -->
                            <div>
                                <x-input-label for="classroom_id" :value="__('Kelas')" />
                                <select name="classroom_id" id="classroom_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}" @if(old('classroom_id', $schedule->classroom_id) == $classroom->id) selected @endif>{{ $classroom->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('classroom_id')" class="mt-2" />
                            </div>

                            <!-- Hari -->
                            <div>
                                <x-input-label for="day_of_week" :value="__('Hari')" />
                                <select name="day_of_week" id="day_of_week" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Hari</option>
                                    <option value="1" @if(old('day_of_week', $schedule->day_of_week) == 1) selected @endif>Senin</option>
                                    <option value="2" @if(old('day_of_week', $schedule->day_of_week) == 2) selected @endif>Selasa</option>
                                    <option value="3" @if(old('day_of_week', $schedule->day_of_week) == 3) selected @endif>Rabu</option>
                                    <option value="4" @if(old('day_of_week', $schedule->day_of_week) == 4) selected @endif>Kamis</option>
                                    <option value="5" @if(old('day_of_week', $schedule->day_of_week) == 5) selected @endif>Jumat</option>
                                    <option value="6" @if(old('day_of_week', $schedule->day_of_week) == 6) selected @endif>Sabtu</option>
                                    <option value="7" @if(old('day_of_week', $schedule->day_of_week) == 7) selected @endif>Minggu</option>
                                </select>
                                <x-input-error :messages="$errors->get('day_of_week')" class="mt-2" />
                            </div>

							<div>
    							<x-input-label for="time_slot_id" :value="__('Jam Pelajaran')" />
    							<select name="time_slot_id" id="time_slot_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        							<option value="">Pilih Jam Pelajaran</option>
        							@foreach($timeSlots as $slot)
            							<option value="{{ $slot->id }}" @if(old('time_slot_id', $schedule->time_slot_id) == $slot->id) selected @endif>
                							Jam ke-{{ $slot->lesson_number }} ({{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }})
           							</option>
        							@endforeach
    							</select>
    							<x-input-error :messages="$errors->get('time_slot_id')" class="mt-2" />
							</div>
                                
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Batal') }}</a>
                            <x-primary-button>{{ __('Perbarui') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>