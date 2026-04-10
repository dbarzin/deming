@extends("layout")

@section("content")
<form action="/radar/domains">
    <div data-role="panel" data-title-caption={{ trans("cruds.welcome.dashboard") }} data-collapsible="false" data-title-icon="<span class='mif-gauge'></span>">

        <div class="row">
            <div class="cell-2">
                <strong>{{ trans("cruds.measure.fields.clause") }}</strong>
                <select name="clauses" data-role="select" id="clauses" data-filter="true">
                    <option></option>
                    @foreach ($clauses as $clause)
                    <option value="{{ trim($clause) }}"
                        @if (request()->get('clause') == trim($clause)) selected @endif>
                        {{ $clause }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="cell-2">
            @if (($scopes!=null) && ($scopes->count()>0))
                <strong>{{ trans("cruds.control.fields.scope") }}</strong>
                <select name="scopes" data-role="select" id="scopes" data-filter="true">
                    <option></option>
                    @foreach ($scopes as $scope)
                    <option value="{{ trim($scope) }}"
                        @if (request()->get('scope') == trim($scope)) selected @endif>
                        {{ $scope }}
                    </option>
                    @endforeach
                </select>
            @endif
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
window.addEventListener('DOMContentLoaded', function() {
    var clauseSelect = document.getElementById('clauses');
    clauseSelect.addEventListener('change', function() {
        const clauseSelectOption = clauseSelect.options[clauseSelect.selectedIndex];
        window.location = '/radar/bob?clause=' + encodeURIComponent(clauseSelectOption.value);
    }, false);

    var scopeSelect = document.getElementById('scopes');
    scopeSelect.addEventListener('change', function() {
        const clauseSelectOption = clauseSelect.options[clauseSelect.selectedIndex];
        const scopeSelectOption = scopeSelect.options[scopeSelect.selectedIndex];
        window.location = '/radar/bob?clause=' +
            encodeURIComponent(clauseSelectOption.value) +
            '&scope=' + encodeURIComponent(scopeSelectOption.value);
    }, false);


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
