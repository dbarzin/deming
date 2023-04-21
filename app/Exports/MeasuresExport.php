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
            trans('cruds.measure.fields.domain'),
            trans('cruds.measure.fields.clause'),
            trans('cruds.measure.fields.name'),
            trans('cruds.measure.fields.objective'),
            trans('cruds.measure.fields.attributes'),
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
            'A' => 10,  // Domain
            'B' => 10,  // Clause
            'C' => 30,  // Name
            'D' => 50,  // Objectif
            'E' => 50,  // Attibuts
            'F' => 50,  // Modele
            'G' => 50,  // Indicateur
            'H' => 50,  // Plan d'action
            'I' => 20,  // Responsable
            'J' => 20,  // Period
        ];
    }

    /**
     * @var Measure $measure
     */
    public function map($measure): array
    {
        return [
            [
                $measure->domain->title,
                $measure->clause,
                $measure->name,
                $measure->objective,
                $measure->attributes,
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
        return Measure::with('domain')->orderBy('clause');
    }
}
