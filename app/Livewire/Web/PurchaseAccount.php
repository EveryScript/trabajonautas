<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\TbnSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PurchaseAccount extends Component
{
    #[Locked]
    public $account_type_id;
    // Component propeties
    public $location_id, $profesion_id;
    public $client;
    public $small_step = 1;

    public function mount()
    {
        if (!auth()->check() || !$this->account_type_id || $this->account_type_id == 1)
            return $this->redirect('/panel', navigate: true);

        $user = auth()->user();

        $this->client = User::with(['account.type', 'location', 'profesion'])
            ->select('id', 'name', 'phone', 'location_id', 'profesion_id')
            ->findOrFail($user->id);

        if ($this->client->latestPendingSubscription)
            return $this->redirect('/panel', navigate: true);

        $this->location_id = $this->client->location_id;
        $this->profesion_id = $this->client->profesion_id;
    }

    public function confirmAndSave()
    {
        $validated = $this->validate([
            'profesion_id' => 'required|exists:profesions,id',
            'location_id' => 'required|exists:locations,id',
            'account_type_id' => 'required|exists:account_types,id'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $acc_type = AccountType::select('price')->findOrFail($this->account_type_id);

                $this->client->update([
                    'location_id' => $validated['location_id'],
                    'profesion_id' => $validated['profesion_id']
                ]);
                $this->client->subscriptions()->create([
                    'account_type_id' => $this->account_type_id,
                    'price' => $acc_type->price
                ]);

                return $this->redirectRoute('dashboard', navigate: true);
            });
        } catch (\Exception $e) {
            Log::error("Error al comprar la cuenta: " . $e->getMessage());
            $this->addError('transaction', 'No pudimos procesar tu pago. Revisa los datos.');
        }
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function backToDashboard()
    {
        $this->redirectRoute('dashboard', navigate: true);
    }

    #[Computed]
    public function profesions()
    {
        return Profesion::select(['id', 'profesion_name'])->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::select(['id', 'location_name'])->get();
    }

    #[Computed]
    public function accountType()
    {
        return AccountType::select(['id', 'name', 'price', 'duration_days'])->find($this->account_type_id);
    }

    public function render()
    {
        return view('livewire.web.purchase-account', [
            'qr_pro' => TbnSetting::where('key', 'qr_pro')->first(),
            'qr_promax' => TbnSetting::where('key', 'qr_promax')->first()
        ]);
    }
}
