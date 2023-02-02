@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.attribute.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		<div class="grid">
			<div class="row">
				<div class="cell-5">

				</div>
				<div class="cell-7" align="right">
					<button class="button primary" onclick="location.href = '/attributes/create';">
			            <span class="mif-plus"></span>
			            &nbsp;
						{{ trans('common.new') }}
					</button>
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
					<th width="10">{{ trans('cruds.attribute.fields.name') }}</th>
					<th width="200">{{ trans('cruds.attribute.fields.values') }}</th>
			    </tr>
			    </thead>
			    <tbody>
			@foreach($attributes as $attribute)
				<tr>
					<td><a href="/attributes/{{ $attribute->id}}">{{ $attribute->name }}</a></td>
					<td data-cell-wrapper="true" >{{ $attribute->values }}</td>
				</tr>
			@endforeach
				</tbody>
			</table>
			<br>
		</div>
	</div>
</div>


	
@endsection

