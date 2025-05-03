@extends("layout")

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.action.close') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	@if (count($errors))
	<div class= “form-group”>
		<div class= “alert alert-danger”>
			<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif

    <form method="POST" action="/action/close">
		@csrf
		<input type="hidden" name="id" value="{{ $action->id }}"/>

		<div class="grid">
        	<div class="row">
        		<div class="cell-1">
                    <strong>{{ trans("cruds.action.fields.reference") }}</strong>
    	    	</div>
                <div class="cell-2">
                    {{ $action->reference }}
                </div>
                <div class="cell-1" align="right">
                    <strong>{{ trans("cruds.action.fields.type") }}</strong>
    	    	</div>
                <div class="cell-1">
                    {{ $action->type }}
                </div>
                <div class="cell-1" align="right">
                    <strong>{{ trans('cruds.action.fields.due_date') }}</strong>
    	    	</div>
                <div class="cell-2">
                    {{$action->due_date}}
    			</div>
            </div>

        	<div class="row">
        		<div class="cell-1">
                    <strong>{{ trans("cruds.action.fields.clauses") }}</strong>
    	    	</div>
        		<div class="cell-4">
                    @foreach($action->measures as $measure)
                        <a href="/alice/show/{{ $measure->id }}">{{ $measure->clause }}</a>
                        @if(!$loop->last)
                        ,
                        @endif
                    @endforeach
                </div>
            </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.action.fields.name') }}</strong>
		    	</div>
                <div class="cell-5">
                    @if ($action->control_id!==null)
                    <a href="/bob/show/{{ $action->control_id }}">
					    {{ $action->name }}
                    </a>
                    @else
					    {{ $action->name }}
                    @endif
				</div>
                @if ($action->scope!==null)
                <div class="cell-2">
		    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
		    		&nbsp;
	    			{{ $action->scope }}
	    		</div>
                @endif
			</div>

	    	<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans('cruds.action.fields.cause') }}</strong>
		    	</div>
				<div class="cell-5">
                    <pre>{{ $action->cause }}</pre>
				</div>
			</div>

    	<div class="row">
		</div>

    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.remediation') }}</strong>
	    	</div>
			<div class="cell-6">
                {!! \Parsedown::instance()->text($action->remediation) !!}
			</div>
		</div>

    	<div class="row">
            <div class="cell-1">
                <strong>{{ trans('cruds.action.fields.status') }}</strong>
            </div>
            <div class="cell-3">
                <select data-role="select" name="status" id="status">
                    <option value="0" {{ $action->status==0 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_open') }}</option>
                    <option value="1" {{ $action->status==1 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_closed') }}</option>
                    <option value="2" {{ $action->status==2 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_rejected') }}</option>
                </select>
            </div>
            <div class="cell-1" align="right">
                <strong>{{ trans('cruds.action.fields.close_date') }}</strong>
	    	</div>
            <div class="cell-2">
                <input type="text" data-role="calendarpicker" name="close_date" value="{{$action->close_date}}" data-input-format="%Y-%m-%d">
            </div>
        </div>
    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.justification') }}</strong>
	    	</div>
			<div class="cell-6">
                <textarea name="justification" class="easymde" id="justification">{{ $errors->has('justification') ?  old('justification') : $action->justification }}</textarea>
			</div>
		</div>

    	<div class="row">
			<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.owners') }}</strong>
	    	</div>
			<div class="cell">
                @foreach($action->owners as $owner)
					{{ $owner->name }}
                    @if ($action->owners->last()!=$owner)
					,
					@endif
				@endforeach
			</div>
	    </div>

        <div class="row">
    		<div class="cell-1">

            </div>
        </div>
		<div class="grid">
	    	<div class="row-12">
                @if ($action->status==0)
                    <button type="submit" class="button success">
                        <span class="mif-done"></span>
    					&nbsp;
                        {{ trans("common.save") }}
                    </button>
                @endif
	            &nbsp;
                <a class="button dafault" href="/action/show/{{$action->id}}">
	    			<span class="mif-cancel"></span>
	    			&nbsp;
	    			{{ trans("common.cancel") }}
                </a>
    		</div>
		</div>
	</div>
	</form>
</div>
</div>
@endsection
