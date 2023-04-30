@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption='{{ trans("cruds.control.list")}}' data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

    <div class="grid">
        <div class="row">
            <div class="cell-3">
                <select id='domain' name="domain_id" data-role="select">
                    <option value="0">-- {{ trans("cruds.control.fields.choose_domain")}} --</option>
                    @foreach ($domains as $domain)
                        <option value="{{ $domain->id }}"
                            @if (((int)Session::get("domain"))==$domain->id)        
                                selected 
                            @endif >
                            {{ $domain->title }} - {{ $domain->description }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="cell-3">
                <select id='attribute' name="attribute" data-role="select">
                    <option value="none">-- {{ trans("cruds.control.fields.choose_attribute")}} --</option>
                    @foreach ($attributes as $attribute)
                        <option
                            @if (Session::get("attribute")==$attribute)        
                                selected 
                            @endif >
                            {{ $attribute }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="cell-3"> 
                <select id='cur_period' name="period" data-role="select">
                    <option value="99"
                        @if (Session::get("period")=="99")
                            selected 
                        @endif
                    >-- {{ trans("cruds.control.fields.choose_period") }} --</option>
                        @for ($i = -12; $i < 12; $i++)
                            <option value="{{ $i }}"
                            @if (((int)Session::get("period"))==$i)
                                selected 
                            @endif 
                            >
                            {{ now()->day(1)->addMonth($i)->format("M Y") }}
                            </option>
                        @endfor
                    </select>
                </div>
            <div class="cell-3">
                <input type="radio" data-role="radio" data-style="2" 
                name="status" value="0" id="status0"
                @if (Session::get("status")=="0" || Session::get("status")==null)
                checked
                @endif
                > 
                {{ trans("cruds.control.fields.status_all") }}
                <input type="radio" data-role="radio" data-style="2" 
                name="status" value="1" id="status1"
                @if (Session::get("status")=="1")
                checked
                @endif
                > {{ trans("cruds.control.fields.status_done") }}
                <input type="radio" data-role="radio" data-style="2" 
                name="status" value="2" id="status2"
                @if (Session::get("status")=="2")
                checked
                @endif
                > {{ trans("cruds.control.fields.status_todo") }}
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function(){
            var select = document.getElementById('domain');
            select.addEventListener('change', function(){
                window.location = '/controls?domain=' + this.value;
            }, false);

            var select = document.getElementById('attribute');
            select.addEventListener('change', function(){
                window.location = '/controls?attribute=' + encodeURIComponent(this.value);
            }, false);

            select = document.getElementById('cur_period');
            select.addEventListener('change', function(){
                window.location = '/controls?period=' + this.value;
            }, false);

            select = document.getElementById('status0');
            select.addEventListener('change', function(){
                window.location = '/controls?status=0';
            }, false);

            select = document.getElementById('status1');
            select.addEventListener('change', function(){
                window.location = '/controls?status=1';
            }, false);

            select = document.getElementById('status2');
            select.addEventListener('change', function(){
                window.location = '/controls?status=2';
            }, false);
        }, false);

    </script>

    <table class="table striped row-hover cell-border"
       data-role="table"
       data-rows="25"
       data-show-activity="true"
       data-rownum="false"
       data-check="false"
       data-check-style="1"
       onDraw="alert('change')"
       >
        <thead>
            <tr>
                <th class="sortable-column" width="5%">{{ trans("cruds.control.fields.domain") }}</th>
                <th class="sortable-column" width="5%">{{ trans("cruds.control.fields.measure") }}</th>
                <th class="sortable-column" width="50%">{{ trans("cruds.control.fields.name") }}</th>
                <th class="sortable-column" width="5%">{{ trans("cruds.control.fields.score") }}</th>
                <th class="sortable-column sort-asc"  width="5%">{{ trans("cruds.control.fields.planned") }}</th>
                <th class="sortable-column sort-asc"  width="5%">{{ trans("cruds.control.fields.realized") }}</th>
                <th class="sortable-column"  width="5%">{{ trans("cruds.control.fields.next") }}</th>
            </tr>
        </thead>
        <tbody>
    @foreach($controls as $control)
        <tr>
            <td>
                <a href="/domains/{{ $control->domain_id}} ">
                    {{ $control->title }}
                </a>
            </td>
            <td>
                <a href="/measures/{{ $control->measure_id }}">
                    {{ $control->clause }}
                </a>
            </td>
            <td>
                    {{ $control->name }} 
            </td>
            <td>
                <center>
                    @if ($control->score==1)
                    <a href="/action/{{ $control->id }}">
                        &#128545; 
                    </a>
                    @elseif ($control->score==2)
                    <a href="/action/{{ $control->id }}">
                        &#128528;
                    </a>
                    @elseif ($control->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                    @else
                        &#9675; <!-- &#9899; -->
                    @endif
                </center>
            </td>
            <td>
                <!-- format in red when month passed -->
                @if ($control->realisation_date == null)
                <a href="/control/show/{{$control->id}}">
                <b>
                    @if( strtotime($control->plan_date) >= strtotime('now') ) 
                        <font color="green">{{ $control->plan_date }}</font>
                    @else
                        <font color="red">{{ $control->plan_date }}</font>
                    @endif
                </b>
                </a>
                @else 
                    {{ $control->plan_date }}
                @endif
            </td>
            <td>
                <b>
                    <a href="/control/show/{{$control->id}}">
                        {{ $control->realisation_date }}
                    </a>
                </b>
            </td>
            <td>
                <b>
                    @if ($control->next_id!=null)
                    <a href="/controls/{{$control->next_id}}">
                        {{ $control->next_date }}
                    </a>
                    @endif
                </b>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
</div>
@endsection

