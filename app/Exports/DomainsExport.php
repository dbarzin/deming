<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DomainsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            '#',
            'Description',
        ];
    }

    public function styles(Worksheet $_worksheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 50,
        ];
    }

    public function map($domain): array
    {
        return [
            [
                $domain->title, $domain->description,
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return DB::table('domains')->orderBy('id');
    }
}
