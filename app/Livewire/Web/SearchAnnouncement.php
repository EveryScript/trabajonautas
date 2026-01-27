<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\Profesion;
use App\Traits\AuthorizeClients;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchAnnouncement extends Component
{
    use AuthorizeClients;

    public $profesion_id, $location_id;

    protected function announceBaseQuery()
    {
        return Announcement::query()->where('expiration_time', '>=', now())
            // Filter by profesion
            ->when($this->profesion_id, function ($query, $profesion_id) {
                $query->whereHas('profesions', function ($q) use ($profesion_id) {
                    $q->where('profesions.id', $profesion_id);
                });
            })
            // Filter by location
            ->when($this->location_id, function ($query, $location_id) {
                $query->whereHas('locations', function ($q) use ($location_id) {
                    $q->where('locations.id', $location_id);
                });
            });
    }

    #[Computed]
    public function announcements()
    {
        return $this->announceBaseQuery()
            ->select('id', 'announce_title', 'company_id', 'pro', 'expiration_time', 'created_at', 'updated_at')
            ->with([
                'company:id,company_name,company_image',
                'locations:id,location_name'
            ])
            ->latest('updated_at')
            ->simplePaginate(12);
    }

    #[Computed]
    public function hasResults()
    {
        return $this->announcements->isNotEmpty();
    }

    #[Computed]
    public function recommends()
    {
        if (!$this->profesion_id)
            return collect();

        $profesion = Profesion::find($this->profesion_id);
        if (!$profesion)
            return collect();

        return Announcement::query()
            ->select('id', 'announce_title', 'company_id', 'area_id', 'pro', 'expiration_time', 'created_at', 'updated_at')
            ->with([
                'company:id,company_name,company_image',
                'locations:id,location_name',
                'profesions:id,profesion_name,area_id',
                'area:id,area_name'
            ])
            ->where('expiration_time', '>=', now())
            // Recommends from same area
            ->whereHas('profesions', function ($q) use ($profesion) {
                $q->where('area_id', $profesion->area_id);
            })
            // Recommends from the same location if has results
            ->when($this->hasResults() && $this->location_id, function ($q) {
                $q->whereHas('locations', fn($l) => $l->where('locations.id', $this->location_id));
            })
            // Remove actual results
            ->whereNotIn('announcements.id', $this->announcements->pluck('id')->toArray())
            ->limit($this->hasResults() ? 6 : 10)
            ->get();
    }

    #[Computed]
    public function totalResults()
    {
        return $this->announceBaseQuery()->count();
    }

    public function render()
    {
        return view('livewire.web.search-announcement', [
            'announcements' => $this->announcements,
            'recommends' => $this->recommends,
            'hasResults' => $this->hasResults,
            'profesions' => Profesion::select('id', 'profesion_name')->orderBy('profesion_name')->get(),
            'locations' => Location::select('id', 'location_name')->orderBy('location_name')->get(),
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent()
        ]);
    }
}
