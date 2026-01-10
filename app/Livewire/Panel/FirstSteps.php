<?php

namespace App\Livewire\Panel;

use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\TbnSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FirstSteps extends Component
{
    public $user_id;                            // Component parameter
    public $gender, $age, $phone;               // Step 1
    public $grade_profile_id;                   // Step 2
    public $profesion_id;                       // Step 3
    public $location_id;                        // Step 4
    public $account_type_id;                    // Step 5
    public $user;                               // Current user
    public $locations, $profesions, $account_types; // Data to use in the view
    public $country_code = '+591';

    public function mount()
    {
        if (Auth::check()) {
            $this->user = User::with(['account.accountType'])
                ->select('id', 'name', 'phone')->find($this->user_id);
        } else {
            redirect()->route('login');
        }
    }
    public function confirmAndSave()
    {
        $this->validate([
            'gender' => 'required|in:M,F',
            'age' => 'required|numeric|in:1,2,3',
            'phone' => 'required|numeric|digits_between:8,10',
            'country_code' => 'required|string|in:+591',
            'grade_profile_id' => 'required|exists:grade_profiles,id',
            'profesion_id' => 'required|exists:profesions,id',
            'location_id' => 'required|exists:locations,id',
            'account_type_id' => 'required|exists:account_types,id'
        ]);
        $this->user->update([
            'gender' => $this->gender,
            'age' => intval($this->age),
            'phone' => $this->country_code . $this->phone,
            'location_id' => intval($this->location_id),
            'profesion_id' => intval($this->profesion_id),
            'grade_profile_id' => intval($this->grade_profile_id),
            'register_completed' => true,
            'last_announce_check' => now()
        ]);
        $this->user->account()->create([
            'user_id' => $this->user_id,
            'account_type_id' => intval($this->account_type_id)
        ]);

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        $this->locations = Location::all();
        $this->profesions = Profesion::all();
        $this->account_types = AccountType::all();
        $qr_image = TbnSetting::where('key', 'qr_image')->first();
        return view('livewire.panel.first-steps', [
            'qr_image' => $qr_image
        ]);
    }
}
