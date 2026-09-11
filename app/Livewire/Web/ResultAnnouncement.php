<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\User;
use App\Traits\AuthorizeClients;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ResultAnnouncement extends Component
{
    use AuthorizeClients;

    public int $id; // Announce id component

    public function mount()
    {
        if (!$this->announcement)
            return $this->redirect('/', true);
        if ($this->announcement->pro && !$this->isAuthClientProVerifiedAndCurrent())
            return $this->redirect('/panel', true);
    }

    public function unlock()
    {
        /** @var User $user */ // Casting de User
        $user = auth()->user();

        DB::transaction(function () use ($user) {
            $user->decrement('coins', 1);
            $user->unlockedAnnounces()->attach($this->id);
        });
    }

    #[Computed]
    public function announcement()
    {
        return Announcement::with([
            'company.companyType',
            'profesions:id,profesion_name',
            'locations:id,location_name'
        ])->find($this->id);
    }

    public function saveAnnounce(int $id)
    {
        if (!auth()->check())
            return $this->redirectRoute('register', navigate: true);

        $user = User::find(auth()->user()->id);
        if (!$user->myAnnounces->contains($id))
            $user->myAnnounces()->attach($id);
    }

    public function removeAnnounce(int $id)
    {
        if (!auth()->check())
            return $this->redirectRoute('register', navigate: true);

        $user = User::find(auth()->user()->id);
        $user->myAnnounces()->detach($id);
    }

    public function formatDate(string $datetime)
    {
        return Carbon::parse($datetime)->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');
    }

    public function render()
    {
        return view('livewire.web.result-announcement', [
            'announcement' => $this->announcement,
            'total_locations' => Cache::remember('total_locations_count', 86400, fn() => Location::count()),
            'client' => $this->getAuthClientWithAccount(),
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent(),
            'coins' => $this->getAuthClientWithAccount()->coins ?? 0
        ]);
    }
}
