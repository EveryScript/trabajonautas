<?php

namespace App\Livewire\Report;

use App\Exports\UsersExport;
use App\Models\TbnSetting;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ReportClient extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $start_date, $end_date;
    public $qr_new_image;

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

    public function saveQRImage()
    {
        $this->validate([
            'qr_new_image' => 'required|image|max:2048'
        ]);
        $image_path = $this->qr_new_image->store('img', 'public');
        $tbn_setting = TbnSetting::firstOrNew(['key' => 'qr_image']);
        $tbn_setting->value = $image_path;
        $tbn_setting->save();
        $this->reset('qr_new_image');
        $this->dispatch('qr-image-saved');
    }

    public function render()
    {
        // QR data (settings)
        $qr_image = TbnSetting::where('key', 'qr_image')->first();
        
        // Clients data
        $base_query = User::whereHas('account', function ($query) {
            if ($this->start_date && $this->end_date)
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->start_date)->startOfDay(),
                    Carbon::parse($this->end_date)->endOfDay()
                ]);
            $query->whereIn('account_type_id', [2, 3]);
            $query->where('verified_payment', true);
        })->with('account.accountType');
        // $clients = $base_query->simplePaginate(7);
        $clients = $base_query->get();
        $sum_prices = $base_query->get()->pluck('account.accountType.price')->sum();
        return view('livewire.report.report-client', [
            'clients' => $clients,
            'sum_prices' => $sum_prices,
            'qr_image' => $qr_image
        ]);
    }
}
