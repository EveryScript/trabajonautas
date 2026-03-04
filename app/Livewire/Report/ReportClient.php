<?php

namespace App\Livewire\Report;

use App\Exports\UsersExport;
use App\Models\Subscription;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ReportClient extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $start_date;

    #[Url(history: true)]
    public $end_date;

    public function mount()
    {
        $this->start_date = $this->start_date ?? now()->format('Y-m-d');
        $this->end_date = $this->end_date ?? now()->format('Y-m-d');
    }

    public function searchData($start, $end)
    {
        $this->start_date = $start;
        $this->end_date = $end;
    }

    #[Computed]
    public function subscriptions()
    {
        return Subscription::query()
            ->where('verified_payment', true)
            ->whereIn('account_type_id', [2, 3])
            ->when($this->start_date && $this->end_date, function ($query) {
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->start_date)->startOfDay(),
                    Carbon::parse($this->end_date)->endOfDay()
                ]);
            })
            ->with(['user' => fn($q) => $q->withTrashed(), 'type'])
            ->latest('updated_at')
            ->get();
    }

    #[Computed]
    public function totalPrice()
    {
        return Subscription::query()
            ->where('verified_payment', true)
            ->whereIn('account_type_id', [2, 3])
            ->when($this->start_date && $this->end_date, function ($query) {
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->start_date)->startOfDay(),
                    Carbon::parse($this->end_date)->endOfDay()
                ]);
            })
            ->sum('price');
    }

    public function exportData()
    {
        return Excel::download(new UsersExport($this->start_date, $this->end_date), 'Clientes-' . Carbon::now()->format('d-m-Y') . '.xlsx');
    }

    public function render()
    {
        return view('livewire.report.report-client');
    }
}
