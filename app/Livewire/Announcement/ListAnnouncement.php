<?php

namespace App\Livewire\Announcement;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithPagination;

class ListAnnouncement extends Component
{
    use WithPagination;

    public $search;

    public function delete($id)
    {
        Announcement::find($id)->delete();
    }

    public function render()
    {
        $base_query = Announcement::with('profesions')->orderBy('updated_at', 'DESC');

        $filter_query = (clone $base_query)
            ->when($this->search, fn($query) => $query->where('announce_title', 'LIKE', '%' . $this->search . '%'));

        $count_results = $filter_query->count();

        $announcements = $count_results > 0
            ? $filter_query->paginate(7)
            : $base_query->paginate(7);
        return view('livewire.announcement.list-announcement', [
            'announcements' => $announcements,
            'count_results' => $count_results,
            'search_title' => $this->search
        ]);
    }
}
