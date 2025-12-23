<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Livewire\Component;

class PurchaseAccount extends Component
{
    // Component parameters
    public $account_type_id;
    // Component propeties
    public $location_id, $profesion_id;
    // Component data
    public $client, $account_type, $locations, $profesions;
    public $small_step = 1;

    public function mount()
    {
        if (intval($this->account_type_id) !== 1 && AccountType::where('id', $this->account_type_id)->exists() && auth()->check()) {
            $this->client = User::with(['profesion', 'location'])
                ->select('id', 'name', 'phone', 'location_id', 'profesion_id')
                ->find(auth()->user()->id);
            $this->location_id = $this->client->location->id;
            $this->profesion_id = $this->client->location->id;
            $this->account_type = AccountType::find($this->account_type_id);
        } else {
            $this->redirect('/', true);
        }
    }

    public function confirmAndSave()
    {
        $this->validate([
            'profesion_id' => 'required|exists:profesions,id',
            'location_id' => 'required|exists:locations,id',
            'account_type_id' => 'required|exists:account_types,id'
        ]);
        $this->client->update([
            'location_id' => intval($this->location_id),
            'profesion_id' => intval($this->profesion_id)
        ]);
        $this->client->account->update([
            'account_type_id' => intval($this->account_type_id),
            'verified_payment' => false
        ]);
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function backToDashboard()
    {
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function downloadQR()
    {
        return response()->download(storage_path('app/public/img/tbn-qr.png'), 'trabajonautas.png');
    }

    public function render()
    {
        $this->locations = Location::select(['id', 'location_name'])->get();
        $this->profesions = Profesion::select(['id', 'profesion_name'])->get();

        return view('livewire.web.purchase-account');
    }
}
