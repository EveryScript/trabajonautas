<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create user from Google if not exist
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(32)),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'terms_accepted_at' => now()
                ]);
                $user->assignRole(env('CLIENT_ROLE')); // Every user is CLIENT
            }

            // Delete all sessions from user
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->intended('panel');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al iniciar sesión con Google');
        }
    }
}
