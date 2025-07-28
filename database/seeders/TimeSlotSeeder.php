<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        TimeSlot::create(['lesson_number' => 1, 'start_time' => '08:00', 'end_time' => '08:35']);
        TimeSlot::create(['lesson_number' => 2, 'start_time' => '08:35', 'end_time' => '09:10']);
        TimeSlot::create(['lesson_number' => 3, 'start_time' => '09:10', 'end_time' => '09:45']);
        TimeSlot::create(['lesson_number' => 4, 'start_time' => '09:45', 'end_time' => '10:20']); // Ada istirahat
        TimeSlot::create(['lesson_number' => 5, 'start_time' => '10:20', 'end_time' => '10:55']);
        TimeSlot::create(['lesson_number' => 6, 'start_time' => '12:30', 'end_time' => '13:05']);
        TimeSlot::create(['lesson_number' => 7, 'start_time' => '13:05', 'end_time' => '13:40']); // Ada istirahat
        TimeSlot::create(['lesson_number' => 8, 'start_time' => '13:40', 'end_time' => '14:15']);
        TimeSlot::create(['lesson_number' => 9, 'start_time' => '14:15', 'end_time' => '14:50']);
    }
}