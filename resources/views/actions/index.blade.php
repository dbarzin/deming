@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.action.index') }}" data-collapsible="false" data-title-icon="<span class='mif-pending-actions'></span>">

<div class="grid">
    <div class="row">
        <div class="cell-2">
            <select id='type' name="type" data-role="select">
                <option value="">-- {{ trans("cruds.action.fields.choose_type")}} --</option>
                <option value="1" {{ (Session::get('type')==1) ? 'selected' : '' }}>{{ trans('cruds.action.types.major') }}</option>
                <option value="2" {{ (Session::get('type')==2) ? 'selected' : '' }}>{{ trans('cruds.action.types.minor') }}</option>
                <option value="3" {{ (Session::get('type')==3) ? 'selected' : '' }}>{{ trans('cruds.action.types.observation') }}</option>
                <option value="4" {{ (Session::get('type')==4) ? 'selected' : '' }}>{{ trans('cruds.action.types.opportunity') }}</option>
            </select>
        </div>
        <div class="cell-3 mt-2">
             <input type="radio" data-role="radio" data-append="{{ trans("cruds.action.fields.status_open") }}" value="0" id="status0" {{ (Session::get("status")=="0") ? 'checked' : '' }}/>
             <input type="radio" data-role="radio" data-append="{{ trans("cruds.action.fields.status_closed") }}" value="1" id="status1" {{ (Session::get("status")=="1") ? 'checked' : '' }}/>
             <input type="radio" data-role="radio" data-append="{{ trans("cruds.action.fields.status_all") }}" value="2" id="status2" {{ (Session::get("status")=="2") ? 'checked' : '' }}/>
        </div>
        <div class="cell-4">
        </div>
        <div class="cell-2">
            <select id='scope' name="scope" data-role="select">
                <option value="">-- {{ trans("cruds.action.fields.choose_scope")}} --</option>
                @foreach ($scopes as $scope)
                    <option {{ (Session::get('scope')==$scope) ? 'selected' : '' }}>
                        {{ $scope }}
                    </option>
                @endforeach
            </select>
        </div>
		<div class="cell-1" align="right">
		@if ((Auth::User()->role==1)||(Auth::User()->role==2))
            <a class="button primary" href='/action/create'>
	            <span class="mif-plus"></span>
	            &nbsp;
				{{ trans('common.new') }}
            </a>
        @endif
		</div>
    </div>
</div>

<script>
    window.addEventListener('load', function(){

        var select = document.getElementById('type');
        select.addEventListener('change', function(){
            window.location = '/actions?type=' + this.value;
        }, false);

        var select = document.getElementById('scope');
        select.addEventListener('change', function(){
            window.location = '/actions?scope=' + this.value;
        }, false);

        select = document.getElementById('status0');
        select.addEventListener('change', function(){
            window.location = '/actions?status=0';
        }, false);

        select = document.getElementById('status1');
        select.addEventListener('change', function(){
            window.location = '/actions?status=1';
        }, false);

        select = document.getElementById('status2');
        select.addEventListener('change', function(){
            window.location = '/actions?status=2';
        }, false);
    }, false);

</script>

    <div class="row mt-3">

        <table id="actions" class="table striped row-hover cell-border"
	       data-role="table"
	       data-rows="100"
	       data-show-activity="true"
	       data-rownum="false"
	       data-check="false"
	       data-check-style="1">
	    <thead>
	    <tr>
            <th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.reference') }}</th>
            <th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.type') }}</th>
            <th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.status') }}</th>
            <th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.progress') }}</th>
            <th class="sortable-column sort-asc" width="50%">{{ trans('cruds.action.fields.name') }}</th>
			<th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.scope') }}</th>
            <th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.due_date') }}</th>
	    </tr>
	    </thead>
	    <tbody>
	@foreach($actions as $action)
        <tr>
            <td>
                <b id="{{ $action->reference }}"><a href="/action/show/{{ $action->id }}">{{ $action->reference }}<a>
            </td>
            <td id="{{ $action->type }}">
                    @if ($action->type==1)
                    <p class="fg-red text-bold">
                    {{ trans('cruds.action.types.major') }}
                    </p>
                    @elseif ($action->type==2)
                    <p class="fg-orange text-bold">
                    {{ trans('cruds.action.types.minor') }}
                    </p>
                    @elseif ($action->type==3)
                    <p class="fg-yellow text-bold">
                    {{ trans('cruds.action.types.observation') }}
                    </p>
                    @elseif ($action->type==4)
                    <p class="fg-green text-bold">
                    {{ trans('cruds.action.types.opportunity') }}
                    </p>
                    @endif
            </td>
            <td id="{{ $action->status }}">
                @if ($action->status==0)
                {{ trans('cruds.action.fields.status_open') }}
                @elseif ($action->status==1)
                {{ trans('cruds.action.fields.status_closed') }}
                @elseif ($action->status==2)
                {{ trans('cruds.action.fields.status_rejected') }}
                @else
                {{ $action->status }}
                @endif
            </td>
            <td>
                @if ($action->progress!==null)
                {{ $action->progress }} %
                @endif
            </td>
            <td>
                <b>{{ $action->name }}</b>
                {!! \Parsedown::instance()->text($action->cause) !!}
            </td>
            <td>
                {{ $action->scope }}
            </td>
            <td>
            @if ($action->due_date!==null)
                <b>
                @if (today()->lte($action->due_date))
                    <font color="green">{{ $action->due_date }}</font>
                @else
                    <font color="red">{{ $action->due_date }}</font>
                @endif
                </b>
            @endif
            </td>
		</tr>
	@endforeach
	</tbody>
</table>
		</div>
	</div>
</div>
@endsection
