<?php

namespace App\Actions\Fortify;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'disposable_email'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'turnstileToken' => ['required', 'string'], // CloudFare Turnstile (input validation)
        ], [
            'turnstileToken.required' => 'Lo sentimos, no pudimos verificar tu identidad como humano. Por favor, intenta de nuevo.',
        ])->validate();

        // Validation from CloudFare Turnstile
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => env('TURNSTILE_SECRET_KEY'),
            'response' => $input['turnstileToken'],
            'remoteip' => request()->ip(),
        ]);
        $responseData = $response->json();
        if (!($responseData['success'] ?? false)) {
            throw ValidationException::withMessages([
                'turnstileToken' => ['Lo sentimos, no pudimos verificar tu identidad como humano. Por favor, intenta de nuevo.'],
            ]);
        }

        // Register
        $new_user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'terms_accepted_at' => now()
        ]);

        $new_user->assignRole(env('CLIENT_ROLE')); // Every user is CLIENT

        return $new_user;
    }
}
