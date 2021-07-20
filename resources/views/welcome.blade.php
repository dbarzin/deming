@extends("layout")

@section("title")
Tableau de bord
@endsection

@section("content")
<!--
<div class="m-3">
<div class="row mt-2">
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-cyan">
            <div class="icon bg-cyan fg-white"><span class="mif-cog"></span></div>
            <div class="content p-4">
                <div class="text-upper">measurements</div>
                <div class="text-upper text-bold text-lead">90%</div>
            </div>
        </div>
    </div>
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-red">
            <div class="icon bg-red fg-white"><span class="mif-google-plus"></span></div>
            <div class="content p-4">
                <div class="text-upper">controls</div>
                <div class="text-upper text-bold text-lead">41,410</div>
            </div>
        </div>
    </div>
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-green">
            <div class="icon bg-green fg-white"><span class="mif-cart"></span></div>
            <div class="content p-4">
                <div class="text-upper">sales</div>
                <div class="text-upper text-bold text-lead">1024</div>
            </div>
        </div>
    </div>
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-orange">
            <div class="icon bg-orange fg-white"><span class="mif-users"></span></div>
            <div class="content p-4">
                <div class="text-upper">new members</div>
                <div class="text-upper text-bold text-lead">3,300</div>
            </div>
        </div>
    </div>
</div>
-->

<!------------------------------------------------------------------------------------------>

<div class="row">
    <div class="cell-lg-3 cell-md-6 mt-2">
        <div class="more-info-box bg-orange fg-white">
            <div class="content">
                <h2 class="text-bold mb-0">
                    {{ $active_domains_count }} / {{ count($domains) }}
                </h2>
                <div>Domaines</div>
            </div>
            <div class="icon">
                <span class="mif-books"></span>
            </div>
            <a href="/domains" class="more"> More info <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-3 cell-md-6 mt-2">
        <div class="more-info-box bg-cyan fg-white">
            <div class="content">
                <h2 class="text-bold mb-0">
                    {{ $active_controls_count }} / {{ $controls_count }}
                </h2>
                <div>Controls</div>
            </div>
            <div class="icon">
                <span class="mif-event-available"></span>
            </div>
            <a href="/controls" class="more"> More info <span class="mif-arrow-right"></span></a>
        </div>
    </div>

    <div class="cell-lg-3 cell-md-6 mt-2">
        <div class="more-info-box bg-green fg-white">
            <div class="content">
                <h2 class="text-bold mb-0">
                    {{ $measurements_made_count }}
                </h2>
                @if ($measurements_made_count>1)
                <div>Mesures</div>
                @else
                <div>Mesure</div>
                @endif
            </div>
            <div class="icon">
                <span class="mif-paste"></span>
            </div>
            <a href="/measurements?domain=0&period=99&status=1" class="more"> More info <span class="mif-arrow-right"></span></a>
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
        <div class="panel mt-4">
            <div data-role="panel" data-title-caption="Etat des contrôles au {{ date('d/m/Y')}}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
                <div class="p-4">
                    <canvas id="canvas-status" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="cell-md-5">
        <div class="panel mt-4">

        <div data-role="panel" data-title-caption="Performances" data-collapsible="true" data-title-icon="<span class='mif-paragraph-left'></span>" class="">
    
            <div class="clear">
                <div class="place-left">Mesures réussies</div>
                <div class="place-right">
                    <strong>
                        <?php $count=0; ?>
                        @foreach($active_measurements as $m)
                          <?php if ($m->score=="3") { $count++; } ?>
                        @endforeach 
                        {{ $count }}
                    </strong>
                    /
                    {{ $active_controls_count }}
                </div>
            </div>
            <div data-role="progress" data-value="35" class="progress" data-role-progress="true">
                <div class="bar bg-green" style="width: {{ (count($active_measurements)>0) ? $count/count($active_measurements)*100 : 0 }}%  ;">
                </div>
            </div>

            <div class="clear">
                <div class="place-left">Mesures en alerte</div>
                <div class="place-right">
                    <strong>
                        <?php $count=0; ?>
                        @foreach($active_measurements as $m)
                          <?php if ($m->score=="2") { $count++; } ?>
                        @endforeach 
                        {{ $count }}
                    </strong>
                    /
                    {{ $active_controls_count }}
                </div>
            </div>
            <div data-role="progress" data-value="{{ count($active_measurements) }}" class="progress" data-role-progress="true">
                <div class="bar bg-orange" style="width: {{ (count($active_measurements)>0) ? $count/count($active_measurements)*100 : 0 }}% ;">
                </div>
            </div>

            <div class="clear">
                <div class="place-left">Mesures en échec</div>
                <div class="place-right">
                    <strong>
                        <?php $count=0; ?>
                        @foreach($active_measurements as $m)
                          <?php if ($m->score=="1") { $count++; 
                          } ?>
                        @endforeach 
                        {{ $count }}
                    </strong>
                    /
                    {{ $active_controls_count }}
                </div>
            </div>
            <div data-role="progress" data-value="{{ count($active_measurements) }}" class="progress" data-role-progress="true">
                <div class="bar bg-red" style="width: {{ (count($active_measurements)>0) ? $count/count($active_measurements)*100 : 0 }}%  ;">
                </div>
            </div>

            <div class="clear">
                <div class="place-left">Mesures non-réalisées</div>
                <div class="place-right">
		    <strong>
                       ???
                    </strong>
                    /
                    {{ $active_controls_count }}
                </div>
            </div>
            <div data-role="progress" data-value="35" class="progress" data-role-progress="true">
                <div class="bar bg-gray" style="width: {{ (count($active_measurements)>0) ? $count/count($active_measurements)*100 : 0 }}%  ;">
                </div>
            </div>

            <p class="text-small">Les performances passées ne préjugent pas des performances futures.</p>
            </div>
        </div>
    </div>
</div>

<!------------------------------------------------------------------------------------------>

<div class="row">
    <div class="cell-md-7">
        <div class="panel mt-4">
            <div data-role="panel" data-title-caption="Contrôles plannifiés" data-collapsible="true" data-title-icon="<span class='mif-calendar'></span>" class="">
                <div class="p-4">

            <table class="table striped row-hover cell-border"
               data-role="table"
               data-rows="10"
               data-show-activity="true"
               data-rownum="false"
               data-check="false"
               data-check-style="1"
               id="MeasurementTable"
               >
                <thead>
                    <tr>
                        <th class="sortable-column" width="5%">Domain</th>
                        <th width="5%">Clause</th>
                        <th width="85%">Name</th>
                        <th class="sortable-column sort-asc"  width="5%">Date</th>
                    </tr>
                </thead>
                <tbody>

            @foreach($measurements_todo as $measurement)
                <tr onclick="window.location = '/measurements/{{$measurement->id}}';">
                    <td>
                        <a href="/domains/{{$measurement->domain_id}}">
                        {{ \App\Domain::find($measurement->domain_id)->title }} 
                        </a>
                        </td>
                    <td>
                        <a href="/controls/{{ $measurement->control_id }}">{{ $measurement->clause }}</a>
                    </td>
                    <td>{{ $measurement->name }} </td>
                    <td>
                        <!-- format in red when month passed -->
                        <b>
                            <a href="/measurements/{{ $measurement->id }}">
                        @if ($measurement->realisation_date==null)
                            @if (
                            \Carbon\Carbon::
                            createFromFormat('Y-m-d',$measurement->plan_date)
                            ->isAfter(\Carbon\Carbon::now()))
                                <font color="green">
                            @else
                                <font color="red">
                            @endif
                        @endif
                            {{ 
                            \Carbon\Carbon::
                            createFromFormat('Y-m-d',$measurement->plan_date)
                            ->format('Y-m-d') }} 
                        @if ($measurement->realisation_date!=null)
                            </font>
                        @endif
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

    <div class="cell-md-5">
        <div class="panel mt-4">

        <div data-role="panel" data-title-caption="Radar" data-collapsible="true" data-title-icon="<span class='mif-meter'></span>" class="">
    
        <canvas id="canvas-radar" style="display: block; width: 500px; height: 300px;" width="400" height="300" class="chartjs-render-monitor">
        </canvas>

    </div>
</div>
</div>

<script src="/vendors/chartjs/Chart.bundle.min.js"></script>
<script src="/js/utils.js"></script>
</div>
</div>
</div>

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
            backgroundColor: '#61b045',
            borderColor: window.chartColors.green,
            pointBackgroundColor: window.chartColors.green,        
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain) 
                    <?php $count=0; ?>
                    @foreach($active_measurements as $m)
                      <?php if (($m->score==3)&&($m->title==$domain->title)) { $count++; } ?>
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
                    @foreach($active_measurements as $m)
                      <?php if (($m->score==2)&&($m->title==$domain->title)) { $count++; 
                      } ?>
                    @endforeach 
                    {{ $count }}
                    {{ $loop->last ? '' : ',' }}
                @endforeach 
            ]
        }, {
            // label: 'Rouge',
            backgroundColor: '#FF0000',
            borderColor: window.chartColors.red,
            pointBackgroundColor: window.chartColors.red,        
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain) 
                    <?php $count=0; ?>
                    @foreach($active_measurements as $m)
                      <?php if (($m->score==1)&&($m->title==$domain->title)) { $count++; 
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
                    @foreach($active_measurements as $m)
                      <?php if (($m->score==null)&&($m->title==$domain->title)) { $count++; 
                      } ?>
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
            window.location.href="measurements?status=1&period=99&domain_title="+label;
        };    
    </script>


<!------------------------->
<!-- RADAR -->
<!------------------------->
<script>

    var options = {
        responsive: true,
        legend: {
            display: false,
        },
        title: {
            display: false
        }
    };

    var ctx2 = document.getElementById('canvas-radar').getContext('2d');

    var marksData = {
      labels: [
            @foreach ($domains as $domain) 
                '{{ $domain->title }}'
                 {{ $loop->last ? '' : ',' }}
            @endforeach 
            ],
      datasets: [
      { // red
        backgroundColor: color(window.chartColors.red).alpha(0.2).rgbString(),
        borderColor: window.chartColors.red,
        pointBackgroundColor: window.chartColors.red,        
        data: [
        @foreach ($domains as $domain) 
            <?php $count=0; $total=0; ?>
            @foreach($active_measurements as $m)
              <?php 
                if ($m->title==$domain->title) { $total++;
                }
                if (($m->score==1)&&($m->title==$domain->title)) { $count++;
                }
                ?>
            @endforeach 
            {{ $count }}
            {{ $loop->last ? '' : ',' }}
        @endforeach 
        ]
      },{
        // orange
        backgroundColor: color(window.chartColors.orange).alpha(0.2).rgbString(),
        borderColor: window.chartColors.orange,
        pointBackgroundColor: window.chartColors.orange,
        data: [
        @foreach ($domains as $domain) 
            <?php $count=0; $total=0; ?>
            @foreach($active_measurements as $m)
              <?php 
                if ($m->title==$domain->title) { $total++;
                }
                if ((($m->score==1)||($m->score==2))&&($m->title==$domain->title)) { $count++; 
                } 
                ?>
            @endforeach 
            {{ $count }}
            {{ $loop->last ? '' : ',' }}
        @endforeach 
        ]
      }, {
        // Green
        backgroundColor: color(window.chartColors.green).alpha(0.2).rgbString(),
        borderColor: window.chartColors.green,
        pointBackgroundColor: window.chartColors.green,
        data: [
        @foreach ($domains as $domain) 
            <?php $count=0; $total=0; ?>
            @foreach($active_measurements as $m)
              <?php 
                if ($m->title==$domain->title) { $total++;
                }
                if ((($m->score==1)||($m->score==2)||($m->score==3))&&($m->title==$domain->title)) { $count++; 
                } 
                ?>
            @endforeach 
            {{ $count }}
            {{ $loop->last ? '' : ',' }}
        @endforeach 
        ]
      },       {
        // label: "Zero",
        backgroundColor: "rgba(0,0,0,1)",
        data: [0,0,0,0]
      } 
      ]
    };
         
    var radarChart = new Chart(ctx2, {
      type: 'radar',
      data: marksData,
      options: options
    });

</script>


@endsection

