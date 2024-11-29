<?php

namespace App\Livewire\Panel;

use App\Models\Area;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardClient extends Component
{
    public $user_id; // Parameter
    public $area_id; // area_id to add
    public $profesion_id; // profesion_id to add
    public $location_id; // location_id to add

    public function saveArea()
    {
        $user = User::find($this->user_id);
        $user->myAreas()->syncWithoutDetaching($this->area_id);
    }

    public function saveProfesion()
    {
        $user = User::find($this->user_id);
        $user->myProfesions()->syncWithoutDetaching($this->profesion_id);
    }

    public function saveLocation()
    {
        $user = User::find($this->user_id);
        $user->update(['location_id' => intval($this->location_id)]);
    }

    public function deleteArea($id)
    {
        $user = User::find($this->user_id);
        $user->myAreas()->detach($id);
    }

    public function deleteProfesion($id)
    {
        $user = User::find($this->user_id);
        $user->myProfesions()->detach($id);
    }

    public function render()
    {
        $areas = Area::all('id', 'area_name');
        $profesions = Profesion::all('id', 'profesion_name');
        $locations = Location::all('id', 'location_name');
        $user = User::with('myAreas.announcements')->find(Auth::user()->id);
        $area_announces = $user->myAreas->flatMap(function ($area) {
            return $area->announcements;
        });
        return view('livewire.panel.dashboard-client', compact(
            'user',
            'area_announces',
            'areas',
            'profesions',
            'locations'
        ));
    }
}
