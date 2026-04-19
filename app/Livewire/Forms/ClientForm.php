<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ClientForm extends Form
{
    public ?User $client = null;

    public $name;
    public $email;
    public $gender;
    public $age;
    public $phone;
    public $grade_profile_id;
    public $profesion_id;
    public $location_id;
    public $account_type_id;
    public $account_price;

    public function setClient(User $client)
    {
        $this->client = $client;

        $this->fill($client->only([
            'name',
            'email',
            'gender',
            'age',
            'phone',
            'grade_profile_id',
            'profesion_id',
            'location_id',
        ]));

        $this->account_type_id = $this->client->account->account_type_id;
    }

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

    public function update()
    {
        $this->validate([
            'name' => 'required|max:200',
            'email' => [
                'required',
                'min:5',
                Rule::unique('users', 'email')->ignore($this->client?->id)
            ],
            'phone' => 'required|numeric|digits:8',
            'location_id' => 'required|exists:locations,id',
            'gender' => 'required|in:M,F',
            'age' => 'required|numeric|in:1,2,3',
            'profesion_id' => 'required|exists:profesions,id',
            'grade_profile_id' => 'required|exists:grade_profiles,id',
            'account_price' => 'required|numeric'
        ]);

        $this->client->update($this->except(['client']));
        // Update account if change
        if (intval($this->account_type_id) !== $this->client->account->account_type_id) {
            DB::transaction(function () {
                if (intval($this->account_type_id) === 1) {
                    // Create account FREE
                    $this->client->account()->update([
                        'account_type_id' => (int) $this->account_type_id
                    ]);
                    // Create subscription FREE
                    $this->client->subscriptions()->create([
                        'user_id' => $this->client->id,
                        'account_type_id' => (int) $this->account_type_id,
                        'price' => 0,
                        'verified_payment' => true,
                        'verified_by_user_id' => null
                    ]);
                } else {
                    // Create subscription pending
                    $this->client->subscriptions()->create([
                        'account_type_id' => (int) $this->account_type_id,
                        'price' => $this->account_price
                    ]);
                }
            });
        }
    }

    public function delete()
    {
        $this->client->delete();
    }

    public function forceDelete()
    {
        DB::transaction(function () {
            $this->client->deleteProfilePhoto();
            $this->client->tokens()->delete();

            $this->client->notificationLogs()->delete();
            $this->client->subscriptions()->delete();
            $this->client->account?->delete();

            $this->client->notices()->delete();
            $this->client->companies()->delete();

            $this->client->announcements->each(function ($announcement) {
                $announcement->usersOf()->detach();
                $announcement->locations()->detach();
                $announcement->profesions()->detach();
                $announcement->announceFiles()->delete();
                $announcement->delete();
            });

            $this->client->myAnnounces()->detach();

            $this->client->forceDelete();
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
