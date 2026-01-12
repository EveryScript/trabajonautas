<?php

namespace App\Livewire\Charts;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsUnregistered extends Component
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
        $without_account_clients = User::role(env('CLIENT_ROLE'))
            ->whereDoesntHave('account')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])->count();

        $inactive_clients = User::role(env('CLIENT_ROLE'))
            ->where('actived', false)
            ->whereHas('account')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])->count();

        $unverified_clients = User::role(env('CLIENT_ROLE'))
            ->where('actived', true)
            ->whereHas('account', function ($query) {
                $query->where(function ($q) {
                    $q->where('account_type_id', '!=', 1)->where('verified_payment', false);
                });
            })
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])->count();

        return [
            'Sin verificación' => $unverified_clients,
            'Deshabilitados' => $inactive_clients,
            'Sin cuenta' => $without_account_clients
        ];
    }

    public function render()
    {
        return view('livewire.charts.clients-unregistered');
    }
}
