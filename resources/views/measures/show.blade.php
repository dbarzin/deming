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
				{{ $measure->domain->title }}
				</a>
				-
				{{ $measure->domain->description }}
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

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
	    	</div>
			<div class="cell">
				<pre>{{ $measure->attributes }}</pre>
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
	    		<strong>{{ trans('cruds.measure.fields.owner') }}</strong>
	    	</div>
			<div class="cell">
				{{ $measure->owner }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.periodicity') }}</strong>
	    	</div>
			<div class="cell">
				@if ($measure->periodicity==1) {{ trans("common.monthly") }} @endif
				@if ($measure->periodicity==3) {{ trans("common.quarterly") }} @endif
				@if ($measure->periodicity==6) {{ trans("common.biannual") }} @endif
				@if ($measure->periodicity==12) {{ trans("common.annual") }} @endif
				</div>
			</div>
	    </div>


	<div class="form-group">
	    <form action="">
	    	<button class="button primary" onclick='this.form.action="/measures/{{ $measure->id }}/edit"';>
	            <span class="mif-wrench"></span>
	            &nbsp;
		    	{{ trans('common.edit') }}
	    	</button>
	    	&nbsp;				
	    	<button class="button" onclick='this.form.action="/measures";'>
				<span class="mif-cancel"></span>
				&nbsp;
		    	{{ trans('common.cancel') }}
	    	</button>
		</form>
	</div>
</div>
</div>

@endsection

