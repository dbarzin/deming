@extends("layout")

@section("content")
<form action="/radar/domains">
    <div data-role="panel" data-title-caption={{ trans("cruds.welcome.dashboard") }} data-collapsible="false" data-title-icon="<span class='mif-gauge'></span>">

        <div class="row">
            <div class="cell-2">
                <strong>{{ trans("cruds.measure.fields.clause") }}</strong>
                <select name="measures" data-role="select" id="measures" data-filter="true">
                    <option></option>
                    @foreach ($clauses as $clause)
                    <option
                        @if (request()->get('id')==trim($clause))
                            selected
                        @endif >
                        {{ $clause }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @foreach($measures as $measure)
    <div class="mt-2" data-role="panel" data-title-caption="{{ $measure->name }}" data-collapsible="true" data-title-icon="<span class='mif-line-chart'></span>">

        <div class="p-7" style="height: 300px;">
            <canvas id="scoreChart-{{ $measure->id }}"></canvas>
        </div>

        <div>
            <div style="overflow-x: auto;">
                <table class="table table-border cell-border striped" style="width: max-content;">
                    <tbody>
                        <tr>
                            <td class="fw-bold">{{ trans("cruds.control.fields.realisation_date") }}</td>
                            @foreach($measure->controls as $control)
                            <td><a href="/bob/show/{{ $control->id }}">{{ $control->realisation_date }}</a></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ trans("cruds.control.fields.score") }}</td>
                            @foreach($measure->controls as $control)
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
        </div>
    </div>

    @endforeach

</form>

<script>
window.addEventListener('load', function() {
    var select = document.getElementById('measures');
    select.addEventListener('change', function() {
        const selectedOption = select.options[select.selectedIndex];
        window.location = '/radar/bob?id=' + encodeURIComponent(selectedOption.value);
    }, false);
});

document.addEventListener("DOMContentLoaded", function () {
@foreach($measures as $measure)

    const labels{{$measure->id}} = @json($measure->controls->pluck('realisation_date'));
    const data{{$measure->id}} = @json($measure->controls->pluck('note'));

    const ctx{{$measure->id}} = document.getElementById('scoreChart-{{$measure->id}}').getContext('2d');

    new Chart(ctx{{$measure->id}}, {
        type: 'line',
        data: {
            labels: labels{{$measure->id}},
            datasets: [{
                data: data{{$measure->id}},
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
                },
                datalabels: {
                    display: false
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day',
                        tooltipFormat: 'yyyy-MM-dd'
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
@endforeach
});
</script>

@endsection
