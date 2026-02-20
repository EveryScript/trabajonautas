<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use App\Traits\AuthorizeClients;
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
    }

    #[Computed]
    public function client()
    {
        return User::select('id', 'name', 'phone', 'location_id', 'profesion_id', 'grade_profile_id', 'last_announce_check')
            ->with([
                'account:id,user_id,account_type_id,limit_time,device_token',
                'account.type:id,name,price,duration_days',
                'location:id,location_name',
                'profesion:id,profesion_name,area_id',
                'gradeProfile:id,profile_name',
                'myAnnounces:id,announce_title,company_id,pro,expiration_time,created_at,updated_at'
            ])->find($this->user_id);
    }

    #[Computed]
    public function hasNewAnnounces()
    {
        return Announcement::where('expiration_time', '>=', now())
            ->where('updated_at', '>=', $this->client->last_announce_check)
            ->whereHas('locations', fn($sub) => $sub->where('location_id', $this->client->location->id))
            ->whereHas('profesions', fn($sub) => $sub->where('profesion_id', $this->client->profesion->id))
            ->exists();
    }

    public function checkIfTokenExists()
    {
        return $this->client->account && $this->client->account->device_token ? true : false;
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

    public function updateLastCheck()
    {
        if ($this->hasNewAnnounces) {
            $this->client->update(['last_announce_check' => now()]);
            $this->dispatch('announcements-updated');
        }
    }

    public function render()
    {
        return view('livewire.panel.dashboard-client', [
            'client' => $this->client
        ]);
    }
}
