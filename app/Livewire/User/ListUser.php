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
        define('FREE', env('FREE_CLIENT_ROLE'));
        define('PRO', env('PRO_CLIENT_ROLE'));
        define('USER', env('USER_ROLE'));
        define('ADMIN', env('ADMIN_ROLE'));

        if ($this->search) {
            $users = User::orderBy('updated_at', 'DESC')
                ->when($this->search, fn($query) => $query->where('name', 'LIKE', '%' . $this->search . '%'))
                ->paginate(10);
        } else {
            $users = User::role($this->filterClients ?
                [env('FREE_CLIENT_ROLE'), env('PRO_CLIENT_ROLE')] :
                [env('ADMIN_ROLE'), env('USER_ROLE')])
                ->with('roles')
                ->orderBy('updated_at', 'DESC')
                ->paginate(10);
        }

        return view('livewire.user.list-user', compact('users'));
    }
}
