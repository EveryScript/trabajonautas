<x-app-layout>
    @if (auth()->user()->hasRole('ADMIN|USER'))
        @livewire('panel.dashboard-user')
    @else
        @livewire('panel.dashboard-client', ['user_id' => auth()->user()->id])
    @endif
</x-app-layout>
