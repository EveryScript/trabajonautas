@if (auth()->user()->hasRole('ADMIN|USER'))
    <x-app-layout>
        @livewire('panel.dashboard-user')
    </x-app-layout>
@else
    @if (auth()->user()->register_completed)
        <x-app-layout>
            @livewire('panel.dashboard-client', ['user_id' => auth()->user()->id])
        </x-app-layout>
    @else
        <x-guest-layout>
            @livewire('panel.first-steps', ['user_id' => auth()->user()->id])
        </x-guest-layout>
    @endif
@endif
