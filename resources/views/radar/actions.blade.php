@extends("layout")

@section("content")
<form action="/radar/domains">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.radar') }}" data-collapsible="false" data-title-icon="<span class=' mif-stacked-bar-chart'></span>">

    <div class="row">
        <div class="cell-md-9">
            <div class="row">
                <div class="cell-auto">
                    <strong>Period</strong>
                    <input
                    id="start"
                    name="start"
                    value="{{ $start }}"
                    data-prepend="Start"
                    data-role="calendarpicker"
                    data-format="YYYY-MM-DD"/>
                </div>
                <div class="cell-auto">
                    <br>
                    <input
                    id="end"
                    name="end"
                    data-prepend="End"
                    value="{{ $end }}"
                    data-role="calendarpicker"
                    data-format="YYYY-MM-DD"/>
                </div>
                <div class="cell-4">
                    <strong>Scope</strong>
                    <select id='scope' name="scope" data-role="select">
                        <option value="">-- {{ trans("cruds.action.fields.choose_scope")}} --</option>
                        @foreach ($scopes as $scope)
                            <option {{ (Session::get('scope')==$scope) ? 'selected' : '' }}>
                                {{ $scope }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="panel mt-2">
                <div data-role="panel" data-title-caption="{{ trans("cruds.domain.measure_date") }} {{ date('d/m/Y')}}" data-collapsible="false" data-title-icon="<span class='mif-stacked-bar-chart'></span>">
                    <div class="p-8">
                        <canvas id="actionsChart" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>

            <div class="panel mt-2">
                <div class="row">
                    <div class="cell-md-12">
                        <table
                            id="controls"
                            class="table striped row-hover cell-border"
                            >
                            <thead>
                                <tr>
                                    <th class="sortable-column sort-asc">{{ trans('cruds.action.fields.reference') }}</th>
                                    <th class="sortable-column sort-asc">{{ trans('cruds.action.fields.type') }}</th>
                                    <th class="sortable-column sort-asc">{{ trans('cruds.action.fields.status') }}</th>
                                    <th class="sortable-column sort-asc">{{ trans('cruds.action.fields.name') }}</th>
                                    <th class="sortable-column sort-asc">{{ trans('cruds.action.fields.due_date') }}</th>
                                    <th class="sortable-column sort-asc">{{ trans('cruds.action.fields.progress') }}</th>
                        			<th class="sortable-column sort-asc"></th>
                                </tr>
                            </thead>
                            <tbody>
                        @foreach($actions as $action)
                        <tr>
                            <td>
                            <b id="{{ $action->reference }}"><a href="/action/show/{{ $action->id }}">{{ $action->reference==null ? ("ACT-".$action->id) : $action->reference }}<a>
                            </td>
                            <td>
                                <p id="{{ $action->type }}">
                                    @if ($action->type==1)
                                    <p class="fg-red text-bold">
                                    {{ trans('cruds.action.types.major') }}
                                    </p>
                                    @elseif ($action->type==2)
                                    <p class="fg-orange text-bold">
                                    {{ trans('cruds.action.types.minor') }}
                                    </p>
                                    @elseif ($action->type==3)
                                    <p class="fg-yellow text-bold">
                                    {{ trans('cruds.action.types.observation') }}
                                    </p>
                                    @elseif ($action->type==4)
                                    <p class="fg-green text-bold">
                                    {{ trans('cruds.action.types.opportunity') }}
                                    </p>
                                    @endif
                                </p>
                            </td>
                            <td id="{{ $action->status }}">
                                @if ($action->status==0)
                                {{ trans('cruds.action.fields.status_open') }}
                                @elseif ($action->status==1)
                                {{ trans('cruds.action.fields.status_closed') }}
                                @elseif ($action->status==2)
                                {{ trans('cruds.action.fields.status_rejected') }}
                                @else
                                {{ $action->status }}
                                @endif
                            </td>
                            <td>{{ $action->name }}</td>
                            <td>
                            @if ($action->due_date!==null)
                                <b>
                                @if (today()->lte($action->due_date))
                                    <font color="green">{{ $action->due_date }}</font>
                                @else
                                    <font color="red">{{ $action->due_date }}</font>
                                @endif
                                </b>
                            @endif
                            </td>
                            <td width="40">
                                <div data-role="donut" data-value="{{ $action->progress }}"
                                {{ $action->progress }}
                                @if ($action->progress<25)
                                    class="donut-red"
                                @elseif ($action->progress<50)
                                    class="donut-orange"
                                @else
                                    class="donut-green"
                                @endif
                                    >
                                </div>
                            </td>
                            <td style="padding: 0; text-align: center; vertical-align: middle;" width=400 height=110>
                                <canvas id="progressChart{{ $action->id }}" width="400" height="100"></canvas>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const chartData = @json($data);
    const labels = ['Major', 'Minor', 'Observation', 'Opportunity'];
    const colors = ['#ce352c', '#fa6800', '#D3D936', '#60a917'];

    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Open',
                data: chartData.map(d => d.open),
                backgroundColor: chartData.map(d => colors[d.type - 1]),
                stack: 'Stack 0'
            },
            {
                label: 'Closed',
                data: chartData.map(d => d.closed),
                backgroundColor: '#bdbdbd',
                stack: 'Stack 0'
            }
        ]
    };

    const ctx = document.getElementById('actionsChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: false },
                datalabels: {
                    color: 'white',
                    anchor: 'center',
                    align: 'center',
                    font: {
                        weight: 'bold'
                    },
                    formatter: (value) => value > 0 ? value : ''
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });

    window.addEventListener('load', function() {
        ['scope', 'start'].forEach(function(id) {
            var select = document.getElementById(id);
            if (select) {
                select.addEventListener('change', function() {
                    window.location = '/radar/actions?' + id + '=' + this.value;
                }, false);
            }
        });
    });


    @foreach($actions as $action)
    new Chart(document.getElementById("progressChart{{$action->id}}"), {
        type: 'line',
        data: {
            datasets: [{
                label: 'Progression',
                data: [
                    @foreach($action->progress_history as $history)
                    { x: "{{ $history['date'] }}", y: {{ $history['progress'] }} },
                    @endforeach
                ],
                borderColor: "#2196F3",
                backgroundColor: "transparent",
                borderWidth: 2,
                pointRadius: 3
            }]
        },
        options: {
            responsive: false,
            scales: {
                x: {
                    type: 'time',
                    display: false // ⛔️ masque les dates
                },

                y: {
                    beginAtZero: true,
                    max: 100,
                    display: false
                }
            },
            plugins: {
                legend: { display: false },
               datalabels: {
                    display: false // 👈 Désactive les labels à côté des points
                },
            }
        }
    });
    @endforeach

});
</script>

@endsection
