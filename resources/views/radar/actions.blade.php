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
                <div data-role="panel" data-title-caption="Etat des actions au {{ date('d/m/Y')}}" data-collapsible="false" data-title-icon="<span class='mif-stacked-bar-chart'></span>">
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
                            data-role="table"
                            data-rows="100"
                            data-show-activity="true"
                            data-rownum="false"
                            data-check="false"
                            data-check-style="1"
                            data-show-search="false"
                            data-show-pagination="false"
                            data-show-rows-steps="false"
                            >
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Scope</th>
                                    <th>Name</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                        @foreach($actions as $action)
                        <tr>
                            <td>{{ $action->reference }}</td>
                            <td>{{ $action->scope }}</td>
                            <td>{{ $action->name }}</td>
                            <td>
                                <div data-role="donut" data-value="{{ $action->progress }}" class="donut-red"
                                    data-stroke="#f5f5f5" data-fill="#9C27B0" data-color="#FFFFFF">
                                </div>
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

});
</script>

@endsection
