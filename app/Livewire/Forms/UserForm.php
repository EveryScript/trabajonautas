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

    public function editClient()
    {
        $user_id = auth()->user()->id;
        $user_edit = User::find($user_id);
        $this->location_id = $user_edit->location_id;
        $this->area_id = $user_edit->area_id;
        $this->profesions = $user_edit->myProfesions->pluck('id');
    }
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
    public function updateClient($client_id)
    {
        $this->validate([
            'location_id' => 'required',
            'area_id' => 'required',
        ]);
        $client = User::find($client_id);
        $client->update([
            'location_id' => $this->location_id,
            'area_id' => $this->area_id
        ]);
        $client->myProfesions()->sync($this->profesions);
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
                env('PRO_CLIENT_ROLE'),
                env('FREE_CLIENT_ROLE')
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
