<?php

namespace App\Livewire\Charts;

use App\Models\GradeProfile;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsGrade extends Component
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

        $grade_profiles = GradeProfile::withCount(['users' => function ($query) {
            $query->role(config('app.client_role'))
                ->whereHas('account')
                ->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
        }])->get();

        foreach ($grade_profiles as $grade_profile) {
            array_push($labels, $grade_profile->profile_name);
            array_push($data, $grade_profile->users_count);
        }

        $this->total = array_sum($data);

        return ['labels' => $labels, 'data' => $data];
    }

    public function render()
    {
        return view('livewire.charts.clients-grade');
    }
}
