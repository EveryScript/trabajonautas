<?php

namespace App\Livewire\Panel;

use Carbon\Carbon;
use Livewire\Component;

class DashboardUser extends Component
{
    public $start_date, $end_date, $labels;

    public function mount()
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
        $this->setLabels();
    }

    public function setRangeDate($start, $end)
    {

        $this->start_date = $start;
        $this->end_date = $end;
        $this->setLabels();
        $this->dispatch('refresh-charts');
    }

    public function setLabels()
    {
        $from = Carbon::parse($this->start_date)->format('d/m/Y');
        $to = Carbon::parse($this->end_date)->format('d/m/Y');
        $this->labels = [
            'format_date' => 'Desde el ' . $from . ' hasta el ' . $to,
            'color' => '#ff420a'
        ];
    }

    public function render()
    {
        return view('livewire.panel.dashboard-user');
    }
}
