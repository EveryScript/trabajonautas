<?php

namespace App\Livewire\User;

use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListClient extends Component
{
    use WithPagination;

    #[Url]
    public ?string $filter_client = '';
    public ?int $location_id = null;
    public ?int $profesion_id = null;
    public ?string $search = '';

    #[Computed]
    public function clients()
    {
        return User::withTrashed()
            ->role(config('app.client_role'))
            ->select(
                'id',
                'name',
                'email',
                'phone',
                'location_id',
                'profesion_id',
                'register_completed',
                'actived',
                'updated_at',
                'deleted_at'
            )
            ->with([
                'latestPendingSubscription.type',
                'account.type',
                'location:id,location_name',
            ])
            ->when($this->filter_client, function ($query) {
                if ($this->filter_client === 'unaccount') {
                    return $query->where('register_completed', false);
                }
                if ($this->filter_client === 'deleted') {
                    return $query->onlyTrashed();
                }
                $query->where('register_completed', true)
                    ->whereNull('deleted_at');

                if ($this->filter_client === 'pending') {
                    return $query->whereHas('latestPendingSubscription');
                }

                if ($this->filter_client === 'active') {
                    return $query->where('actived', true);
                }

                if ($this->filter_client === 'inactive') {
                    return $query->where('actived', false);
                }

                return $query->whereHas('account', function ($q) {
                    $q->where('account_type_id', $this->filter_client);
                });
            }, function ($query) {
                return $query->where('register_completed', true)
                    ->whereNull('deleted_at');
            })
            // By profesion
            ->when($this->profesion_id, function ($query) {
                $query->where('profesion_id', $this->profesion_id);
            })
            // By location
            ->when($this->location_id, function ($query) {
                $query->where('location_id', $this->location_id);
            })
            // By name, email or phone
            ->when($this->search, function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('name', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $this->search . '%');
                });
            })

            ->latest('updated_at')
            ->paginate(10);
    }

    public function getFilterClientLabel()
    {
        return match ($this->filter_client) {
            'pending'   => 'Pendientes',
            '1'         => 'Clientes FREE',
            '2'         => 'Clientes PRO',
            '3'         => 'Clientes PRO-MAX',
            'active'    => 'Solo Activos',
            'inactive'  => 'Solo Inactivos',
            'unaccount' => 'Sin cuenta',
            'deleted'   => 'Eliminados',
            default     => 'Todos',
        };
    }

    public function resetAllFilters()
    {
        $this->reset(['search', 'profesion_id', 'location_id', 'filter_client']);
        $this->resetPage();
    }

    #[Computed]
    public function profesions()
    {
        return Cache::remember('profesions_list', 3600, fn() => Profesion::select('id', 'profesion_name')->orderBy('profesion_name')->get());
    }

    #[Computed]
    public function locations()
    {
        return Cache::remember('locations_list', 3600, fn() => Location::select('id', 'location_name')->orderBy('id')->get());
    }

    #[On('client-saved')]
    public function refreshList()
    {
        $this->resetPage(); // Reset page when searching
    }

    public function render()
    {
        return view('livewire.user.list-client', [
            'clients' => $this->clients,
            'profesions' => $this->profesions,
            'locations' => $this->locations
        ]);
    }
}
