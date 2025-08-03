<?php

namespace Database\Seeders; // Jangan lupa namespace

use App\Models\User;
use Illuminate\Database\Seeder; // Import Seeder class
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder // Deklarasi Class
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek dan buat user admin
        if (!User::where('email', 'admin@sekolah.com')->exists()) {
            User::create([
                'name' => 'Admin Piket',
                'email' => 'admin@sekolah.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }
        
        // Cek dan buat user piket - menggunakan DB raw untuk bypass enum issue
        if (!User::where('email', 'piket@sekolah.com')->exists()) {
            try {
                User::create([
                    'name' => 'Piket Guru',
                    'email' => 'piket@sekolah.com',
                    'password' => Hash::make('password'),
                    'role' => 'piket',
                ]);
            } catch (\Exception $e) {
                // Fallback menggunakan DB raw query
                DB::statement("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, 'piket', NOW(), NOW())", [
                    'Piket Guru',
                    'piket@sekolah.com',
                    Hash::make('password')
                ]);
            }
        }
        
        // Cek dan buat user guru
        if (!User::where('email', 'budi@sekolah.com')->exists()) {
            User::create([
                'name' => 'Budi Sudarsono',
                'email' => 'budi@sekolah.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]);
        }
        
        if (!User::where('email', 'siti@sekolah.com')->exists()) {
            User::create([
                'name' => 'Siti Aminah',
                'email' => 'siti@sekolah.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]);
        }
    }
}