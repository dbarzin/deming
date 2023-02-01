@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.exports.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

		<b>{{ trans('cruds.exports.report_title') }}</b>

		@if (count($errors))
		<div class= “form-group”>
			<div class= “alert alert-danger”>
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
		</div>
		@endif

		<form action="/reports/pilotage" target="_new">

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-5">
				{{ trans('cruds.exports.steering') }}
				</div>
			</div>

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

			<div class="row">
		        <div class="cell-3">
		        	<b>
				</div>
			</div>

			<div class="row">
		        <div class="cell-3">	    
					<b>{{ trans('cruds.exports.data_export_title') }}</b>
				</div>
			</div>

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
	</form>
@endsection