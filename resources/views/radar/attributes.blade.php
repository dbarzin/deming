@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.radar') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

<div class="grid">    
    <div class="row">
        <div class="cell-12">

    @foreach($attributes as $attribute)
    <div class="row">
        <div class="cell-10">
        </div>


    </div>
    <div class="row">
        <div class="cell-6">
            <table class="table table-bordered">
                  <thead>
                  <tr>
                    <th width="80%">{{ $attribute->name }}</th>
                    <th width="20%">{{ trans("cruds.control.fields.note") }}</th>
                  </tr>
                  </thead>
                  <tbody>
                @foreach(explode(" ",$attribute->values) as $value)
                    @if(strlen($value)>0)
                        <?php $score1=0; $score2=0; $score3=0; ?>
                        @foreach($controls as $control)
                            @if (str_contains($control->attributes.' ', $value.' '))
                                @if ($control->score==1)
                                    <?php $score1++; ?>
                                @elseif ($control->score==2)
                                    <?php $score2++; ?>
                                @elseif ($control->score==3)
                                    <?php $score3++; ?>
                                @endif
                            @endif
                        @endforeach
                    <tr>
                        <td><a href="/controls?domain=0&period=99&status=1&attribute={{ urlencode($value) }}">{{ $value }}</a></td>
                        <td>
                            <font color="30FF30"><?php echo $score3; ?></font> -
                            <font color="ff5733"><?php echo $score2; ?></font> -
                            <font color="FF1010"><?php echo $score1; ?></font>
                        </td>
                    </tr>                    
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="cell-6">
            <canvas id="canvas-radar-{{ $attribute->id }}" width="100" height="100"></canvas>
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

@foreach($attributes as $attribute)


    var ctx_{{ $attribute->id }} = document.getElementById('canvas-radar-{{ $attribute->id }}').getContext('2d');

    var marksData_{{ $attribute->id }} = {
      labels: [
            @foreach(explode(" ",$attribute->values) as $value)
                @if(strlen($value)>0)
                    "{{ $value }}"
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
            @foreach(explode(" ",$attribute->values) as $value)
                @if(strlen($value)>0)
                    <?php $score1=0; $score2=0; $score3=0; $total=0; ?>
                    @foreach($controls as $control)
                        @if (str_contains($control->attributes.' ', $value.' '))
                            @if ($control->score==1)
                                <?php $score1++; ?>
                            @elseif ($control->score==2)
                                <?php $score2++; ?>
                            @elseif ($control->score==3)
                                <?php $score3++; ?>
                            @endif
                            <?php $total++; ?>
                        @endif
                    @endforeach
                    {{ $total == 0 ? 0 : 2.5 * $score3 / $total }} 
                    {{ $loop->last ? '' : ',' }}  
                @endif
            @endforeach
        ]
      },
      {
       // red
        backgroundColor: color(window.chartColors.red).alpha(0.3).rgbString(),
        borderColor: window.chartColors.red,
        pointBackgroundColor: window.chartColors.red,        
        data: [
        @foreach (explode(" ",$attribute->values) as $value)
            @if (strlen($value)>0)
                2
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
        @foreach (explode(" ",$attribute->values) as $value)
            @if (strlen($value)>0)
                2.5
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
        @foreach (explode(" ",$attribute->values) as $value)
            @if (strlen($value)>0)
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
         
    var radarChart_{{ $attribute->id }} = new Chart(ctx_{{ $attribute->id }}, {
      type: 'radar',
      data: marksData_{{ $attribute->id }},
      options: options
    });
@endforeach

</script>
@endsection


