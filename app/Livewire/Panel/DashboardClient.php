<?php

namespace App\Livewire\Panel;

use App\Models\User;
use App\Services\FirebaseNotificationService;
use App\Traits\AuthorizeClients;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardClient extends Component
{
    use WithPagination;
    use AuthorizeClients;

    // Parameter
    public $user_id;

    public function mount()
    {
        if (!auth()->check())
            return $this->redirect('/', true);

        $this->updateAccountClientIfNotCurrent();
    }

    #[Computed]
    public function client()
    {
        return User::select('id', 'name', 'location_id', 'profesion_id', 'grade_profile_id')
            ->with([
                'account' => function ($query) {
                    $query->select('id', 'user_id', 'account_type_id', 'verified_payment', 'limit_time', 'device_token');
                },
                'location' => function ($query) {
                    $query->select('id', 'location_name');
                },
                'profesion' => function ($query) {
                    $query->select('id', 'profesion_name');
                },
                'gradeProfile' => function ($query) {
                    $query->select('id', 'profile_name');
                },
                'account.accountType:id,name'
            ])->find($this->user_id);
    }

    public function updateAccountClientIfNotCurrent()
    {
        if (
            $this->client->account->account_type_id !== 1 &&
            $this->client->account->verified_payment &&
            Carbon::parse($this->client->account->limit_time)->isBefore(Carbon::now())
        ) {
            $this->client->account->update([
                'verified_payment' => false,
                'account_type_id' => 1
            ]);
        }
    }

    public function checkIfTokenExists()
    {
        return $this->client->account->device_token ? true : false;
    }

    public function isClientAccountExpired()
    {
        if(!$this->client->account->limit_time)
            return false;

        return Carbon::parse($this->client->account->limit_time)->isBefore(Carbon::now());
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
        return view('livewire.panel.dashboard-client', [
            'client' => $this->client,
            'client_account_expire_days' => Carbon::now()->diffInDays(Carbon::parse($this->client->account->limit_time)),
            'client_account_expired' => $this->isClientAccountExpired()
        ]);
    }
}
