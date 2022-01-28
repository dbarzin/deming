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

<form method="POST" action="/measures/{{ $measure->id }}">
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
				    	{{ $domain->id==$measure->domain_id ? "selected" : ""}} >
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
				value="{{ $errors->has('clause') ?  old('clause') : $measure->clause }}" 
				size='60'>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
			<div class="cell-5">
				<input type="text" name="name" 
					value="{{ $errors->has('name') ?  old('name') : $measure->name }}" 
					size='60'>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Objectif</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="objective" rows="3" cols="80">{{ $errors->has('objective') ?  old('objective') : $measure->objective }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Attributs</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="attributes" rows="3" cols="80">{{ $errors->has('attributes') ?  old('attributes') : $measure->attributes }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Calcul</strong>
	    	</div>
			<div class="cell-5">
				<textarea class="textarea" name="model" rows="3" cols="80">{{ $errors->has('model') ?  old('model') : $measure->model }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Indicateur</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="indicator" rows="3" cols="80">{{ $errors->has('indicator') ?  old('indicator') : $measure->indicator }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Plan d'actions</strong>
	    	</div>
			<div class="cell-5">
				<textarea name="action_plan" rows="3" cols="80">{{ $errors->has('action_plan') ?  old('action_plan') : $measure->action_plan }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-1">
	    		<strong>Owner</strong>
	    	</div>
			<div class="cell-5">
			<input name="owner" type="text"
				value="{{ $errors->has('owner') ?  old('owner') : $measure->owner }}" 
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
				    <option value="1" {{ $measure->periodicity==1 ? "selected" : ""}} >Mensuel</option>
				    <option value="3" {{ $measure->periodicity==3 ? "selected" : ""}}>Trimestriel</option>
				    <option value="4" {{ $measure->periodicity==4 ? "selected" : ""}}>Quatrimestriel</option>
				    <option value="6" {{ $measure->periodicity==6 ? "selected" : ""}}>Semstriel</option>
				    <option value="12" {{ $measure->periodicity==12 ? "selected" : ""}}>Annuel</option>
				 </select>
			</div>
		</div>

		<div class="row">
    		<div class="cell-5">
				<button type="submit" class="button success">Sauver</button>
				</form>

				<form action="/measures/{{ $measure->id }}" method="post">
		        	{{ method_field('delete') }}
		        	@csrf
		            <button class="button alert" type="submit">Supprimer</button>
		        </form>
				
				<form action="/measures/{{ $measure->id }}">
					<button type="submit" class="button">
						<span class="mif-cancel"></span> Annuler
					</button>
				</form>
			</div>
		</div>

@endsection

