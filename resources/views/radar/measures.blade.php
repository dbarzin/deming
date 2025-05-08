@extends("layout")

@section("content")
<form action="/radar/domains">
    <div data-role="panel" data-title-caption="Tableau de bord" data-collapsible="true" data-title-icon="<span class='mif-timeline'></span>">

    <div class="row">
        <div class="cell-2">
            <strong>{{ trans("cruds.measure.fields.clause") }}</strong>
            <select name="measures" data-role="select" id="measures">
                <option></option>
                @foreach ($measures as $measure)
                <option id='{{ $measure->id }}'
                    @if (request()->get('id')==(string)$measure->id)
                        selected
                    @endif >
                    {{ $measure->clause }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="panel mt-2">
        <div data-role="panel" data-title-caption="Suivi temporel de la mesure" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
            <div class="p-7" style="height: 300px;">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>
    </div>

    <div>
        &nbsp;
    </div>

    <div>
        @if ($controls !== null)
        <div style="overflow-x: auto;">
            <table class="table table-border cell-border striped" style="width: max-content;">
                <tbody>
                    <tr>
                        <td class="fw-bold">{{ trans("cruds.control.fields.realisation_date") }}</td>
                        @foreach($controls as $control)
                        <td><a href="/bob/show/{{ $control->id }}">{{ $control->realisation_date }}</a></td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="fw-bold">{{ trans("cruds.control.fields.score") }}</td>
                        @foreach($controls as $control)
                        <td class="text-center"
                            {!! $control->score == 1 ? 'style="background-color: #ce352c;"' : '' !!}
                            {!! $control->score == 2 ? 'style="background-color: #fa6800;"' : '' !!}
                            {!! $control->score == 3 ? 'style="background-color: #60a917;"' : '' !!}>
                        {{ $control->note }}
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
    </div>
</form>

<script>
window.addEventListener('load', function() {
    var select = document.getElementById('measures');
    select.addEventListener('change', function() {
        const selectedOption = select.options[select.selectedIndex];
        window.location = '/radar/bob?id=' + selectedOption.id;
    }, false);
});

document.addEventListener("DOMContentLoaded", function () {
@if ($controls != null)
    const labels = @json($controls->pluck('realisation_date'));
    const data = @json($controls->pluck('note'));

    const ctx = document.getElementById('scoreChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                borderColor: 'rgba(0,123,255,1)',
                backgroundColor: 'rgba(0,123,255,0.1)',
                fill: true,
                tension: 0.2, // Remplace lineTension
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgba(0,123,255,1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day',
                        tooltipFormat: 'YYYY-MM-DD'
                    },
                    ticks: {
                        autoSkip: true
                    }
                },
                y: {
                    ticks: {
                        beginAtZero: true
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    });
@endif
});
</script>

@endsection
