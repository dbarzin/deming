@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.edit') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
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
		<form method="POST" action="/domains/{{ $domain->id }}">
			@method("PATCH")
			@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.domain.fields.name') }}</label>
			    	</div>
					<div class="cell-3">
						<input type="text" class="input {{ $errors->has('title') ? 'is-danger' : ''}}" name="title" value="{{ $errors->has('title') ?  old('title') : $domain->title }}" size='5'>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label class="label" for="description">{{ trans('cruds.domain.fields.description') }}</label>
			    	</div>
					<div class="cell-8">						
						<textarea name="description" rows="5" cols="80">{{ $errors->has('description') ?  old('description') : $domain->description }}</textarea>
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
			<form action="/domains/{{ $domain->id }}" method="post">
	           {{ method_field('delete') }}
	           @csrf
		        <button class="button alert" type="submit">
					<span class="mif-fire"></span>
					&nbsp;
			        {{ trans('common.delete') }}
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



