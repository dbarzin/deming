@extends("layout")

@section("title")
Mesure
@endsection

@section("content")

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
    		<div class="cell">
	    		<a href="/controls/{{ $measurement->control_id}}">{{ $measurement->clause }}</a> &nbsp; - &nbsp; {{ $measurement->name }}
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Objectif</strong>
	    	</div>
    		<div class="cell-6">
	    		<pre>{!! $measurement->objective !!}</pre>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Attributs</strong>
	    	</div>
			<div class="cell-6">
				<pre>{!! $measurement->attributes !!}</pre>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Observations</strong>
	    	</div>
			<div class="cell-6">
				<pre>{!! $measurement->observations !!}</pre>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Calcul</strong>
	    	</div>
			<div class="cell-6">
				<pre>{{ $measurement->model }}</pre>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Date de planification</strong>
	    	</div>
			<div class="cell-1">
	    		{{ $measurement->plan_date }}
			</div>

    		<div class="cell-1">
	    		<strong>Date de la mesure</strong>
	    	</div>
			<div class="cell-1">
	    		{{ $measurement->realisation_date }}
			</div>

    		<div class="cell-1">
    			<strong>Précédent</strong>
	    		<strong>Suivant</strong>	    		
	    	</div>
			<div class="cell-1">
		    	@if ($prev_id!=null)
					<a href="/measurement/show/{{ $prev_id }}">
		    			{{ $prev_date }}
		    		</a>
				@else
					N/A
				@endif
				<br>
		    	@if ($next_id!=null)
					<a href="/measurement/show/{{ $next_id }}">
		    			{{ $next_date }}
		    		</a>
				@else
					N/A
				@endif
			</div>

		</div>

		@if ($measurement->observations!=null)
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Observations</strong>
		    	</div>
				<div class="cell-5">
					<pre>{{ $measurement->observations }}</pre>
				</div>
		    </div>
		@endif

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
		    		{{ $measurement->note }}
	    		</div>
		    </div>


		@if ($measurement->realisation_date !=null)
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Indicateur</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $measurement->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Score</strong>
		    	</div>
				<div class="cell">
                    @if ($measurement->score==1)
                        &#128545;
                    @elseif ($measurement->score==2)
                        &#128528;
                    @elseif ($measurement->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(80deg)">&#128512;</span>
                    @else
                        &#9899;
                    @endif
					&nbsp; - &nbsp;
					@if ($measurement->score==1) 
						Rouge
					@elseif ($measurement->score==2) 
						Orange
					@elseif ($measurement->score==3) 
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
					<pre>{{ $measurement->action_plan }}</pre>
				</div>
			</div>

	@endif

   	<div class="row">
   		<div class="cell-3">
			@if (Auth::User()->role==1)
		    <form action="/measurement/edit/{{ $measurement->id }}">
	    		<button class="button primary">Edit</button>
			</form>
			@endif
   			@if ($measurement->realisation_date==null)
		    <form action="/measurement/make/{{ $measurement->id }}">
	    		<button class="button success">Faire</button>
			</form>
   			@endif
		    <form action="/measurements">
	    	<!--
	    	<button class="button primary" onclick='this.form.action="/measurements/{{ $measurement->id }}/edit"';>Edit</button>
		    -->
	    		<button class="button">Cancel</button>
			</form>
		</div>
	</div>

@endsection
