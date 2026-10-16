@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-bookshelf"></i> {{ trans('cruds.domain.add') }}</div>
    <div class="card-body">

    @include('partials.errors')

	<form method="POST" action="/domains">
	@csrf
		<div class="container-fluid px-0">
	    	<div class="row">
	    		<div class="col-1">
		    		<strong>{{ trans('cruds.domain.fields.framework') }}</strong>
		    	</div>
	    		<div class="col-5">
					<input class="form-control" type="text" name="framework" value="{{ old('framework') }}" maxlength='32'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-1">
		    		<strong>{{ trans('cruds.domain.fields.name') }}</strong>
		    	</div>
	    		<div class="col-5">
					<input class="form-control" type="text" name="title" value="{{ old('title') }}" maxlength='32'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-1">
		    		<strong>{{ trans('cruds.domain.fields.description') }}</strong>
		    	</div>
	    		<div class="col-5">
					<input class="form-control" type="text" name="description" value="{{ old('description') }}" maxlength='255'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-5">
					<button type="submit" class="btn btn-success">
                        <span class="bi bi-floppy"></span> {{ trans('common.save') }}
					</button>
                    <a href="/domains" class="btn btn-light border">
    						<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
                    </a>
				</div>
			</div>
		</div>
	</form>
</div>
</div>

@endsection
