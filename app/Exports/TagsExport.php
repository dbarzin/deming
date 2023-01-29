<?php

namespace App\Exports;

use App\Tag;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TagsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Name',
            'Values',
        ];
    }

    public function styles(Worksheet $sheet)
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

    /**
     * @var Tag $tag
     */
    public function map($tag): array
    {
        return [
            [
                $tag->name, $tag->values,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return DB::table('tags')->orderBy('name');
    }
}
