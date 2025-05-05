@extends("layout")

@section("content")
    <div data-role="panel" data-title-caption='{{ trans("cruds.control.list")}}' data-collapsible="true" data-title-icon="<span class='mif-paste'></span>">
    <div class="grid mb-2">
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
                <select id='clause' name="clause" data-role="select">
                    <option value="none">-- {{ trans("cruds.control.fields.choose_clause")}} --</option>
                    @foreach ($clauses as $clause)
                        <option
                            @if (Session::get("clause")==trim($clause))
                                selected
                            @endif >
                            {{ $clause }}
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
            <div class="cell-3 mt-2">
                <input type="radio" data-role="radio" data-style="2" name="status"
                value="0" id="status0" {{ (Session::get("status")=="0") ? 'checked' : '' }}>
                <span style="position: relative; top: -3px;">
                    {{ trans("cruds.control.fields.status_all") }}
                </span>
                <input type="radio" data-role="radio" data-style="2" name="status"
                value="1" id="status1" {{ (Session::get("status")=="1") ? 'checked' : '' }}>
                <span style="position: relative; top: -3px;">
                    {{ trans("cruds.control.fields.status_done") }}
                </span>
                <input type="radio" data-role="radio" data-style="2" name="status"
                value="2" id="status2" {{ (Session::get("status")=="2") ? 'checked' : '' }}>
                <span style="position: relative; top: -3px;">
                    {{ trans("cruds.control.fields.status_todo") }}
                </span>
            </div>
			<div class="cell-1" align="right">
			@if ((Auth::User()->role==1)||(Auth::User()->role==2))
				<button class="button primary" onclick="location.href = '/bob/create';">
		            <span class="mif-plus"></span>
		            &nbsp;
					{{ trans('common.new') }}
               </button>
            @endif
			</div>
        </div>
    </div>

    <table
       class="table data-table striped row-hover cell-border"
       data-role="table"
       data-rows="100"
       data-show-activity="true"
       data-rownum="false"
       data-check="false"
       data-check-style="1"
       >
        <thead>
            <tr>
                <th class="sortable-column" width="5%">{{ trans("cruds.control.fields.clauses") }}</th>
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
                @foreach($control->measures as $measure)
                <a id="{{ $measure['clause'] }}" href="/alice/show/{{ $measure['id'] }}">
                    {{ $measure['clause'] }}
                </a>
                @if (!$loop->last)
                ,
                @endif
                @endforeach
            </td>
            <td>
                    {{ $control->name }}
            </td>
            <td>
                    {{ $control->scope }}
            </td>
            <td>
                <center id="{{ $control->score }}">
                    @if ($control->action_id!=null)
                        <a href="/action/show/{{ $control->action_id }}">
                    @endif
                    @if ($control->score==1)
                        &#128545;
                    @elseif ($control->score==2)
                        &#128528;
                    @elseif ($control->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                    @else
                        &#9675; <!-- &#9899; -->
                    @endif
                    @if ($control->action_id!=null)
                    </a>
                    @endif
                </center>
            </td>
            <td>
                <!-- format in red when month passed -->
                @if (($control->status === 0)||($control->status === 1))
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
                    @if ( ($control->status===1 )&& ((Auth::User()->role===1)||(Auth::User()->role===2)))
                        &nbsp;
                        <a href="/bob/make/{{ $control->id }}">&#8987;</a>
                    @endif
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Récupère le paramètre search
            let params = new URLSearchParams(window.location.search);
            const searchValue =  params.get('search');
            if (searchValue) {
                // get serach filter
                let searchInput = document.querySelector('.table-search-block input');
                searchInput.value = searchValue;
                // Trouve la table et applique la recherche
                let tableElement = document.querySelector('.data-table');
                let table = Metro.getPlugin(tableElement, "table");
                if (table)
                    table.search(searchValue);

            }
            // Auto submits
            var select = document.getElementById('domain');
            select.addEventListener('change', function(){
                let url = new URL(window.location.href);
                url.pathname = '/bob/index';
                url.searchParams.set('domain', this.value);
                let searchInput = document.querySelector('.table-search-block input');
                url.searchParams.set('search', searchInput.value);
                window.location = url.toString();
            }, false);

            var select = document.getElementById('scope');
            select.addEventListener('change', function(){
                let url = new URL(window.location.href);
                url.pathname = '/bob/index';
                url.searchParams.set('scope', this.value);
                let searchInput = document.querySelector('.table-search-block input');
                url.searchParams.set('search', searchInput.value);
                window.location = url.toString();
            }, false);

            var select = document.getElementById('clause');
            select.addEventListener('change', function(){
                let url = new URL(window.location.href);
                url.pathname = '/bob/index';
                url.searchParams.set('clause', this.value);
                let searchInput = document.querySelector('.table-search-block input');
                url.searchParams.set('search', searchInput.value);
                window.location = url.toString();
            }, false);

            select = document.getElementById('cur_period');
            select.addEventListener('change', function(){
                let url = new URL(window.location.href);
                url.pathname = '/bob/index';
                url.searchParams.set('period', this.value);
                let searchInput = document.querySelector('.table-search-block input');
                url.searchParams.set('search', searchInput.value);
                window.location = url.toString();
            }, false);

            select = document.getElementById('status0');
            select.addEventListener('change', function() {
                let url = new URL(window.location.href);
                url.pathname = '/bob/index';
                url.searchParams.set('status', 0);
                let searchInput = document.querySelector('.table-search-block input');
                url.searchParams.set('search', searchInput.value);
                window.location = url.toString();
            }, false);

            select = document.getElementById('status1');
            select.addEventListener('change', function(){
                let url = new URL(window.location.href);
                url.pathname = '/bob/index';
                url.searchParams.set('status', 1);
                let searchInput = document.querySelector('.table-search-block input');
                url.searchParams.set('search', searchInput.value);
                window.location = url.toString();
            }, false);

            select = document.getElementById('status2');
            select.addEventListener('change', function(){
                let url = new URL(window.location.href);
                url.pathname = '/bob/index';
                url.searchParams.set('status', 2);
                let searchInput = document.querySelector('.table-search-block input');
                url.searchParams.set('search', searchInput.value);
                window.location = url.toString();
            }, false);
        }, false);
    </script>
@endsection
