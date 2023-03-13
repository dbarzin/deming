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
            <b>{{ $attribute->name }}</b>
        </div>


    </div>
    <div class="row">
        <div class="cell-8">
            <br>
                <canvas id="canvas-radar-{{ $attribute->id }}" width="100" height="100"></canvas>
        </div>
        <div class="cell-4">
            <table class="table table-bordered">
                  <thead>
                  <tr>
                    <th width="80%">{{ trans("cruds.control.fields.attributes") }}</th>
                    <th width="20%">{{ trans("cruds.control.fields.note") }}</th>
                  </tr>
                  </thead>
                  <tbody>
                @foreach(explode(" ",$attribute->values) as $value)
                    @if(strlen($value)>0)
                        <?php $score1=0; $score2=0; $score3=0; ?>
                        @foreach($controls as $control)
                            @if (str_contains($control->attributes, $value))
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
                        <td>{{ $value }}</td>
                        <td>
                            <?php echo $score1; ?> -
                            <?php echo $score2; ?> -
                            <?php echo $score3; ?> 
                        </td>
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

@endsection
