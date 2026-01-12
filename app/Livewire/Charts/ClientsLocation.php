<?php

namespace App\Livewire\Charts;

use App\Models\Location;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsLocation extends Component
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
        $labels = [];
        $data = [];

        $locations = Location::whereHas('users')->withCount(['users' => function ($query) {
            $query->role(env('CLIENT_ROLE'))
                ->whereHas('account', function ($q) {
                    $q->where(function ($q) {
                        $q->where('account_type_id', 1)
                            ->orWhere('verified_payment', true);
                    });
                })->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
        }])->get();

        foreach ($locations as $location) {
            array_push($labels, $location->location_name);
            array_push($data, $location->users_count);
        }

        $this->total = array_sum($data);

        return ['labels' => $labels, 'data' => $data];
    }

    public function render()
    {
        return view('livewire.charts.clients-location');
    }
}
