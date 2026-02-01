<?php

namespace App\Exports;

use App\Models\Subscription;
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
        return Subscription::query()
            ->where('verified_payment', true)
            ->whereIn('account_type_id', [2, 3])

            // 2. Filtro de fechas (aplicado directamente a la suscripción)
            ->when($this->start_date && $this->end_date, function ($query) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->start_date)->startOfDay(),
                    Carbon::parse($this->end_date)->endOfDay()
                ]);
            })

            // 3. Eager Loading para que el Excel sea veloz
            ->with(['user', 'verifiedBy', 'type'])
            ->get()
            ->map(fn($s) => [
                'id_user' => $s->user_id,
                'name' => $s->user->name ?? 'N/A',
                'type' => $s->type->name ?? 'N/A',
                'verified_by' => $s->verifiedBy->name ?? 'Sistema',
                'verified_date' => $s->created_at->format('d-m-Y H:i'),
                'phone' => $s->user->phone ?? '',
                'price' => $s->price,
            ]);
    }

    public function headings(): array
    {
        return ['ID cliente', 'Nombre', 'Tipo de cuenta', 'Verificado por', 'Fecha de verificación', 'Celular', 'Ganancia (Bs.)'];
    }
}
