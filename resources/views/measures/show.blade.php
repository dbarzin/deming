@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Mesure de sécurité" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Domaine</strong>
	    	</div>
			<div class="cell">
				<a href="/domains/{{$measure->domain_id}}">
				{{ $measure->domain->title }}
				</a>
				-
				{{ $measure->domain->description }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
			<div class="cell">
				{{ $measure->clause }} - {{ $measure->name }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Objetif</strong>
	    	</div>
			<div class="cell-6">
				{{ $measure->objective }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Attributs</strong>
	    	</div>
			<div class="cell">
				<pre>{{ $measure->attributes }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Calcul</strong>
	    	</div>
			<div class="cell-6">
				<pre>{{ $measure->model }}</pre>
			</div>
	    </div>


    	<div class="row">
			<div class="cell-1">
	    		<strong>Indicateur</strong>
	    	</div>
			<div class="cell">
				<pre>{{ $measure->indicator }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Plan d'action</strong>
	    	</div>
			<div class="cell-6">
	    		<pre>{{ $measure->action_plan }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Owner</strong>
	    	</div>
			<div class="cell">
				{{ $measure->owner }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>Périodicité</strong>
	    	</div>
			<div class="cell">
				@if ($measure->periodicity==1) Mensuel @endif
				@if ($measure->periodicity==3) Triestriel @endif
				@if ($measure->periodicity==4) Quadrimestriel @endif
				@if ($measure->periodicity==6) Semestriel @endif
				@if ($measure->periodicity==12) Annuel @endif
				</div>
			</div>
	    </div>


	<div class="form-group">
	    <form action="">
	    	<button class="button primary" onclick='this.form.action="/measures/{{ $measure->id }}/edit"';>Edit</button>
	    	<button class="button" onclick='this.form.action="/measures";'>Cancel</button>
		</form>
	</div>
</div>
</div>

@endsection

