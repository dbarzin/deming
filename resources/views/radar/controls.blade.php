@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.radar') }}" data-collapsible="true" data-title-icon="<span class='mif-pie-chart'></span>">

<div class="grid">
    <div class="row">
        <div class="cell-12">

    <form action="/radar/alice">
        <div class="row">
            <div class="cell-10">
            </div>
            <div class="cell-2">
                {{ trans("cruds.control.fields.scope") }}
                <select name="scope" data-role="select" id="scope">
                    @foreach ($scopes as $scope)
                    <option
                        @if (Session::get("scope")==$scope)
                            selected
                        @endif >
                        {{ $scope }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @foreach($domains as $domain)

    <div class="row">
        <div class="cell-10">
            <b>{{ $domain->title }} - {{ $domain->description }}</b>
        </div>
    </div>
    <div class="row">
        <div class="cell-4">
            <canvas id="canvas-radar-{{ $domain->id }}" width="100%"></canvas>
        </div>
        <div class="cell-8">
            <table class="table table-bordered">
                  <thead>
                  <tr>
                    <th>{{ trans("cruds.control.fields.note") }}</th>
                    <th><center>#</center></th>
                    <th>{{ trans("cruds.control.fields.name") }}</th>
                    <th>{{ trans("cruds.control.fields.scope") }}</th>
                    <th>{{ trans("cruds.control.fields.realisation_date") }}</th>
                    <th>{{ trans("cruds.control.fields.next") }}</th>
                  </tr>
                  </thead>
                  <tbody>
            @foreach($controls as $control)
                @if ($control->domain_id == $domain->id)
                    <tr>
                        <td><center>
                    @if ($control->score==1)
                        &#128545;
                    @elseif ($control->score==2)
                        &#128528;
                    @elseif ($control->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(80deg)">&#128512;</span>
                    @else
                        &#9675;
                    @endif
                        </center></td>

                    <td><a href="/alice/show/{{ $control->measure_id }}">{{ $control->clause }}</a></td>
                    <td>{{ $control->name }}</td>
                    <td>{{ $control->scope }}</td>
                    <td><a href="/bob/show/{{ $control->control_id }}">{{ $control->realisation_date }}</a></td>
                    <td><a href="/bob/show/{{ $control->next_id }}">{{ $control->next_date }}</a></td>
                    </tr>
                @endif
            @endforeach
        </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

</div>
</div>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

const options = {
    responsive: true,
    plugins: {
        legend: {
            display: false,
        },
        title: {
            display: false
        }
    }
};

@foreach($domains as $domain)

const ctx_{{ $domain->id }} = document.getElementById('canvas-radar-{{ $domain->id }}').getContext('2d');

const marksData_{{ $domain->id }} = {
    labels: [
        @foreach ($controls as $m)
            @if ($m->domain_id == $domain->id)
                '{{ $m->clause }}'{{ $loop->last ? '' : ',' }}
            @endif
        @endforeach
    ],
    datasets: [
        {
            backgroundColor: 'rgba(0,123,255,0.9)', // blue
            borderColor: 'rgba(0,123,255,1)',
            pointBackgroundColor: 'rgba(0,123,255,1)',
            data: [
                @foreach ($controls as $m)
                    @if ($m->domain_id == $domain->id)
                        @if ($m->score == 1)
                            0.5
                        @elseif ($m->score == 2)
                            1.5
                        @elseif ($m->score == 3)
                            2.5
                        @else
                            0
                        @endif
                        {{ $loop->last ? '' : ',' }}
                    @endif
                @endforeach
            ]
        },
        {
            backgroundColor: 'rgba(255,0,0,0.3)', // red
            borderColor: 'rgba(255,0,0,1)',
            pointBackgroundColor: 'rgba(255,0,0,1)',
            data: [
                @foreach ($controls as $m)
                    @if ($m->domain_id == $domain->id)
                        1
                        {{ $loop->last ? '' : ',' }}
                    @endif
                @endforeach
            ]
        },
        {
            backgroundColor: 'rgba(255,165,0,0.3)', // orange
            borderColor: 'rgba(255,165,0,1)',
            pointBackgroundColor: 'rgba(255,165,0,1)',
            data: [
                @foreach ($controls as $m)
                    @if ($m->domain_id == $domain->id)
                        2
                        {{ $loop->last ? '' : ',' }}
                    @endif
                @endforeach
            ]
        },
        {
            backgroundColor: 'rgba(0,128,0,0.3)', // green
//            borderColor: 'rgba(128,128,128,1)',
//            pointBackgroundColor: 'rgba(0,128,0,1)',
            data: [
                @foreach ($controls as $m)
                    @if ($m->domain_id == $domain->id)
                        3
                        {{ $loop->last ? '' : ',' }}
                    @endif
                @endforeach
            ]
        },
        {
            backgroundColor: 'rgba(0,0,0,1)', // black
            data: [0, 0, 0, 0]
        }
    ]
};

const radarChart_{{ $domain->id }} = new Chart(ctx_{{ $domain->id }}, {
    type: 'radar',
    data: marksData_{{ $domain->id }},
    options: options
});

@endforeach

    window.addEventListener('load', function() {
        var select = document.getElementById('scope');
        select.addEventListener('change', function() {
            window.location = '/radar/alice?scope=' + this.value;
        }, false);
    });
});
</script>

@endsection
