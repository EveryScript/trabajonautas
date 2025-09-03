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
        $base_query = User::whereHas('account')->with('account.accountType');

        $filter_query = (clone $base_query)
            ->when($this->search, fn($query) => $query->where('name', 'LIKE', '%' . $this->search . '%'));

        $count_results = $filter_query->count();

        $clients = $count_results > 0
            ? $filter_query->paginate(8)
            : $base_query->paginate(8);

        return view('livewire.user.list-client', [
            'clients' => $clients,
            'count_results' => $count_results,
            'search' => $this->search
        ]);
    }
}
