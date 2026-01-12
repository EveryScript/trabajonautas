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
        $query = User::role(env('CLIENT_ROLE'))
            ->whereHas('account', function ($query) {
                $query->where(function ($q) {
                    $q->where('account_type_id', 1)
                        ->orWhere('verified_payment', true);
                });
            })
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
