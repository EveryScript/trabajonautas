<?php

namespace App\Livewire\User;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Livewire\Component;

class CreateUser extends Component
{
    public $id;         // Edit
    public UserForm $user;

    public function mount($id = null)
    {
        if ($id && User::find($id)) {
            $this->id = $id;
            $this->user->editUser($id);
        }
    }

    public function save()
    {
        $this->user->saveUser();
        $this->redirectRoute('user', navigate: true);
    }

    public function update()
    {
        $this->user->updateUser($this->id);
        $this->redirectRoute('user', navigate: true);
    }

    public function render()
    {
        define('FREE', env('FREE_CLIENT_ROLE'));
        define('PRO', env('PRO_CLIENT_ROLE'));
        define('USER', env('USER_ROLE'));
        define('ADMIN', env('ADMIN_ROLE'));
        return view('livewire.user.create-user');
    }
}
