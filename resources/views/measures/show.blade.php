@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.show') }}" data-collapsible="true" data-title-icon="<span class='mif-books'></span>">
		@if (count($errors))
		<div class="grid">
		    <div class="cell-5 bg-red fg-white">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
		</div>
		@endif

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.domain') }}</strong>
	    	</div>
			<div class="cell">
				<a href="/domains/{{$measure->domain_id}}">
				{{ $measure->domain->title ?? ""}}
				</a>
				-
				{{ $measure->domain->description ?? "" }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.clause') }}</strong>
	    	</div>
			<div class="cell">
				{{ $measure->clause }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
	    	</div>
			<div class="cell">
				{{ $measure->name }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
	    	</div>
			<div class="cell-6">
                {!! \Parsedown::instance()->text($measure->objective) !!}
			</div>
	    </div>

		@if ($measure->attributes!=null)
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.measure.fields.attributes") }}</strong>
	    	</div>
    		<div class="cell-6">
	    		{{ $measure->attributes }}
    		</div>
    	</div>
    	@endif

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.input') }}</strong>
	    	</div>
			<div class="cell">
                {!! \Parsedown::instance()->text($measure->input) !!}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.model') }}</strong>
	    	</div>
			<div class="cell-6">
				<pre>{{ $measure->model }}</pre>
			</div>
	    </div>


    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.indicator') }}</strong>
	    	</div>
			<div class="cell">
				<pre>{{ $measure->indicator }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.action_plan') }}</strong>
	    	</div>
			<div class="cell-6">
                {!! \Parsedown::instance()->text($measure->action_plan) !!}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
			</div>
		</div>

		<div class="form-group">
			@if (Auth::User()->role === 1)
		    <form action="/alice/plan/{{ $measure->id }}">
		    	<button class="button info">
		            <span class="mif-calendar"></span>
		            &nbsp;
			    	{{ trans('common.plan') }}
		    	</button>
		    </form>
		    &nbsp;
		    <form action="/alice/{{ $measure->id }}/edit">
		    	<button class="button primary">
		            <span class="mif-wrench"></span>
		            &nbsp;
			    	{{ trans('common.edit') }}
		    	</button>
		    </form>
		    &nbsp;
		    <form action="/alice/clone/{{ $measure->id }}">
		    	<button class="button yellow">
		            <span class="mif-plus"></span>
		            &nbsp;
			    	{{ trans('common.clone') }}
		    	</button>
		    </form>
		    &nbsp;
			<form action="/alice/delete/{{ $measure->id }}" method="POST" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
                @csrf
				<button class="button alert" type="submit">
					<span class="mif-fire"></span>
					&nbsp;
				    {{ trans('common.delete') }}
				</button>
	        </form>
		    &nbsp;
		    @endif
			@if (Auth::User()->role === 5)
		    <form action="/bob/index">
		    	<button class="button">
					<span class="mif-cancel"></span>
					&nbsp;
			    	{{ trans('common.cancel') }}
		    	</button>
			</form>
            @else
		    <form action="/alice/index">
		    	<button class="button">
					<span class="mif-cancel"></span>
					&nbsp;
			    	{{ trans('common.cancel') }}
		    	</button>
			</form>
            @endif
		</div>
    </div>
</div>
</div>
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.title') }}" data-collapsible="true" data-title-icon="<span class='mif-paste'></span>">

        <div>
			<table class="table striped row-hover cell-border"
       data-role="table"
                data-show-search="false"
                    data-show-pagination="false"
                    data-show-rows-steps="false"
                   >
			   <thead>
                    <tr class="row-hover">
                        <th class="sortable-column sort-asc" width="65%">{{ trans("cruds.welcome.controls") }}</th>
                        <th class="sortable-column sort-asc" width="65%">{{ trans("cruds.control.fields.scope") }}</th>
                        <th class="sortable-column sort-asc" width="5%">{{ trans("cruds.control.fields.score") }}</th>
                        <th class="sortable-column sort-asc" width="15%">{{ trans("cruds.control.fields.plan_date") }}</th>
                        <th class="sortable-column sort-asc" width="15%">{{ trans("cruds.control.fields.realisation_date") }}</th>
				    </tr>
			    </thead>
			    <tbody>
            @foreach($controls as $control)
				<tr>
					<td>
                        {{ $control->name }}
					</td>
					<td>
                        <a id="{{ $control->scope }}" href="/bob/show/{{$control->id}}">
                            {{ $control->scope }}
						</a>
					</td>
                    <td>
                        <center id="{{ $control->score }}">
                            @if ($control->score==1)
                                &#128545;
                            @elseif ($control->score==2)
                                &#128528;
                            @elseif ($control->score==3)
                                <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                            @else
                                &#9675; <!-- &#9899; -->
                            @endif
                        </center>
					</td>
					<td>
                        <!-- format in red when month passed -->
                        @if (($control->status === 0)||($control->status === 1))
                        <a id="{{ $control->plan_date }}" href="/bob/show/{{$control->id}}">
                        <b> @if (today()->lte($control->plan_date))
                                <font color="green">{{ $control->plan_date }}</font>
                            @else
                                <font color="red">{{ $control->plan_date }}</font>
                            @endif
                        </b>
                        </a>
                        @else
                            {{ $control->plan_date }}
                        @endif
					</td>
					<td>
                        <b id="{{ $control->realisation_date }}">
                            <a href="/bob/show/{{$control->id}}">
                                {{ $control->realisation_date }}
                            </a>
                            @if ( ($control->status===1 )&& ((Auth::User()->role===1)||(Auth::User()->role===2)))
                                &nbsp;
                                <a href="/bob/make/{{ $control->id }}">&#8987;</a>
                            @endif
                        </b>
					</td>
                </tr>
            @endforeach
            </tbody>
            </table>
        </div>
        <div>
        </div>
	</div>
</div>
@endsection
