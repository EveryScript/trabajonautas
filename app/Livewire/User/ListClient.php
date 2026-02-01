<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListClient extends Component
{
    use WithPagination;

    #[Url]
    public $filter_client = '';
    public $search;

    #[Computed]
    public function clients()
    {
        return User::role(config('app.client_role'))
            ->select('id', 'name', 'location_id', 'register_completed', 'actived')
            ->where('register_completed', true)
            ->with(['latestPendingSubscription.type', 'account.type', 'location:id,location_name'])
            // Filter by account type
            ->when($this->filter_client, function ($query) {
                if ($this->filter_client === 'active')
                    return $query->where('actived', true);

                if ($this->filter_client === 'inactive')
                    return $query->where('actived', false);

                return $query->whereHas('account', function ($q) {
                    $q->where('account_type_id', $this->filter_client);
                });
            })
            // Filter by name
            ->when($this->search, function ($query) {
                $query->where('name', 'LIkE', '%' . $this->search . '%');
            })
            ->latest('updated_at')
            ->simplePaginate(10);
        $this->resetPage();
    }

    #[On('client-saved')]
    public function render()
    {
        return view('livewire.user.list-client', [
            'clients' => $this->clients
        ]);
    }
}
