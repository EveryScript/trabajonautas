<?php

namespace App\Livewire\Report;

use App\Exports\UsersExport;
use App\Models\Subscription;
use App\Models\TbnSetting;
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
    public $qr_new_pro, $qr_new_promax;

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
        $fields = [
            'qr_new_pro'    => 'qr_pro',
            'qr_new_promax' => 'qr_promax',
        ];

        foreach ($fields as $property => $key) {
            if ($this->$property) {
                $this->processAndSaveImage($property, $key);
            }
        }
    }

    private function processAndSaveImage($property, $key)
    {
        $this->validate([$property => 'required|image|max:2048']);
        $path = $this->$property->store('img', 'public');
        TbnSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $path]
        );
        $this->reset($property);
        $this->dispatch('qr-image-saved');
    }

    public function render()
    {
        // QR data (settings)
        $qr_pro = TbnSetting::where('key', 'qr_pro')->first();
        $qr_promax = TbnSetting::where('key', 'qr_promax')->first();

        // Clients data
        $base_query = Subscription::query()
            ->where('verified_payment', true)
            ->whereIn('account_type_id', [2, 3])
            ->when($this->start_date && $this->end_date, function ($query) {
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->start_date)->startOfDay(),
                    Carbon::parse($this->end_date)->endOfDay()
                ]);
            })
            ->with(['user' => function ($query) {
                $query->withTrashed();
            }, 'type'])
            ->latest('updated_at');

        $subscriptions = $base_query->get();
        $total_price = $subscriptions->sum('price');

        return view('livewire.report.report-client', [
            'subscriptions' => $subscriptions,
            'total_price' => $total_price,
            'qr_pro' => $qr_pro,
            'qr_promax' => $qr_promax
        ]);
    }
}
