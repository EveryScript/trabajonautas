<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Company;
use App\Models\Location;
use App\Models\Profesion;
use App\Traits\AuthorizeClients;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class SearchAnnouncement extends Component
{
    use AuthorizeClients;

    #[Url(keep: false)]
    public ?int $profesion_id = null;

    #[Url(keep: false)]
    public ?int $location_id = null;

    #[Url(keep: false)]
    public ?int $company_id = null;

    #[Url(keep: false)]
    public ?string $post_date = null;

    public int $per_page = 12;

    public function mount()
    {
        if ($this->profesion_id) {
            $exists = Profesion::where('id', $this->profesion_id)->exists();
            if (!$exists) {
                $this->profesion_id = null;
            }
        }

        if ($this->location_id) {
            $exists = Location::where('id', $this->location_id)->exists();
            if (!$exists) {
                $this->location_id = null;
            }
        }

        if ($this->company_id) {
            $exists = Company::where('id', $this->company_id)->exists();
            if (!$exists) {
                $this->company_id = null;
            }
        }

        if ($this->post_date) {
            try {
                Carbon::createFromFormat('d/m/Y', $this->post_date);
            } catch (\Exception $e) {
                $this->post_date = null;
            }
        }
    }

    /**
     * Resets pagination limit back to default (12) whenever any filter updates.
     */
    public function updating($name)
    {
        if (in_array($name, ['profesion_id', 'location_id', 'company_id', 'post_date'])) {
            $this->per_page = 12;
        }
    }

    protected function announceBaseQuery()
    {
        return Announcement::query()
            ->where('expiration_time', '>=', now())
            // Filter no scheduled announcements
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
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
            })
            // Filter by company
            ->when($this->company_id, function ($query, $company_id) {
                $query->where('company_id', $company_id);
            })
            // Filter by created_at date
            ->when($this->post_date, function ($query, $post_date) {
                try {
                    $parsedDate = Carbon::createFromFormat('d/m/Y', $post_date);
                    $query->whereBetween('created_at', [
                        $parsedDate->copy()->startOfDay(),
                        $parsedDate->copy()->endOfDay(),
                    ]);
                } catch (\Exception $e) {
                    // Silently ignore invalid date formats
                }
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
            ->limit($this->per_page)
            ->get();
    }

    public function loadMore()
    {
        $this->per_page += 12;
    }

    #[Computed]
    public function hasResults()
    {
        return $this->announcements->isNotEmpty();
    }

    #[Computed]
    public function recommends()
    {
        if (!$this->profesion_id) {
            return collect();
        }

        $profesion = Profesion::find($this->profesion_id);
        if (!$profesion) {
            return collect();
        }

        return Announcement::query()
            ->select('id', 'announce_title', 'company_id', 'pro', 'expiration_time')
            ->with(['company:id,company_name,company_image', 'locations:id,location_name'])
            ->where('expiration_time', '>=', now())
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->whereHas('profesions.areas', function ($q) use ($profesion) {
                $q->where('areas.id', $profesion->area_id);
            })
            ->whereNotIn('id', $this->announcements->pluck('id')->toArray())
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function totalResults()
    {
        return $this->announceBaseQuery()->count();
    }

    #[Computed]
    public function profesions()
    {
        return Cache::remember('web-profesions', 3600, fn() => Profesion::select('id', 'profesion_name')->orderBy('profesion_name')->get());
    }

    #[Computed]
    public function locations()
    {
        return Cache::remember('web-locations', 3600, fn() => Location::select('id', 'location_name')->orderBy('location_name')->get());
    }

    #[Computed]
    public function companies()
    {
        return Cache::remember('web-companies', 3600, fn() => Company::select('id', 'company_name')->orderBy('company_name')->get());
    }

    public function render()
    {
        return view('livewire.web.search-announcement', [
            'announcements' => $this->announcements,
            'recommends' => $this->recommends,
            'hasResults' => $this->hasResults,
            'profesions' => $this->profesions,
            'locations' => $this->locations,
            'companies' => $this->companies,
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent()
        ]);
    }
}
