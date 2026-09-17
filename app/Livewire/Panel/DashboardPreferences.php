<?php

namespace App\Livewire\Panel;

use App\Models\AnnouncementType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardPreferences extends Component
{
    public string $title;
    public string $description;
    public $types = [];
    public array $excluded = [];

    public function mount(): void
    {
        $this->types = AnnouncementType::orderBy('name')->get();

        /** @var User $user */
        $user = Auth::user();

        $this->excluded = $user
            ->excludedAnnouncementTypes()
            ->pluck('announcement_types.id')
            ->toArray();
    }

    public function save(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $user->excludedAnnouncementTypes()->sync($this->excluded);
        $this->dispatch('preferences-updated');
    }

    public function render()
    {
        return view('livewire.panel.dashboard-preferences');
    }
}
