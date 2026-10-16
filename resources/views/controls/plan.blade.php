@extends("layout")

@section('title', $control->name)

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-clipboard-check"></i> {{ trans('cruds.control.plan') }}</div>
    <div class="card-body">

@include('partials.errors')

<form method="POST" action="/alice/activate/{{ $control->id }}">
@csrf
    <div class="container-fluid px-0">

    	<div class="row">
    		<div class="col-lg-1 col-md-2">
        		<strong>{{ trans("cruds.measure.fields.clauses") }}</strong>
        	</div>
    		<div class="col-lg-6 col-md-8">
    			<select class="form-select select2" name="controls[]" multiple>
    				@foreach($all_controls as $ac)
    				    <option value="{{ $ac->id }}" {{ in_array($ac->id, old("controls", $controls)) ? "selected" : "" }}>
                            {{ $ac->clause }} - {{$ac->name}}
                        </option>
    			    @endforeach
    			 </select>
            </div>
        </div>

    	<div class="row">
    		<div class="col-lg-1 col-md-2">
        		<strong>{{ trans('cruds.control.fields.name') }}</strong>
        	</div>
            <div class="col-lg-4 col-md-5">
    			<input type="text" class="form-control" name="name" value="{{ old('name', $control->name) }}" maxlength='255'>
    		</div>
            <div class="col-lg-1 col-md-1 text-md-end">
        		<strong>{{ trans('cruds.measure.fields.scope') }}</strong>
        	</div>
    		<div class="col-lg-2 col-xl-1 col-md-2">
    			<input class="form-control" type="text" name="scope" autocomplete="on" size="32"
    			value="{{ old('scope',$control->scope) }}"/>
    		</div>
        </div>

    	<div class="row">
    		<div class="col-lg-1 col-md-2">
        		<strong>{{ trans('cruds.control.fields.objective') }}</strong>
        	</div>
            <div class="col-lg-6 col-md-8">
                <textarea name="objective" class="easymde form-control" id="objetive">{{ old('objective', optional($control)->objective) }}</textarea>
    		</div>
        </div>

    		<div class="row">
        		<div class="col-lg-1 col-md-2">
    	    		<strong>{{ trans('cruds.control.fields.attributes') }}</strong>
    	    	</div>
                <div class="col-lg-6 col-md-8">
    				<select class="form-select select2" name="attributes[]" multiple>
    					@foreach($values as $value)
    				    <option {{ str_contains($control->attributes, $value) ? "selected" : ""}} >{{$value}}</option>
    				    @endforeach
    				 </select>
    			</div>
    		</div>

        	<div class="row">
        		<div class="col-lg-1 col-md-2">
    				<strong>{{ trans("cruds.control.fields.input") }}</strong>
    			</div>
                <div class="col-lg-6 col-md-8">
                    <textarea name="input" class="easymde form-control" id="input">{{ old('input', optional($control)->input) }}</textarea>
    			</div>
    		</div>

        	<div class="row">
        		<div class="col-lg-1 col-md-2">
    				<strong>{{ trans("cruds.control.fields.model") }}</strong>
    			</div>
    			<div class="col-lg-6 col-md-8">
    				<textarea class="form-control" name="model" rows="3">{{ old('model', optional($control)->model) }}</textarea>
    			</div>
    		</div>

        	<div class="row">
        		<div class="col-lg-1 col-md-2">
    				<strong>{{ trans("cruds.control.fields.indicator") }}</strong>
    			</div>
    			<div class="col-lg-6 col-md-8">
    				<textarea class="form-control" name="indicator" rows="3">{{ old('indicator', optional($control)->indicator) }}</textarea>
    			</div>
    		</div>

        	<div class="row">
        		<div class="col-lg-1 col-md-2">
    				<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
    			</div>
    			<div class="col-lg-6 col-md-8">
                    <textarea name="action_plan" class="easymde form-control" id="action_plan">{{ old('action_plan', optional($control)->action_plan) }}</textarea>
    			</div>
    		</div>

    	<div class="row">
    		<div class="col-lg-1 col-md-2">
    			<strong>{{ trans('cruds.measure.fields.plan_date') }}</strong>
        	</div>
    		<div class="col-lg-3 col-md-4">
    			<input class="form-control" type="date" name="plan_date"
                    value=""/>
    		</div>
    	</div>

    	<div class="row">
    		<div class="col-lg-1 col-md-2">
        		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
        	</div>
    		<div class="col-lg-3 col-md-4">
    			<select class="form-select select2" name="periodicity">
    			    <option value="0" {{ $control->periodicity==0 ? "selected" : ""}}>{{ trans('common.once') }}</option>
    			    <option value="-1" {{ $control->periodicity==-1 ? "selected" : ""}}>{{ trans('common.weekly') }}</option>
    			    <option value="1" {{ $control->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
    			    <option value="3" {{ $control->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
    			    <option value="6" {{ $control->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
    			    <option value="12" {{ ($control->periodicity==null) || ($control->periodicity==12) ? "selected" : ""}}>{{ trans('common.annually') }}</option>
    			 </select>
    		</div>
    	</div>

    <div class="row">
    	<div class="col-lg-1 col-md-2">
    		<strong>{{ trans('cruds.measure.fields.owners') }}</strong>
    	</div>
    	<div class="col-lg-5 col-md-7">
            <select class="form-select select2" name="owners[]" id="owners" multiple>
                @foreach($owners as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, old('owners', [])) ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
    	</div>
    </div>

    <div class="row">
    	<div class="col">
    	</div>
    </div>

    <div class="row">
        <div class="col-md-12 col-lg-6">
            @if (Auth::User()->role !== 3)
        	<button class="btn btn-success">
                <span class="bi bi-calendar3"></span> {{ trans('common.plan') }}
        	</button>
            @endif
            &nbsp;
            <a href="/alice/show/{{ $control->id }}" class="btn btn-light border">
        			<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
            </a>
        </div>
    </div>
    </form>
</div>
</div>
</div>
@endsection
