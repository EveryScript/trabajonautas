<?php

namespace App\Livewire\Panel;

use App\Livewire\Forms\ClientForm;
use App\Mail\WelcomeAccount;
use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\TbnSetting;
use App\Models\User;
use App\Services\BanecoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FirstSteps extends Component
{
    public ClientForm $form;

    public $user_id;
    public $user;
    public $country_code = '+591';

    public function mount()
    {
        $this->user = User::with(['account.type'])
            ->select('id', 'name', 'email', 'phone')
            ->find($this->user_id);
    }

    #[Computed]
    public function locations()
    {
        return Location::select('id', 'location_name')->get();
    }

    #[Computed]
    public function profesions()
    {
        return Profesion::select('id', 'profesion_name')->orderBy('profesion_name', 'ASC')->get();
    }

    #[Computed]
    public function account_types()
    {
        return AccountType::select('id', 'name', 'price', 'duration_days')->get();
    }

    public function confirmAndSave()
    {
        try {
            $account_type_id   = (int) $this->form->account_type_id;
            $account_type_name = $this->account_types->find($account_type_id)->name;

            // Save client data and send Welcome mail
            $this->form->store($this->user, $this->country_code);
            Mail::to($this->user->email)->queue(new WelcomeAccount($this->user, $account_type_name));

            // Generate QR (PRO or PRO-MAX)
            if ($account_type_id !== 1) {
                $subscription = $this->user->subscriptions()
                    ->where('verified_payment', false)
                    ->latest()
                    ->first();

                if ($subscription) {
                    try {
                        $baneco  = app(BanecoService::class);
                        $dueDate = now()->addDay()->format('Y-m-d');
                        $qr      = $baneco->generateQR(
                            transactionId: (string) $subscription->id,
                            amount: (float) $this->form->account_price,
                            description: 'Suscripción ' . $this->user->name,
                            dueDate: $dueDate,
                        );
                        $subscription->update([
                            'qr_id'         => $qr['qrId'],
                            'qr_image'      => $qr['qrImage'],
                            'qr_expires_at' => now()->addDay(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error('No se pudo generar QR para suscripción', [
                            'subscription_id' => $subscription->id,
                            'user_id'         => $this->user->id,
                            'error'           => $e->getMessage(),
                        ]);
                    }
                }
            }
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('confirmAndSave failed', ['error' => $e->getMessage()]);
            $this->dispatch('register-failed');
        }
    }

    public function render()
    {
        return view('livewire.panel.first-steps', [
            'profesions'    => $this->profesions,
            'locations'     => $this->locations,
            'account_types' => $this->account_types,
            'tbn_coins'     => TbnSetting::where('key', 'tbn_coins')->value('value'),
        ]);
    }
}
