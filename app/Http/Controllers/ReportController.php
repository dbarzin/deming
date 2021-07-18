<?php

namespace App\Http\Controllers;

Use \Carbon\Carbon;

use App\Exports\MeasurementsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Control;
use App\Domain;
use App\Measurement;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome(Request $request)
    {
        // count active controls
        $controls = DB::table('controls')
            ->count();

        //  count mesurement made
        $measurements = DB::table('measurements')
            ->whereNull('realisation_date')
            ->count();


        $domains = DB::table('domains')->get();

        // status                
        $status=
            DB::table('measurements')            
            ->select(
                'control_id',
                DB::raw('max(measurements.id) as id'),
                'domains.title as title', 
                'realisation_date', 
                'score')
            ->join('domains', 'domains.id', '=', 'measurements.domain_id')
            ->whereNotNull('realisation_date')
            ->groupBy('control_id')
            ->get();
        
        //dd($status);

        // get planned controls
        $plannedMeasurements=DB::table('measurements')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date","<",(new Carbon('first day of next month'))->toDateString()]
                    ]
            )
            ->get();
        // dd($plannedMeasurements);

        // planed measurements this month
        $planed_measurements=DB::table('measurements')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date",">=", (new Carbon('first day of this month'))->toDateString()],
                    ["plan_date","<", (new Carbon('first day of next month'))->toDateString()]
                    ]
            )
            ->count();
        $request->session()->put("planed_measurements", $planed_measurements);

        // late measurements
        $lateMeasurements=DB::table('measurements')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date","<", Carbon::today()->toDateString()],
                    ]
            )
            ->count();
        $request->session()->put("late_measurements", $lateMeasurements);

        // action plans
        $actions=count(
            DB::select(
                DB::raw(
                    "
                SELECT 
                    m2.id as id, 
                    m2.control_id as control_l, 
                    m2.clause as clause, 
                    m2.name as name, 
                    m2.plan_date as plan_date, 
                    m2.score as score 
                FROM 
                ( SELECT 
                    max(id) as id,
                    control_id
                FROM  
                    measurements 
                WHERE (score=1 or score=2) 
                GROUP BY control_id) as m1, measurements m2
                WHERE m1.id=m2.id;
            "
                )
            )
        );
        $request->session()->put("actions", $actions);

        // return 
        return view("welcome")
            ->with('status', $status)
            ->with('controls', $controls)
            ->with('measurements', $measurements)
            ->with('domains', $domains)
            ->with('actions', $actions)
            ->with('plannedMeasurements', $plannedMeasurements);
    }

    /**
     * Generate tests data. !!!! DANGEROUS !!!!
     *
     * @return \Illuminate\Http\Response
     */
    public function generateTests()
    {        
        // remove all measurements
        DB::table('documents')->delete();
        DB::table('measurements')->delete();

        // period in month
        $period = 12;

        // Start date
        $curDate=Carbon::now()->addMonth(-$period)->day(1);
        // Log::Alert("startDate=" . $curDate->toDateString());

        // get all controls
        $controls = Control::All();
        $cntControls = DB::table('controls')->count();
        // Log::Alert("controld count=" . $cntControls);

        // controls per period
        $perPeriod = (int)($cntControls / $period);
        // Log::Alert("control per period=" . $perPeriod);

        // loop on controls
        $curControl = 1; 
        foreach ($controls as $control) {
            // go to next period
            if (($curControl++ % $perPeriod)==0) {
                $curDate->addMonth(1);                
            }

            // Log::Alert("Control " . $control->clause . " curDate=" . $curDate->toDateString());

            // create a measurement
            // TODO : loop on plan_date until date is in the futur
            $measurement = new Measurement();
            $measurement->control_id=$control->id;
            $measurement->domain_id=$control->domain_id;
            $measurement->name=$control->name;
            $measurement->clause=$control->clause;
            $measurement->objective = $control->objective;
            $measurement->attributes = $control->attributes;
            $measurement->model = $control->model;
            $measurement->indicator = $control->indicator;
            $measurement->action_plan = $control->action_plan;
            $measurement->owner = $control->owner;
            $measurement->periodicity = $control->periodicity;
            $measurement->retention = $control->retention;
            // do it            
            $measurement->plan_date = $curDate->toDateString();
            $measurement->realisation_date = (new Carbon($curDate))->addDay(rand(0, 28))->toDateString();
            $measurement->note = rand(0, 10);
            $measurement->score = rand(0, 100)<90 ? 3 : (rand(0, 2)<2 ? 2 : 1);
            // save it
            $measurement->save();

            // create next measurement
            $measurement = new Measurement();
            $measurement->control_id=$control->id;
            $measurement->domain_id=$control->domain_id;
            $measurement->name=$control->name;
            $measurement->clause=$control->clause;
            $measurement->objective = $control->objective;
            $measurement->attributes = $control->attributes;
            $measurement->model = $control->model;
            $measurement->indicator = $control->indicator;
            $measurement->action_plan = $control->action_plan;
            $measurement->owner = $control->owner;
            $measurement->periodicity = $control->periodicity;
            $measurement->retention = $control->retention;
            // next one            
            $measurement->plan_date = (new Carbon($curDate))->addMonth($control->periodicity)->toDateString();
            // fix it
            $measurement->realisation_date=null;
            $measurement->note=null;
            $measurement->score=null;
            // save it
            $measurement->save();
        }
        return redirect("/");
    }

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

        // get template
        $templateProcessor = new TemplateProcessor(
            storage_path('app/models/pilotage.docx')
        );

        //-------------------------------------------------------------
        // make changes 
        //-------------------------------------------------------------
        $templateProcessor->setValue('today', $today->format('d/m/Y'));
        $templateProcessor->setValue('start_date', $start_date->format('d/m/Y'));
        $templateProcessor->setValue('end_date', $end_date->format('d/m/Y'));

        // addText('', $fontStyle);

        //----------------------------------------------------------------        
        $measurements =  Measurement::where(
            [
                    ["realisation_date",">=",$start_date],            
                    ["realisation_date","<",$end_date],
            ]
        )
            ->orderBy("realisation_Date")->get();
        /*
        $values = [];
        foreach($measurements as $measurement) {

            $values[] =  [
                'ctrl_id' => $measurement->clause,
                'ctrl_date' => $measurement->realisation_date, 
                'ctrl_name' => $measurement->name,
                'ctrl_score' => '<w:highlight w:val="red">'.'⬤'.$measurement->score.'</w:highlight>',
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

        foreach($measurements as $measurement) {
            $table->addRow();
            $table->addCell(2500)->addText($measurement->clause);
            $table->addCell(12500)->addText($measurement->name);
            $table->addCell(2800)->addText($measurement->realisation_date, null, ['align'=>'center']);
            $table->addCell(2000)->addText(
                '⬤',
                ($measurement->score==1 ? ['color'=>'#FF0000'] : 
                ($measurement->score==2 ? ['color'=>'#FF8000'] :
                ($measurement->score==3 ? ['color'=>'#00CC00'] : null))),
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
            DB::raw(
                "
            SELECT 
            m2.control_id as control_id, 
            m2.domain_id as domain_id,
            m2.score as score,
            m2.realisation_date as realisation_date
            FROM
                (select 
                control_id,
                max(id) as id
                from measurements
                where realisation_date is not null and score is not null
                group by control_id) as m1, measurements as m2
            where m1.id=m2.id;
                "
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
        $actions=DB::select(
            DB::raw(
                "
                SELECT 
                    m2.id as id, 
                    m2.control_id as control_id, 
                    m2.clause as clause, 
                    m2.name as name, 
                    m2.plan_date as plan_date, 
                    m2.score as score,
                    m2.realisation_date as realisation_date,
                    m3.plan_date as next_date,
                    m2.action_plan as action_plan                    
                FROM 
                ( SELECT 
                    max(id) as id,
                    control_id
                FROM  
                    measurements 
                WHERE ((score=1 or score=2) and realisation_date is not null)                
                GROUP BY control_id
                ) as m1, 
                measurements m2
                LEFT JOIN measurements m3 on ( 
                    m2.id<>m3.id and m2.control_id = m3.control_id and m3.realisation_date is null)
                WHERE m1.id=m2.id;
                "
            )
        );

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
            $table->addCell(18000, ['gridSpan' => 3])->addText(
                $action->action_plan, 
            );
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

