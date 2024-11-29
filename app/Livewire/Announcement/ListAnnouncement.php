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

    public function countPROClientsByProfesions($profesions)
    {
        return $profesions->sum(function ($profesion) {
            return $profesion->users()->whereHas('roles', fn($query)  => $query->where('name', 'PRO_CLIENT'))->count();
        });
    }

    public function render()
    {
        $announcements = Announcement::with('profesions')->orderBy('updated_at', 'DESC')
            ->when($this->search, fn($query) => $query->where('announce_title', 'LIKE', '%' . $this->search . '%'))
            ->simplePaginate(7);
        return view('livewire.announcement.list-announcement', compact('announcements'));
    }
}
