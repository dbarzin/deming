@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.attribute.edit') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		@if (count($errors))
		<div class= “form-group”>
			<div class= “alert alert-danger”>
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
		</div>
		@endif
		<form method="POST" action="/attributes/{{ $attribute->id }}">
			@method("PATCH")
			@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.attribute.fields.name') }}</label>
			    	</div>
					<div class="cell-3">
						<input type="text" class="input {{ $errors->has('name') ? 'is-danger' : ''}}" name="name" value="{{ $errors->has('name') ?  old('name') : $attribute->name }}" size='5'>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label class="label" for="description">{{ trans('cruds.attribute.fields.values') }}</label>
			    	</div>
					<div class="cell-8">
						<textarea name="values" rows="5" cols="80">{{ $errors->has('values') ?  old('values') : $attribute->values }}</textarea>
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
		<form action="/attributes/{{ $attribute->id }}">
    		<button type="submit" class="button">
				<span class="mif-cancel"></span>
				&nbsp;
    			{{ trans('common.cancel') }}
    		</button>
		</form>
	</div>
</div>
@endsection



