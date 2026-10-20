<?php

namespace App\Livewire\Charts;

use App\Models\Account;
use App\Models\AccountType;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClientsAccount extends Component
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
        $data = [];

        $account_types = AccountType::withCount([
            'users' => function ($query) {
                $query->role(config('app.client_role'))->where('actived', true)
                    ->whereHas('latestVerifiedSubscription')
                    ->when(
                        $this->filterClient,
                        fn($query, $client) =>
                        $query->whereHas('account', fn($q) => $q->where('account_type_id', $client))
                    )
                    ->whereBetween('users.created_at', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ]);
            }
        ])->get();

        foreach ($account_types as $account_type)
            array_push($data, $account_type->users_count);

        $this->total = array_sum($data);

        return $data;
    }

    public function render()
    {
        return view('livewire.charts.clients-account');
    }
}
