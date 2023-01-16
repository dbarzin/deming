@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.title.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		<div class="grid">
			<div class="row">
				<div class="cell-5">

				</div>
				<div class="cell-7" align="right">
					<button class="button primary" onclick="location.href = '/domains/create';">{{ trans('common.new') }}</button>
				</div>
			</div>

			<div class="row">
				<div class="cell-12">

			<table class="table striped row-hover cell-border"
		       data-role="table"
		       data-rows="10"
			   data-show-search="false"
		       data-show-activity="true"
		       data-rownum="false"
		       data-check="false"
		       data-check-style="1">
			    <thead>
			    <tr>
					<th width="50">{{ trans('cruds.domain.fields.name') }}</th>
					<th width="200">{{ trans('cruds.domain.fields.description') }}</th>
			    </tr>
			    </thead>
			    <tbody>
			@foreach($domains as $domain)
				<tr>
					<td><a href="/domains/{{ $domain->id}}">{{ $domain->title }}</a></td>
					<td>{{ $domain->description }}</td>
				</tr>
			@endforeach
				</tbody>
			</table>
			<br>
		</div>
	</div>
</div>


	
@endsection

