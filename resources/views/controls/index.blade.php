@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption='{{ trans("cruds.control.list")}}' data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

    <div class="grid">
        <div class="row">
            <div class="cell-2">
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
            <div class="cell-2">
                <select id='scope' name="scope" data-role="select">
                    <option value="none">-- {{ trans("cruds.control.fields.choose_scope")}} --</option>
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
            <div class="cell-2">
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
                        @if (Session::get("period")==="99")
                            selected 
                        @endif
                    >-- {{ trans("cruds.control.fields.choose_period") }} --</option>
                        @for ($i = -12; $i < 12; $i++)
                            <option value="{{ $i }}"
                            @if ((Session::get("period"))==strval($i))
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
                @if (Session::get("status")=="0")
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
                @if ((Session::get("status")=="2") || (Session::get("status")==null))
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
                window.location = '/bob/index?domain=' + this.value;
            }, false);

            var select = document.getElementById('scope');
            select.addEventListener('change', function(){
                window.location = '/bob/index?scope=' + this.value;
            }, false);

            var select = document.getElementById('attribute');
            select.addEventListener('change', function(){
                window.location = '/bob/index?attribute=' + encodeURIComponent(this.value);
            }, false);

            select = document.getElementById('cur_period');
            select.addEventListener('change', function(){
                window.location = '/bob/index?period=' + this.value;
            }, false);

            select = document.getElementById('status0');
            select.addEventListener('change', function(){
                window.location = '/bob/index?status=0';
            }, false);

            select = document.getElementById('status1');
            select.addEventListener('change', function(){
                window.location = '/bob/index?status=1';
            }, false);

            select = document.getElementById('status2');
            select.addEventListener('change', function(){
                window.location = '/bob/index?status=2';
            }, false);
        }, false);

    </script>

    <table class="table striped row-hover cell-border"
       data-role="table"
       data-rows="100"
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
                <th width="40%">{{ trans("cruds.control.fields.name") }}</th>
                <th class="sortable-column" width="10%">{{ trans("cruds.control.fields.scope") }}</th>
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
                <a id="{{ $control->title }}" href="/domains/{{ $control->domain_id}} ">
                    {{ $control->title }}
                </a>
            </td>
            <td>
                <a id="{{ $control->clause }}" href="/alice/show/{{ $control->measure_id }}">
                    {{ $control->clause }}
                </a>
            </td>
            <td>
                    {{ $control->name }} 
            </td>
            <td>
                    {{ $control->scope }} 
            </td>
            <td>
                <center id="{{ $control->score }}">
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
                <a id="{{ $control->plan_date }}" href="/bob/show/{{$control->id}}">
                <b> @if (today()->lte($control->plan_date))
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
                <b id="{{ $control->realisation_date }}">
                    <a href="/bob/show/{{$control->id}}">
                        {{ $control->realisation_date }}
                    </a>
                </b>
            </td>
            <td>
                <b id="{{ $control->next_date }}">
                    @if ($control->next_id!=null)
                    <a href="/bob/show/{{$control->next_id}}">
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

