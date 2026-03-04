<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\User;
use App\Traits\AuthorizeClients;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ResultAnnouncement extends Component
{
    use AuthorizeClients;

    public $id; // Announce id component
    public $announce_saved;

    public function mount()
    {
        if (!$this->announcement) return $this->redirect('/', true);

        $client = $this->getAuthClientWithAccount();

        if ($this->announcement->pro) {
            if (!$this->isAuthClientProVerifiedAndCurrent()) {
                return $this->redirect('/panel', true);
            }
            if ($client->account && !$this->announcement->profesions->contains($client->profesion_id)) {
                return $this->redirect('/prohibido', true);
            }
        }
    }

    #[Computed]
    public function announcement()
    {
        return Announcement::with(['company.companyType', 'announceSuggests', 'profesions:id,profesion_name'])->find($this->id);
    }

    #[Computed]
    public function suggests()
    {
        return $this->announcement->getSuggests(
            $this->announcement->id,
            $this->announcement->area_id
        )->take(6)->get();
    }

    public function saveAnnounce($id)
    {
        if (!auth()->check())
            return $this->redirectRoute('register', navigate: true);

        $user = User::find(auth()->user()->id);
        if (!$user->myAnnounces->contains($id))
            $user->myAnnounces()->attach($id);
    }

    public function removeAnnounce($id)
    {
        if (!auth()->check())
            return $this->redirectRoute('register', navigate: true);

        $user = User::find(auth()->user()->id);
        $user->myAnnounces()->detach($id);
    }

    public function formatDate($datetime)
    {
        return Carbon::parse($datetime)->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');
    }

    public function render()
    {
        return view('livewire.web.result-announcement', [
            'announcement' => $this->announcement,
            'suggests' => $this->suggests,
            'total_locations' => Location::count(),
            'client' => $this->getAuthClientWithAccount(),
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent(),
        ]);
    }
}
