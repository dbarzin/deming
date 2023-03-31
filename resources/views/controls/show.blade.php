@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.title_singular') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.name") }}</strong>
	    	</div>
    		<div class="cell">
	    		<a href="/measures/{{ $control->measure_id }}">{{ $control->clause }}</a> &nbsp; - &nbsp; {{ $control->name }}
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.attributes") }}</strong>
	    	</div>
    		<div class="cell-6">
	    		{{ $control->attributes }}
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.objective") }}</strong>
	    	</div>
    		<div class="cell-6">
	    		<pre>{!! $control->objective !!}</pre>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.input") }}</strong>
	    	</div>
			<div class="cell-6">
				<pre>{!! $control->input !!}</pre>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.plan_date") }}</strong>
	    	</div>
			<div class="cell-1">
	    		{{ $control->plan_date }}
			</div>

    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.realisation_date") }}</strong>
	    	</div>
			<div class="cell-1">
	    		{{ $control->realisation_date }}
			</div>

    		<div class="cell-1">
    			<strong>{{ trans("common.previous") }}</strong>
    			<br>
	    		<strong>{{ trans("common.next") }}</strong>	    		
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
		    		<strong>{{ trans("cruds.control.fields.observations") }}</strong>
		    	</div>
				<div class="cell-5">
					<pre>{!! $control->observations !!}</pre>
				</div>
		    </div>
		@endif

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.model") }}</strong>
	    	</div>
			<div class="cell-6">
				<pre>{{ $control->model }}</pre>
			</div>
		</div>

		@if ($documents->isNotEmpty())
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.control.fields.evidence") }}</strong>
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
		    		<strong>{{ trans("cruds.control.fields.note") }}</strong>
		    	</div>
	    		<div class="cell-2">
		    		{{ $control->note }}
	    		</div>
		    </div>


		@if ($control->realisation_date !=null)
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.control.fields.indicator") }}</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.control.fields.score") }}</strong>
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
						{{ trans("common.red") }}
					@elseif ($control->score==2) 
						{{ trans("common.orange") }}
					@elseif ($control->score==3) 
						{{ trans("common.green") }}
					@else
						
					@endif
				</div>
			</div>

		</div>
		@else
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->action_plan }}</pre>
				</div>
			</div>

	@endif

   	<div class="row">
   		<div class="cell-5">
		    <form action="/control/make/{{ $control->id }}">
	    		<button class="button success">
					<span class="mif-assignment"></span>
					&nbsp;	    			
		    		{{ trans("common.make") }}
		    	</button>
			</form>
			&nbsp;
			@if ($control->realisation_date==null)
		    <form action="/control/plan/{{ $control->id }}">
	    		<button class="button info">
					<span class="mif-calendar"></span>
					&nbsp;
		    		{{ trans("common.plan") }}
	    		</button>
			</form>
			&nbsp;
			@endif
			@if (Auth::User()->role==1)
		    <form action="/control/edit/{{ $control->id }}">
	    		<button class="button primary">
					<span class="mif-wrench"></span>
					&nbsp;
	    			{{ trans("common.edit") }}
	    		</button>
			</form>
			&nbsp;
   			@endif
		    <form action="/controls">
	    		<button class="button">
					<span class="mif-cancel"></span>
					&nbsp;
		    		{{ trans("common.cancel") }}
	    		</button>
			</form>
		</div>
	</div>

</div>
</div>

@endsection
