@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		<div class="grid">
			<div class="row">
				<div class="cell-5">
				</div>
				@if (Auth::User()->role==1)
				<div class="cell-7" align="right">
					<button class="button primary" onclick="location.href = '/domains/create';">
			            <span class="mif-plus"></span>
			            &nbsp;
						{{ trans('common.new') }}
					</button>
				</div>
				@endif
			</div>

			<div class="row">
				<div class="cell-12">

			<table class="table striped row-hover cell-border"
		       data-role="table"
		       data-rows="100"
			   data-show-search="false"
		       data-show-activity="true"
		       data-rownum="false"
		       data-check="false"
		       data-check-style="1">
			    <thead>
			    <tr>
					<th width="50">{{ trans('cruds.domain.fields.framework') }}</th>
					<th width="50">{{ trans('cruds.domain.fields.name') }}</th>
					<th width="200">{{ trans('cruds.domain.fields.description') }}</th>
					<th width="200">{{ trans('cruds.domain.fields.measures') }}</th>
			    </tr>
			    </thead>
			    <tbody>
			@foreach($domains as $domain)
				<tr>
					<td>{{ $domain->framework }}</td>
					<td><a href="/domains/{{ $domain->id}}">{{ $domain->title }}</a></td>
					<td>{{ $domain->description }}</td>
					<td><a href="/alice/index?domain={{ $domain->id }}">{{ $domain->measures }}</a></td>
				</tr>
			@endforeach
				</tbody>
			</table>
			<br>
		</div>
	</div>
</div>
@endsection
