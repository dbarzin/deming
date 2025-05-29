@extends("layout")

@section("content")
<form action="/radar/domains">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.radar') }}" data-collapsible="false" data-title-icon="<span class='mif-stacked-bar-chart'></span>">

    <div class="row">
        <div class="cell-md-9">
            <div class="row">
                <div class="cell-2">
                    <strong>{{ trans("cruds.domain.fields.framework") }}</strong>
                    <select name="framework" data-role="select" id="framework">
                        <option value='none'></option>
                        @foreach ($frameworks as $framework)
                        <option
                            @if (Session::get("framework")==$framework->title)
                                selected
                            @endif >
                            {{ $framework->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="cell-2">
                    <strong>{{ trans("cruds.control.fields.scope") }}</strong>
                    <select name="scope" data-role="select" id="scope">
                        <option value='none'></option>
                        @foreach ($scopes as $key => $value)
                        <option
                            @if (Session::get("scope")==$value->scope)
                                selected
                            @endif >
                            {{ $value->scope }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="cell-6">
                </div>
                <div class="cell-2">
                    <strong>{{ trans("cruds.control.groupBy") }}</strong>
                    <select name="group" data-role="select" id="group">
                        <option value="0" {{ Session::get("group")==="0" ? "selected" : "" }}>{{ trans("cruds.welcome.measures") }}</option>
                        <option value="1" {{ Session::get("group")==="1" ? "selected" : "" }}>{{ trans("cruds.welcome.controls") }}</option>
                    </select>
                </div>
            </div>

            <div class="panel mt-2">
                <div data-role="panel" data-title-caption="Etat des contrôles au {{ date('d/m/Y')}}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
                    <div class="p-8">
                        <canvas id="canvas-status" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-2">
        <table class="table table-border cell-border">
            <thead>
                <tr>
                    <th>{{ trans("cruds.control.fields.domain") }}</th>
                    <th>{{ trans("cruds.control.fields.clause") }}</th>
                    <th>{{ trans("cruds.control.fields.name") }}</th>
                    <th>{{ trans("cruds.control.fields.scope") }}</th>
                    <th>{{ trans("cruds.control.fields.score") }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($active_controls as $control)
                <tr>
                    <td>{{ $control->title }}</td>
                    <td>
                        <a href="/alice/show/{{ $control->measure_id }}">
                            {{ $control->clause }}
                        </a>
                    </td>
                    <td>
                        <a href="/bob/show/{{ $control->control_id }}">{{ $control->name }}</a>
                    </td>
                    <td>{{ $control->scope }}</td>
                    <td>
                        @if ($control->score==1)
                            &#128545;
                        @elseif ($control->score==2)
                            &#128528;
                        @elseif ($control->score==3)
                            <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                        @else
                            &#9675; <!-- &#9899; -->
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var barChartData = {
        labels : [
            @foreach ($domains as $domain)
                '{{ $domain->title }}'{{ $loop->last ? '' : ',' }}
            @endforeach
        ],
        datasets: [{
            backgroundColor: '#60a917',
            borderColor: '#60a917',
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain)
                    <?php $count = 0; ?>
                    @foreach($active_controls as $c)
                      <?php if ($c->score == 3 && $c->title == $domain->title) {
                          $count++;
                      } ?>
                    @endforeach
                    {{ $count }}{{ $loop->last ? '' : ',' }}
                @endforeach
            ]
        }, {
            backgroundColor: '#fa6800',
            borderColor: '#fa6800',
            borderWidth: 1,
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain)
                    <?php $count = 0; ?>
                    @foreach($active_controls as $c)
                      <?php if ($c->score == 2 && $c->title == $domain->title) {
                          $count++;
                      } ?>
                    @endforeach
                    {{ $count }}{{ $loop->last ? '' : ',' }}
                @endforeach
            ]
        }, {
            backgroundColor: '#ce352c',
            borderColor: '#ce352c',
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain)
                    <?php $count = 0; ?>
                    @foreach($active_controls as $c)
                      <?php if ($c->score == 1 && $c->title == $domain->title) {
                          $count++;
                      } ?>
                    @endforeach
                    {{ $count }}{{ $loop->last ? '' : ',' }}
                @endforeach
            ]
        }, {
            label: 'Gris',
            backgroundColor: 'rgba(128,128,128,1)', // 100% opaque
            borderColor: '#000000',
            borderWidth: 1,
            stack: 'Stack 0',
            data: [
                @foreach ($domains as $domain)
                    <?php $count = 0; ?>
                    @foreach($controls_never_made as $c)
                      <?php if ($c->domain_id == $domain->id) {
                          $count++;
                      } ?>
                    @endforeach
                    {{ $count }}{{ $loop->last ? '' : ',' }}
                @endforeach
            ]
        }]
    };

    var ctx1 = document.getElementById('canvas-status').getContext('2d');
    window.myBar = new Chart(ctx1, {
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
            scales: {
                y: {
                    ticks: {
                        reverse: false,
                        stepSize: 10
                    }
                }
            }
        }
    });

    document.getElementById('canvas-status').onclick = function(evt) {
        const points = window.myBar.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
        if (points.length) {
            const firstPoint = points[0];
            const label = barChartData.labels[firstPoint.index];
            const value = barChartData.datasets[firstPoint.datasetIndex].data[firstPoint.index];
            window.location.href = "/bob/index?attribute=none&status=2&period=99&domain_title=" + encodeURIComponent(label);
        }
    };

    window.addEventListener('load', function() {
        ['scope', 'framework', 'group'].forEach(function(id) {
            var select = document.getElementById(id);
            if (select) {
                select.addEventListener('change', function() {
                    window.location = '/radar/domains?' + id + '=' + this.value;
                }, false);
            }
        });
    });
});
</script>

@endsection
