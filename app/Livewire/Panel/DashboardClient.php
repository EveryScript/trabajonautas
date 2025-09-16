<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardClient extends Component
{
    use WithPagination;
    public $user_id;            // Parameter
    public $client, $free_client = true, $pro_verified = false;
    public $time_left = 'Tiempo expirado';
    public $alert_time_left = false;
    public $notify_token_actived;

    public function mount()
    {
        $this->client = User::with('account.accountType')->find($this->user_id);
        $this->free_client = $this->client->account->account_type_id == 1;
        $this->pro_verified = $this->client->account->account_type_id !== 1 && $this->client->account->verified_payment;
        // Check Expiration
        if ($this->client->account->limit_time) {
            $limit_time = Carbon::parse($this->client->account->limit_time);
            $now = Carbon::now();
            if ($limit_time->isBefore($now)) {
                $this->client->account->update([
                    'limit_time' => null,
                    'verified_payment' => false,
                    'account_type_id' => 1
                ]);
                $this->free_client = true;
            } else {
                $this->time_left = $now->longAbsoluteDiffForHumans($limit_time);
                if($now->diffInDays($limit_time) <= 5)
                    $this->alert_time_left = true;
            }
        }
    }

    public function verifyHasToken()
    {
        $this->notify_token_actived = $this->client->account->device_token ? true : false;
    }

    public function saveClientToken($token)
    {
        if ($this->client && is_string($token)) {
            $this->client->account->update([
                'device_token' => $token
            ]);
            $this->notify_token_actived = true;
        }
    }

    public function render()
    {
        $suggests = Announcement::where('expiration_time', '>=', now())
            ->when($this->free_client, function ($query) {
                $query->whereHas('locations', fn($subquery) => $subquery->where('location_id', $this->client->location_id));
            })
            ->when(!$this->free_client, function ($query) {
                $query->whereHas('area', fn($subquery) => $subquery->where('id', $this->client->area->id))
                    ->orWhereHas('locations', fn($subquery) => $subquery->where('location_id', $this->client->location_id));
                // $query->whereHas('profesions', fn($subquery) => $subquery->whereIn('profesion_id', $this->client->myProfesions->pluck('id')));
            })
            ->orderBy('updated_at', 'DESC');
        return view('livewire.panel.dashboard-client', [
            'suggests' => $suggests->simplePaginate(8)
        ]);
    }
}
