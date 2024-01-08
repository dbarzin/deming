<?php

namespace App\Exports;

use App\Models\Control;
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
            trans('cruds.control.fields.scope'),
            trans('cruds.control.fields.objective'),
            trans('cruds.control.fields.attributes'),
            trans('cruds.control.fields.model'),
            trans('cruds.control.fields.indicator'),
            trans('cruds.control.fields.realisation_date'),
            trans('cruds.control.fields.observations'),
            trans('cruds.control.fields.score'),
            trans('cruds.control.fields.note'),
            trans('cruds.control.fields.owners'),
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
            'C' => 20,  // Scope
            'D' => 50,  // Objectif
            'E' => 50,  // Attibuts
            'F' => 50,  // Modele
            'G' => 50,  // Indicateur
            'H' => 15,  // Date
            'I' => 50,  // Observation
            'J' => 15,  // Score
            'K' => 15,  // Note
            'L' => 50,  // Responsibles
            'M' => 50,  // Plan d'action
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
                $control->scope,
                $control->objective,
                $control->input,
                $control->model,
                $control->indicator,
                $control->realisation_date,
                $control->observations,
                $control->score,
                $control->note,
                $control->owners()->implode('name', ', '),
                $control->action_plan,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return Control::orderBy('clause');
    }
}
