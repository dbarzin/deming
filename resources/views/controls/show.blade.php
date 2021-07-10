@extends("layout")

@section("title")
View Control
@endsection

@section("content")

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Domain</strong>
	    	</div>
			<div class="cell">
				<a href="/domains/{{$control->domain_id}}">
				{{ $control->domain($control->domain_id)->title }}
				</a>
				-
				{{ $control->domain($control->domain_id)->description }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
			<div class="cell">
				{{ $control->clause }} - {{ $control->name }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Objetif</strong>
	    	</div>
			<div class="cell-6">
				{{ $control->objective }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Attributs</strong>
	    	</div>
			<div class="cell">
				<pre>{{ $control->attributes }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Calcul</strong>
	    	</div>
			<div class="cell-6">
				<pre>{{ $control->model }}</pre>
			</div>
	    </div>


    	<div class="row">
			<div class="cell-1">
	    		<strong>Indicateur</strong>
	    	</div>
			<div class="cell">
				<pre>{{ $control->indicator }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Plan d'action</strong>
	    	</div>
			<div class="cell-6">
	    		<pre>{{ $control->action_plan }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Owner</strong>
	    	</div>
			<div class="cell">
				{{ $control->owner }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Périodicité</strong>
	    	</div>
			<div class="cell">
				@if ($control->periodicity==1) Mensuel @endif
				@if ($control->periodicity==3) Triestriel @endif
				@if ($control->periodicity==4) Quadrimestriel @endif
				@if ($control->periodicity==6) Semestriel @endif
				@if ($control->periodicity==12) Annuel @endif
				</div>
			</div>
	    </div>


	<div class="form-group">
	    <form action="">
	    	<button class="button primary" onclick='this.form.action="/controls/{{ $control->id }}/edit"';>Edit</button>
	    	<button class="button" onclick='this.form.action="/controls";'>Cancel</button>
		</form>
	</div>


@endsection

