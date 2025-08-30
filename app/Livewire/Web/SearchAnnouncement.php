<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SearchAnnouncement extends Component
{
    use WithPagination;
    public $title;  // Component parameter
    public $client, $pro_verified = false;
    public $locations, $companies;
    public $search_title, $search_location;

    public function mount()
    {
        if (auth()->check()) {
            $this->client = User::with('account.accountType')->find(auth()->user()->id);
            $this->pro_verified = auth()->user()->roles->pluck('name')->first() === env('CLIENT_ROLE')
                ? $this->client->account->verified_payment : true;
        }
        $this->locations = Location::select(['id', 'location_name'])->get();
    }

    public function searchAnnounces($title, $location_id)
    {
        $this->search_title = $title;
        $this->search_location = $location_id;
        $this->title = null;
    }

    public function clearSearch()
    {
        $this->search_title = null;
        $this->search_location = null;
        $this->title = null;
    }

    public function render()
    {
        if ($this->title) $this->search_title = $this->title;
        else $this->title = null;
        $base_query = Announcement::where('expiration_time', '>=', now())
            ->orderBy('updated_at', 'DESC');

        $filter_query = (clone $base_query)
            ->when($this->search_title, fn($query) => $query->where('announce_title', 'LIKE', '%' . $this->search_title . '%'))
            ->when($this->search_location, fn($query) => $query->whereHas('locations', fn($sub_query) => $sub_query
                ->where('location_id', $this->search_location)));

        $count_results = $filter_query->count();

        $announcements = $count_results > 0
            ? $filter_query->simplePaginate(12)
            : $base_query->simplePaginate(12);

        return view('livewire.web.search-announcement', [
            'announcements' => $announcements,
            'count_results' => $count_results,
            'search_title' => $this->search_title
        ]);
    }
}
