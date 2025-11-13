<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cari user berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if($user){
                // Jika user ada, update google_id (jika sebelumnya manual) dan login
                $user->update([
                    'google_id' => $googleUser->id,
                    // Jika login via Google, otomatis verifikasi email
                    'email_verified_at' => now(), 
                ]);
                
                Auth::login($user);
                
                // Redirect sesuai role
                return $user->isAdmin() ? redirect('/admin/dashboard') : redirect('/customer/dashboard');
            } else {
                // Jika user baru, buat akun (Otomatis Customer)
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'username' => strtolower(str_replace(' ', '', $googleUser->name)) . rand(100,999), // Generate username
                    'google_id' => $googleUser->id,
                    'password' => null, // Tidak butuh password
                    'role' => 'customer', // Default role
                    'email_verified_at' => now(), // Otomatis terverifikasi
                ]);
        
                Auth::login($newUser);
                return redirect('/customer/dashboard');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Google gagal, silakan coba lagi.');
        }
    }
}