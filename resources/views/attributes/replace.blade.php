@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-tags"></i> {{ trans('cruds.attribute.replace.title') }}</div>
    <div class="card-body">

    @include('partials.errors')

	<form method="POST" action="/attribute/replace">
	@csrf
		<div class="container-fluid px-0">
	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
		    		<strong>{{ trans('cruds.attribute.replace.old_value') }}</strong>
		    	</div>
	    		<div class="col-md-10 col-lg-11">
					<select class="form-select select2" name="old_value">
						<option></option>
						@foreach($values as $value)
							<option value="{{ $value }}" {{ old('old_value')==$value ? 'selected' : '' }}>{{ $value }}</option>
						@endforeach
					</select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
		    		<strong>{{ trans('cruds.attribute.replace.new_value') }}</strong>
		    	</div>
	    		<div class="col-md-10 col-lg-11">
					<input type="text" class="{{ $errors->has('new_value') ? 'is-invalid' : ''}} form-control" name="new_value" value="{{ old('new_value') }}" size='25'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-md-10 col-lg-5">
					<button type="submit" class="btn btn-success">
                        <span class="bi bi-floppy"></span> {{ trans('common.save') }}
					</button>
    				&nbsp;
                    <a href="/attributes" class="btn cancel btn-light border">
    					<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
                    </a>
				</div>
			</div>
		</div>
	</form>
</div>
</div>
@endsection
