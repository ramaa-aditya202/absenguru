<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreatePiketUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-piket {--email=piket@sekolah.com} {--name=Piket Guru} {--password=password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new piket user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $name = $this->option('name');
        $password = $this->option('password');

        // Cek apakah user sudah ada
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        try {
            // Coba buat dengan Eloquent
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'piket',
            ]);

            $this->info("Piket user created successfully!");
            $this->info("Email: {$email}");
            $this->info("Password: {$password}");

        } catch (\Exception $e) {
            // Fallback dengan raw SQL
            $this->warn("Eloquent failed, trying raw SQL...");
            
            try {
                DB::statement("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, 'piket', NOW(), NOW())", [
                    $name,
                    $email,
                    Hash::make($password)
                ]);

                $this->info("Piket user created successfully with raw SQL!");
                $this->info("Email: {$email}");
                $this->info("Password: {$password}");

            } catch (\Exception $e2) {
                $this->error("Failed to create piket user: " . $e2->getMessage());
                $this->error("Please check your database enum settings for 'role' column.");
                return 1;
            }
        }

        return 0;
    }
}
