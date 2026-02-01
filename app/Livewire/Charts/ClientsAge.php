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
    public $startDate;
    #[Reactive]
    public $endDate;
    #[Reactive]
    public $labels;
    // Format propeties
    public $total;

    public function getChartData()
    {
        $query = User::role(config('app.client_role'))
            ->whereHas('account')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])
            ->selectRaw('age, count(*) as total')
            ->groupBy('age')
            ->pluck('total');
        $this->total = $query->sum();

        return $query;
    }

    public function render()
    {
        return view('livewire.charts.clients-age');
    }
}
