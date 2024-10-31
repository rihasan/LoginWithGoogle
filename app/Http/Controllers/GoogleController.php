<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find user by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                 // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make('demopassword'),
                ]);
            } else {
                // Update existing user details
                $user->update([
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make('demopassword'),
                ]);
               
            }

            // Log the user in
            Auth::login($user);

            // Redirect to intended page or dashboard
            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['login' => 'Authentication failed. Please try again.']);
        }
    }
}
