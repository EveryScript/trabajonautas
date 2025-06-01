<?php

namespace App\Livewire\User;

use App\Models\ProAccount;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListUser extends Component
{
    use WithPagination;

    public $search;
    public $filterClients = true;

    public function edit($id)
    {
        return $this->redirect("/create-user?id=" . $id, true);
    }
    public function render()
    {
        define('USER', env('USER_ROLE'));
        define('ADMIN', env('ADMIN_ROLE'));

        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', [USER, ADMIN]);
        })->get();

        return view('livewire.user.list-user', compact('users'));
    }
}
