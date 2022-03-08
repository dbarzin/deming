<?php

namespace App\Exports;

use App\Measure;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeasuresExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'Clause',
            'Nom',
            'Objectif',
            'Attributs',
            'Modèle',
            'Indicateur',
            'Plan d\'action',
            'Responsables',
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
            'A' => 10,  // Clause
            'B' => 30,  // Nom
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
    * @var Measure $measure
    */
    public function map($measure): array
    {
        return [
            [
                $measure->clause, 
                $measure->name,
                $measure->objective,
                $measure->attributes,
                $measure->model,
                $measure->indicator,
                $measure->action_plan,
                $measure->owner,
                $measure->periodicity
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return DB::table('measures')->orderBy("clause");
    }

}


