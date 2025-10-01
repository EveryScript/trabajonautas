<?php

namespace App\Livewire\Panel;

use App\Models\AccountType;
use App\Models\Area;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FirstSteps extends Component
{
    public $user_id;                            // Component parameter
    public $gender, $age, $location_id, $phone; // Step 1
    public $grade_profile_id, $area_id;         // Step 2
    public $account_type_id;                    // Step 3
    public $user_profesions;                    // Step 4
    public $user;                               // Current user
    public $areas, $locations, $account_types; // Data to use in the view
    public $step = 0;

    public function mount()
    {
        if (Auth::check()) {
            $this->user = User::with('account.accountType')->find($this->user_id);
        }
    }

    public function savePersonalData()
    {
        $this->validate([
            'gender' => 'required|in:M,F',
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
            'grade_profile_id' => 'required|exists:grade_profiles,id',
            'area_id' => 'required|exists:areas,id'
        ]);
        $user = User::find($this->user_id);
        $user->update([
            'grade_profile_id' => intval($this->grade_profile_id),
            'area_id' => intval($this->area_id)
        ]);
        $this->step = 3;
    }
    public function saveAccountData()
    {
        $this->validate([
            'account_type_id' => 'required|exists:account_types,id'
        ]);
        $user = User::find($this->user_id);
        $user->account()->create([
            'user_id' => $this->user_id,
            'account_type_id' => intval($this->account_type_id)
        ]);
        if (intval($this->account_type_id) == 1) {
            $user->update(['register_completed' => true]);
            $this->redirectRoute('dashboard', navigate: true);
        } else {
            $this->redirectRoute('purchase-account', ['account_type_id' => $this->account_type_id], navigate: true);
        }
    }
    public function stepBack()
    {
        $this->step--;
    }
    public function render()
    {
        $this->areas = Area::orderBy('area_name', 'ASC')->get();
        $this->locations = Location::all();
        $this->account_types = AccountType::all();
        return view('livewire.panel.first-steps');
    }
}
