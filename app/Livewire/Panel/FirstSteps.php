<?php

namespace App\Livewire\Panel;

use App\Models\Area;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FirstSteps extends Component
{
    public $user_id; // Parameter
    public $profesions, $areas, $locations;
    public $user_profesions = [];
    public $user_areas = [];
    public $user_location;
    public $step = 1;

    public function saveProfesions($selected_profesions)
    {
        $this->user_profesions = $selected_profesions;
        $this->validate(
            [
                'user_profesions' => 'required|array',
                'user_profesions.*' => 'exists:profesions,id'
            ],
            ['user_profesions' => 'No es posible guardar tus profesiones']
        );
        $user = User::find($this->user_id);
        $user->myProfesions()->attach($this->user_profesions);
        $this->step = 2;
    }

    public function saveAreas($selected_areas)
    {
        $this->user_areas = $selected_areas;
        $this->validate(
            [
                'user_profesions' => 'required|array',
                'user_profesions.*' => 'exists:profesions,id'
            ],
            ['user_profesions' => 'No es posible guardar tus profesiones']
        );
        $user = User::find($this->user_id);
        $user->myAreas()->attach($this->user_areas);
        $this->step = 3;
    }

    public function saveLocation($selected_location)
    {
        $this->user_location = $selected_location;
        $this->validate(
            [
                'user_location' => 'required|exists:locations,id',
            ],
            ['user_location' => 'No es posible guardar tu ubicación']
        );
        $user = User::find($this->user_id);
        $user->update(['location_id' => intval($this->user_location)]);
        $this->redirectRoute('search', navigate: true);
    }

    public function render()
    {
        $this->profesions = Profesion::orderBy('profesion_name', 'ASC')->get();
        $this->areas = Area::orderBy('area_name', 'ASC')->get();
        $this->locations = Location::all();
        return view('livewire.panel.first-steps');
    }
}
