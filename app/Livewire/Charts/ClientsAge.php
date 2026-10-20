<?php

namespace App\Livewire\Charts;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsAge extends Component
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
        $query = User::role(config('app.client_role'))
            ->whereHas('latestVerifiedSubscription')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])
            ->when(
                $this->filterClient,
                fn($query, $client) =>
                $query->whereHas('account', fn($q) => $q->where('account_type_id', $client))
            )
            ->selectRaw('age, count(*) as total')
            ->groupBy('age')
            ->pluck('total', 'age');

        $query_data = collect([1, 2, 3])->mapWithKeys(function ($age) use ($query) {
            return [$age => $query->get($age, 0)];
        })->values()->toArray();

        $this->total = $query->sum();

        return $query_data;
    }

    public function render()
    {
        return view('livewire.charts.clients-age');
    }
}
