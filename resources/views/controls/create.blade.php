@extends("layout")

@section("content")
<div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'controls.create'])<i class="bi bi-book"></i> {{ trans('cruds.control.create') }}</div>
    <div class="card-body">
    @include('partials.errors')

	<form method="POST" action="/alice/store">
	@csrf
		<div class="container-fluid px-0">
	    	<div class="row">
                <div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.control.fields.domain") }}</strong>
		    	</div>
                <div class="col-lg-11 col-md-10">
					<select class="form-select select2" name="domain_id" value="{{ old('domain_id') }}" size="1" width='10'>
					    <option value="">-- {{ trans("cruds.domain.choose") }} --</option>
						@foreach ($domains as $domain)
					    	<option value="{{ $domain->id }}" {{ old('domain_id', optional($control)->domain_id)==$domain->id ? "selected" : "" }} >
					    		{{ $domain->title }} - {{ $domain->description }}
					    	</option>
					    @endforeach
					</select>
				</div>
			</div>
	    	<div class="row">
                <div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.control.fields.clause") }}</strong>
		    	</div>
                <div class="col-lg-2 col-md-4">
                    <input type="text" class="form-control" name="clause" value="{{ old('clause', optional($control)->clause) }}" maxlength='32'/>
				</div>
			</div>

	    	<div class="row">
                <div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.control.fields.name") }}</strong>
		    	</div>
                <div class="col-lg-11 col-md-10">
                    <input type="text" class="form-control" name="name" value="{{ old('name', optional($control)->name) }}" maxlength='255'/>
				</div>
			</div>

	    	<div class="row">
                <div class="col-lg-1 col-md-2">
					<strong>{{ trans("cruds.control.fields.objective") }}</strong>
				</div>
                <div class="col-lg-11 col-md-10">
                    <textarea name="objective" class="easymde form-control" id="objetive">{{ old('objective', optional($control)->objective) }}</textarea>
				</div>
			</div>

			<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans('cruds.control.fields.attributes') }}</strong>
		    	</div>
                    <div class="col-lg-11 col-md-10">
					<select class="form-select select2" name="attributes[]" multiple>
						@foreach($values as $value)
					    <option value="{{ $value }}"
                            @if(in_array($value, old('attributes', $selectedAttributes ?? []))) selected @endif>
                            {{$value}}
                        </option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
					<strong>{{ trans("cruds.control.fields.input") }}</strong>
				</div>
                <div class="col-lg-11 col-md-10">
                    <textarea name="input" class="easymde form-control" id="input">{{ old('input', optional($control)->input) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
					<strong>{{ trans("cruds.control.fields.model") }}</strong>
				</div>
				<div class="col-lg-11 col-md-10">
					<textarea class="form-control" name="model" rows="3">{{ old('model', optional($control)->model) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
					<strong>{{ trans("cruds.control.fields.indicator") }}</strong>
				</div>
				<div class="col-lg-11 col-md-10">
					<textarea class="form-control" name="indicator" rows="3">{{ old('indicator', optional($control)->indicator) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
					<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
				</div>
				<div class="col-lg-11 col-md-10">
                    <textarea name="action_plan" class="easymde form-control" id="action_plan">{{ old('action_plan', optional($control)->action_plan) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-md-10 col-lg-5">
					<button type="submit" class="btn btn-success">
                        <span class="bi bi-floppy"></span> {{ trans("common.save") }}
					</button>
                    <a class="btn btn-light border" href="/alice/index">
						<span class="bi bi-x-circle"></span> {{ trans("common.cancel") }}
                    </a>
				</div>
			</div>
		</div>
	</form>
</div>
</div>
@endsection
