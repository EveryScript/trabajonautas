<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchAnnouncement extends Component
{
    use WithPagination;
    #[Url]
    public $title;
    public $locations, $companies;
    public $search_title, $search_location;
    public $announce_area;

    public function mount($title = null)
    {
        if ($title) {
            $this->search_title = $this->title;
        }
        $this->locations = Location::select(['id', 'location_name'])->get();
    }

    public function searchAnnounces($title, $location_id)
    {
        $this->search_title = $title;
        $this->search_location = $location_id;
    }

    public function render()
    {
        sleep(0.8); // Delay or loading
        $user = Auth::check() ? User::find(Auth::user()->id) : null;
        $announcements = Announcement::orderBy('updated_at', 'DESC')
            ->when(!$user || $user->hasRole(env('FREE_CLIENT_ROLE')), fn($query)
            => $query->where('pro', false))
            ->when($this->search_title, fn($query)
            => $query->where('announce_title', 'LIKE', '%' . $this->search_title . '%'))
            ->when(
                $this->search_location,
                fn($query)
                => $query->whereHas('locations', fn($sub_query)
                => $sub_query->where('location_id', $this->search_location))
            );
        return view('livewire.web.search-announcement', [
            'announcements' => $announcements->simplePaginate(12)
        ]);
    }
}
