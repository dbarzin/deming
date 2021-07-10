@extends("layout")

@section("title")
Plan d'action
@endsection

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

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
		    		<strong>Nom</strong>
		    	</div>
				<div class="cell-5">
					{{ $action->name }}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Objectif</strong>
		    	</div>
				<div class="cell-5">
					<pre>{{ $action->objective }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Observation</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $action->observations }}</pre>
				</div>
			</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Prochaine revue</strong>
	    	</div>
			<div class="cell-2">
					<input type="text" data-role="calendarpicker" name="next_date" value="{{ 
				\Carbon\Carbon
				::createFromFormat('Y-m-d',$action->next_date)
				->format('Y-m-d')
				}}" 
				data-input-format="%Y-%m-%d"> 
			</div>
		</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Plan d'action</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="action_plan" rows="10" cols="80">{{ $errors->has('action_plan') ?  old('action_plan') : $action->action_plan }}</textarea>
				</div>
			</div>



		<div class="grid">
	    	<div class="row-12">
			<button type="submit" class="button success">Save</button>

    		<button type="submit" class="button" onclick='this.form.action="/actions";this.form.method="GET";'>Cancel</button>
    		</div>
    	</div>

	</form>


@endsection