<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListUser extends Component
{
    use WithPagination;

    public $search;

    public function render()
    {
        $users = User::orderBy('name', 'ASC')
            ->when($this->search, fn($query) => $query->where('name', 'LIKE', '%' . $this->search . '%'))
            ->paginate(10);
        return view('livewire.user.list-user', compact('users'));
    }
}
