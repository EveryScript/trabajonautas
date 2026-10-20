<?php

namespace App\Livewire\Charts;

use App\Models\Profesion;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsProfesion extends Component
{
    // Parameters
    #[Reactive]
    public string $startDate;
    #[Reactive]
    public string $endDate;
    #[Reactive]
    public array $labels;
    #[Reactive]
    public string $filterClient;
    // Format propeties
    public int $total;

    public function getChartData()
    {
        $profesions = Profesion::whereHas('users')
            ->withCount(['users' => function ($query) {
                $query->role(config('app.client_role'))
                    ->whereHas('latestVerifiedSubscription')
                    ->when(
                        $this->filterClient,
                        fn($query, $client) =>
                        $query->whereHas('account', fn($q) => $q->where('account_type_id', $client))
                    )
                    ->whereBetween('created_at', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ]);
            }])
            ->orderByDesc('users_count')
            ->take(50) // Máximo que permitiremos manipular en el cliente
            ->get();

        // El total global permanece inalterado y exacto
        $this->total = User::role(config('app.client_role'))
            ->whereHas('latestVerifiedSubscription')
            ->when(
                $this->filterClient,
                fn($query, $client) =>
                $query->whereHas('account', fn($q) => $q->where('account_type_id', $client))
            )
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->whereNotNull('profesion_id')
            ->count();

        return [
            'labels' => $profesions->pluck('profesion_name'),
            'data' => $profesions->pluck('users_count'),
        ];
    }

    public function render()
    {
        return view('livewire.charts.clients-profesion');
    }
}
