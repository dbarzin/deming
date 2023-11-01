@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.show') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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
	    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
	    	</div>
			<div class="cell">
				{{ $measure->clause }} - {{ $measure->name }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
	    	</div>
			<div class="cell-6">
				{{ $measure->objective }}
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
				<pre>{{ $measure->input }}</pre>
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
	    		<pre>{{ $measure->action_plan }}</pre>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
			</div>
		</div>

		<div class="form-group">
			@if (Auth::User()->role !== 3)
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
			<form action="/alice/delete/{{ $measure->id }}" method="POST" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
				<button class="button alert" type="submit">
					<span class="mif-fire"></span>
					&nbsp;
				    {{ trans('common.delete') }}
				</button>
	        </form>
		    &nbsp;
		    @endif
		    <form action="/alice/index">
		    	<button class="button">
					<span class="mif-cancel"></span>
					&nbsp;
			    	{{ trans('common.cancel') }}
		    	</button>
			</form>
		</div>
	</div>
</div>

@endsection
