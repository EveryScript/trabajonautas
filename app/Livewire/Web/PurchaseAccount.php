<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\TbnSetting;
use App\Models\User;
use App\Services\BanecoService;
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

                $acc_type = AccountType::select('price')
                    ->findOrFail($this->account_type_id);

                $this->client->update([
                    'location_id' => $validated['location_id'],
                    'profesion_id' => $validated['profesion_id']
                ]);

                $subscription = $this->client->subscriptions()->create([
                    'account_type_id'  => $this->account_type_id,
                    'price'            => $acc_type->price,
                    'verified_payment' => false,
                ]);

                $expiresAt = now()->addDay();

                $baneco = app(BanecoService::class);

                $qr = $baneco->generateQR(
                    transactionId: (string) $subscription->id,
                    amount: (float) $acc_type->price,
                    description: 'Suscripción ' . $this->client->name,
                    dueDate: $expiresAt->format('Y-m-d'),
                );

                $subscription->update([
                    'qr_id'         => $qr['qrId'],
                    'qr_image'      => $qr['qrImage'],
                    'qr_expires_at' => $expiresAt,
                ]);
            });

            $this->redirectRoute('dashboard', navigate: true);
        } catch (\Exception $e) {
            Log::error('Error al comprar la cuenta', [
                'user_id' => $this->client->id,
                'error'   => $e->getMessage(),
            ]);
            $this->addError(
                'transaction',
                'No pudimos procesar la compra. Por favor, intenta nuevamente.'
            );
        }
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
        return view('livewire.web.purchase-account');
    }
}
