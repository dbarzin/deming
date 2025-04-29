@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.attribute.add') }}" data-collapsible="true" data-title-icon="<span class='mif-pencil'></span>">
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

		<form method="POST" action="/attributes">
		@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.attribute.fields.name') }}</strong>
			    	</div>
		    		<div class="cell-5">
						<input type="text" name="name" value="{{ old('name') }}" size='25'>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.attribute.fields.values') }}</strong>
			    	</div>
		    		<div class="cell-5">
						<textarea name="values" rows="5" cols="80">{{ old('values') }}</textarea>
						<br>
						format: #tag #tag #tag ...
						<br><br>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-5">
						<button type="submit" class="button success">
				            <span class="mif-floppy-disk"></span>
							&nbsp;
							{{ trans('common.save') }}
						</button>
					</form>
					&nbsp;
					<form action="/attributes">
			    		<button type="submit" class="button">
							<span class="mif-cancel"></span>
							&nbsp;
			    			{{ trans('common.cancel') }}
			    		</button>
					</form>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection
