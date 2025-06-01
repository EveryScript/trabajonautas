<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\Profesion;
use App\Models\User;
use Livewire\Component;

class PurchaseAccount extends Component
{
    public $client, $pro_verified = false;
    public $profesions, $account_type_id;

    public function mount()
    {
        if (auth()->check()) {
            $this->client = User::with('account.accountType')->find(auth()->user()->id);
            $this->pro_verified = auth()->user()->roles->pluck('name')->first() === env('CLIENT_ROLE')
                ? $this->client->account->verified_payment : true;
        }
    }

    public function saveChanges($type_id, $profesions)
    {
        $this->profesions = $profesions;
        $this->account_type_id = intval($type_id);

        $this->validate([
            'profesions' => 'required|array',
            'profesions.*' => 'exists:profesions,id',
            'account_type_id' => 'required|exists:account_types,id'
        ]);
        $this->client->myProfesions()->sync($this->profesions);
        $this->client->account->update([
            'account_type_id' => intval($this->account_type_id),
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
