<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use App\Traits\AuthorizeClients;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardClient extends Component
{
    use WithPagination;
    use AuthorizeClients;

    #[Locked] // Don't show user_id
    public $user_id;
    public int $refresh_version = 0;

    public function mount()
    {
        if (!auth()->check())
            return $this->redirect('/', true);

        $this->user_id = $this->user_id ?? auth()->id(); // Assign id if auth
    }

    #[Computed(persist: true)]
    public function client()
    {
        return User::select('id', 'name', 'phone', 'location_id', 'profesion_id', 'grade_profile_id', 'last_announce_check')
            ->with([
                'account:id,user_id,account_type_id,limit_time,device_token',
                'account.type:id,name,price,duration_days',
                'location:id,location_name',
                'profesion:id,profesion_name,area_id',
                'gradeProfile:id,profile_name',
                'myAnnounces:id'
            ])->findOrFail($this->user_id);
    }

    #[Computed]
    public function hasNewAnnounces()
    {
        $client = $this->client;

        return Announcement::where('expiration_time', '>=', now())
            ->where('updated_at', '>', $client->last_announce_check ?? now()->subDays(7))
            ->whereHas('locations', fn($q) => $q->where('locations.id', $client->location_id))
            ->whereHas('profesions', fn($q) => $q->where('profesions.id', $client->profesion_id))
            ->exists();
    }

    public function checkIfTokenExists()
    {
        return $this->client->account && $this->client->account->device_token ? true : false;
    }

    public function saveClientToken($token)
    {
        if ($token && $this->client->account) {
            $this->client->account->update(['device_token' => $token]);
            $this->dispatch('token-saved');
        }
    }

    public function updateLastCheck()
    {
        if ($this->hasNewAnnounces) {
            $this->client->update(['last_announce_check' => now()]);
            $this->refresh_version++; // Update dashboard-card components 
            unset($this->client);
            $this->dispatch('announcements-updated');
        }
    }

    public function render()
    {
        return view('livewire.panel.dashboard-client');
    }
}
