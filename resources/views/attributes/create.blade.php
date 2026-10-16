@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-tags"></i> {{ trans('cruds.attribute.add') }}</div>
    <div class="card-body">

    @include('partials.errors')

	<form method="POST" action="/attributes">
	@csrf
		<div class="container-fluid px-0">
	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
		    		<strong>{{ trans('cruds.attribute.fields.name') }}</strong>
		    	</div>
	    		<div class="col-md-10 col-lg-5">
					<input class="form-control" type="text" name="name" value="{{ old('name') }}" size='25'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
		    		<strong>{{ trans('cruds.attribute.fields.values') }}</strong>
		    	</div>
	    		<div class="col-md-10 col-lg-5">
					<textarea class="form-control" name="values" rows="5" cols="80">{{ old('values') }}</textarea>
					<br>
					format: #tag #tag #tag ...
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
