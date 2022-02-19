@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Domaines" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		<div class="grid">
			<div class="row">
				<div class="cell-5">

				</div>
				<div class="cell-7" align="right">
					<a href="/domains/create"><span class="mif-add"></span>Nouveau</a>
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
					<th width="50">Title</th>
					<th width="200">Descrition</th>
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

