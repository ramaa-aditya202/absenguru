<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Mapel
        $math = Subject::create(['name' => 'Matematika Wajib']);
        $phys = Subject::create(['name' => 'Fisika']);

        // Buat Kelas
        $classA = Classroom::create(['name' => 'X-A']);
        $classB = Classroom::create(['name' => 'X-B']);

        // Buat Jadwal (user_id 2 & 3 adalah guru)
        // Senin
        Schedule::create(['user_id' => 2, 'subject_id' => $math->id, 'classroom_id' => $classA->id, 'day_of_week' => 1, 'start_time' => '07:00', 'end_time' => '08:30']);
        Schedule::create(['user_id' => 3, 'subject_id' => $phys->id, 'classroom_id' => $classB->id, 'day_of_week' => 1, 'start_time' => '08:30', 'end_time' => '10:00']);
        // Selasa
        Schedule::create(['user_id' => 3, 'subject_id' => $phys->id, 'classroom_id' => $classA->id, 'day_of_week' => 2, 'start_time' => '07:00', 'end_time' => '08:30']);
    }
}