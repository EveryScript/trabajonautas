<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    // User fields
    public $name;
    public $email;
    public $password;
    public $role;
    public $actived;
    // Client fields
    public $location_id;
    public $area_id;
    public $profesions;

    public function editUser($user_id)
    {
        $user_edit = User::find($user_id);
        $this->name = $user_edit->name;
        $this->email = $user_edit->email;
        $this->role = $user_edit->getRoleNames()->first();
        $this->actived = $user_edit->actived;
    }
    public function saveUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8',
        ]);
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ]);
        $user->assignRole(env('USER_ROLE')); // Always user
    }
    public function updateUser($user_id)
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->user()->id,
            'password' => 'nullable|min:8',
            'role' => 'required|in:' . implode(',', [
                env('USER_ROLE'),
                env('ADMIN_ROLE'),
                env('CLIENT_ROLE'),
            ]),
        ]);
        $user = User::find($user_id);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'actived' => $this->actived
        ]);
        $user->syncRoles($this->role);
        if ($this->password) {
            $user->update([
                'password' => Hash::make($this->password)
            ]);
        }
    }
}
