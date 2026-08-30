<?php

namespace App\Exports;

use App\Models\Announcement;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Override;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnnouncesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function __construct(
        protected Carbon $from,
        protected Carbon $to
    ) {}

    #[Override]
    public function query()
    {
        return Announcement::query()
            ->with('announceFiles')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->orderBy('created_at', 'desc');
    }

    #[Override]
    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Descripción',
            'Ubicaciones',
            'Publicación',
            'Expiración',
            'Sueldo',
            'Premium',
            'Empresa',
            'Usuario',
            'Archivos adjuntos'
        ];
    }

    #[Override]
    public function map($announce): array
    {
        return [
            $announce->id,
            $announce->announce_title,
            $announce->description,
            $announce->locations->pluck('location_name')->implode(' | '),
            $announce->created_at,
            $announce->expiration_time,
            $announce->salary,
            $announce->pro ? 'SI' : 'NO',
            $announce->company->company_name,
            $announce->user->name,
            $announce->announceFiles->pluck('original_name')->implode(' | ')
        ];
    }

    #[Override]
    public function columnWidths(): array
    {
        return [
            'C' => 60
        ];
    }

    #[Override]
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
