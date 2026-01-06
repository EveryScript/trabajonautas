<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->user();
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->id
            ]);
        } else {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'password' => bcrypt(Str::random(32)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'email_verified_at' => now(), // Email verified now
                'terms_accepted_at' => now()
            ]);
            $user->assignRole(env('CLIENT_ROLE')); // Every user is CLIENT
        }

        Auth::login($user, remember: true);
        return redirect()->intended(route('dashboard'));
    }
}
