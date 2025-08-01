<?php

namespace App\Livewire\Panel;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Area;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Livewire\Component;

class DashboardUser extends Component
{
    public $primary_color = "#034b8d";

    public function getClientsByAccount()
    {
        $account_types = AccountType::all('id', 'name');
        $labels = [];
        $data = [];
        $background = ['#22c55e', '#034b8d', '#f29000'];
        foreach ($account_types as $account_type) {
            array_push($labels, $account_type->name);
            array_push($data, Account::where('account_type_id', $account_type->id)->count());
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }

    public function getAnnouncesByArea()
    {
        $areas = Area::all('id', 'area_name');
        $labels = [];
        $data = [];
        $background = [];
        foreach ($areas as $area) {
            array_push($labels, $area->area_name);
            array_push($data, $area->announcements()->get()->count());
            array_push($background, $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }

    public function getAnnouncesByUser()
    {
        $users = User::role(['USER', 'ADMIN'])->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($users as $user) {
            array_push($labels, $user->name);
            array_push($data, $user->announcements()->get()->count());
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }

    public function getUsersByProfesion()
    {
        $profesions = Profesion::withCount('users')->orderBy('users_count', 'DESC')->take(10)->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($profesions as $profesion) {
            array_push($labels, $profesion->profesion_name);
            array_push($data, $profesion->users()->get()->count());
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }

    public function getUsersByLocation()
    {
        $locations = Location::whereHas('users', fn($subquery) => $subquery->role(env('CLIENT_ROLE')))->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($locations as $location) {
            array_push($labels, $location->location_name);
            array_push($data, $location->users()->count());
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }

    public function render()
    {
        $tbn_clients = $this->getClientsByAccount();
        $tbn_announces_area = $this->getAnnouncesByArea();
        $tbn_announces_user = $this->getAnnouncesByUser();
        $tbn_users_profesion = $this->getUsersByProfesion();
        $tbn_users_location = $this->getUsersByLocation();

        return view('livewire.panel.dashboard-user', compact(
            'tbn_clients',
            'tbn_announces_area',
            'tbn_announces_user',
            'tbn_users_profesion',
            'tbn_users_location'
        ));
    }
}
