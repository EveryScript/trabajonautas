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
    public $startDate;
    #[Reactive]
    public $endDate;
    #[Reactive]
    public $labels;
    // Format propeties
    public $total;

    public function getChartData()
    {
        $query =  User::role(config('app.client_role'))
            ->whereHas('latestVerifiedSubscription')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])
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
