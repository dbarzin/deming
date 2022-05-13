<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;

use App\Exports\ControlsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Domain;
use App\Measure;
use App\Control;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Chart;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\PhpWord;

class ReportController extends Controller
{

    /**
     * Rapport de pilotage du SMSI
     *
     * @return \Illuminate\Http\Response
     */
    public function pilotage(Request $request)
    {

        // start date
        $start_date = $request->get("start_date");
        if ($start_date==null) {
            return back()
                ->withErrors(['pilotage' => 'pas de date de début'])
                ->withInput();
        }            
        
        $start_date=\Carbon\Carbon::createFromFormat('Y-m-d', $start_date);

        // end date
        $end_date = $request->get("end_date");
        if ($end_date==null) {
            return back()
                ->withErrors(['pilotage' => 'pas de date de fin'])
                ->withInput();
        }
        $end_date=\Carbon\Carbon::createFromFormat('Y-m-d', $end_date);

        // start_date > end_date
        if($start_date->gt($end_date)){
            return back()
                ->withErrors(['pilotage' => 'date début > date fin'])
                ->withInput();
        }

        // today
        $today=\Carbon\Carbon::today();

        // end_date<=today
        if($end_date->gt($today)){
            return back()
                ->withErrors(['pilotage' => 'date de fin dans le futur'])
                ->withInput();
        }

        // Get template file
        $template_filename = storage_path('app/models/pilotage_.docx');
        if (!file_exists($template_filename))
            $template_filename = storage_path('app/models/pilotage.docx');

        // create templateProcessor
        $templateProcessor = new TemplateProcessor($template_filename);

        //-------------------------------------------------------------
        // make changes 
        //-------------------------------------------------------------
        $templateProcessor->setValue('today', $today->format('d/m/Y'));
        $templateProcessor->setValue('start_date', $start_date->format('d/m/Y'));
        $templateProcessor->setValue('end_date', $end_date->format('d/m/Y'));

        // addText('', $fontStyle);

        //----------------------------------------------------------------        
        $controls =  Control::where(
            [
                    ["realisation_date",">=",$start_date],            
                    ["realisation_date","<",$end_date],
            ]
        )
            ->orderBy("realisation_Date")->get();
        /*
        $values = [];
        foreach($controls as $control) {

            $values[] =  [
                'ctrl_id' => $control->clause,
                'ctrl_date' => $control->realisation_date, 
                'ctrl_name' => $control->name,
                'ctrl_score' => '<w:highlight w:val="red">'.'⬤'.$control->score.'</w:highlight>',
            ];
        }

        $templateProcessor->cloneRowAndSetValues('ctrl_id', $values);
        */
        //----------------------------------------------------------------

        // $myParagraphStyle = array('align'=>'left', 'spaceBefore'=>50, 'spaceafter' => 50);        
        // $myFontStyle = array('name' => 'Arial', 'size' => 10, 'bold' => true, 'color' => '#FF0000');

        // create table
        $table =new Table(array('borderSize' => 3, 'borderColor' => 'black', 'width' => 9800 , 'unit' => TblWidth::TWIP));
        // create header
        $table->addRow();
        $table->addCell(2500, ['bgColor'=>'#FFD5CA'])
            ->addText('#', ['bold' => true ], ['align'=>'center']);
        $table->addCell(12500, ['bgColor'=>'#FFD5CA'])
            ->addText('Nom', ['bold' => true]);
        $table->addCell(2800, ['bgColor'=>'#FFD5CA'])
            ->addText('Date', ['bold' => true], ['align'=>'center']);
        $table->addCell(2000, ['bgColor'=>'#FFD5CA'])
            ->addText('Score', ['bold' => true], ['align'=>'center']);

        foreach($controls as $control) {
            $table->addRow();
            $table->addCell(2500)->addText($control->clause);
            $table->addCell(12500)->addText($control->name);
            $table->addCell(2800)->addText($control->realisation_date, null, ['align'=>'center']);
            $table->addCell(2000)->addText(
                '⬤',
                ($control->score==1 ? ['color'=>'#FF0000'] : 
                ($control->score==2 ? ['color'=>'#FF8000'] :
                ($control->score==3 ? ['color'=>'#00CC00'] : null))),
                ['align'=>'center']
            );
        }
        $templateProcessor->setComplexBlock('made_control_table', $table);

        // ---------------------------------------------------------------        
        // https://github.com/PHPOffice/PHPWord/pull/1864        

        // $domains = [];
        $values = [];

        // get all domains
        $domains = DB::table('domains')->get();

        // get status report
        $controls=DB::select(
            DB::raw("
            SELECT 
            c1.measure_id, 
            c1.domain_id,
            c1.score,
            c1.realisation_date
            FROM
                controls c1 left join controls c2 on c1.next_id=c2.id
            WHERE
                c2.realisation_date is null and c1.next_id is not null
            group by c1.measure_id order by c1.clause;"
            )
        );

        for($j=0;$j<count($domains);$j++) {
            $values[0][$j]=0;
            $values[1][$j]=0;
            $values[2][$j]=0;
        }

        //
        $colors = [];
        foreach($domains as $domain) { 
            $colors[] = '00CC00';
        }
        foreach($domains as $domain) { 
            $colors[] = 'FF8000';
        }
        foreach($domains as $domain) { 
            $colors[] = 'FF0000';
        }

        $i=0;
        foreach($domains as $domain) {
            $domains[$i]=$domain->title;            
            foreach($controls as $control) {
                if ($control->domain_id==$domain->id) {
                    $values[3-$control->score][$i]=$values[3-$control->score][$i]+1;
                }
            }
            $i++;
        }
        
        $chart = new Chart("stacked_column", $domains, $values[0]);
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
            // ->setValueLabelPosition("none")
            ->setColors($colors)
            ->setDataLabelOptions(['showCatName'=>false,]);

        $templateProcessor->setChart('control_table', $chart);            
        
        //----------------------------------------------------------------
        // kpi_table

        // get all domains
        $domains = DB::table('domains')->get();

        // create table
        $table =new Table(array('borderSize' => 3, 'borderColor' => 'black', 'width' => 9800 , 'unit' => TblWidth::TWIP));
        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor'=>'#FFD5CA'])
            ->addText('#', ['bold' => true, ], ['align'=>'center']);
        $table->addCell(12500, ['bgColor'=>'#FFD5CA'])
            ->addText('Domaine', ['bold' => true]);
        $table->addCell(2500, ['bgColor'=>'#FFD5CA'])
            ->addText('KPI', ['bold' => true], ['align'=>'center']);
        $table->addCell(1000, ['bgColor'=>'#FFD5CA'])
            ->addText('0', ['bold' => true, 'color' => '#FF0000' ], ['align'=>'center']);
        $table->addCell(1000, ['bgColor'=>'#FFD5CA'])
            ->addText('1', ['bold' => true, 'color' => '#FF8000'], ['align'=>'center']);
        $table->addCell(1000, ['bgColor'=>'#FFD5CA'])
            ->addText('2', ['bold' => true, 'color' => '#00CC00'], ['align'=>'center']);


        $d=0;
        foreach($domains as $domain) {
            $table->addRow();
            $table->addCell(2000)->addText(
                $domain->title, null,
                ['spaceBefore'=>0,'spaceAfter'=>0,'align'=>'center']
            );
            $table->addCell(12500)->addText(
                $domain->description, null,
                ['spaceBefore'=>0,'spaceAfter'=>0]
            );

            // PKI
            $v=$values[0][$d]+$values[1][$d]+$values[2][$d];
            if ($v!=0)
                $v=intdiv($values[0][$d]*100, $v);

            $table->addCell(2500)
                ->addText(
                    $v .'%',
                    ($v>=90 ? ['bold' => true, 'color' => '#00CC00'] :
                    ($v>=80 ? ['bold' => true, 'color' => '#FF8000'] :
                    ['bold' => true,'color' => '#FF0000'])),
                    ['align'=>'center','spaceBefore'=>0,'spaceAfter'=>0]
                );
            // values
            $table->addCell(1000)
                ->addText(
                    $values[2][$d],
                    ['bold' => true, 'color' => '#FF0000'  ], 
                    ['align'=>'center','spaceBefore'=>0,'spaceAfter'=>0 ]
                );
            $table->addCell(1000)
                ->addText(
                    $values[1][$d],
                    ['bold' => true, 'color' => '#FF8000'],
                    ['align'=>'center','spaceBefore'=>0,'spaceAfter'=>0 ]
                );
            $table->addCell(1000)
                ->addText(
                    $values[0][$d],
                    ['bold' => true, 'color' => '#00CC00'],
                    ['align'=>'center','spaceBefore'=>0,'spaceAfter'=>0 ]
                );

            // next
            $d++;
        }

        $templateProcessor->setComplexBlock('kpi_table', $table);

        //----------------------------------------------------------------
        // Action plans
        $actions=
            DB::select("
                select
                    c1.measure_id,
                    c1.id,
                    c1.clause,
                    c1.action_plan,
                    c1.score,
                    c1.name,
                    c1.plan_date,
                    c1.realisation_date,
                    c2.id as next_id,
                    c2.plan_date as next_date,
                    c1.action_plan 
                from
                    controls c1 left join controls c2 on c1.next_id=c2.id
                where
                    (c1.score=1 or c1.score=2) and c2.next_id is null
                order by measure_id;");

        $table =new Table(array('borderSize' => 3, 'borderColor' => 'black', 'width' => 9800 , 'unit' => TblWidth::TWIP));

        // create header
        $table->addRow();
        $table->addCell(2000, ['bgColor'=>'#FFD5CA'])
            ->addText('#', ['bold' => true, ], ['align'=>'center']);
        $table->addCell(13000, ['bgColor'=>'#FFD5CA'])
            ->addText('Titre', ['bold' => true]);
        $table->addCell(3000, ['bgColor'=>'#FFD5CA'])
            ->addText('Next', ['bold' => true]);

        // table content
        foreach($actions as $action) {
            $table->addRow();
            $table->addCell(2000)->addText(
                $action->clause, null,
                ['align'=>'center']
            );
            $table->addCell(13000)->addText(
                $action->name, null,
                ['align'=>'left']
            );
            $table->addCell(3000)->addText(
                $action->next_date, null,
                ['align'=>'left']
            );

            $table->addRow();            
            $section=$table->addCell(18000, ['gridSpan' => 3]);
            $textlines = explode("\n", $action->action_plan);
            foreach ($textlines as $textline) 
                $section->addText($textline);
        }

        // get action plans
        $domains = DB::table('domains')->get();

        $templateProcessor->setComplexBlock('action_plans_table', $table);

        //----------------------------------------------------------------
        // save a copy
        $filepath=storage_path('templates/pilotage-'. Carbon::today()->format("Y-m-d") .'.docx');
        // if (file_exists($filepath)) unlink($filepath);
        $templateProcessor->saveAs($filepath);

        // return
        return response()->download($filepath);       
    }
}

