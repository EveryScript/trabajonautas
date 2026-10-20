<?php

namespace App\Livewire\Panel;

use Carbon\Carbon;
use Livewire\Component;

class DashboardUser extends Component
{
    public string $start_date;
    public string $end_date;
    public string $filter_client = '';
    public array $labels;

    public function mount()
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
        $this->setLabels();
    }

    public function setChartParameters(string $start, string $end, string $filter_client)
    {
        $this->start_date = $start;
        $this->end_date = $end;
        $this->filter_client = $filter_client;
        $this->setLabels();
        $this->dispatch('refresh-charts');
    }

    public function setLabels()
    {
        $from = Carbon::parse($this->start_date)->format('d/m/Y');
        $to = Carbon::parse($this->end_date)->format('d/m/Y');
        $client_type = match ($this->filter_client) {
            '1' => 'Clientes FREE',
            '2' => 'Clientes PRO',
            '3' => 'Clientes PRO-MAX',
            default => '',
        };
        $this->labels = [
            'format_date' => 'Desde el ' . $from . ' hasta el ' . $to,
            'color' => '#ff420a',
            'client_type' => $client_type
        ];
    }

    public function render()
    {
        return view('livewire.panel.dashboard-user');
    }
}
