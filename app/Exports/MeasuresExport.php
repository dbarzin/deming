<?php

namespace App\Exports;

use App\Measure;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeasuresExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            trans('cruds.measure.fields.clause'),
            trans('cruds.measure.fields.name'),
            trans('cruds.measure.fields.objective'),
            trans('cruds.measure.fields.input'),
            trans('cruds.measure.fields.model'),
            trans('cruds.measure.fields.indicator'),
            trans('cruds.measure.fields.action_plan'),
            trans('cruds.measure.fields.owner'),
            trans('cruds.measure.fields.periodicity'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'top',
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Clause
            'B' => 30,  // Nom
            'C' => 50,  // Objectif
            'D' => 50,  // Attibuts
            'E' => 50,  // Modele
            'F' => 50,  // Indicateur
            'G' => 50,  // Plan d'action
            'H' => 20,  // Responsable
            'I' => 20,    // Period
        ];
    }

    /**
     * @var Measure $measure
     */
    public function map($measure): array
    {
        return [
            [
                $measure->clause,
                $measure->name,
                $measure->objective,
                $measure->input,
                $measure->model,
                $measure->indicator,
                $measure->action_plan,
                $measure->owner,
                $measure->periodicity,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return DB::table('measures')->orderBy('clause');
    }
}
