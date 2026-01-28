<?php

namespace App\Livewire\Profile;

use App\Models\AccountType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateAccountInformationForm extends Component
{
    public $state = [];

    public function mount()
    {
        $user = Auth::user();
        $this->state = $user->account ? $user->account->toArray() : [
            'account_type_id' => ''
        ];
    }

    public function render()
    {
        return view('livewire.profile.update-account-information-form', [
            'account_types' => AccountType::select('id', 'name')->get()
        ]);
    }
}
