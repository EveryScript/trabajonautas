<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Models\Area;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardUser extends Component
{
    public $primary_color = "#006BFF";

    public function getClientsByRole()
    {
        return [
            'labels' => ['Clientes FREE', 'Clientes PRO'],
            'data' => [User::role('FREE_CLIENT')->get()->count(), User::role('PRO_CLIENT')->get()->count()],
            'backgroundColor' => ['#6cff76', '#006BFF']
        ];
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
        $locations = Location::withCount('users')->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($locations as $location) {
            array_push($labels, $location->location_name);
            array_push($data, $location->users()->get()->count());
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }

    public function render()
    {
        $tbn_clients = $this->getClientsByRole();
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
