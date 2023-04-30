@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.welcome.dashboard') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
        <div class="row">
            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-orange fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">
                            {{ $active_domains_count }} 
                        </h2>
                        <div>{{ trans("cruds.welcome.domains") }}</div>
                    </div>
                    <div class="icon">
                        <span class="mif-library"></span>
                    </div>
                    <a href="/domains" class="more"> {{ trans('common.more_info' )}} <span class="mif-arrow-right"></span></a>
                </div>
            </div>
            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-cyan fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">
                            {{ $active_measures_count }} 
                        </h2>
                        <div>{{ trans('cruds.welcome.measures') }}</div>
                    </div>
                    <div class="icon">
                        <span class="mif-books"></span>
                    </div>
                    <a href="/measures" class="more"> {{ trans('common.more_info' )}} <span class="mif-arrow-right"></span></a>
                </div>
            </div>

            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-green fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">
                            {{ $controls_made_count }}
                        </h2>
                        <diV>{{ trans('cruds.welcome.controls') }}</diV>
                    </div>
                    <div class="icon">
                        <span class="mif-paste"></span>
                    </div>
                    <a href="/controls?attribute=none&domain=0&period=99&status=1" class="more"> {{ trans('common.more_info' )}} <span class="mif-arrow-right"></span></a>
                </div>
            </div>
            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-red fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">
                            {{ $action_plans_count }}
                        </h2>
                    <div>{{ trans('cruds.welcome.action_plans') }}</div>
                    </div>
                    <div class="icon">
                        <span class="mif-open-book"></span>
                    </div>
                    <a href="/actions" class="more"> {{ trans('common.more_info' )}} <span class="mif-arrow-right"></span></a>
                </div>
            </div>
        </div>
    </div>

<!---------------------------------------->

<div class="row">
    <div class="cell-md-7">
        <div class="panel mt-2">
            <div data-role="panel" data-title-caption="{{ trans('cruds.welcome.control_planning') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
                <div class="p-7">
                    <canvas id="canvas-status" style="display: block; width: 100%; height:500px; " class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!--------------------------------------------------------------------->

    <div class="cell-md-5">
        <div class="panel mt-2">
            <div data-role="panel" data-title-caption="{{ trans('cruds.welcome.control_status') }}" data-collapsible="true" data-title-icon="<span class='mif-meter'></span>">
                <div class="p-7">
                    <canvas id="canvas-doughnut" style="display: block; width: 100%; height: 500px;"  class="chartjs-render-monitor" 
                    ></canvas>                    
                </div>
            </div>
        </div>
    </div>

</div>

<!------------------------------------------------------------------------------------------>

<div class="row">
    <div class="cell-md-12">
        <div class="panel mt-2">
            <div data-role="panel" data-title-caption="{{ trans('cruds.welcome.next_controls') }}" data-collapsible="true" data-title-icon="<span class='mif-calendar'></span>" class="">

            <table class="table striped table-border mt-4"
               data-role="table"
               data-rows="20"
               data-rows-steps="5, 10"
               data-show-activity="false"
               data-check-style="2"
               data-cell-wrapper="false" 
               data-show-search="false"
               data-show-rows-steps="false"
               >
                <thead>
                    <tr>
                        <th data-sortable="true">{{ trans('cruds.control.fields.domain') }}</th>
                        <th data-sortable="true">{{ trans('cruds.control.fields.clause') }}</th>
                        <th >{{ trans('cruds.control.fields.name') }}</th>
                        <th >{{ trans('cruds.control.fields.score') }}</th>
                        <th>{{ trans('cruds.control.fields.realisation_date') }}</th>
                        <th>{{ trans('cruds.control.fields.plan_date') }}</th>
                    </tr>
                </thead>
            <tbody>

            @foreach($controls_todo as $control)
                <tr onclick="window.location = '/controls/{{$control->id}}';">
                    <td>
                        <a href="/domains/{{$control->domain_id}}">
                            {{ $control->domain }}
                        </a>
                        </td>
                    <td>
                        <a href="/measures/{{ $control->measure_id }}">{{ $control->clause }}</a>
                    </td>
                    <td >{{ $control->name }}</td>

                    <td>
                        <center>
                            <a href="/control/show/{{ $control->prev_id }}">
                            @if ($control->score==1)
                                &#128545; 
                            @elseif ($control->score==2)
                                &#128528;
                            @elseif ($control->score==3)
                                <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                            @else
                                &#9675; <!-- &#9899; -->
                            @endif
                            </a>
                        </center>
                    </td>

                    <td>
                        <a href="/control/show/{{$control->prev_id}}">
                            {{ $control->prev_date }}
                        </a>
                    </td>
                    <td>
                        <b>
                            <a href="/controls/{{ $control->id }}">
                            @if( strtotime($control->plan_date) >= strtotime('now') ) 
                                <font color="green">
                            @else
                                <font color="red">
                            @endif
                                {{ $control->plan_date }} 
                            </font>
                            </a>
                        </b>
                    </td>
                </tr>
                </a>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
    </div>

</div>
</div>

<!------------------------------------------------------------------------------------->

<script>
    var color = Chart.helpers.color;
    var barChartData = {
        labels : [
        <?php 
        for ($i=-12;$i<12;$i++) { 
            $now = \Carbon\Carbon::now();
            echo '"';
            echo $now->startOfMonth()->addMonth($i)->format("m/Y");
            echo '",';
        }
        ?>
      ],
      datasets: [
      { 
        backgroundColor: "#60a917",
        borderColor: "#60a917",
        pointBackgroundColor: window.chartColors.green,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0;
                $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->score==3) && 
                    ($control->realisation_date!=null) && 
                    (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))
                ) { $count++; } ?>
            @endforeach   
            {{ $count }},
            <?php } ?> 
        ]
      },
      { 
        backgroundColor: "#fa6800",
        borderColor: "#fa6800",
        pointBackgroundColor: window.chartColors.orange,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0; 
                $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->score==2) && 
                    ($control->realisation_date!=null) && 
                    (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))
                ) { $count++; }
                ?>
            @endforeach          
            {{ $count }},
            <?php } ?> 
        ]
      },
      { 
        backgroundColor: "#ce352c",
        borderColor: "#ce352c",
        pointBackgroundColor: window.chartColors.red,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0; 
                $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->score==1) &&
                    ($control->realisation_date!=null) && 
                    (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))
                ) { $count++; }
                ?>
            @endforeach          
            {{ $count }},
            <?php } ?> 
        ]
      },
      { 
        backgroundColor: color(window.chartColors.grey).alpha(0.3).rgbString(),
        borderColor: window.chartColors.grey,
        pointBackgroundColor: window.chartColors.grey,        
        stack: 'Stack 0',
        data: [
            <?php 
            for ($i=-12; $i<12; $i++) { 
                $count=0; 
                $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                ?>
            @foreach ($controls as $control)
                <?php
                if (($control->realisation_date==null)
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
      ]
    };

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

    document.getElementById('canvas-status').onclick = function(evt){
            var activePoints = window.myBar.getElementsAtEvent(evt);
            var firstPoint = activePoints[0];
            // var label = barChartData.labels[firstPoint._index];
            window.location.href="/controls?domain=0&attribute=none&status=0&period="+(firstPoint._index-12);
        }; 


    var options = {
        responsive: true,
        legend: {
            display: true,
            position: 'bottom',
        },
        title: {
            display: false
        }
    };

    var ctx2 = document.getElementById('canvas-doughnut').getContext('2d');

    var marksData = {
      labels: [
            'Echec','Alerte','Réussi','Unknown'
            ],
      datasets: [
      { 
        backgroundColor: 
            [
                '#ce352c', '#fa6800', '#60a917', window.chartColors.grey
            ],
        borderColor: 
            [
                window.chartColors.red,
                window.chartColors.orange,
                window.chartColors.green,
                window.chartColors.gray,
            ],
        data: [ 
            <?php $count=0; ?>
            @foreach($active_controls as $c)
              <?php if ($c->score=="1") { $count++; } ?>
            @endforeach 
            {{ $count }},
            <?php $count=0; ?>
            @foreach($active_controls as $c)
              <?php if ($c->score=="2") { $count++; } ?>
            @endforeach 
            {{ $count }},
            <?php $count=0; ?>
            @foreach($active_controls as $c)
              <?php if ($c->score=="3") { $count++; } ?>
            @endforeach 
            {{ $count }},
            {{ count($controls_never_made) }}
            ]
        } 
      ]
    };
         
    var radarChart = new Chart(ctx2, {
      type: 'doughnut',
      data: marksData,
      options: options
    });

</script>


@endsection

