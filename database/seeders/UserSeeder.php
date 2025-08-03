<?php

namespace Database\Seeders; // Jangan lupa namespace

use App\Models\User;
use Illuminate\Database\Seeder; // Import Seeder class
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder // Deklarasi Class
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan updateOrCreate untuk menghindari duplikasi
        User::updateOrCreate(
            ['email' => 'admin@sekolah.com'],
            [
                'name' => 'Admin Piket',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'piket@sekolah.com'],
            [
                'name' => 'Piket Guru',
                'password' => Hash::make('password'),
                'role' => 'piket',
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'budi@sekolah.com'],
            [
                'name' => 'Budi Sudarsono',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'siti@sekolah.com'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
    }
}