<?php

namespace App\Livewire\Panel;

use App\Models\User;
use Livewire\Component;

class PendingPayment extends Component
{
    public $user_id;
    public ?string $qr_image      = null;
    public ?string $qr_id         = null;
    public ?string $qr_expires_at = null;
    public ?string $account_name  = null;
    public ?float  $amount        = null;

    public function mount()
    {
        $user = User::find($this->user_id);
        $subscription = $user->latestPendingSubscription()
            ->whereNotNull('qr_id')
            ->with('type')
            ->first();

        if (!$subscription) {
            redirect()->route('dashboard');
            return;
        }

        $this->qr_image      = $subscription->qr_image;
        $this->qr_id         = $subscription->qr_id;
        $this->qr_expires_at = $subscription->qr_expires_at?->format('d/m/Y H:i');
        $this->amount        = $subscription->price;
        $this->account_name  = $subscription->type?->name;
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
