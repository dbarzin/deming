@extends("layout")

@section('title', $control->name)

@section("content")
<div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'controls.edit'])<i class="bi bi-book"></i> {{ trans('cruds.control.edit') }}</div>
    <div class="card-body">

@include('partials.errors')

<form method="POST" action="/alice/save/{{ $control->id }}">
	@csrf
	<div class="container-fluid px-0">
    	<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.domain') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
			<select class="form-select select2" name="domain_id">
				    <option value="">--{{ trans('cruds.domain.choose') }}--</option>
					@foreach ($domains as $domain)
				    	<option value="{{ $domain->id }}"
				    	{{ $domain->id==$control->domain_id ? "selected" : ""}} >
				    	{{ $domain->title }} - {{ $domain->description }}
						</option>
				    @endforeach
				</select>
			</div>
		</div>
		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.clause') }}</strong>
	    	</div>
			<div class="col-lg-3 col-md-5">
				<input class="form-control" type="text" name="clause"
				value="{{ old('clause', $control->clause) }}"
				size='60'>
			</div>
		</div>

		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.name') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
				<input class="form-control" type="text" name="name"
					value="{{ old('name', $control->name) }}"
					size='60'>
			</div>
		</div>

		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.objective') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
                <textarea name="objective" class="easymde form-control" id="mde1">{{ old('objective', $control->objective) }}</textarea>
			</div>
		</div>

		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.attributes') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
				<select class="form-select select2" name="attributes[]" multiple>
					@foreach($values as $value)
				    <option {{ (old('attributes') ? in_array($value, old('attributes')) : str_contains($control->attributes ?? '', $value)) ? 'selected' : ''}}>{{$value}}</option>
				    @endforeach
				 </select>
			</div>
		</div>

		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.input') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
                <textarea name="input" class="easymde form-control" id="input">{{ old('input', $control->input) }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.model') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
				<textarea class="form-control" name="model" rows="3">{{ old('model', $control->model) }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.indicator') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
				<textarea class="form-control" name="indicator" rows="3">{{ old('indicator', $control->indicator) }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="col-lg-1 col-md-2">
	    		<strong>{{ trans('cruds.control.fields.action_plan') }}</strong>
	    	</div>
			<div class="col-lg-11 col-md-10">
                <textarea name="action_plan" class="easymde form-control" id="action_plan">{{ old('action_plan', $control->action_plan) }}</textarea>
			</div>
		</div>

    	<div class="row">
			<div class="col-lg-1 col-md-2">
			</div>
		</div>

		<div class="row">
    		<div class="col-lg-8 col-md-12">
				<button type="submit" class="btn btn-success">
                    <span class="bi bi-floppy"></span> {{ trans('common.save') }}
				</button>
				&nbsp;
                <a type="button" class="btn btn-light border" href="/alice/show/{{ $control->id }}">
					<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
                </a>
			</div>
		</div>
	</div>
</form>
</div>
</div>
@endsection
