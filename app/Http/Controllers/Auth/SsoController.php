<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    /**
     * Redirect the user to the SSO authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect()
    {
        return Socialite::driver('sso')->redirect();
    }

    /**
     * Obtain the user information from SSO.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback()
    {
        try {
            $ssoUser = Socialite::driver('sso')->user();
            
            // Cari user berdasarkan email
            $user = User::where('email', $ssoUser->getEmail())->first();
            
            if ($user) {
                // Update user info dari SSO
                $user->update([
                    'name' => $ssoUser->getName(),
                    'sso_id' => $ssoUser->getId(),
                    'avatar' => $ssoUser->getAvatar(),
                ]);
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $ssoUser->getName(),
                    'email' => $ssoUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // Random password karena login via SSO
                    'sso_id' => $ssoUser->getId(),
                    'avatar' => $ssoUser->getAvatar(),
                    'role' => 'guru', // Default role, bisa disesuaikan
                    'email_verified_at' => now(), // Anggap email sudah verified via SSO
                ]);
            }
            
            Auth::login($user);
            
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['sso' => 'SSO authentication failed: ' . $e->getMessage()]);
        }
    }
}