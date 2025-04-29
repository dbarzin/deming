@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.edit') }}" data-collapsible="true" data-title-icon="<span class='mif-pencil'></span>">
		@if (count($errors))
		<div class="grid">
		    <div class="cell-3 bg-red fg-white">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
		</div>
		@endif
		<form method="POST" action="/domains/{{ $domain->id }}">
			@method("PATCH")
			@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.domain.fields.framework') }}</label>
			    	</div>
					<div class="cell-3">
						<input type="text" class="input {{ $errors->has('framework') ? 'is-danger' : ''}}" name="framework" value="{{ $errors->has('framework') ?  old('framework') : $domain->framework }}" maxlength='32'>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.domain.fields.name') }}</label>
			    	</div>
					<div class="cell-3">
						<input type="text" class="input {{ $errors->has('title') ? 'is-danger' : ''}}" name="title" value="{{ $errors->has('title') ?  old('title') : $domain->title }}" maxlength='32'>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label class="label" for="description">{{ trans('cruds.domain.fields.description') }}</label>
			    	</div>
					<div class="cell-8">
						<input type="text" class="input {{ $errors->has('description') ? 'is-danger' : ''}}" name="description" value="{{ $errors->has('description') ?  old('description') : $domain->description }}" maxlength='255'>
					</div>
				</div>
			</div>
			<button type="submit" class="button success">
	            <span class="mif-floppy-disk"></span>
				&nbsp;
				{{ trans('common.save') }}
			</button>
		</form>
        &nbsp;
		<form action="/domains/{{ $domain->id }}">
			<button type="submit" class="button">
				<span class="mif-cancel"></span>
				&nbsp;
    			{{ trans('common.cancel') }}
    		</button>
		</form>
	</div>
</div>
@endsection
