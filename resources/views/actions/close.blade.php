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
    <div class="card">
    <div class="card-header"><i class="bi bi-list-check"></i> {{ trans('cruds.action.close') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form method="POST" action="/action/close">
		@csrf
		<input type="hidden" name="id" value="{{ $action->id }}"/>

		<div class="container-fluid px-0">
        	<div class="row">
        		<div class="col-md-2 col-lg-1">
                    <strong>{{ trans("cruds.action.fields.reference") }}</strong>
    	    	</div>
                <div class="col-md-4 col-lg-2">
                    {{ $action->reference }}
                </div>
                <div class="col-md-2 col-lg-1 text-md-end">
                    <strong>{{ trans("cruds.action.fields.type") }}</strong>
    	    	</div>
                <div class="col-md-2 col-lg-1">
                    @if ($action->type==1)
                    <p class="text-danger fw-bold">
                    {{ trans('cruds.action.types.major') }}
                    </p>
                    @elseif ($action->type==2)
                    <p class="text-orange fw-bold">
                    {{ trans('cruds.action.types.minor') }}
                    </p>
                    @elseif ($action->type==3)
                    <p class="text-warning fw-bold">
                    {{ trans('cruds.action.types.observation') }}
                    </p>
                    @elseif ($action->type==4)
                    <p class="text-success fw-bold">
                    {{ trans('cruds.action.types.opportunity') }}
                    </p>
                    @endif
                </div>
                <div class="col-md-2 col-lg-1 text-md-end">
                    <strong>{{ trans('cruds.action.fields.due_date') }}</strong>
    	    	</div>
                <div class="col-md-4 col-lg-2">
                    {{$action->due_date}}
    			</div>
            </div>

        	<div class="row">
        		<div class="col-md-2 col-lg-1">
                    <strong>{{ trans("cruds.action.fields.clauses") }}</strong>
    	    	</div>
        		<div class="col-md-8 col-lg-4">
                    @foreach($action->controls as $control)
                        <a href="/alice/show/{{ $control->id }}">{{ $control->clause }}</a>
                        @if(!$loop->last)
                        ,
                        @endif
                    @endforeach
                </div>
            </div>

	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
		    		<strong>{{ trans('cruds.action.fields.name') }}</strong>
		    	</div>
                <div class="col-md-10 col-lg-5">
                    @if ($action->control_id!==null)
                    <a href="/bob/show/{{ $action->control_id }}">
					    {{ $action->name }}
                    </a>
                    @else
					    {{ $action->name }}
                    @endif
				</div>
                @if ($action->scope!==null)
                <div class="col-md-4 col-lg-2">
		    		<strong>{{ trans("cruds.measure.fields.scope") }}</strong>
		    		&nbsp;
	    			{{ $action->scope }}
	    		</div>
                @endif
			</div>

	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
                    <strong>{{ trans('cruds.action.fields.cause') }}</strong>
		    	</div>
				<div class="col-md-10 col-lg-5">
                    <pre>{{ $action->cause }}</pre>
				</div>
			</div>

    	<div class="row">
		</div>

    	<div class="row">
    		<div class="col-md-2 col-lg-1">
                <strong>{{ trans('cruds.action.fields.remediation') }}</strong>
	    	</div>
			<div class="col-md-12 col-lg-6">
                {!! \Parsedown::instance()->text($action->remediation) !!}
			</div>
		</div>

    	<div class="row">
            <div class="col-md-2 col-lg-1">
                <strong>{{ trans('cruds.action.fields.status') }}</strong>
            </div>
            <div class="col-md-4 col-lg-2">
                <select class="form-select select2" name="status" id="status">
                    <option value="0" {{ $action->status==0 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_open') }}</option>
                    <option value="1" {{ $action->status==1 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_closed') }}</option>
                    <option value="2" {{ $action->status==2 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_rejected') }}</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-3 text-md-end">
                <strong>{{ trans('cruds.action.fields.close_date') }}</strong>
	    	</div>
            <div class="col-md-2 col-lg-2 col-xl-1">
                <input class="form-control" type="date"
                    name="close_date"
                    value="{{$action->close_date}}"
                    />
            </div>
        </div>
    	<div class="row">
    		<div class="col-md-2 col-lg-1">
                <strong>{{ trans('cruds.action.fields.justification') }}</strong>
	    	</div>
			<div class="col-md-12 col-lg-6">
                <textarea name="justification" class="easymde form-control" id="justification">{{ old('justification', $action->justification) }}</textarea>
			</div>
		</div>

    	<div class="row">
			<div class="col-md-2 col-lg-1">
                <strong>{{ trans('cruds.action.fields.owners') }}</strong>
	    	</div>
            <div class="col-md-12 col-lg-6">
                @foreach($action->owners as $owner)
					{{ $owner->name }}
                    @if ($action->owners->last()!=$owner)
					,
					@endif
				@endforeach
			</div>
	    </div>

        <div class="row">
    		<div class="col-md-2 col-lg-1">

            </div>
        </div>
		<div class="container-fluid px-0">
	    	<div class="mt-2">
                @if ($action->status==0)
                    <button type="submit" class="btn btn-success">
                        <span class="bi bi-check-lg"></span> {{ trans("common.save") }}
                    </button>
                @endif
	            &nbsp;
                <a class="btn btn-light border" href="/action/show/{{$action->id}}">
	    			<span class="bi bi-x-circle"></span> {{ trans("common.cancel") }}
                </a>
    		</div>
		</div>
	</div>
	</form>
</div>
</div>
</div>
@endsection
