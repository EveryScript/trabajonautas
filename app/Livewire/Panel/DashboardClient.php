<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Models\Area;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardClient extends Component
{
    public $user_id;            // Parameter
    public $ann_suggestions;    // Suggestions to FREE or PRO clients


    // public $area_id;        // area_id to add
    // public $profesion_id;   // profesion_id to add
    // public $location_id;    // location_id to add

    /*
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
        */

    public function render()
    {
        $user = User::find($this->user_id);
        if ($user->hasRole(env('FREE_CLIENT_ROLE'))) {
            $this->ann_suggestions = Announcement::where('pro', false)->get();
        } else {
            $this->ann_suggestions = Announcement::all();
        }
        // $areas = Area::all('id', 'area_name');
        // $profesions = Profesion::all('id', 'profesion_name');
        // $locations = Location::all('id', 'location_name');
        // $user = User::with('myAreas.announcements')->find(Auth::user()->id);
        // $area_announces = $user->hasRole(env('FREE_CLIENT_ROLE')) ?
        //     $user->myAreas->flatMap(fn($area) => $area->announcements->where('pro', false)) :
        //     $user->myAreas->flatMap(fn($area) => $area->announcements);

        return view('livewire.panel.dashboard-client', compact(
            'user',
        ));
    }
}
