@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.radar') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

<div class="grid">    
    <div class="row">
        <div class="cell-12">

    @foreach($domains as $domain)
    <div class="row">
        <div class="cell-10">
            <b>{{ $domain->title }} - {{ $domain->description }}</b>
        </div>

        @if ($loop->first)
               
        <div class="cell-2" valign="right">
            <form action="/measurement/radar">
            <input type="text" 
                    data-role="calendarpicker" 
                    name="cur_date" 
                    value="{{$cur_date}}"
                    data-input-format="%Y-%m-%d"                    
                    onchange="this.form.submit()">            
            </form>
        </div>

        @endif

    </div>
    <div class="row">
        <div class="cell-4">
            <br>
                <canvas id="canvas-radar-{{ $domain->id }}" width="100" height="100"></canvas>
        </div>
        <div class="cell-8">
            <table class="table table-bordered">
                  <thead>
                  <tr>
                    <th>{{ trans("cruds.control.fields.note") }}</th>
                    <th><center>#</center></th>
                    <th>{{ trans("cruds.control.fields.name") }}</th>
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

                    <td><a href="/measures/{{ $control->measure_id }}">{{ $control->clause }}</a></td>
                    <td>{{ $control->name }}</td>
                    <td><a href="/controls/{{ $control->control_id }}">{{ $control->realisation_date }}</a></td>
                    <td><a href="/controls/{{ $control->next_id }}">{{ $control->next_date }}</a></td>
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

<script src="/vendors/chartjs/Chart.bundle.min.js"></script>

<script src="/js/utils.js"></script>

    <script>

    var color = Chart.helpers.color;

    var options = {
        responsive: true,
        legend: {
            display: false,
        },
        title: {
            display: false
        }
    };

@foreach($domains as $domain)


    var ctx_{{ $domain->id }} = document.getElementById('canvas-radar-{{ $domain->id }}').getContext('2d');

    var marksData_{{ $domain->id }} = {
      labels: [
            @foreach ($controls as $m) 
                @if ($m->domain_id==$domain->id)
                    '{{ $m->clause }}'
                    {{ $loop->last ? '' : ',' }}
                @endif
            @endforeach 
            ],
      datasets: [
        {
        // blue
        backgroundColor: color(window.chartColors.blue).alpha(0.9).rgbString(),
        borderColor: window.chartColors.blue,
        pointBackgroundColor: window.chartColors.blue,
        data: [
        @foreach ($controls as $m) 
            @if ($m->domain_id==$domain->id) 
                @if ($m->score==1)
                    .5
                @elseif ($m->score==2)
                    1.5
                @elseif ($m->score==3)
                    2.5
                @else
                    0
                @endif
            {{ $loop->last ? '' : ',' }}  
            @endif
        @endforeach 
        ]
      },{        
       // red
        backgroundColor: color(window.chartColors.red).alpha(0.3).rgbString(),
        borderColor: window.chartColors.red,
        pointBackgroundColor: window.chartColors.red,        
        data: [
        @foreach ($controls as $m) 
            @if ($m->domain_id==$domain->id) 
                1
            {{ $loop->last ? '' : ',' }}  
            @endif
        @endforeach 
        ]
      },{
        // orange
        backgroundColor: color(window.chartColors.orange).alpha(0.3).rgbString(),
        borderColor: window.chartColors.orange,
        pointBackgroundColor: window.chartColors.orange,
        data: [
        @foreach ($controls as $m) 
            @if ($m->domain_id==$domain->id) 
                2
            {{ $loop->last ? '' : ',' }}  
            @endif
        @endforeach 
        ]
      },{
        // green
        backgroundColor: color(window.chartColors.green).alpha(0.3).rgbString(),
        borderColor: window.chartColors.green,
        pointBackgroundColor: window.chartColors.green,
        data: [
        @foreach ($controls as $m) 
            @if ($m->domain_id==$domain->id) 
                3
            {{ $loop->last ? '' : ',' }}  
            @endif
        @endforeach 
        ]
      },
       {
        // label: "Zero",
        backgroundColor: "rgba(0,0,0,1)",
        data: [0,0,0,0]
      } 
      ]
    };
         
    var radarChart_{{ $domain->id }} = new Chart(ctx_{{ $domain->id }}, {
      type: 'radar',
      data: marksData_{{ $domain->id }},
      options: options
    });
@endforeach

</script>
@endsection

