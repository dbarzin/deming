@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-bookshelf"></i> {{ trans('cruds.domain.edit') }}</div>
    <div class="card-body">

    @include('partials.errors')

	<form method="POST" action="/domains/{{ $domain->id }}">
		@method("PATCH")
		@csrf
		<div class="container-fluid px-0">
	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
					<label>{{ trans('cruds.domain.fields.framework') }}</label>
		    	</div>
				<div class="col-md-6 col-lg-3">
					<input type="text" class="{{ $errors->has('framework') ? 'is-invalid' : ''}} form-control" name="framework" value="{{ old('framework', $domain->framework) }}" maxlength='32'>
				</div>
			</div>
	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
					<label>{{ trans('cruds.domain.fields.name') }}</label>
		    	</div>
				<div class="col-md-6 col-lg-3">
					<input type="text" class="{{ $errors->has('title') ? 'is-invalid' : ''}} form-control" name="title" value="{{ old('title', $domain->title) }}" maxlength='32'>
				</div>
			</div>
	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
					<label class="form-label" for="description">{{ trans('cruds.domain.fields.description') }}</label>
		    	</div>
				<div class="col-md-12 col-lg-11">
					<input type="text" class="{{ $errors->has('description') ? 'is-invalid' : ''}} form-control" name="description" value="{{ old('description', $domain->description) }}" maxlength='255'>
				</div>
			</div>
        	<div class="row">
                <div class="col-md-12 col-lg-6">
            		<button type="submit" class="btn btn-success">
                        <span class="bi bi-floppy"></span> {{ trans('common.save') }}
            		</button>
                    <a href="/domains/{{ $domain->id }}" class="btn btn-light border">
                		<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
                    </a>
                </div>
            </div>
        </div>
	</form>
</div>
</div>
@endsection
