<?php

namespace App\Exports;

use App\Control;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ControlsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
 	public function headings(): array
    {
        return [
            'Clause',
            'Nom',
            'Objctif',
            'Attributs',
            'Modèle',
            'Indicateur',
            'Plan d\'action',
            'Responsable',
            'Périodicité'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true],
        			'alignment' => [
        				'wrapText' => true,
        				'vertical' => 'top'
        				],
        		]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, 	// Clause
            'B' => 30,	// Nom
            'C' => 50,  // Objectif
            'D' => 50,  // Attibuts
            'E' => 50,  // Modele
            'F' => 50,  // Indicateur
            'G' => 50,  // Plan d'action
            'H' => 20,  // Responsable
            'I' => 20    // Period
        ];
    }

    /**
    * @var Control $control
    */
    public function map($control): array
    {
        return [
            [
                $control->clause, 
                $control->name,
                $control->objective,
                $control->attributes,
                $control->model,
                $control->indicator,
                $control->action_plan,
                $control->owner,
                $control->periodicity
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        // return Domain::all();
        return DB::table('controls')->orderBy("clause");
    }

}
