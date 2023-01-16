@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.title.add') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
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

		<form method="POST" action="/domains">
		@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.domain.fields.name') }}</strong>
			    	</div>
		    		<div class="cell-5">
						<input type="text" name="title" placeholder="title" value="{{ old('title') }}" size='25'>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.domain.fields.description') }}</strong>
			    	</div>
		    		<div class="cell-5">
						<textarea name="description" rows="5" cols="80">{{ old('description') }}</textarea>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-5">
						<button type="submit" class="button success">{{ trans('common.save') }}</button>
						<button type="submit" class="button" onclick='this.form.action="/domains";this.form.method="GET";'>{{ trans('common.cancel') }}</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection

