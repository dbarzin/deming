<?php

namespace App\Exports;

use App\Measurement;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeasurementsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
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
            'Date',
            'Observation',
            'Score',
            'Note',
            'Plan d\'action'
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
            'G' => 15,  // Date
            'H' => 50,  // Observation
            'I' => 15,  // Score
            'J' => 15,  // Note
            'K' => 50,  // Plan d'action
        ];
    }

    /**
    * @var Measurement $measurement
    */
    public function map($measurement): array
    {
        return [
            [
                $measurement->clause, 
                $measurement->name,
                $measurement->objective,
                $measurement->attributes,
                $measurement->model,
                $measurement->indicator,
                $measurement->realisation_date,
                $measurement->observations,
                $measurement->score,
                $measurement->action_plan,
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        // return Domain::all();
        return DB::table('measurements')->orderBy("clause");
    }

}

