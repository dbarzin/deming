@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.title_singular') }}" data-collapsible="true" data-title-icon="<span class='mif-paste'></span>">

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
	    	</div>
    		<div class="cell-4">
                @foreach($control->measures as $measure)
                    <a href="/alice/show/{{ $measure->id }}">{{ $measure->clause }}</a>
                    @if(!$loop->last)
                    ,
                    @endif
                @endforeach
            </div>
        </div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.name") }}</strong>
	    	</div>
            @if ($control->scope===null)
    		<div class="cell-6">
	    		 {{ $control->name }}
    		</div>
            @else
    		<div class="cell-4">
	    		 {{ $control->name }}
    		</div>
    		<div class="cell-1" align="right">
	    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
	    	</div>
    		<div class="cell-1">
                <a href="/bob/index?scope={{ $control->scope }}">
    			{{ $control->scope }}
                </a>
    		</div>
            @endif
    	</div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.objective") }}</strong>
	    	</div>
    		<div class="cell-6">
                {!! \Parsedown::instance()->text($control->objective) !!}
    		</div>
    	</div>

		@if ($control->attributes!=null)
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.attributes") }}</strong>
	    	</div>
    		<div class="cell-6">
	    		{{ $control->attributes }}
    		</div>
    	</div>
    	@endif

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.input") }}</strong>
	    	</div>
			<div class="cell-6">
                {!! \Parsedown::instance()->text($control->input) !!}
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.model") }}</strong>
	    	</div>
			<div class="cell-6">
				<pre>{!! $control->model !!}</pre>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.plan_date") }}</strong>
	    	</div>
			<div class="cell-1">
	    		{{ $control->plan_date }}
			</div>

            <div class="cell-2 text-right">
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
					<a href="/bob/show/{{ $prev_id }}">
		    			{{ $prev_date }}
		    		</a>
				@else
					N/A
				@endif
				<br>
		    	@if ($next_id!=null)
					<a href="/bob/show/{{ $next_id }}">
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


    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.indicator") }}</strong>
	    	</div>
			<div class="cell-6">
				<pre>{{ $control->indicator }}</pre>
			</div>
		</div>

		@if ($control->score!==null)
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
					@endif
				</div>
			</div>
		@endif

		@if (($control->realisation_date!=null)&&($control->score!=3))
		</div>
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
		    	</div>
				<div class="cell-6">
                    {!! \Parsedown::instance()->text($control->action_plan) !!}
				</div>
			</div>
		@endif

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
	    	</div>
			<div class="cell">
				@if ($control->periodicity==0) {{ trans("common.once") }} @endif
				@if ($control->periodicity==1) {{ trans("common.monthly") }} @endif
				@if ($control->periodicity==3) {{ trans("common.quarterly") }} @endif
				@if ($control->periodicity==6) {{ trans("common.biannually") }} @endif
				@if ($control->periodicity==12) {{ trans("common.annually") }} @endif
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.control.fields.owners') }}</strong>
	    	</div>
			<div class="cell">
                @foreach($control->owners as $owner)
					{{ $owner->name }}
                    @if ($control->owners->last()!=$owner)
					,
					@endif
				@endforeach
			</div>
	    </div>


   	<div class="row">
   		<div class="cell-7">

            @if ($control->canMake())
			    <form action="/bob/make/{{ $control->id }}">
		    		<button class="button success">
						<span class="mif-assignment"></span>
						&nbsp;
			    		{{ trans("common.make") }}
			    	</button>
				</form>
				&nbsp;
            @endif

			@if (
                    ($control->status===1) &&
                    (
                        (Auth::User()->role===1)||
                        (Auth::User()->role===2)
                    )
                )
			    <form action="/bob/make/{{ $control->id }}">
		    		<button class="button success">
						<span class="mif-assignment"></span>
						&nbsp;
			    		{{ trans("common.validate") }}
			    	</button>
				</form>
				&nbsp;
            @endif
            @if (($control->status===0)||($control->status===1))
                @if ((Auth::User()->role===1)||(Auth::User()->role===2))
    			    <form action="/bob/plan/{{ $control->id }}">
    		    		<button class="button info">
    						<span class="mif-calendar"></span>
    						&nbsp;
    			    		{{ trans("common.plan") }}
    		    		</button>
    				</form>
    				&nbsp;
				@endif
			@endif
			@if (Auth::User()->role==1)
		    <form action="/bob/edit/{{ $control->id }}">
	    		<button class="button primary">
					<span class="mif-wrench"></span>
					&nbsp;
	    			{{ trans("common.edit") }}
	    		</button>
			</form>
			&nbsp;
		    <form action="/bob/clone/{{ $control->id }}">
		    	<button class="button yellow">
		            <span class="mif-plus"></span>
		            &nbsp;
			    	{{ trans('common.clone') }}
		    	</button>
		    </form>
			&nbsp;
		    <form action="/bob/delete/{{ $control->id }}" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
	    		<button class="button alert">
					<span class="mif-fire"></span>
					&nbsp;
	    			{{ trans("common.delete") }}
	    		</button>
			</form>
		    &nbsp;
   			@endif
		    <form action="/bob/index">
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
