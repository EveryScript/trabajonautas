<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UpdateAccountInformationForm extends Component
{
    public $state = [];

    #[Computed]
    public function client()
    {
        if (!auth()->check())
            return redirect('/panel', true);

        return User::select('id', 'name', 'phone', 'location_id', 'profesion_id', 'grade_profile_id', 'last_announce_check')
            ->with([
                'account:id,user_id,account_type_id,limit_time,created_at,updated_at',
                'account.type:id,name,price,duration_days',
            ])->find(auth()->user()->id);
    }

    public function render()
    {
        return view('livewire.profile.update-account-information-form', [
            'client' => $this->client
        ]);
    }
}
