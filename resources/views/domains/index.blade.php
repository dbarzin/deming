@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-bookshelf"></i> {{ trans('cruds.domain.index') }}</div>
    <div class="card-body">
	<div class="container-fluid px-0">
		<div class="row">
			<div class="col-5">
			</div>
			@if (Auth::User()->role==1)
			<div class="col-7" align="right">
				<button class="btn btn-primary" onclick="location.href = '/domains/create';">
		            <span class="bi bi-plus-lg"></span> {{ trans('common.new') }}
				</button>
			</div>
			@endif
		</div>

		<div class="row">
			<div class="col-12">

		<table data-order="[]" data-searching="false" data-page-length="100"
			id="domains"
			class="table table-striped table-hover table-bordered datatable">
		    <thead>
		    <tr>
				<th width="50">{{ trans('cruds.domain.fields.framework') }}</th>
				<th width="50">{{ trans('cruds.domain.fields.name') }}</th>
				<th width="200">{{ trans('cruds.domain.fields.description') }}</th>
				<th  width="200">{{ trans('cruds.domain.fields.measures') }}</th>
		    </tr>
		    </thead>
		    <tbody>
		@foreach($domains as $domain)
			<tr>
				<td>{{ $domain->framework }}</td>
				<td><a href="/domains/{{ $domain->id}}">{{ $domain->title }}</a></td>
				<td>{{ $domain->description }}</td>
				<td><a href="/alice/index?domain={{ $domain->id }}">{{ $domain->measures_count }}</a></td>
			</tr>
		@endforeach
			</tbody>
		</table>
		<br>
	</div>
</div>
</div>
</div>
@endsection
