<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use App\Services\Report\TemplateTableRenderer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpWord\Element\Chart;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function show(): View
    {
        // For administrators and users only
        abort_if(
            !Auth::user()->isAdmin() && !Auth::user()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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
     * @return  \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function pilotage(Request $request)
    {
        // For administrators and users only
        abort_if(
            !Auth::user()->isAdmin() && !Auth::user()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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
            $template_filename = storage_path('app/models/pilotage_' . Auth::user()->language . '.docx');
        }

        // create templateProcessor
        $templateProcessor = new TemplateProcessor($template_filename);

        //-------------------------------------------------------------
        // make changes
        //-------------------------------------------------------------
        $templateProcessor->setValue('today', $today->format('d/m/Y'));
        $templateProcessor->setValue('start_date', $start_date->format('d/m/Y'));
        $templateProcessor->setValue('end_date', $end_date->format('d/m/Y'));
        $templateProcessor->setValue('framework', $framework ?? '');

        $this->generateMadeControlTable($templateProcessor, $framework, $start_date, $end_date);
        $values = $this->generateControlTable($templateProcessor, $framework);
        $this->generateKPITable($templateProcessor, $framework, $values);
        $this->generateActionPlanTable($templateProcessor, $framework);

        // Tableaux personnalisés : balises ${table:<table>:<colonnes>|<options>}
        (new TemplateTableRenderer())->render($templateProcessor, [
            'start' => $start_date,
            'end' => $end_date,
            'framework' => $framework,
        ]);
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function soa(Request $request): BinaryFileResponse
    {
        // For administrators and users only
        abort_if(
            !Auth::user()->isAdmin() && !Auth::user()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );


        // Get all scopes
        $scopes = DB::table('measures')
            ->select('scope')
            ->whereIn('status', [0,1])
            ->distinct()
            ->orderBy('scope')
            ->pluck('scope')
            ->toArray();

        // Get all security measures (controls) with scope
        $controls = DB::table('controls')
            ->select(
                [
                    'domains.title',
                    'controls.clause',
                    'controls.name',
                    'measures.scope',
                    'measures.plan_date',
                ]
            )
            ->leftjoin('domains', 'controls.domain_id', '=', 'domains.id')
            ->leftjoin('control_measure', 'control_measure.control_id', '=', 'controls.id')
            ->leftjoin('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->whereIn('measures.status', [0,1])
            ->orderBy('domains.title')
            ->orderBy('controls.clause')
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

        // loop on controls
        $cur_clause = null;
        $row = 1;
        foreach ($controls as $control) {
            if ($cur_clause !== $control->clause) {
                $cur_clause = $control->clause;
                $row++;
                $sheet->setCellValue("A{$row}", $control->title);
                $sheet->setCellValue("B{$row}", $control->clause);
                $sheet->setCellValue("C{$row}", $control->name);
            }
            // find row
            $key = array_search($control->scope, $scopes);
            $col = chr(ord('D') + $key);
            $sheet->setCellValue("{$col}{$row}", $control->plan_date);
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
        $measures = Measure::where(
            [
                ['realisation_date','>=',$start_date],
                ['realisation_date','<',$end_date],
            ]
        );

        if ($framework !== null) {
            $measures = $measures
                ->join('control_measure', 'measures.id', '=', 'control_measure.measure_id')
                ->join('controls', 'control_measure.control_id', '=', 'controls.id')
                ->join('domains', 'controls.domain_id', '=', 'domains.id')
                ->where('domains.framework', '=', $framework);
        }
        $measures = $measures
            ->where('status', 2)
            ->orderBy('realisation_date')
            ->get();

        //----------------------------------------------------------------
        // create table
        $table = new Table(
            [
                'borderSize' => 3,
                'borderColor' => 'black',
                'width' => 9800,
                'unit' => TblWidth::TWIP,
                'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
            ]
        );
        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText('#', ['bold' => true ], ['align' => 'center']);
        $table->addCell(12000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.measure.fields.name'), ['bold' => true]);
        $table->addCell(3300, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.measure.fields.realisation_date'), ['bold' => true], ['align' => 'center']);
        $table->addCell(3000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.measure.fields.scope'), ['bold' => true], ['align' => 'center']);
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.measure.fields.score'), ['bold' => true], ['align' => 'center']);

        foreach ($measures as $measure) {
            $table->addRow();
            $table->addCell(2500)->addText($measure->controls()->get()->implode('clause', ', '));
            $table->addCell(12500)->addText($measure->name);
            $table->addCell(2800)->addText($measure->realisation_date, null, ['align' => 'center']);
            $table->addCell(12500)->addText($measure->scope);
            $table->addCell(2000)->addText(
                '⬤',
                ($measure->score === 1 ? ['color' => '#FF0000'] :
                ($measure->score === 2 ? ['color' => '#FF8000'] :
                ($measure->score === 3 ? ['color' => '#00CC00'] : null))),
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
        $domains = $domains->get()->toArray();

        // get status report (audit instances)
        $measures = DB::table('measures as c1')
            ->select([
                'c1.id',
                'c1.score',
                'c1.realisation_date',
            ])
            ->leftJoin('measures as c2', 'c1.next_id', '=', 'c2.id')
            ->whereNull('c2.next_id')
            ->where('c1.status', 2);
        if ($framework !== null) {
            $measures = $measures
                ->join('control_measure', 'c1.id', '=', 'control_measure.measure_id')
                ->join('controls', 'control_measure.control_id', '=', 'controls.id')
                ->join('domains', 'controls.domain_id', '=', 'domains.id')
                ->where('domains.framework', '=', $framework);
        }
        $measures = $measures->get();

        // Fetch security measures (controls) for all audit instances in one query
        $controlMeasures = DB::table('control_measure')
            ->select([
                'measure_id',
                'control_id',
                'domain_id',
                'clause',
            ])
            ->join('controls', 'controls.id', '=', 'control_id')
            ->whereIn('measure_id', $measures->pluck('id'))
            ->orderBy('clause')
            ->get();

        // Group by measure_id (audit instance id)
        $measuresByControlId = $controlMeasures->groupBy('measure_id');

        // map clauses
        foreach ($measures as $measure) {
            $measure->controls = $measuresByControlId->get($measure->id, collect())->map(function ($controlMeasure) {
                return [
                    'id' => $controlMeasure->control_id,
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
            foreach ($measures as $measure) {
                foreach ($measure->controls as $control) {
                    if ($control['domain_id'] === $domain->id) {
                        $values[3 - $measure->score][$i] += 1;
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
        $table = new Table(
            [
                'borderSize' => 3,
                'borderColor' => 'black',
                'width' => 9800,
                'unit' => TblWidth::TWIP,
                'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
            ]
        );
        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText('#', ['bold' => true], ['align' => 'center']);
        $table->addCell(12500, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.domain.title'), ['bold' => true]);
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
            DB::table('actions')
                ->select([
                    'actions.id',
                    'actions.reference',
                    'actions.type',
                    'actions.scope',
                    'actions.name',
                    'actions.cause',
                    'actions.remediation',
                    'actions.due_date',
                ])
                ->where('status', 0);
        // filter on framework
        if ($framework !== null) {
            $actions = $actions
                ->join('action_control', 'actions.id', '=', 'action_control.action_id')
                ->join('controls', 'controls.id', '=', 'action_control.control_id')
                ->join('domains', 'domains.id', '=', 'controls.domain_id')
                ->where('domains.framework', '=', $framework);
        }
        // get it
        $actions = $actions->get();

        /*
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
        */

        $table = new Table(
            [
                'borderSize' => 3,
                'borderColor' => 'black',
                'width' => 9800,
                'unit' => TblWidth::TWIP,
                'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
            ]
        );

        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.report.action_plan.id'), ['bold' => true], ['align' => 'center']);
        $table->addCell(13000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.report.action_plan.title'), ['bold' => true]);
        $table->addCell(3000, ['bgColor' => '#FFD5CA'])
            ->addText(trans('cruds.action.fields.due_date'), ['bold' => true]);

        // table content
        foreach ($actions as $action) {
            $table->addRow();
            $table->addCell(2000)->addText(
                $action->reference,
                null,
                ['align' => 'center']
            );
            $table->addCell(13000)->addText(
                $action->name,
                ['bold' => true],
                ['align' => 'left']
            );
            $table->addCell(3000)->addText(
                $action->due_date,
                null,
                ['align' => 'left']
            );

            $table->addRow();
            $section = $table->addCell(18000, ['gridSpan' => 3]);
            $textlines = explode("\n", $action->cause);
            foreach ($textlines as $textline) {
                $section->addText($textline);
            }

            $table->addRow();
            $section = $table->addCell(18000, ['gridSpan' => 3]);
            $textlines = explode("\n", $action->remediation);
            foreach ($textlines as $textline) {
                $section->addText($textline);
            }
        }

        $templateProcessor->setComplexBlock('action_plans_table', $table);
    }
}
