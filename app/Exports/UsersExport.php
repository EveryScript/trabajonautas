<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $start_date;
    protected $end_date;

    public function __construct($start, $end)
    {
        $this->start_date = $start;
        $this->end_date = $end;
    }

    public function collection()
    {
        return User::whereHas('account', function ($query) {
            if ($this->start_date && $this->end_date)
                $query->whereBetween('updated_at', [
                    Carbon::parse($this->start_date)->startOfDay(),
                    Carbon::parse($this->end_date)->endOfDay()
                ]);
            $query->whereIn('account_type_id', [2, 3]);
        })
            ->with('account.accountType')
            ->get()
            ->map(fn($client) => [
                'id' => $client->id,
                'name' => $client->name,
                'account_type_name' => $client->account->accountType->name,
                'phone' => $client->phone,
                'updated_at' => $client->account->updated_at,
                'price' => $client->account->accountType->price
            ]);
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Tipo de cuenta', 'Celular', 'Fecha de verificación', 'Ganancia (Bs.)'];
    }
}
