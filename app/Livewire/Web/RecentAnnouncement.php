<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\User;
use Livewire\Component;

class RecentAnnouncement extends Component
{
    public $client, $pro_verified = false;
    public $announcements;

    public function mount()
    {
        if (auth()->check()) {
            $this->client = User::with('account.accountType')->find(auth()->user()->id);
            $this->pro_verified = auth()->user()->roles->pluck('name')->first() === env('CLIENT_ROLE')
                ? $this->client->account->verified_payment : true;
        }
        $this->announcements = Announcement::where('expiration_time', '>=', now())
            ->orderBy('updated_at', 'DESC')->limit(6)->get();
    }

    public function render()
    {
        return <<<'HTML'
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-12">
            @forelse ($announcements as $announce)
            <a href="{{ $announce->pro && (!$client || !$pro_verified) ? route('purchase-cards') : route('result', ['id' => $announce->id]) }}"
                    wire:navigate wire:key='announce-{{ $announce->id }}'>
                <x-card-announce logo_url="{{ $announce->company ? $announce->company->company_image : '' }}"
                    pro="{{ $announce->pro }}">
                    <x-slot name="area">
                        {{ $announce->area ? $announce->area->area_name : '' }}
                        @foreach ($announce->profesions as $profesion)
                            {{ '| ' . $profesion->profesion_name }}
                        @endforeach
                    </x-slot>
                    <x-slot name="title">{{ $announce->announce_title }}</x-slot>
                    <x-slot name="company">{{ $announce->company ? $announce->company->company_name : '' }}</x-slot>
                    <x-slot name="locations">
                        {{ $announce->locations[0]->location_name }}
                        @if ($announce->locations->count() > 1)
                            <i class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                        @endif
                    </x-slot>
                    @if($announce->expiration_time < Carbon\Carbon::now())
                        <x-slot name="expiration">
                            Expiró {{ (new Carbon\Carbon($announce->expiration_time))->diffForHumans() }}
                        </x-slot>
                    @endif
                </x-card-announce>
            </a>
            @empty
                <span class="text-tbn-dark text-sm text-center">No hay elementos para mostrar</span>
            @endforelse
        </div>
        HTML;
    }
}
