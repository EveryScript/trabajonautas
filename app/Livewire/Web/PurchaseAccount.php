<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\TbnSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        if (!auth()->check() && $this->account_type_id)
            $this->redirect('/panel', true);

        if (!AccountType::where('id', $this->account_type_id)->exists() || $this->account_type_id == 1)
            $this->redirect('/panel', true);

        $this->client = User::with(['profesion', 'location', 'account.type'])
            ->select('id', 'name', 'email', 'phone', 'location_id', 'profesion_id')
            ->find(auth()->user()->id);

        if ($this->client->latestPendingSubscription)
            $this->redirect('/panel', true);

        $this->location_id = $this->client->location->id;
        $this->profesion_id = $this->client->profesion->id;
    }

    public function confirmAndSave()
    {
        $this->validate([
            'profesion_id' => 'required|exists:profesions,id',
            'location_id' => 'required|exists:locations,id',
            'account_type_id' => 'required|exists:account_types,id'
        ]);

        try {
            DB::transaction(function () {
                $this->client->update([
                    'location_id' => intval($this->location_id),
                    'profesion_id' => intval($this->profesion_id)
                ]);
                $this->client->subscriptions()->create([
                    'account_type_id' => intval($this->account_type_id),
                    'price' => $this->account_type->price
                ]);

                $this->redirectRoute('dashboard', navigate: true);
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->addError('transaction', 'Error al procesar la solicitud');
        }
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function backToDashboard()
    {
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        $this->locations = Location::select(['id', 'location_name'])->get();
        $this->profesions = Profesion::select(['id', 'profesion_name'])->get();
        $this->account_type = AccountType::select(['id', 'name', 'price', 'duration_days'])->where('id', $this->account_type_id)->first();
        $qr_pro = TbnSetting::where('key', 'qr_pro')->first();
        $qr_promax = TbnSetting::where('key', 'qr_promax')->first();

        return view('livewire.web.purchase-account', [
            'qr_pro' => $qr_pro,
            'qr_promax' => $qr_promax
        ]);
    }
}
