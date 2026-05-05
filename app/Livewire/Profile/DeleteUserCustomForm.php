<?php

namespace App\Livewire\Profile;

use App\Actions\Jetstream\DeleteUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class DeleteUserCustomForm extends Component
{
    public $confirmingUserDeletion = false;
    public $password = '';

    public function confirmUserDeletion()
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        $user = Auth::user();

        // Password validation
        if ($user->password) {
            $this->validate(['password' => 'required|string']);
            if (!Hash::check($this->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => [__('La contraseña es incorrecta.')],
                ]);
            }
        }
        // Logout session
        Auth::guard('web')->logout();
        // Delete user (JetStream)
        app(DeleteUser::class)->delete($user);
        // Delete session data
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.profile.delete-user-custom-form');
    }
}
