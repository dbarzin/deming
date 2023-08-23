@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.add') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
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

		<form method="POST" action="/domains">
		@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.domain.fields.name') }}</strong>
			    	</div>
		    		<div class="cell-5">
						<input type="text" name="title" value="{{ old('title') }}" size='25'>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.domain.fields.description') }}</strong>
			    	</div>
		    		<div class="cell-5">
						<input type="text" name="description" value="{{ old('description') }}" size='60'>
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
					<form action="/domains">
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

