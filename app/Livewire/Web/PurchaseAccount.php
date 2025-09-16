<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Livewire\Component;

class PurchaseAccount extends Component
{
    public $account_type_id;  // Component parameter
    public $client, $pro_verified = false;
    public $account_type, $user_profesions, $user_location;
    public $locations, $profesions;

    public function mount()
    {
        $exists_type_id = AccountType::where('id', $this->account_type_id)->exists();
        if ($exists_type_id && auth()->check()) {
            $this->account_type = AccountType::where('id', $this->account_type_id)->first();
            $this->client = User::with('account.accountType')->find(auth()->user()->id);
            $this->pro_verified = auth()->user()->roles->pluck('name')->first() === env('CLIENT_ROLE')
                ? $this->client->account->verified_payment : true;

            $this->user_profesions = $this->client->myProfesions->pluck('id');
            $this->user_location = $this->client->location->id;
        } else {
            $this->redirect('/', true);
        }
    }

    public function saveChanges($user_profesions)
    {
        $this->user_profesions = $user_profesions;
        $user = User::find(auth()->user()->id);
        $this->validate([
            'user_profesions' => 'required|array',
            'user_profesions.*' => 'exists:profesions,id',
            'user_location' => 'required|exists:locations,id',
            'account_type_id' => 'required|exists:account_types,id'
        ]);
        $this->client->myProfesions()->sync($this->user_profesions);
        $this->client->account->update([
            'account_type_id' => intval($this->account_type_id),
            'verified_payment' => false
        ]);
        $user->update([
            'register_completed' => true,
            'location_id' => intval($this->user_location)
        ]);
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function downloadQR()
    {
        return response()->download(storage_path('app/public/img/tbn-qr.png'), 'trabajonautas.png');
    }

    public function render()
    {
        $this->profesions = Profesion::select(['id', 'profesion_name'])->get();
        $this->locations = Location::all();
        return view('livewire.web.purchase-account');
    }
}
