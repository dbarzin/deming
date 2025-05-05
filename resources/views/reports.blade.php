@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.exports.steering') }}" data-collapsible="true" data-title-icon="<span class='mif-file-text'></span>">

	@if (count($errors))
	<div class="grid">
	    <div class="cell-3 bg-red fg-white">
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
	        <div class="cell-1">
                {{ trans("cruds.domain.fields.framework") }}
            </div>
            <div class="cell-2">
                <select name="framework" data-role="select" id="framework">
                    <option></option>
                    @foreach ($frameworks as $framework)
                    <option
                        @if (Session::get("framework")===$framework->framework)
                            selected
                        @endif >
                        {{ $framework->framework }}
                    </option>
                    @endforeach
                </select>
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
	</div>
</form>

</div>

<div class="mt-3" data-role="panel" data-title-caption="{{ trans('cruds.soa.title') }}" data-collapsible="true" data-title-icon="<span class='mif-file-text'></span>">
	<div class="row">
        <div class="cell-3">
        	<ul>
        		<li>
					<a href="/reports/soa" target="_blank">{{ trans('cruds.soa.generate') }}</a>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="mt-3" data-role="panel" data-title-caption="{{ trans('cruds.exports.index') }}" data-collapsible="true" data-title-icon="<span class='mif-file-text'></span>">
	<div class="row">
        <div class="cell-3">
        	<ul>
        		<li>
					<a href="/export/domains" target="_blank">{{ trans('cruds.exports.domains_export') }}</a>
				</li>
				<li>
					<a href="/export/alices" target="_blank">{{ trans('cruds.exports.controls_export') }}</a>
				</li>
				<li>
					<a href="/export/bobs" target="_blank">{{ trans('cruds.exports.measures_export') }}</a>
				</li>
				<li>
                    <a href="/export/actions" target="_blank">{{ trans('cruds.exports.actions_export') }}</a>
				</li>
			</ul>
		</div>
	</div>
</div>
@endsection
