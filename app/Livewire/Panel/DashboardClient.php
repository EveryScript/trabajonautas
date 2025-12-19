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
    public $client, $expiration_days;
    protected $VAPID_KEY;

    public function mount()
    {
        $this->client = User::with(['gradeProfile', 'account.accountType'])->find($this->user_id);
        $this->expiration_days = Carbon::now()->diffInDays(Carbon::parse($this->client->account->limit_time));
        $this->VAPID_KEY = env('VAPID_KEY');
    }
    public function render()
    {
        return view('livewire.panel.dashboard-client');
    }
}
