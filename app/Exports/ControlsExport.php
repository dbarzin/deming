<?php

namespace App\Exports;

use App\Models\Control;
use Illuminate\Database\Eloquent\Builder;
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
            trans('cruds.control.fields.input'),
            trans('cruds.control.fields.model'),
            trans('cruds.control.fields.indicator'),
            trans('cruds.control.fields.plan_date'),
            trans('cruds.control.fields.realisation_date'),
            trans('cruds.control.fields.observations'),
            trans('cruds.control.fields.score'),
            trans('cruds.control.fields.note'),
            trans('cruds.control.fields.owners'),
            trans('cruds.control.fields.status'),
            trans('cruds.control.fields.action_plan'),
        ];
    }

    public function styles(Worksheet $_sheet)
    {
        // return
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
            'F' => 50,  // Input
            'G' => 50,  // Modele
            'H' => 50,  // Indicateur
            'I' => 15,  // Plan date
            'J' => 15,  // Realisation date
            'K' => 50,  // Observation
            'L' => 15,  // Score
            'M' => 15,  // Note
            'N' => 50,  // Responsibles
            'O' => 15,  // Status
            'P' => 50,  // Plan d'action
        ];
    }

    public function map($control): array
    {
        return [
            [
                $control->measures()->implode('clause', ', '),
                $control->name,
                $control->scope,
                $control->objective,
                $control->attributes,
                $control->input,
                $control->model,
                $control->indicator,
                $control->plan_date,
                $control->realisation_date,
                $control->observations,
                $control->score,
                $control->note,
                implode(
                    ', ',
                    array_filter(
                        [
                            $control->users()->implode('name', ', '),
                            $control->groups()->implode('name', ', '),
                        ]
                    )
                ),
                $control->status,
                $control->action_plan,
            ],
        ];
    }

    public function query(): Builder
    {
        return Control::orderBy('realisation_date');
    }
}
