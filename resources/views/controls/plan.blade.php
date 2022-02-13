@extends("layout")

@section("title")
Plannifier un contrôle
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

	<form method="POST" action="/control/plan">
	@csrf

	<input type="hidden" name="id" value="{{ $control->id }}"/>

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
			<div class="cell">
	    		<a href="/measures/{{ $control->measure_id }}">{{ $control->clause }}</a> &nbsp; - &nbsp; {{ $control->name }}
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Objectif</strong>
	    	</div>
			<div class="cell">
				{{ $control->objective }}
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Dernière mesure</strong>
	    	</div>
			<div class="cell">
				...
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Périodicite</strong>
	    	</div>
			<div class="cell">
				@if ($control->periodicity==1) Mensuel @endif
				@if ($control->periodicity==3) Triestriel @endif
				@if ($control->periodicity==4) Quadrimestriel @endif
				@if ($control->periodicity==6) Semestriel @endif
				@if ($control->periodicity==12) Annuel @endif				
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
				<strong>Plan Date</strong>
	    	</div>
			<div class="cell-2">
					<input type="text" data-role="calendarpicker" name="plan_date" value="{{ 
				\Carbon\Carbon
				::createFromFormat('Y-m-d',$control->plan_date)
				->format('Y-m-d')
				}}" 
				data-input-format="%Y-%m-%d"> 
			</div>
		</div>
    	<div class="row">
    		<div class="cell-12">
				<button type="submit" class="button success">Plan</button>
				<button type="submit" class="button" onclick="this.form.action='/controls';">Cancel</button>
			</div>
		</div>
	</div>
	</form>

@endsection

