@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Tableau de bord" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
        <div class="row">
            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-orange fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">
                            {{ $active_domains_count }} 
                        </h2>
                        <div>Domaines</div>
                    </div>
                    <div class="icon">
                        <span class="mif-library"></span>
                    </div>
                    <a href="/domains" class="more"> More info <span class="mif-arrow-right"></span></a>
                </div>
            </div>
            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-cyan fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">
                            {{ $active_measures_count }} 
                        </h2>
                        <div>Mesures de sécurité</div>
                    </div>
                    <div class="icon">
                        <span class="mif-books"></span>
                    </div>
                    <a href="/measures" class="more"> More info <span class="mif-arrow-right"></span></a>
                </div>
            </div>

            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-green fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">
                            {{ $controls_made_count }}
                        </h2>
                        @if ($controls_made_count>1)
                        <div>Contrôles</div>
                        @else
                        <div>Contrôle</div>
                        @endif
                    </div>
                    <div class="icon">
                        <span class="mif-paste"></span>
                    </div>
                    <a href="/controls?domain=0&period=99&status=1" class="more"> More info <span class="mif-arrow-right"></span></a>
                </div>
            </div>
            <div class="cell-lg-3 cell-md-6 mt-2">
                <div class="more-info-box bg-red fg-white">
                    <div class="content">
                        <h2 class="text-bold mb-0">{{ $action_plans_count }} </h2>
                        @if ($action_plans_count>1)
                        <div>Plans d'action</div>
                        @else
                        <div>Plan d'action</div>
                        @endif
                    </div>
                    <div class="icon">
                        <span class="mif-open-book"></span>
                    </div>
                    <a href="/actions" class="more"> More info <span class="mif-arrow-right"></span></a>
                </div>
            </div>
        </div>
    </div>

<!---------------------------------------->

<div class="row">
    <div class="cell-md-7">
        <div class="panel mt-2">
            <div data-role="panel" data-title-caption="Etat des contrôles au {{ date('d/m/Y')}}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
                <div class="p-7">
                    <canvas id="canvas-status" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!--------------------------------------------------------------------->

    <div class="cell-md-5">
        <div class="panel mt-2">
            <div data-role="panel" data-title-caption="Répartition" data-collapsible="true" data-title-icon="<span class='mif-meter'></span>">
                <div class="p-7">
                    <canvas id="canvas-doughnut" style="display: block; width: 200px; height: 146px;"  class="chartjs-render-monitor" 
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
            <div data-role="panel" data-title-caption="Contrôles plannifiés" data-collapsible="true" data-title-icon="<span class='mif-calendar'></span>" class="">

            <table class="table striped table-border mt-4"
               data-role="table"
               data-rows="10"
               data-rows-steps="5, 10"
               data-show-activity="false"
               data-check-style="2"
               data-cell-wrapper="false" 
               data-show-search="false"
               data-show-rows-steps="false"
               >
                <thead>
                    <tr>
                        <th class="sortable-column">Domain</th>
                        <th>Clause</th>
                        <th >Name</th>
                        <th >Note</th>
                        <th >Réalisé</th>
                        <th class="sortable-column">Planifié</th>
                    </tr>
                </thead>
            <tbody>

            @foreach($controls_todo as $control)
                <tr onclick="window.location = '/controls/{{$control->id}}';">
                    <td>
                        <a href="/domains/{{$control->domain_id}}">
                        {{ \App\Domain::find($control->domain_id)->title }} 
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

<script src="/vendors/chartjs/Chart.bundle.min.js"></script>
<script src="/js/utils.js"></script>

<!------------------------------------------------------------------------------------->

<script>
    var color = Chart.helpers.color;
    var barChartData = {
        labels : [
            @foreach ($domains as $domain) 
                '{{ $domain->title }}'
                 {{ $loop->last ? '' : ',' }}
            @endforeach 
            ],
        datasets: [{
            // label: 'Vert',
            backgroundColor: '#60a917',
            borderColor: window.chartColors.green,
            pointBackgroundColor: window.chartColors.green,        
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain) 
                    <?php $count=0; ?>
                    @foreach($active_controls as $c)
                      <?php if (($c->score==3)&&($c->title==$domain->title)) { $count++; } ?>
                    @endforeach 
                    {{ $count }}
                    {{ $loop->last ? '' : ',' }}
                @endforeach 
            ]
        }, {
            // label: 'Orange',
            backgroundColor: '#fa6800',
            borderColor: window.chartColors.orange,
            borderWidth: 1,
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain) 
                    <?php $count=0; ?>
                    @foreach($active_controls as $c)
                      <?php if (($c->score==2)&&($c->title==$domain->title)) { $count++; 
                      } ?>
                    @endforeach 
                    {{ $count }}
                    {{ $loop->last ? '' : ',' }}
                @endforeach 
            ]
        }, {
            // label: 'Rouge',
            backgroundColor: '#ce352c',
            borderColor: window.chartColors.red,
            pointBackgroundColor: window.chartColors.red,        
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain) 
                    <?php $count=0; ?>
                    @foreach($active_controls as $c)
                      <?php if (($c->score==1)&&($c->title==$domain->title)) { $count++; 
                      } ?>
                    @endforeach 
                    {{ $count }}
                    {{ $loop->last ? '' : ',' }}
                @endforeach 
            ]
        }, {
            label: 'Gris',
            backgroundColor: color(window.chartColors.grey).alpha(1).rgbString(),
            borderColor: window.chartColors.black,
            borderWidth: 1,
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain) 
                    <?php $count=0; ?>
                    @foreach($controls_never_made as $c)
                      <?php if ($c->domain_id==$domain->id) { $count++; } ?>
                    @endforeach 
                    {{ $count }}
                    {{ $loop->last ? '' : ',' }}
                @endforeach 
            ]
        }]

    };

    var ctx1 = document.getElementById('canvas-status').getContext('2d');
    window.myBar = new Chart(ctx1, {
        responsive: true,
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
            var label = barChartData.labels[firstPoint._index];
            var value = barChartData.datasets[firstPoint._datasetIndex].data[firstPoint._index];
            window.location.href="/controls?status=0&period=99&domain_title="+label;
        };    
    </script>


<!------------------------->
<!-- DOUGHNUT -->
<!------------------------->
<script>

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

