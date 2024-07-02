<?php

namespace App\Exports;

use App\Models\Measure;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeasuresExport extends StringValueBinder implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            trans('cruds.domain.fields.framework'),
            trans('cruds.domain.title'),
            trans('cruds.domain.title') . ' - ' . trans('cruds.domain.fields.description'),
            trans('cruds.measure.fields.clause'),
            trans('cruds.measure.fields.name'),
            trans('cruds.measure.fields.objective'),
            trans('cruds.measure.fields.attributes'),
            trans('cruds.measure.fields.input'),
            trans('cruds.measure.fields.model'),
            trans('cruds.measure.fields.indicator'),
            trans('cruds.measure.fields.action_plan'),
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

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Framework
            'B' => 10,  // Domain name
            'C' => 30,  // Domain description
            'D' => 10,  // Clause
            'E' => 30,  // Name
            'F' => 50,  // Objectif
            'G' => 50,  // Attibuts
            'H' => 50,  // Input
            'I' => 50,  // Modele
            'J' => 50,  // Indicateur
            'K' => 50,  // Plan d'action
        ];
    }

    /**
     * @var Measure $measure
     */
    public function map($measure): array
    {
        return [
            [
                $measure->domain->framework,
                $measure->domain->title,
                $measure->domain->description,
                $measure->clause,
                $measure->name,
                $measure->objective,
                $measure->attributes,
                $measure->input,
                $measure->model,
                $measure->indicator,
                $measure->action_plan,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return Measure::with('domain')
            ->orderBy('clause');
    }
}
