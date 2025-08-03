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
        User::create([
            'name' => 'Admin Piket',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Piket Guru',
            'email' => 'piket@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'piket',
        ]);
        User::create([
            'name' => 'Budi Sudarsono',
            'email' => 'budi@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);
        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);
    }
}