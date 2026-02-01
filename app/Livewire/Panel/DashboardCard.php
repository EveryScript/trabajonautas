<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Traits\AuthorizeClients;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardCard extends Component
{
    use AuthorizeClients;
    // Parameters
    public $title, $description;
    public $my_announces_mode = false;
    public $client;
    // Propeties
    public $per_page = 7;

    #[Computed]
    public function announcements()
    {
        $query = Announcement::where('expiration_time', '>=', now())
            ->whereHas('profesions', fn($sub) => $sub->where('profesion_id', $this->client->profesion->id))
            ->selectRaw(
                "id, announce_title, company_id, area_id, pro, expiration_time, created_at, updated_at,
                (created_at >= ?) as is_today,
                (created_at >= ?) as is_week,
                (created_at >= ?) as is_month,
                (CASE 
                    WHEN EXISTS (SELECT 1 FROM announcement_location WHERE announcement_id = announcements.id AND location_id = ?) 
                        AND EXISTS (SELECT 1 FROM announcement_profesion WHERE announcement_id = announcements.id AND profesion_id = ?) THEN 1
                    ELSE 2
                END) as priority_level",
                [now()->startOfDay(), now()->subDays(7), now()->startOfMonth(), $this->client->location_id, $this->client->profesion_id]
            )
            ->with([
                'company:id,company_name,company_image',
                'locations:id,location_name',
                'profesions:id'
            ])
            ->orderBy('priority_level', 'ASC')
            ->latest('updated_at')
            ->take($this->per_page)
            ->get();

        return $query->groupBy('priority_level');
    }

    public function loadMore()
    {
        $this->per_page += 7;
    }

    #[On('announcements-updated')]
    public function refreshComponent() {}

    public function render()
    {
        $hasRecommends = $this->announcements->has(1);
        return view('livewire.panel.dashboard-card', [
            'announces' => $this->my_announces_mode ? $this->client->myAnnounces->groupBy(fn() => 1) : $this->announcements,
            'hasRecommends' => $hasRecommends,
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent()
        ]);
    }
}
