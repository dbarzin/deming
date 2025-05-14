@extends("layout")

@section("content")
    <div data-role="panel" data-title-caption="{{ trans('cruds.welcome.dashboard') }}" data-collapsible="false" data-title-icon="<span class='mif-home'></span>">
    <div class="row">
        <div class="cell-lg-3 cell-md-6 mt-2">
            <div class="more-info-box fg-white" style="background-color: #fa6800 !important;">
                <div class="content">
                    <h2 class="text-bold mb-0">
                        {{ $active_domains_count }}
                    </h2>
                    <div>{{ trans('menu.domains') }}</div>
                </div>
                <div class="icon">
                    <span class="mif-library"></span>
                </div>
                <a href="/domains" class="more"> {{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
            </div>
        </div>
        <div class="cell-lg-3 cell-md-6 mt-2">
            <div class="more-info-box bg-blue fg-white">
                <div class="content">
                    <h2 class="text-bold mb-0">
                        {{ $active_measures_count }}
                    </h2>
                    <div>{{ trans('menu.measures') }}</div>
                </div>
                <div class="icon">
                    <span class="mif-books"></span>
                </div>
                <a href="/alice/index" class="more"> {{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
            </div>
        </div>
        <div class="cell-lg-3 cell-md-6 mt-2">
            <div class="more-info-box bg-green fg-white">
                <div class="content">
                    <h2 class="text-bold mb-0">
                        {{ $controls_made_count }}
                    </h2>
                    <div>{{ trans('menu.controls') }}</div>
                </div>
                <div class="icon">
                    <span class="mif-paste"></span>
                </div>
                <a href="/bob/index?attribute=none&amp;domain=0&amp;scope=none&amp;period=99&amp;status=1" class="more"> {{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
            </div>
        </div>
        <div class="cell-lg-3 cell-md-6 mt-2">
            <div class="more-info-box bg-crimson fg-white">
                <div class="content">
                    <h2 class="text-bold mb-0">
                        {{ $action_plans_count }}
                    </h2>
                <div>{{ trans('menu.action_plan') }}</div>
                </div>
                <div class="icon">
                    <span class="mif-pending-actions"></span>
                </div>
                <a href="/actions" class="more"> {{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="cell-lg-7 cell-md-12 mt-3">
        <div data-role="panel" data-height='580' data-title-caption="{{ trans('cruds.welcome.control_planning') }}" data-collapsible="false" data-title-icon="<span class='mif-stacked-bar-chart'></span>">
            <div class="d-flex justify-content-center align-items-center" style="height: 510px;">
                <canvas id="canvas-status" style="max-height: 510px;"  class="chartjs-render-monitor"></canvas>
            </div>
        </div>
    </div>
    <div class="cell-lg-5 cell-md-12 mt-3">
        <div data-role="panel" data-height='580' data-title-caption="{{ trans('cruds.welcome.control_status') }}" data-collapsible="false" data-title-icon="<span class='mif-pie-chart'></span>">
            <div class="d-flex justify-content-center align-items-center" style="height: 510px;">
                <canvas id="canvas-doughnut" style="max-height: 510px;"  class="chartjs-render-monitor"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div data-role="panel" data-title-caption="{{ trans('cruds.welcome.next_controls') }}" data-collapsible="false" data-title-icon="<span class='mif-alarm'></span>">
                <table
                    class="table data-table striped row-hover cell-border"
                    >
                    <thead>
                        <tr>
                            <th data-sortable="true">{{ trans('cruds.control.fields.clauses') }}</th>
                            <th data-sortable="true">{{ trans('cruds.control.fields.name') }}</th>
                            <th data-sortable="true">{{ trans('cruds.control.fields.scope') }}</th>
                            <th data-sortable="true">{{ trans('cruds.control.fields.score') }}</th>
                            <th data-sortable="true">{{ trans('cruds.control.fields.realisation_date') }}</th>
                            <th data-sortable="true" data-sort-dir="asc">{{ trans('cruds.control.fields.plan_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($controls_todo as $control)
                        <tr>
                            <td>
                                @foreach($control->measures as $measure)
                                    <a id="{{ $measure['clause'] }}" href="/alice/show/{{ $measure['id'] }}">{{ $measure['clause'] }}</a>@if(!$loop->last),@endif
                                @endforeach
                            </td>
                            <td class="bg-danger text-white">{{ $control->name }}</td>
                            <td>
                                <a href="/bob/index?domain=0&attribute=none&scope={{ urlencode($control->scope) }}&status=0&period=99">
                                    {{ $control->scope }}
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="/bob/show/{{ $control->prev_id }}" style="text-decoration: none;">
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
                            </td>
                            <td><a href="/bob/show/{{$control->prev_id}}">{{ $control->prev_date }}</a></td>
                            <td>
                                <a id="{{ $control->plan_date }}" href="/bob/show/{{ $control->id }}">
                                    <span style="font-weight: bold; color: {{ today()->lte($control->plan_date) ? 'green' : 'red' }}">{{ $control->plan_date }}</span>
                                </a>
                                @if ($control->status===1)
                                    &nbsp;<a href="/bob/make/{{ $control->id }}">⌗</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <br><br><br><br><br>
            </div>
        </div>
    </div>

<!------------------------------------------------------------------------------------->
<script>
document.addEventListener("DOMContentLoaded", function () {
    var barChartData = {
        labels: [
        <?php
        for ($i = -12; $i < 12; $i++) {
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
                stack: 'Stack 0',
                data: [
                    <?php
                    for ($i = -12; $i < 12; $i++) {
                        $count = 0;
                        $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                        $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                        ?>
                        @foreach ($controls as $control)
                            <?php
                            if (($control->score == 3) && ($control->realisation_date != null) && (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))) { $count++; }
                            ?>
                        @endforeach
                        {{ $count }},
                    <?php } ?>
                ]
            },
            {
                backgroundColor: "#fa6800",
                borderColor: "#fa6800",
                stack: 'Stack 0',
                data: [
                    <?php
                    for ($i = -12; $i < 12; $i++) {
                        $count = 0;
                        $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                        $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                        ?>
                        @foreach ($controls as $control)
                            <?php
                            if (($control->score == 2) && ($control->realisation_date != null) && (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))) { $count++; }
                            ?>
                        @endforeach
                        {{ $count }},
                    <?php } ?>
                ]
            },
            {
                backgroundColor: "#ce352c",
                borderColor: "#ce352c",
                stack: 'Stack 0',
                data: [
                    <?php
                    for ($i = -12; $i < 12; $i++) {
                        $count = 0;
                        $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                        $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                        ?>
                        @foreach ($controls as $control)
                            <?php
                            if (($control->score == 1) && ($control->realisation_date != null) && (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))) { $count++; }
                            ?>
                        @endforeach
                        {{ $count }},
                    <?php } ?>
                ]
            },
            {
                backgroundColor: "rgba(128, 128, 128, 0.3)", // gris avec transparence
                borderColor: "gray",
                stack: 'Stack 0',
                data: [
                    <?php
                    for ($i = -12; $i < 12; $i++) {
                        $count = 0;
                        $first = \Carbon\Carbon::today()->startOfMonth()->addMonth($i);
                        $second = \Carbon\Carbon::today()->startOfMonth()->addMonth($i)->endOfMonth();
                        ?>
                        @foreach ($controls as $control)
                            <?php
                            if (($control->realisation_date == null) && (\Carbon\Carbon::parse($control->plan_date)->between($first, $second))) {
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
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: false,
                }
            },
            onHover: (event, chartElement) => {
                event.native.target.style.cursor = chartElement.length ? 'pointer' : 'default';
            },
            onClick: (event, elements) => {
                const activePoints = window.myBar.getElementsAtEventForMode(event.native, 'nearest', { intersect: true }, true);
                if (activePoints.length) {
                    const firstPoint = activePoints[0];
                    window.location.href = "/bob/index?domain=0&attribute=none&scope=none&status=0&period=" + (firstPoint.index - 12);
                }
            }
        },
    });

    var options = {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            },
            title: {
                display: false,
            }
        }
    };

    var ctx2 = document.getElementById('canvas-doughnut').getContext('2d');

    var marksData = {
        labels: [
            "{{ trans('common.fail') }}",
            "{{ trans('common.alert') }}",
            "{{ trans('common.success') }}",
            "{{ trans('common.unknown') }}"
        ],
        datasets: [
            {
                backgroundColor: [
                    '#ce352c', '#fa6800', '#60a917', 'gray'
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
                    {{ $controls_never_made }}
                ]
            }
        ]
    };

    var radarChart = new Chart(ctx2, {
        type: 'doughnut',
        data: marksData,
        options: options
    });
});
</script>
@endsection
