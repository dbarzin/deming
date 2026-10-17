@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-tags"></i> {{ trans('cruds.attribute.edit') }}</div>
    <div class="card-body">

    @include('partials.errors')

	<form method="POST" action="/attributes/{{ $attribute->id }}">
		@method("PATCH")
		@csrf
		<div class="container-fluid px-0">
	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
					<label>{{ trans('cruds.attribute.fields.name') }}</label>
		    	</div>
				<div class="col-md-6 col-lg-3">
					<input type="text" class="{{ $errors->has('name') ? 'is-invalid' : ''}} form-control" name="name" value="{{ old('name', $attribute->name) }}" size='5'>
				</div>
			</div>
	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
					<label class="form-label" for="description">{{ trans('cruds.attribute.fields.values') }}</label>
		    	</div>
				<div class="col-md-12 col-lg-11">
					<textarea class="form-control" name="values" rows="5" cols="80">{{ old('values', $attribute->values) }}</textarea>
					<br>
					format: #tag #tag #tag ...
					<br><br>
				</div>
			</div>
		</div>
		<button type="submit" class="btn btn-success">
			<span class="bi bi-floppy"></span> {{ trans('common.save') }}
		</button>
		</form>
    &nbsp;
	<form action="/attributes/{{ $attribute->id }}">
		<button type="submit" class="btn btn-light border">
			<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
		</button>
	</form>
</div>
</div>
@endsection
