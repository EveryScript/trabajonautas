<?php

namespace App\Livewire\Panel;

use App\Models\User;
use App\Services\FirebaseNotificationService;
use App\Traits\CheckClientsProVerified;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardClient extends Component
{
    use WithPagination;
    use CheckClientsProVerified;

    // Parameter
    public $user_id;
    // Data
    public $client, $client_account_expiration_days;
    public $VAPID_KEY;

    public function mount()
    {
        $this->client = User::with(['account.accountType'])->find($this->user_id);
        if(intval($this->client->account->accountType->id) !== 1)
            $this->checkIfAccountPROIsExpired();
        $this->client_account_expiration_days = Carbon::now()->diffInDays(Carbon::parse($this->client->account->limit_time));
        $this->VAPID_KEY = env('VAPID_KEY');
    }

    public function checkIfAccountPROIsExpired()
    {
        if (Carbon::parse($this->client->account->limit_time)->isBefore(Carbon::now())) {
            $this->client->account->update([
                'limit_time' => null,
                'verified_payment' => false,
                'account_type_id' => 1
            ]);
        }
    }

    public function checkIfTokenExists()
    {
        return $this->client->account->device_token ? true : false;
    }

    public function saveClientToken($token)
    {
        if ($this->client && is_string($token)) {
            $this->client->account->update([
                'device_token' => $token
            ]);
            $this->dispatch('token-saved');
        }
    }

    public function render()
    {
        return view('livewire.panel.dashboard-client');
    }
}
