<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data ini dibuat dengan asumsi UserSeeder dan TimeSlotSeeder sudah dijalankan
        
        // Buat Mapel
        $math = Subject::create(['name' => 'Matematika Wajib']);
        $phys = Subject::create(['name' => 'Fisika']);

        // Buat Kelas
        $classA = Classroom::create(['name' => 'X-A']);
        $classB = Classroom::create(['name' => 'X-B']);
        
        // Buat Jadwal menggunakan time_slot_id, bukan start_time/end_time
        // user_id 2 & 3 adalah guru (Budi & Siti dari UserSeeder)
        // time_slot_id 1, 2, 3... sesuai urutan di TimeSlotSeeder

        // Senin
        // Jam ke-1 oleh Budi
        Schedule::create([
            'user_id' => 2, 
            'subject_id' => $math->id, 
            'classroom_id' => $classA->id, 
            'day_of_week' => 1, 
            'time_slot_id' => 1 
        ]);

        // Jam ke-3 oleh Siti
        Schedule::create([
            'user_id' => 3, 
            'subject_id' => $phys->id, 
            'classroom_id' => $classB->id, 
            'day_of_week' => 1, 
            'time_slot_id' => 3
        ]);

        // Selasa
        // Jam ke-2 oleh Siti
        Schedule::create([
            'user_id' => 3, 
            'subject_id' => $phys->id, 
            'classroom_id' => $classA->id, 
            'day_of_week' => 2, 
            'time_slot_id' => 2
        ]);
    }
}