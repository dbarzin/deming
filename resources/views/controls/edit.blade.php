@extends("layout")

@section("title")
Edit Control
@endsection

@section("content")

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

<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

<form method="POST" action="/controls/{{ $control->id }}">
	@method("PATCH")
	@csrf
	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Domain</strong>
	    	</div>
			<div class="cell-5">
			<select name="domain_id" size="1" width='10'>
				    <option value="">--Choose an domain--</option>
					@foreach ($domains as $domain)
				    	<option value="{{ $domain->id }}"
				    	{{ $domain->id==$control->domain_id ? "selected" : ""}} >
				    	{{ $domain->title }} - {{ $domain->description }}
						</option>
				    @endforeach
				</select>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Clause</strong>
	    	</div>
			<div class="cell-5">
				<input type="text" name="clause" 
				value="{{ $errors->has('clause') ?  old('clause') : $control->clause }}" 
				size='60'>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
			<div class="cell-5">
				<input type="text" name="name" 
					value="{{ $errors->has('name') ?  old('name') : $control->name }}" 
					size='60'>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Objectif</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="objective" rows="3" cols="80">{{ $errors->has('objective') ?  old('objective') : $control->objective }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Attributs</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="attributes" rows="3" cols="80">{{ $errors->has('attributes') ?  old('attributes') : $control->attributes }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Calcul</strong>
	    	</div>
			<div class="cell-5">
				<textarea class="textarea" name="model" rows="3" cols="80">{{ $errors->has('model') ?  old('model') : $control->model }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Indicateur</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="indicator" rows="3" cols="80">{{ $errors->has('indicator') ?  old('indicator') : $control->indicator }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Plan d'actions</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="action_plan" rows="3" cols="80">{{ $errors->has('action_plan') ?  old('action_plan') : $control->action_plan }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Owner</strong>
	    	</div>
			<div class="cell-5">
			<input name="owner" type="text"
				value="{{ $errors->has('owner') ?  old('owner') : $control->owner }}" 
				size='20'>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Périodicité</strong>
	    	</div>
			<div class="cell-5">
				<select name="periodicity" size="1" width='20'>
				    <option value="0"></option>
				    <option value="1" {{ $control->periodicity==1 ? "selected" : ""}} >Mensuel</option>
				    <option value="3" {{ $control->periodicity==3 ? "selected" : ""}}>Trimestriel</option>
				    <option value="4" {{ $control->periodicity==4 ? "selected" : ""}}>Quatrimestriel</option>
				    <option value="6" {{ $control->periodicity==6 ? "selected" : ""}}>Semstriel</option>
				    <option value="12" {{ $control->periodicity==12 ? "selected" : ""}}>Annuel</option>
				 </select>
			</div>
		</div>

		<div class="row">
    		<div class="cell-5">
				<button type="submit" class="button success">Sauver</button>
				</form>

				<form action="/controls/{{ $control->id }}" method="post">
		        	{{ method_field('delete') }}
		        	@csrf
		            <button class="button alert" type="submit">Supprimer</button>
		        </form>
				
				<form action="/controls/{{ $control->id }}">
					<button type="submit" class="button">
						<span class="mif-cancel"></span> Annuler
					</button>
				</form>
			</div>
		</div>

@endsection

