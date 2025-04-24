<?php

namespace App\Livewire\Web;

use App\Models\Profesion;
use App\Models\User;
use Livewire\Component;

class PurchaseAccount extends Component
{
    public $profesions, $duration_days;

    public function saveChanges($duration_days, $profesions)
    {
        $this->profesions = $profesions;
        $this->duration_days = $duration_days;

        $this->validate([
            'profesions' => 'required|array',
            'profesions.*' => 'exists:profesions,id',
            'duration_days' => 'required|numeric|in:0,30,60'
        ]);
        $user = User::find(auth()->user()->id);
        $user->syncRoles([env('PRO_CLIENT_ROLE')]);
        $user->proAccount()->create([
            'purchased_at' => now(),
            'duration_days' => $this->duration_days
        ]);
        $this->redirectRoute('dashboard', navigate: true);
    }
    public function render()
    {
        $this->profesions = Profesion::select(['id', 'profesion_name'])->get();
        return view('livewire.web.purchase-account');
    }
}
