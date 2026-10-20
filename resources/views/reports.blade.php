@extends("layout")

@section("content")
<div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'reports'])<i class="bi bi-file-earmark-text"></i> {{ trans('cruds.exports.steering') }}</div>
    <div class="card-body">

	@if (count($errors))
	<div class="container-fluid px-0">
	    <div class="col-md-6 col-lg-3 bg-danger text-white">
			<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif

	<form action="/reports/pilotage" target="_new">
	<div class="container-fluid px-0">

		<div class="row">
	        <div class="col-md-2 col-lg-1">
                {{ trans("cruds.domain.fields.framework") }}
            </div>
			<div class="col-md-6 col-lg-3">
                <select class="form-select select2" name="framework" id="framework">
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
	        <div class="col-md-2 col-lg-1">
				{{ trans('cruds.exports.start') }}
			</div>
			<div class="col-md-4 col-lg-2">
	            <input class="form-control" type="date"
	                    name="start_date"
	                    value="{{ (new \Carbon\Carbon('first day of this month'))->addMonth(-3)->format('Y-m-d')}}"
						/>
	        </div>
	    </div>
		<div class="row">
	        <div class="col-md-2 col-lg-1">
			{{ trans('cruds.exports.end') }}
			</div>
			<div class="col-md-4 col-lg-2">
	            <input class="form-control" type="date"
	                    name="end_date"
	                    value="{{ (new \Carbon\Carbon('last day of this month'))->addMonth(-1)->format('Y-m-d')}}"
						/>
	        </div>
	    </div>

		<div class="row">
	        <div class="col-md-2 col-lg-1">
			    <button type="submit" class="btn btn-primary">{{ trans ('common.create') }}</button>
			</div>
		</div>
	</div>
</form>

</div>
</div>

<div class="card mt-3">
    <div class="card-header"><i class="bi bi-file-earmark-text"></i> {{ trans('cruds.soa.title') }}</div>
    <div class="card-body">
	<div class="row">
		<div class="col-md-12 col-lg-6">
        	<ul>
        		<li>
					<a href="/reports/soa" target="_blank">{{ trans('cruds.soa.generate') }}</a>
				</li>
			</ul>
		</div>
	</div>
</div>
</div>

<div class="card mt-3">
    <div class="card-header"><i class="bi bi-file-earmark-text"></i> {{ trans('cruds.exports.index') }}</div>
    <div class="card-body">
	<div class="row">
		<div class="col-md-12 col-lg-6">
        	<ul>
				<li>
					<a href="/export/users" target="_blank">{{ trans('cruds.exports.users_export') }}</a>
				</li>
        		<li>
					<a href="/export/domains" target="_blank">{{ trans('cruds.exports.domains_export') }}</a>
				</li>
        		<li>
					<a href="/export/attributes" target="_blank">{{ trans('cruds.exports.attributes_export') }}</a>
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
				<li>
                    <a href="/export/risks" target="_blank">{{ trans('cruds.exports.risks_export') }}</a>
				</li>
			</ul>
		</div>
	</div>
</div>
</div>
@endsection
