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
    public $user_id;                            // Parameter
    public $gender, $age, $location_id, $phone; // Step 1
    public $grade_profile_id;                   // Step 2
    public $duration_days;                      // Step 3
    public $user_profesions, $user_area;        // Step 4 
    public $profesions, $areas, $locations;     // Data to use in the view
    public $step = 1;

    public function savePersonalData()
    {
        $this->validate([
            'gender' => 'required|in:M,F,O',
            'age' => 'required|numeric|in:1,2,3',
            'location_id' => 'required|exists:locations,id',
            'phone' => 'required|numeric'
        ]);
        $user = User::find($this->user_id);
        $user->update([
            'gender' => $this->gender,
            'age' => $this->age,
            'location_id' => $this->location_id,
            'phone' => $this->phone
        ]);
        $this->step = 2;
    }
    public function saveProfesionalData()
    {
        $this->validate([
            'grade_profile_id' => 'required|exists:grade_profiles,id'
        ]);
        $user = User::find($this->user_id);
        $user->update(['grade_profile_id' => $this->grade_profile_id]);
        $this->step = 3;
    }
    public function savePurchaseData()
    {
        $this->validate([
            'duration_days' => 'required|numeric|in:0,30,60'
        ]);

        $user = User::find($this->user_id);
        if ($this->duration_days == 0) {
            $user->update(['register_completed' => true]);
            $this->redirectRoute('dashboard', navigate: true);
        } else {
            $user->syncRoles([env('PRO_CLIENT_ROLE')]);
            $user->proAccount()->create([
                'purchased_at' => now(),
                'duration_days' => $this->duration_days
            ]);
            $this->step = 4;
        }
    }
    public function saveProAccountData($form_profesions, $form_area)
    {
        $this->user_profesions = $form_profesions;
        $this->user_area = $form_area;
        $this->validate([
            'user_profesions' => 'required|array',
            'user_profesions.*' => 'exists:profesions,id',
            'user_area' => 'required|exists:areas,id'
        ]);
        $user = User::find($this->user_id);
        $user->myProfesions()->attach($this->user_profesions);
        $user->update([
            'area_id' => intval($this->user_area),
            'register_completed' => true
        ]);
        $this->redirectRoute('dashboard', navigate: true);
    }
    public function stepBack()
    {
        $this->step--;
    }

    public function render()
    {
        $this->profesions = Profesion::orderBy('profesion_name', 'ASC')->get();
        $this->areas = Area::orderBy('area_name', 'ASC')->get();
        $this->locations = Location::all();
        return view('livewire.panel.first-steps');
    }
}
