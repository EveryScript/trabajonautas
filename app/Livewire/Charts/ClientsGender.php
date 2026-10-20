<?php

namespace App\Livewire\Charts;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsGender extends Component
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
        $query =  User::role(config('app.client_role'))
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
            ->selectRaw('gender, count(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $query_data = collect(['M', 'F'])->mapWithKeys(function ($gender) use ($query) {
            return [$gender => $query->get($gender, 0)];
        })->values()->toArray();

        $this->total = $query->sum();

        return $query_data;
    }

    public function render()
    {
        return view('livewire.charts.clients-gender');
    }
}
