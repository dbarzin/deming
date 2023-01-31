<?php

namespace App\Exports;

use App\Control;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ControlsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            trans('cruds.control.fields.clause'),
            trans('cruds.control.fields.name'),
            trans('cruds.control.fields.objective'),
            trans('cruds.control.fields.attributes'),
            trans('cruds.control.fields.model'),
            trans('cruds.control.fields.indicator'),
            trans('cruds.control.fields.realisation_date'),
            trans('cruds.control.fields.observations'),
            trans('cruds.control.fields.score'),
            trans('cruds.control.fields.note'),
            trans('cruds.control.fields.action_plan'),
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
            'G' => 15,  // Date
            'H' => 50,  // Observation
            'I' => 15,  // Score
            'J' => 15,  // Note
            'K' => 50,  // Plan d'action
        ];
    }

    /**
     * @var control $control
     */
    public function map($control): array
    {
        return [
            [
                $control->clause,
                $control->name,
                $control->objective,
                $control->input,
                $control->model,
                $control->indicator,
                $control->realisation_date,
                $control->observations,
                $control->score,
                $control->action_plan,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return DB::table('controls')->orderBy('clause');
    }
}
