<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ClientForm extends Form
{
    public $gender;
    public $age;
    public $phone;
    public $grade_profile_id;
    public $profesion_id;
    public $location_id;
    public $account_type_id;
    public $account_price;

    public function store(User $user, $country_code = '+591')
    {
        $this->validate();

        DB::transaction(function () use ($user, $country_code) {
            // Update user profile
            $user->update([
                'gender' => $this->gender,
                'age' => $this->age,
                'phone' => $country_code . $this->phone,
                'location_id' => (int) $this->location_id,
                'profesion_id' => (int) $this->profesion_id,
                'grade_profile_id' => (int) $this->grade_profile_id,
                'register_completed' => true,
                'last_announce_check' => now(),
            ]);
            if ((int) $this->account_type_id === 1) {
                // Create account FREE
                $user->account()->updateOrCreate(['user_id' => $user->id], [
                    'account_type_id' => (int) $this->account_type_id,
                ]);
                // Create subscription FREE
                $user->subscriptions()->create([
                    'user_id' => $user->id,
                    'account_type_id' => (int) $this->account_type_id,
                    'price' => 0,
                    'verified_payment' => true,
                    'verified_by_user_id' => null
                ]);
            } else {
                // Create subscription pending
                $user->subscriptions()->create([
                    'account_type_id' => (int) $this->account_type_id,
                    'price' => $this->account_price
                ]);
            }
        });
    }

    public function rules()
    {
        return [
            'gender' => 'required|in:M,F',
            'age' => 'required|numeric|in:1,2,3',
            'phone' => 'required|numeric|digits:8',
            'grade_profile_id' => 'required|exists:grade_profiles,id',
            'profesion_id' => 'required|exists:profesions,id',
            'location_id' => 'required|exists:locations,id',
            'account_type_id' => 'required|exists:account_types,id',
            'account_price' => 'required|numeric'
        ];
    }
}
