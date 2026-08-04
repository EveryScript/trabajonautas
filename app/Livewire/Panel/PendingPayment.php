<?php

namespace App\Livewire\Panel;

use App\Models\TbnSetting;
use App\Models\User;
use Livewire\Component;

class PendingPayment extends Component
{
    public $user_id;
    public ?string $client_name, $client_email;
    public ?string $qr_image      = null;
    public ?string $qr_id         = null;
    public ?string $qr_expires_at = null;
    public ?string $account_name  = null;
    public ?float  $amount        = null;
    public ?string $qr_static     = null; // Fallback
    public bool    $is_dynamic_qr = false; // true = Baneco QR  |  false = Default QR

    public function mount()
    {
        $user = User::find($this->user_id);
        $subscription = $user->latestPendingSubscription()->with('type')->first();

        if (!$subscription) {
            redirect()->route('dashboard');
            return;
        }

        $this->client_name = $user->name;
        $this->client_email = $user->email;
        $this->amount       = $subscription->price;
        $this->account_name = $subscription->type?->name;
        $this->qr_id        = $subscription->qr_id;
        $this->qr_expires_at = $subscription->qr_expires_at?->format('d/m/Y H:i');

        // Flag to show QR Baneco/Default
        if ($subscription->qr_image) {
            $this->qr_image      = $subscription->qr_image;
            $this->is_dynamic_qr = true;
        } else {
            $key = $subscription->account_type_id == 2 ? 'qr_pro' : 'qr_promax';
            $setting = TbnSetting::where('key', $key)->first();
            $this->qr_static = $setting?->value;
        }
    }

    public function checkPayment()
    {
        $user = User::find($this->user_id);
        if (!$user->hasPendingPayment()) {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.panel.pending-payment');
    }
}
