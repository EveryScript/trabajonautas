<?php

namespace App\Livewire\Panel;

use App\Livewire\Forms\UserForm;
use App\Models\Announcement;
use App\Models\Area;
use App\Models\User;
use App\Models\Location;
use App\Models\Profesion;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardClient extends Component
{
    use WithPagination;
    public $user_id;            // Parameter
    public UserForm $userForm;  // User profile form

    public function mount()
    {
        $this->userForm->editClient();
    }
    public function updateUser()
    {
        $this->userForm->updateClient($this->user_id);
        $this->dispatch('user-updated');
    }

    public function render()
    {
        $user = User::with('account.accountType')->find($this->user_id);
        $pro_verified = $user->hasRole(env('PRO_CLIENT_ROLE')) && $user->account->verified_payment;
        $suggests = Announcement::where('expiration_time', '>=', now())
            ->when($pro_verified, function ($query) use ($user) {
                $query->whereHas('area', fn($subquery) => $subquery->where('id', $user->area->id));
            })
            // ->orWhereHas('profesions', function ($query) use ($user) {
            //     $query->whereIn('profesion_id', $user->myProfesions->pluck('id'));
            // })
            // ->orWhereHas('locations', function ($query) use ($user) {
            //     $query->whereIn('location_id', [$user->location_id]);
            // })
            ->orderBy('updated_at', 'DESC');
        $locations = Location::all();
        $areas = Area::all();
        $profesions = Profesion::all();
        return view('livewire.panel.dashboard-client', [
            'user' => $user,
            'suggests' => $suggests->simplePaginate(8),
            'locations' => $locations,
            'areas' => $areas,
            'profesions' => $profesions,
            'pro_verified' => $pro_verified
        ]);
    }
}
