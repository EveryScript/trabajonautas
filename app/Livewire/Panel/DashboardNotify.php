<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Traits\AuthorizeClients;
use Livewire\Component;

class DashboardNotify extends Component
{
    use AuthorizeClients;
    // Parameters
    public $title, $description;
    public $client_location_id = null;
    public $client_profesion_id = null;

    public function render()
    {
        $notify_announcements = Announcement::with(['company', 'locations'])
            ->where('expiration_time', '>=', now())
            ->whereHas('locations', fn($sub) => $sub->where('location_id', $this->client_location_id))
            ->whereHas('profesions', fn($sub) => $sub->where('profesion_id', $this->client_profesion_id))
            ->orderBy('updated_at', 'DESC')->limit(10)->get();

        return view('livewire.panel.dashboard-notify', [
            'announces' => $notify_announcements,
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent()
        ]);
    }
}
