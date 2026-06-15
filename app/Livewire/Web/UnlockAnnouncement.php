<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UnlockAnnouncement extends Component
{
    public $id = null; // Announce parameter
    public $coins = null;
    public $announcement = null;

    public function mount()
    {
        /** @var User $user */ // Casting de User
        $user = auth()->user();
        if ($this->id && !Announcement::find($this->id))
            return $this->redirect('/', navigate: true);
        if ($user->unlockedAnnounces()->where('announcement_id', $this->id)->exists())
            return $this->redirect('/panel', true);

        $this->coins = auth()->user()->coins;
        $this->announcement = Announcement::findOrFail($this->id);
    }
    public function unlock()
    {
        /** @var User $user */ // Casting de User
        $user = auth()->user();

        DB::transaction(function () use ($user) {
            $user->decrement('coins', 1);
            $user->unlockedAnnounces()->attach($this->id);
        });

        return $this->redirect('/convocatoria/' . $this->id, navigate: true);
    }
    public function render()
    {
        return view('livewire.web.unlock-announcement', [
            'coins' => $this->coins,
            'announcement' => $this->announcement
        ]);
    }
}
