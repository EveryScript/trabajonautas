<?php

namespace App\Livewire\Admin\Bot;

use App\Models\BotSource;
use Livewire\Component;

class BotSources extends Component
{
    public function render()
    {
        return view('livewire.admin.bot.bot-sources', [
            'sources' => BotSource::withCount(['companies' => fn($query) => $query->where('active', true)])
                ->where('active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
