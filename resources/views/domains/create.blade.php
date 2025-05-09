@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.domain.add') }}" data-collapsible="true" data-title-icon="<span class='mif-library'></span>">

@if (count($errors))
    <div class="remark alert">
        <span class="mif-report icon"></span>
			@foreach ($errors->all() as $error)
                {{ $error }}<br>
			@endforeach
    </div>
@endif

	<form method="POST" action="/domains">
	@csrf
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.domain.fields.framework') }}</strong>
		    	</div>
	    		<div class="cell-5">
					<input type="text" name="framework" value="{{ old('framework') }}" maxlength='32'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.domain.fields.name') }}</strong>
		    	</div>
	    		<div class="cell-5">
					<input type="text" name="title" value="{{ old('title') }}" maxlength='32'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.domain.fields.description') }}</strong>
		    	</div>
	    		<div class="cell-5">
					<input type="text" name="description" value="{{ old('description') }}" maxlength='255'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-5">
					<button type="submit" class="button success">
                        <span class="mif-floppy-disk2"></span>
						&nbsp;
						{{ trans('common.save') }}
					</button>
                    <a href="/domains" class="button">
    						<span class="mif-cancel"></span>
    						&nbsp;
    		    			{{ trans('common.cancel') }}
                    </a>
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
