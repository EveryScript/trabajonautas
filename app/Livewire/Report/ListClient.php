<?php

namespace App\Livewire\Report;

use App\Exports\UsersExport;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ListClient extends Component
{
    public $start_date, $end_date;

    public function mount()
    {
        $this->start_date = Carbon::now();
        $this->end_date = Carbon::now();
    }

    public function searchData($start, $end)
    {
        $this->start_date = $start;
        $this->end_date = $end;
    }

    public function exportData()
    {
        return Excel::download(new UsersExport($this->start_date, $this->end_date), 'Clientes-' . Carbon::now()->format('d-m-Y') . '.xlsx');
    }

    public function render()
    {
        $clients = User::whereHas('account', function ($query) {
            if ($this->start_date && $this->end_date)
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->start_date)->startOfDay(),
                    Carbon::parse($this->end_date)->endOfDay()
                ]);
            $query->whereIn('account_type_id', [2, 3]);
        })
            ->with('account.accountType')
            ->get();
        $sum_prices = $clients->pluck('account.accountType.price')->sum();
        return view('livewire.report.list-client', [
            'clients' => $clients,
            'sum_prices' => $sum_prices
        ]);
    }
}
