@extends("layout")

@section("title")
Contrôle de sécurité
@endsection

@section("content")

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
    		<div class="cell-6">
	    		<pre>{!! $control->objective !!}</pre>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Attributs</strong>
	    	</div>
			<div class="cell-6">
				<pre>{!! $control->attributes !!}</pre>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Date de planification</strong>
	    	</div>
			<div class="cell-1">
	    		{{ $control->plan_date }}
			</div>

    		<div class="cell-1">
	    		<strong>Date de la mesure</strong>
	    	</div>
			<div class="cell-1">
	    		{{ $control->realisation_date }}
			</div>

    		<div class="cell-1">
    			<strong>Précédent</strong>
	    		<strong>Suivant</strong>	    		
	    	</div>
			<div class="cell-1">
		    	@if ($prev_id!=null)
					<a href="/controls/{{ $prev_id }}">
		    			{{ $prev_date }}
		    		</a>
				@else
					N/A
				@endif
				<br>
		    	@if ($next_id!=null)
					<a href="/controls/{{ $next_id }}">
		    			{{ $next_date }}
		    		</a>
				@else
					N/A
				@endif
			</div>

		</div>


		@if ($control->observations!=null)
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Observations</strong>
		    	</div>
				<div class="cell-5">
					<pre>{!! $control->observations !!}</pre>
				</div>
		    </div>
		@endif

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Calcul</strong>
	    	</div>
			<div class="cell-6">
				<pre>{{ $control->model }}</pre>
			</div>
		</div>

		@if ($documents->isNotEmpty())
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Documents</strong>
		    	</div>
				<div class="cell-6">
					@foreach ($documents as $document)
						<a href="/doc/show/{{$document->id}}" target="_new">
							{{$document->filename}}
						</a>
						<br>
					@endforeach
				</div>
		    </div>
		@endif

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Note</strong>
		    	</div>
	    		<div class="cell-2">
		    		{{ $control->note }}
	    		</div>
		    </div>


		@if ($control->realisation_date !=null)
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Indicateur</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Score</strong>
		    	</div>
				<div class="cell">
                    @if ($control->score==1)
                        &#128545;
                    @elseif ($control->score==2)
                        &#128528;
                    @elseif ($control->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(80deg)">&#128512;</span>
                    @else
                        &#9899;
                    @endif
					&nbsp; - &nbsp;
					@if ($control->score==1) 
						Rouge
					@elseif ($control->score==2) 
						Orange
					@elseif ($control->score==3) 
						Vert
					@else
						
					@endif
				</div>
			</div>

		</div>
		@else
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Plan d'action</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->action_plan }}</pre>
				</div>
			</div>

	@endif

   	<div class="row">
   		<div class="cell-3">
			@if (Auth::User()->role==1)
		    <form action="/control/edit/{{ $control->id }}">
	    		<button class="button primary">Edit</button>
			</form>
			@endif
   			@if ($control->realisation_date==null)
		    <form action="/control/make/{{ $control->id }}">
	    		<button class="button success">Faire</button>
			</form>
   			@endif
		    <form action="/controls">
	    		<button class="button">Cancel</button>
			</form>
		</div>
	</div>

@endsection
