@extends("layout")

@section("content")
<form action="/radar/domains">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.radar') }}" data-collapsible="false" data-title-icon="<span class=' mif-stacked-bar-chart'></span>">

    <div class="row">
        <div class="cell-md-9">
            <div class="row">
                <div class="cell-2">
                    <input
                    id="start"
                    name="start"
                    value="{{ $start }}"
                    data-role="calendarpicker"
                    data-format="YYYY-MM-DD"
                    data-prepend="Period"/>
                </div>
                <div class="cell-3">
                    <select id='scope' name="scope" data-prepend="Scope" data-role="select">
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
                    color: 'black',
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
