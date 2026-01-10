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
                <a href="{{ $announce->pro && !$client_pro_verified ? route('purchase-cards') : route('result', ['id' => $announce->id]) }}"
                        wire:navigate wire:key='announce-{{ $announce->id }}'>
                    <x-card-announce
                        logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                        title="{{ $announce->announce_title }}" created_at="{{ $announce->created_at }}" pro="{{ $announce->pro }}">
                        @if ($announce->company)
                            <x-slot name="company">{{ $announce->company->company_name }}</x-slot>
                        @endif
                        <x-slot name="locations">
                            {{ $announce->locations[0]->location_name }}
                            @if ($announce->locations->count() > 1)
                                <span class="text-xs text-gray-400">
                                    ({{ $announce->locations->count() - 1 }} más)</span>
                            @endif
                        </x-slot>
                    </x-card-announce>
                </a>
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
