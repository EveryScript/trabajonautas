<?php

namespace App\Livewire\Panel;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Area;
use App\Models\GradeProfile;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Livewire\Component;

class DashboardUser extends Component
{
    public $primary_color = "#ff420a";
    public $secondary_color = "#485054";
    public $default_color = "#768289";

    public function getClientsByAccount()
    {
        $account_types = AccountType::all('id', 'name');
        $labels = [];
        $data = [];
        $background = [$this->default_color, $this->secondary_color, $this->primary_color];
        foreach ($account_types as $account_type) {
            array_push($labels, $account_type->name);
            array_push($data, Account::where('account_type_id', $account_type->id)->count());
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function getClientsByAge()
    {
        $client_ages = [
            ['value' => 1, 'text' => 'de 18 a 25 años'],
            ['value' => 2, 'text' => 'de 26-32 años'],
            ['value' => 3, 'text' => 'de 33 en adelante'],
        ];
        $labels = [];
        $data = [];
        $background = [$this->default_color, $this->secondary_color, $this->primary_color];
        foreach ($client_ages as $client_age) {
            array_push($labels, $client_age['text']);
            array_push($data, User::role(env('CLIENT_ROLE'))->whereNotNull('age')->where('age', $client_age['value'])->count());
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function getClientsByGender()
    {
        $client_generes = [
            ['value' => 'M', 'text' => 'Hombres'],
            ['value' => 'F', 'text' => 'Mujeres'],
        ];
        $labels = [];
        $data = [];
        $background = [$this->secondary_color, $this->primary_color];
        foreach ($client_generes as $client_genere) {
            array_push($labels, $client_genere['text']);
            array_push($data, User::role(env('CLIENT_ROLE'))->whereNotNull('gender')->where('gender', $client_genere['value'])->count());
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function getClientsByGrade()
    {
        $grades = GradeProfile::whereHas('users', fn($subquery) => $subquery->role(env('CLIENT_ROLE')))->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($grades as $grade) {
            array_push($labels, $grade->profile_name);
            array_push($data, $grade->users()->get()->count());
            array_push($background, $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function getAnnouncesByArea()
    {
        $areas = Area::all('id', 'area_name');
        $areas = Area::whereHas('announcements')->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($areas as $area) {
            array_push($labels, strlen($area->area_name) > 25 ? substr($area->area_name, 0, 20) . '...' : $area->area_name);
            array_push($data, $area->announcements()->get()->count());
            array_push($background, $this->secondary_color);
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
            array_push($background,  $this->secondary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function getClientsByProfesion()
    {
        $profesions = Profesion::withCount(['users as clients_profesion' => function ($query) {
            $query->role(env('CLIENT_ROLE'));
        }])->orderBy('clients_profesion', 'DESC')->take(10)->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($profesions as $profesion) {
            array_push($labels, strlen($profesion->profesion_name) > 25 ?
                substr($profesion->profesion_name, 0, 25) . '...' : $profesion->profesion_name);
            array_push($data, $profesion->clients_profesion);
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function getClientsByLocation()
    {
        $locations = Location::whereHas('users', fn($subquery) => $subquery->role(env('CLIENT_ROLE')))->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($locations as $location) {
            array_push($labels, $location->location_name);
            array_push($data, $location->users()->role(env('CLIENT_ROLE'))->count());
            array_push($background,  $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function getClientsByArea()
    {
        $areas = Area::whereHas('usersOf', fn($subquery) => $subquery->role(env('CLIENT_ROLE')))->get();
        $labels = [];
        $data = [];
        $background = [];
        foreach ($areas as $area) {
            array_push($labels, strlen($area->area_name) > 25 ? substr($area->area_name, 0, 20) . '...' : $area->area_name);
            array_push($data, $area->usersOf()->role(env('CLIENT_ROLE'))->count());
            array_push($background, $this->primary_color);
        }
        return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $background];
    }
    public function render()
    {
        $tbn_clients = $this->getClientsByAccount();
        $tbn_announces_area = $this->getAnnouncesByArea();
        $tbn_announces_user = $this->getAnnouncesByUser();
        $tbn_clients_profesion = $this->getClientsByProfesion();
        $tbn_clients_location = $this->getClientsByLocation();
        $tbn_clients_age = $this->getClientsByAge();
        $tbn_clients_gender = $this->getClientsByGender();
        $tbn_clients_grade = $this->getClientsByGrade();
        $tbn_clients_area = $this->getClientsByArea();

        return view('livewire.panel.dashboard-user', compact(
            'tbn_clients',
            'tbn_announces_area',
            'tbn_announces_user',
            'tbn_clients_profesion',
            'tbn_clients_location',
            'tbn_clients_age',
            'tbn_clients_gender',
            'tbn_clients_grade',
            'tbn_clients_area'
        ));
    }
}
