@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.exports.steering') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

		<form action="/reports/pilotage" target="_new">

		<div class="grid">

			<div class="row">
		        <div class="cell-1">
					{{ trans('cruds.exports.start') }}
				</div>
		        <div class="cell-2">
		            <input type="text"
		                    data-role="calendarpicker" 
		                    name="start_date" 
		                    value="{{ (new \Carbon\Carbon('first day of this month'))->addMonth(-3)->format('Y-m-d')}}"
		                    data-input-format="%Y-%m-%d">
		        </div>
		    </div>
			<div class="row">
		        <div class="cell-1">
				{{ trans('cruds.exports.end') }}
				</div>
		        <div class="cell-2">
		            <input type="text"
		                    data-role="calendarpicker" 
		                    name="end_date"
		                    value="{{ (new \Carbon\Carbon('last day of this month'))->addMonth(-1)->format('Y-m-d')}}"
		                    data-input-format="%Y-%m-%d">
		        </div>
		    </div>

			<div class="row">
		        <div class="cell-1">	    
				    <button type="submit" class="button primary drop-shadow">{{ trans ('common.create') }}</button>
				</div>
			</div>
		</div>
	</form>

	</div>

	<div class="row">
        <div class="cell-3">
        	<b></b>
		</div>
	</div>

    <div data-role="panel" data-title-caption="{{ trans('cruds.exports.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

			<div class="row">
		        <div class="cell-3">
		        	<ul>
		        		<li>
							<a href="/export/domains" target="_blank">{{ trans('cruds.exports.domains_export') }}</a>
						</li>
						<li>
							<a href="/export/measures" target="_blank">{{ trans('cruds.exports.measures_export') }}</a>
						</li>
						<li>
							<a href="/export/controls" target="_blank">{{ trans('cruds.exports.controls_export') }}</a>
						</li>
					</ul>
				</div>
			</div>

		</div>
@endsection