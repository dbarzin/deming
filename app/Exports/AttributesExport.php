<?php

namespace App\Exports;

use App\Attribute;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttributesExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            trans('cruds.attribute.fields.name'),
            trans('cruds.attribute.fields.values'),
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
     * @var Attribute $attribute
     */
    public function map($attribute): array
    {
        return [
            [
                $attribute->name, $attribute->values,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return DB::table('attributes')->orderBy('name');
    }
}
