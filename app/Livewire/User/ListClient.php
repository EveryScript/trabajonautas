<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListClient extends Component
{
    use WithPagination;

    public $search;

    public function render()
    {
        $clients = User::whereHas('account')
            ->when($this->search, fn($query) => $query->where('name', 'LIKE', '%' . $this->search . '%'))
            ->with('account.accountType')
            ->paginate(12);
        return view('livewire.user.list-client', [
            'clients' => $clients
        ]);
    }
}
