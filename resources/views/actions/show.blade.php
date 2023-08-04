@extends("layout")

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.action.show') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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

	<form method="POST" action="/action/save">
		@csrf
		<input type="hidden" name="id" value="{{ $action->id }}"/>

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.action.fields.name') }}</strong>
		    	</div>
				<div class="cell-5">
					{{ $action->name }}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.action.fields.objective') }}</strong>
		    	</div>
				<div class="cell-5">
					<pre>{{ $action->objective }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.action.fields.observation') }}</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $action->observations }}</pre>
				</div>
			</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>{{ trans('cruds.action.fields.next_date') }}</strong>
	    	</div>
			<div class="cell-2">
				<input type="text" data-role="calendarpicker" name="plan_date" value="{{$action->next_date}}" data-input-format="%Y-%m-%d"> 
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.action.fields.action_plan') }}</strong>
	    	</div>
			<div class="cell-6">
				<textarea name="action_plan" rows="10" cols="80">{{ $errors->has('action_plan') ?  old('action_plan') : $action->action_plan }}</textarea>
			</div>
		</div>

		<div class="grid">
	    	<div class="row-12">
				@if (Auth::User()->role==1)
				<button type="submit" class="button success">
		            <span class="mif-floppy-disk"></span>
		            &nbsp;
					{{ trans('common.save') }}
				</button>
				@endif
				</form>
				&nbsp;
	    		<form action="/actions">
		    		<button type="submit" class="button cancel">
		    			<span class="mif-cancel"></span>
		    			&nbsp;
		    			{{ trans("common.cancel") }}
		    		</button>
		    	</form>
    		</div>
		</div>
	</div>
	</form>
</div>
</div>
@endsection