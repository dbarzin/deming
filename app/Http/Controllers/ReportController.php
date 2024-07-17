<?php

namespace App\Http\Controllers;

use App\Models\Control;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Element\Chart;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

class ReportController extends Controller
{
    public function show(Request $request)
    {
        // get all frameworks
        $frameworks = DB::table('domains')
            ->select(DB::raw('distinct framework'))
            ->orderBy('framework')
            ->get();

        return view('reports')
            ->with('frameworks', $frameworks);
    }

    /**
     * Rapport de pilotage du SMSI
     *
     * @return \Illuminate\Http\Response
     */
    public function pilotage(Request $request)
    {
        $framework = $request->get('framework');

        // start date
        $start_date = $request->get('start_date');
        if ($start_date === null) {
            return back()
                ->withErrors(['pilotage' => 'no start date'])
                ->withInput();
        }

        $start_date = \Carbon\Carbon::createFromFormat('Y-m-d', $start_date);

        // end date
        $end_date = $request->get('end_date');
        if ($end_date === null) {
            return back()
                ->withErrors(['pilotage' => 'no end date'])
                ->withInput();
        }
        $end_date = \Carbon\Carbon::createFromFormat('Y-m-d', $end_date);

        // start_date > end_date
        if ($start_date->gt($end_date)) {
            return back()
                ->withErrors(['pilotage' => 'start date > end date'])
                ->withInput();
        }

        // today
        $today = \Carbon\Carbon::today();

        // end_date<=today
        if ($end_date->gt($today)) {
            return back()
                ->withErrors(['pilotage' => 'end date in the futur'])
                ->withInput();
        }

        // Get template file
        $template_filename = storage_path('app/models/pilotage_.docx');
        if (! file_exists($template_filename)) {
            $template_filename = storage_path('app/models/pilotage.docx');
        }

        // create templateProcessor
        $templateProcessor = new TemplateProcessor($template_filename);

        //-------------------------------------------------------------
        // make changes
        //-------------------------------------------------------------
        $templateProcessor->setValue('today', $today->format('d/m/Y'));
        $templateProcessor->setValue('start_date', $start_date->format('d/m/Y'));
        $templateProcessor->setValue('end_date', $end_date->format('d/m/Y'));

        $this->generateMadeControlTable($templateProcessor, $framework, $start_date, $end_date);
        $values = $this->generateControlTable($templateProcessor, $framework);
        $this->generateKPITable($templateProcessor, $framework, $values);
        $this->generateActionPlanTable($templateProcessor, $framework);

        //----------------------------------------------------------------
        // save a copy
        $filepath = storage_path('templates/pilotage-'. Carbon::today()->format('Y-m-d') .'.docx');
        $templateProcessor->saveAs($filepath);

        // return
        return response()->download($filepath);
    }

    /**
     * Générer le SOA
     *
     * @return \Illuminate\Http\Response
     */
    public function soa()
    {
        // Get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            //->whereNull('realisation_date')
            ->whereIn('status', [0,1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

        // Get all measures with scope
        $measures = DB::table('measures')
            ->select(
                [
                    'domains.title',
                    'measures.clause',
                    'measures.name',
                    'controls.scope',
                    'controls.plan_date',
                ]
            )
            ->leftjoin('domains', 'measures.domain_id', '=', 'domains.id')
            ->leftjoin('control_measure', 'control_measure.measure_id', '=', 'measures.id')
            ->leftjoin('controls', 'control_measure.control_id', '=', 'controls.id')
            ->whereIn('controls.status', [0,1])
            ->orderBy('domains.title')
            ->orderBy('measures.clause')
            ->get();

        // create XLSX
        $path = storage_path('app/soa-'. Carbon::today()->format('Ymd') .'.xlsx');

        $header = [
            trans('cruds.domain.title'),
            trans('cruds.measure.fields.clause'),
            trans('cruds.measure.fields.name'),
        ];
        foreach ($scopes as $scope) {
            array_push($header, $scope);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([$header], null, 'A1');

        // bold title
        $sheet->getStyle('1')->getFont()->setBold(true);

        // column size
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        for ($i = 0;$i < 20;$i++) {
            $sheet->getColumnDimension(chr(ord('D') + $i))->setAutoSize(true);
            $sheet->getStyle(chr(ord('D') + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle(chr(ord('D') + $i))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        }

        // loop on measures
        $cur_clause = null;
        $row = 1;
        foreach ($measures as $measure) {
            if ($cur_clause !== $measure->clause) {
                $cur_clause = $measure->clause;
                $row++;
                $sheet->setCellValue("A{$row}", $measure->title);
                $sheet->setCellValue("B{$row}", $measure->clause);
                $sheet->setCellValue("C{$row}", $measure->name);
            }
            // find row
            $key = array_search($measure->scope, $scopes);
            $col = chr(ord('D') + $key);
            $sheet->setCellValue("{$col}{$row}", $measure->plan_date);
        }

        // export to XLSX
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path);
    }

    /*
    * Generate Control Made table
    */
    private function generateMadeControlTable(
        TemplateProcessor $templateProcessor,
        string|null $framework,
        string $start_date,
        string $end_date
    ) {
        $controls = Control::where(
            [
                ['realisation_date','>=',$start_date],
                ['realisation_date','<',$end_date],
            ]
        );

        if ($framework !== null) {
            $controls = $controls
                ->join('control_measure', 'controls.id', '=', 'control_measure.control_id')
                ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
                ->join('domains', 'measures.domain_id', '=', 'domains.id')
                ->where('domains.framework', '=', $framework);
        }
        $controls = $controls
            ->where('status', 2)
            ->orderBy('realisation_date')
            ->get();

        //----------------------------------------------------------------
        // create table
        $table = new Table(['borderSize' => 3, 'borderColor' => 'black', 'width' => 9800, 'unit' => TblWidth::TWIP]);
        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText('#', ['bold' => true ], ['align' => 'center']);
        $table->addCell(12000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.control.fields.name'), ['bold' => true]);
        $table->addCell(3300, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.control.fields.realisation_date'), ['bold' => true], ['align' => 'center']);
        $table->addCell(3000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.control.fields.scope'), ['bold' => true], ['align' => 'center']);
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.control.fields.score'), ['bold' => true], ['align' => 'center']);

        foreach ($controls as $control) {
            $table->addRow();
            $table->addCell(2500)->addText($control->measures->implode('clause', ', '));
            $table->addCell(12500)->addText($control->name);
            $table->addCell(2800)->addText($control->realisation_date, null, ['align' => 'center']);
            $table->addCell(12500)->addText($control->scope);
            $table->addCell(2000)->addText(
                '⬤',
                ($control->score === 1 ? ['color' => '#FF0000'] :
                ($control->score === 2 ? ['color' => '#FF8000'] :
                ($control->score === 3 ? ['color' => '#00CC00'] : null))),
                ['align' => 'center']
            );
        }
        $templateProcessor->setComplexBlock('made_control_table', $table);
    }

    /*
    * Generate Control table
    */
    private function generateControlTable(
        TemplateProcessor $templateProcessor,
        string|null $framework
    ) {
        $values = [];

        // get domains
        $domains = DB::table('domains');
        if ($framework !== null) {
            $domains = $domains->where('framework', '=', $framework);
        }
        $domains = $domains->get();

        // get status report
        $controls = DB::table('controls as c1')
            ->select([
                'c1.id',
                'c1.score',
                'c1.realisation_date',
            ])
            ->leftJoin('controls as c2', 'c1.next_id', '=', 'c2.id')
            ->whereNull('c2.next_id')
            ->where('c1.status', 2);
        if ($framework !== null) {
            $controls = $controls
                ->join('control_measure', 'c1.id', '=', 'control_measure.control_id')
                ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
                ->join('domains', 'measures.domain_id', '=', 'domains.id')
                ->where('domains.framework', '=', $framework);
        }
        $controls = $controls->get();

        // Fetch measures for all controls in one query
        $controlMeasures = DB::table('control_measure')
            ->select([
                'control_id',
                'measure_id',
                'domain_id',
                'clause',
            ])
            ->join('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $controls->pluck('id'))
            ->orderBy('clause')
            ->get();

        // Group measures by control_id
        $measuresByControlId = $controlMeasures->groupBy('control_id');

        // map clauses
        foreach ($controls as $control) {
            $control->measures = $measuresByControlId->get($control->id, collect())->map(function ($controlMeasure) {
                return [
                    'id' => $controlMeasure->measure_id,
                    'domain_id' => $controlMeasure->domain_id,
                    'clause' => $controlMeasure->clause,
                ];
            });
        }

        $count_domains = count($domains);
        for ($j = 0; $j < $count_domains; $j++) {
            $values[0][$j] = 0;
            $values[1][$j] = 0;
            $values[2][$j] = 0;
        }

        $colors = [];
        foreach ($domains as $domain) {
            $colors[] = '00CC00';
        }
        foreach ($domains as $domain) {
            $colors[] = 'FF8000';
        }
        foreach ($domains as $domain) {
            $colors[] = 'FF0000';
        }

        $i = 0;
        foreach ($domains as $domain) {
            $domains[$i] = $domain->title;
            foreach ($controls as $control) {
                foreach ($control->measures as $measure) {
                    if ($measure['domain_id'] === $domain->id) {
                        $values[3 - $control->score][$i] += 1;
                    }
                }
            }
            $i++;
        }

        $chart = new Chart('stacked_column', $domains, $values[0]);
        $chart->addSeries($domains, $values[1]);
        $chart->addSeries($domains, $values[2]);

        $chart->getStyle()
            ->setWidth(Converter::inchToEmu(7))
            ->setHeight(Converter::inchToEmu(3))
            ->setShowGridX(false)
            ->setShowGridY(true)
            ->setShowAxisLabels(true)
            ->set3d(false)
            ->setShowLegend(false)
            ->setColors($colors)
            ->setDataLabelOptions(['showCatName' => false]);

        $templateProcessor->setChart('control_table', $chart);

        return $values;
    }

    /*
    * Genere KPI table
    */
    private function generateKPITable(TemplateProcessor $templateProcessor, $framework, $values)
    {
        // get domains
        $domains = DB::table('domains');
        if ($framework !== null) {
            $domains = $domains->where('framework', '=', $framework);
        }
        $domains = $domains->get();

        // create table
        $table = new Table(['borderSize' => 3, 'borderColor' => 'black', 'width' => 9800, 'unit' => TblWidth::TWIP]);
        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText('#', ['bold' => true], ['align' => 'center']);
        $table->addCell(12500, ['bgColor' => '#FFD5CA'])
            ->addText('Domaine', ['bold' => true]);
        $table->addCell(2500, ['bgColor' => '#FFD5CA'])
            ->addText('KPI', ['bold' => true], ['align' => 'center']);
        $table->addCell(1000, ['bgColor' => '#FFD5CA'])
            ->addText('0', ['bold' => true, 'color' => '#FF0000' ], ['align' => 'center']);
        $table->addCell(1000, ['bgColor' => '#FFD5CA'])
            ->addText('1', ['bold' => true, 'color' => '#FF8000'], ['align' => 'center']);
        $table->addCell(1000, ['bgColor' => '#FFD5CA'])
            ->addText('2', ['bold' => true, 'color' => '#00CC00'], ['align' => 'center']);

        $d = 0;
        foreach ($domains as $domain) {
            $table->addRow();
            $table->addCell(2000)->addText(
                $domain->title,
                null,
                ['spaceBefore' => 0,'spaceAfter' => 0,'align' => 'center']
            );
            $table->addCell(12500)->addText(
                $domain->description,
                null,
                ['spaceBefore' => 0,'spaceAfter' => 0]
            );

            // PKI
            $v = $values[0][$d] + $values[1][$d] + $values[2][$d];
            if ($v !== 0) {
                $v = intdiv($values[0][$d] * 100, $v);
            }

            $table->addCell(2500)
                ->addText(
                    $v .'%',
                    ($v >= 90 ? ['bold' => true, 'color' => '#00CC00'] :
                    ($v >= 80 ? ['bold' => true, 'color' => '#FF8000'] :
                    ['bold' => true,'color' => '#FF0000'])),
                    ['align' => 'center','spaceBefore' => 0,'spaceAfter' => 0]
                );
            // values
            $table->addCell(1000)
                ->addText(
                    $values[2][$d],
                    ['bold' => true, 'color' => '#FF0000'  ],
                    ['align' => 'center','spaceBefore' => 0,'spaceAfter' => 0 ]
                );
            $table->addCell(1000)
                ->addText(
                    $values[1][$d],
                    ['bold' => true, 'color' => '#FF8000'],
                    ['align' => 'center','spaceBefore' => 0,'spaceAfter' => 0 ]
                );
            $table->addCell(1000)
                ->addText(
                    $values[0][$d],
                    ['bold' => true, 'color' => '#00CC00'],
                    ['align' => 'center','spaceBefore' => 0,'spaceAfter' => 0 ]
                );

            // next
            $d++;
        }

        $templateProcessor->setComplexBlock('kpi_table', $table);
    }

    /*
    * Generate Action plan table
    */
    private function generateActionPlanTable(
        TemplateProcessor $templateProcessor,
        string|null $framework
    ) {
        $actions =
            DB::table('controls as c1')
                ->select([
                    'c1.id',
                    'c1.action_plan',
                    'c1.score',
                    'c1.name',
                    'c1.plan_date',
                    'c1.realisation_date',
                    'c2.id as next_id',
                    'c2.plan_date as next_date',
                    'c1.action_plan',
                ])
                ->leftjoin('controls as c2', 'c1.next_id', '=', 'c2.id')
                ->whereIn('c1.score', [1,2])
                ->whereIn('c2.status', [0,1])
                ->whereNull('c2.next_id');
        // filter on framework
        if ($framework !== null) {
            $ations = $actions
                ->join('control_measure', 'c1.id', '=', 'control_measure.control_id')
                ->join('measures', 'measures.id', '=', 'control_measure.measure_id')
                ->join('domains', 'domains.id', '=', 'measures.domain_id')
                ->where('domains.framework', '=', $framework);
        }
        // get it
        $actions = $actions->get();

        // Fetch measures for all controls in one query
        $controlMeasures = DB::table('control_measure')
            ->select([
                'control_id',
                'measure_id',
                'domain_id',
                'clause',
            ])
            ->join('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $actions->pluck('id'))
            ->orderBy('clause')
            ->get();

        // Group measures by control_id
        $measuresByControlId = $controlMeasures->groupBy('control_id');

        // map clauses
        foreach ($actions as $control) {
            $control->measures = $measuresByControlId->get($control->id, collect())->map(function ($controlMeasure) {
                return [
                    'id' => $controlMeasure->measure_id,
                    'domain_id' => $controlMeasure->domain_id,
                    'clause' => $controlMeasure->clause,
                ];
            });
        }

        $table = new Table(['borderSize' => 3, 'borderColor' => 'black', 'width' => 9800, 'unit' => TblWidth::TWIP]);

        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.report.action_plan.id'), ['bold' => true], ['align' => 'center']);
        $table->addCell(13000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.report.action_plan.title'), ['bold' => true]);
        $table->addCell(3000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.report.action_plan.next'), ['bold' => true]);

        // table content
        foreach ($actions as $action) {
            $table->addRow();
            $table->addCell(2000)->addText(
                $action->measures->implode('clause', ', '),
                null,
                ['align' => 'center']
            );
            $table->addCell(13000)->addText(
                $action->name,
                null,
                ['align' => 'left']
            );
            $table->addCell(3000)->addText(
                $action->next_date,
                null,
                ['align' => 'left']
            );

            $table->addRow();
            $section = $table->addCell(18000, ['gridSpan' => 3]);
            $textlines = explode("\n", $action->action_plan);
            foreach ($textlines as $textline) {
                $section->addText($textline);
            }
        }

        $templateProcessor->setComplexBlock('action_plans_table', $table);
    }
}
