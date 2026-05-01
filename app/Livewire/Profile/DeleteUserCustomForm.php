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

        if (!$user instanceof User)
            $user = User::find(Auth::id());

        if ($user->password) {
            $this->validate([
                'password' => 'required|string',
            ]);

            if (!Hash::check($this->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => [__('La contraseña es incorrecta.')],
                ]);
            }
        }
        // app(DeleteUser::class)->delete($user);
        // Envolver en transacción para eliminación segura
        DB::transaction(function () use ($user) {
            $user->deleteProfilePhoto();
            $user->tokens()->delete();
            if (method_exists($user, 'notificationLogs'))
                $user->notificationLogs()->delete();
            $user->subscriptions()->delete();
            $user->account?->delete();
            $user->notices()->delete();
            $user->companies()->delete();
            $user->announcements->each(function ($announcement) {
                $announcement->usersOf()->detach();
                $announcement->locations()->detach();
                $announcement->profesions()->detach();
                $announcement->announceFiles()->delete();
                $announcement->delete();
            });
            $user->myAnnounces()->detach();
            $user->forceDelete();
        });

        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.profile.delete-user-custom-form');
    }
}
