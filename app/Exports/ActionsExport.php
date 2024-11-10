<?php

namespace App\Exports;

use App\Models\Action;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActionsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            trans('cruds.action.fields.reference'),
            trans('cruds.action.fields.type'),
            trans('cruds.action.fields.due_date'),
            trans('cruds.action.fields.scope'),
            trans('cruds.action.fields.clauses'),
            trans('cruds.action.fields.name'),
            trans('cruds.action.fields.cause'),
            trans('cruds.action.fields.owners'),
            trans('cruds.action.fields.remediation'),
            trans('cruds.action.fields.status'),
            trans('cruds.action.fields.close_date'),
            trans('cruds.action.fields.justification'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // fix unused
        $sheet;
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
            'A' => 10,  // Reference
            'B' => 10,  // Type
            'C' => 10,  // creation_date
            'D' => 20,  // scope
            'E' => 30,  // clause
            'F' => 50,  // name
            'G' => 50,  // Cause
            'H' => 20,  // Owners
            'I' => 50,  // remediation
            'J' => 10,  // status
            'K' => 10,  // close_date
            'L' => 50,  // justification
        ];
    }

    /**
     * @var action $action
     */
    public function map($action): array
    {
        return [
            [
            $action->reference,
            $action->type,
            $action->due_date,
            $action->scope,
            $action->measures()->implode('clause', ', '),
            $action->name,
            $action->cause,
            $action->owners()->implode('name', ', '),
            $action->remediation,
            $action->status,
            $action->close_date,
            $action->justification,
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return Action::orderBy('creation_date');
    }
}
