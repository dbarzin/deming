@extends("layout")

@section("title")
Planning des contrôles
@endsection

@section("content")
<div class="grid">
    <div class="row">
        <div class="cell-6">

            <canvas id="canvas-status" width="600" height="300px" class="chartjs-render-monitor">
            </canvas>

<!------------------------------------------------------------------------------------->

    <div class="row">
        <div class="cell-6">
    <table class="table subcompact cell-border">
        <tbody>
            @for ($i=-12;$i<0;$i++) 
            <?php
                $first = today()->day(1)->addMonth($i);
                $second = today()->day(1)->addMonth($i+1)->addDay(-1);
            ?>
            <tr>
                <td align="center">
                <a href="/controls?period={{$i}}&domain=0&status=0">                    
                    {{ now()->addMonth($i)->format("m/Y") }}
                </a>
                </td>
                <td align="center">
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (($control->score!=null) && 
                            (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                        ) 
                        {
                                $count++;
                        }
                        ?>
                    @endforeach
                    {{ $count }} 
                    /
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (
                            (
                                ($control->score==null)&&
                                (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))
                            )||
                            (
                                ($control->score!=null)&&
                                (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                            )
                        ) 
                        {
                                $count++;
                        }
                        ?>
                    @endforeach
                    {{ $count }} 
                </td>
                <td  align="center">
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (($control->score==1) &&
                            (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                        ) {
                                $count++;
                        }
                        ?>
                    @endforeach
                    <font color="red">   
                    @if ($count>0) {{ $count }} 
                        &nbsp; &#9679; 
                    @else
                        &nbsp; &nbsp; &nbsp; 
                    @endif
                    </font>
                </td>
                <td  align="center">
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (($control->score==2) &&
                            (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                        ) {
                                $count++;
                        }
                        ?>
                    @endforeach          
                    <font color="orange">   
                    @if ($count>0) {{ $count }} 
                        &nbsp; &#9679; 
                    @else
                        &nbsp; &nbsp; &nbsp;
                    @endif
                    </font>
                </td>
                <td align="center"> 
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (($control->score==3) &&
                            (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                        ) {
                                $count++;
                        }
                        ?>
                    @endforeach          
                    <font color="green">   
                    @if ($count>0) {{ $count }} &nbsp; &#9679; 
                    @else
                    &nbsp; &nbsp; &nbsp;
                    @endif
                    </font>
                </td>
            </tr>
        @endfor
        </tbody>
    </table>
</div>

<!----------------------------------------------------------->

    <div class="cell-6">
    <table class="table subcompact cell-border">
        <tbody>
        <?php 
        for ($i=0;$i<12;$i++) { 
            $first = \Carbon\Carbon::today()->day(1)->addMonth($i);
            $second = \Carbon\Carbon::today()->day(1)->addMonth($i+1)->addDay(-1);
            ?>
            <tr>
                <td align="center">
                <a href="/controls?period={{$i}}&domain=0&status=0">
                <?php        
                        $now = \Carbon\Carbon::now();
                        echo $now->addMonth($i)->format("m/Y");
                ?>
                </a>
                </td>
                <td align="center">
                <?php $count=0; ?>
                    @foreach ($controls as $control)
                    <?php
                    if (($control->score!=null) &&
                        (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                    ) {
                            $count++;
                    }
                    ?>
                    @endforeach
                    {{ $count }} 
                    /
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (
                            (
                                ($control->score==null) &&
                                (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))
                            )||
                            (
                                ($control->score!=null) &&
                                (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                            )
                        ) {
                                $count++;
                        }
                        ?>
                    @endforeach
                    {{ $count }} 
                </td>
                <td  align="center">
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (($control->score==1)
                            && (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                        ) {
                                $count++;
                        }
                        ?>
                    @endforeach
                    <font color="red">   
                    @if ($count>0) {{ $count }} &nbsp; &#9679; 
                    @else
                    &nbsp; &nbsp; &nbsp;
                    @endif
                    </font>
                </td>
                <td  align="center">
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (
                            ($control->score==2) && 
                            (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                            ) 
                        {
                                $count++;
                        }
                        ?>
                    @endforeach          
                    <font color="orange">   
                    @if ($count>0) {{ $count }} &nbsp; &#9679; 
                    @else
                    &nbsp; &nbsp; &nbsp;
                    @endif
                    </font>
                </td>
                <td align="center"> 
                    <?php $count=0; ?>
                    @foreach ($controls as $control)
                        <?php
                        if (($control->score==3) && 
                            (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                        ) {
                                $count++;
                        }
                        ?>
                    @endforeach          
                    <font color="green">
                    @if ($count>0) {{ $count }} &nbsp; &#9679; 
                    @else
                    &nbsp; &nbsp; &nbsp;
                    @endif
                    </font>
                </td>
            </tr>
        <?php  } ?>
        </tbody>
    </table>
</div>
</div>
</div>

<!------------------------------------------------------------------------------------->
        <div class="cell-6">
            <?php
            $calendar = new \App\Calendar(\Carbon\Carbon::now()->format('y-m-d'));
            foreach ($controls as $control) {
                // echo "check :" . \Carbon\Carbon::parse($control->plan_date) . " " . \Carbon\Carbon::parse($control->realisation_date) . 
                // echo "check :" . $control->clause . " " . $control->plan_date . " " . $control->realisation_date . "<br>";
                if (($control->score==null) && ($control->plan_date!=null)) {
                        $calendar->add_event($control->clause, $control->plan_date, 1, 'grey');
                        // echo 'plan:' . $control->plan_date . '<br>';
                    }
                if (($control->score==1) && ($control->realisation_date!=null)) {
                        $calendar->add_event($control->clause, $control->realisation_date, 1, 'red');
                        // echo 'red:' . $control->realisation_date . '<br>';
                        }
                if (($control->score==2) && ($control->realisation_date!=null)) {
                        $calendar->add_event($control->clause, $control->realisation_date, 1, 'orange');
                        // echo 'orange:' . $control->realisation_date . '<br>';
                        }
                if (($control->score==3) && ($control->realisation_date!=null)) {
                        $calendar->add_event($control->clause, $control->realisation_date, 1, 'green');
                        // echo 'green:' . $control->realisation_date . '<br>';
                    }
                }
            /*
            $calendar->add_event('A.9.4', '2021-02-03', 1, 'green');
            $calendar->add_event('A.12.3', '2021-02-03', 1, 'green');
            $calendar->add_event('A.4.3.2', '2021-02-04', 1, 'red');
            $calendar->add_event('A.7.5.5', '2021-02-16', 1, 'grey');
            */
            echo $calendar;
            ?>            
        </div>

</div>

<!------------------------------------------------------------------------------------->

<script src="/vendors/chartjs/Chart.bundle.min.js"></script>
<script src="/js/utils.js"></script>

<script>
    var color = Chart.helpers.color;
    var barChartData = {
        labels : [
        <?php 
        for ($i=-12;$i<12;$i++) { 
            $now = \Carbon\Carbon::now();
            echo '"';
            echo $now->addMonth($i)->format("m/Y");
            echo '",';
        }
        ?>
      ],
      datasets: [
      { 
        backgroundColor: color(window.chartColors.grey).alpha(0.3).rgbString(),
        borderColor: window.chartColors.grey,
        pointBackgroundColor: window.chartColors.grey,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0; 
                $first = \Carbon\Carbon::today()->day(1)->addMonth($i);
                $second = \Carbon\Carbon::today()->day(1)->addMonth($i+1)->addDay(-1);
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->score==null)
                    && ($control->plan_date!=null)
                    && (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))
                ) {
                        $count++;
                }
                ?>
            @endforeach          
            {{ $count }},
            <?php } ?> 
        ]
      },
      { 
        backgroundColor: color(window.chartColors.red).rgbString(),
        borderColor: window.chartColors.red,
        pointBackgroundColor: window.chartColors.red,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0; 
                $first = \Carbon\Carbon::today()->day(1)->addMonth($i);
                $second = \Carbon\Carbon::today()->day(1)->addMonth($i+1)->addDay(-1);
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->score==1) &&
                    ($control->realisation_date!=null) && 
                    (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                ) {
                    $count++;
                }
                ?>
            @endforeach          
            {{ $count }},
            <?php } ?> 
        ]
      },
      { 
        backgroundColor: color(window.chartColors.orange).rgbString(),
        borderColor: window.chartColors.orange,
        pointBackgroundColor: window.chartColors.orange,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0; 
                $first = \Carbon\Carbon::today()->day(1)->addMonth($i);
                $second = \Carbon\Carbon::today()->day(1)->addMonth($i+1)->addDay(-1);
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->score==2) && 
                    ($control->realisation_date!=null) && 
                    (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                ) { $count++;
                }
                ?>
            @endforeach          
            {{ $count }},
            <?php } ?> 
        ]
      },
      { 
        backgroundColor: color(window.chartColors.green).rgbString(),
        borderColor: window.chartColors.green,
        pointBackgroundColor: window.chartColors.green,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0; 
                $first = \Carbon\Carbon::today()->day(1)->addMonth($i);
                $second = \Carbon\Carbon::today()->day(1)->addMonth($i+1)->addDay(-1);
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->score==3) && 
                    ($control->realisation_date!=null) && 
                    (\Carbon\Carbon::parse($control->realisation_date)->between($first, $second))
                ) { $count++;
                }
                ?>
            @endforeach          
            {{ $count }},
            <?php } ?> 
        ]
      }
      ]
    };
         
    window.onload = function() {
        var ctx = document.getElementById('canvas-status').getContext('2d');
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                title: {
                    display: false
                }
            }
        });

    };
    </script>

    <?php 
    /*
    for ($i=-12; $i<12; $i++) { 
        $count=0; 
        $first = \Carbon\Carbon::today()->day(1)->addMonth($i);
        $second = \Carbon\Carbon::today()->day(1)->addMonth($i+1);
    echo "<hr>";
    echo $first;
    echo " - ";
    echo $second;
    echo "<br>";
    ?>
    @foreach ($controls as $control)
        <?php 
        echo $control->id;
        echo ' : ';
        echo $control->score;
        echo ' : ';
        echo $control->plan_date;
        echo ' -> ';
        echo \Carbon\Carbon::parse($control->plan_date)->between($first, $second,false) ? "true" :"false";
        echo '<br>';
        if (($control->score==null)&&
            ($control->plan_date!=null)&&
            (\Carbon\Carbon::parse($control->plan_date)
            ->between($first, $second))) {
                $count++;
                }
        ?>
    @endforeach          
    {{ $count }},
    <?php } 
    */
    ?> 

@endsection
