<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Traits\AuthorizeClients;
use Livewire\Component;

class RecentAnnouncement extends Component
{
    use AuthorizeClients;

    public $announcements, $client_pro_verified;

    public function render()
    {
        $this->client_pro_verified = $this->isAuthClientProVerifiedAndCurrent();
        $this->announcements = Announcement::where('expiration_time', '>=', now())
            ->orderBy('updated_at', 'DESC')
            ->limit(6)->get();

        return <<<'HTML'
        <div class="grid grid-cols-1 gap-2 mb-12 md:grid-cols-2">
            @forelse ($announcements as $announce)
                <div wire:key='announce-{{ $announce->id }}'>
                    <x-card-announce :announce="$announce" :client="$client_pro_verified" />
                </div>
            @empty
                <div class="col-span-2 text-center">
                    <picture class="block mb-2">
                        <img src="{{ asset('storage/img/tbn-empty.webp') }}" alt="empty" class="max-w-[8rem] mx-auto mb-2">
                    </picture>
                    <p class="text-sm text-tbn-dark">Nuevas convocatorias en camino</p>
                </div>
            @endforelse
        </div>
        HTML;
    }
}
