<?php

namespace App\Livewire\Charts;

use App\Models\Profesion;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsProfesion extends Component
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

        $profesions = Profesion::whereHas('users')->withCount(['users' => function ($query) {
            $query->role(config('app.client_role'))
                ->whereHas('account')
                ->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
        }])->get();

        foreach ($profesions as $profesion) {
            array_push($labels, $profesion->profesion_name);
            array_push($data, $profesion->users_count);
        }

        $this->total = array_sum($data);

        return ['labels' => $labels, 'data' => $data];
    }

    public function render()
    {
        return view('livewire.charts.clients-profesion');
    }
}
