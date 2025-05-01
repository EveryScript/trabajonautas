<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\Profesion;
use App\Models\User;
use Livewire\Component;

class PurchaseAccount extends Component
{
    public $profesions, $account_type_id;

    public function saveChanges($type_id, $profesions)
    {
        $this->profesions = $profesions;
        $this->account_type_id = intval($type_id);

        $this->validate([
            'profesions' => 'required|array',
            'profesions.*' => 'exists:profesions,id',
            'account_type_id' => 'required|exists:account_types,id'
        ]);
        $user = User::find(auth()->user()->id);
        $user->syncRoles([env('PRO_CLIENT_ROLE')]);
        $user->myProfesions()->attach($this->profesions);
        $user->account()->update([
            'account_type_id' => intval($this->account_type_id)
        ]);
        $this->redirectRoute('dashboard', navigate: true);
    }
    public function render()
    {
        $account_types = AccountType::all();
        $this->profesions = Profesion::select(['id', 'profesion_name'])->get();
        return view('livewire.web.purchase-account', [
            'account_types' => $account_types
        ]);
    }
}
