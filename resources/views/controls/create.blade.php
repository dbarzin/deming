@extends("layout")

@section("title")
Ajouter un contrôle
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


	<form method="POST" action="/controls">
	@csrf
	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Domaine</strong>
	    	</div>
			<div class="cell-5">
				<select name="domain_id" value="{{ old('domain_id') }}" size="1" width='10'>
				    <option value="">--Choose an domain--</option>
					@foreach ($domains as $domain)
				    	<option value="{{ $domain->id }}"
							@if (((int)Session::get("domain"))==$domain->id)		
								selected 
							@endif >
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
				<input type="text" class="input" name="clause" value="{{ old('clause') }}" size='60'>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
			<div class="cell-5">
				<input type="text" class="input" name="name" value="{{ old('name') }}" size='60'>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Objectif</strong>
			</div>
			<div class="cell-5">
				<textarea class="textarea" name="objective" rows="3" cols="80">{{ old('objective') }}</textarea>
			</div> 	
		</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Attributs</strong>
			</div>
			<div class="cell-5">			
				<textarea class="textarea" name="attributes" rows="3" cols="80">{{ old('attributes') }}</textarea>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Calcul</strong>
			</div>
			<div class="cell-5">			
				<textarea class="textarea" name="model" rows="3" cols="80">{{ old('model') }}</textarea>
			</div> 	
		</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Indicateur (Rouge, Orange, Vert)</strong>
			</div>
			<div class="cell-5">			
				<textarea class="textarea" name="indicator" rows="3" cols="80">{{ old('indicator') }}</textarea>
			</div> 	
		</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Plan d'action</strong>
			</div>
			<div class="cell-5">
				<textarea class="textarea" name="action_plan" rows="3" cols="80">{{ old('action_plan') }}</textarea>
			</div> 	
		</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Responsable</strong>
			</div>
			<div class="cell-5">
				<input type="text" class="input" name="owner" value="{{ old('owner') }}" size='20'>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
				<strong>Périodicité</strong>
			</div>
			<div class="cell-5">
				<select name="periodicity" size="1" width='20'>
				    <option value="0" {{ old('periodicity')==0 ? 'selected' : ''}} ></option>
				    <option value="1" {{ old('periodicity')==1 ? 'selected' : ''}}>Mensuel</option>
				    <option value="3" {{ old('periodicity')==3 ? 'selected' : ''}}>Trimestriel</option>
				    <option value="4" {{ old('periodicity')==4 ? 'selected' : ''}}> Quatrimestriel</option>
				    <option value="6" {{ old('periodicity')==6 ? 'selected' : ''}}>Semstriel</option>
				    <option value="12" {{ old('periodicity')==12 ? 'selected' : ''}}>Annuel</option>
				 </select>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-5">
				<button type="submit" class="button success">
					<span class="mif-done"></span>Sauver</button>
				<button type="submit" 
					onclick="this.form.method='GET';"
					class="button">
					<span class="mif-cancel"></span> Annuler</button>
			</div>
		</div>
	</div>
</form>

@endsection

