<?php

namespace App\Providers;

use App\Models\User; // <-- Tambahkan ini
use Illuminate\Support\Facades\Gate; // <-- Tambahkan ini
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definisikan Gate di sini
        // Gate ini akan mengembalikan true jika role user adalah 'admin' atau 'piket'
        Gate::define('perform-attendance', function (User $user) {
            return in_array($user->role, ['admin', 'piket']);
        });
    }
}
