@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-people"></i> {{ trans('cruds.group.index') }}</div>
    <div class="card-body">

	<div class="container-fluid px-0">
		<div class="row">
			<div class="col-md-10 col-lg-5">
			</div>

			<div class="col-md-12 col-lg-7 text-md-end">
				@if (Auth::User()->role==1)
                <a class="btn btn-primary" href="/groups/create">
		            <span class="bi bi-plus-lg"></span> {{ trans('common.new') }}
                </a>
				@endif
			</div>
		</div>

		<div class="row">
			<div class="col-12">

		<table data-order="[[0,&quot;asc&quot;]]" data-searching="false" data-page-length="10"
			id="groups"
			class="table table-striped table-hover table-bordered datatable">
		    <thead>
		    <tr>
				<th>{{ trans('cruds.group.fields.name') }}
                </th>
				<th>{{ trans('cruds.group.fields.description') }}
                </th>
				<th># {{ trans('cruds.group.fields.users') }}
                </th>
				<th># {{ trans('cruds.group.fields.controls') }}
                </th>
                <th></th>
		    </tr>
		    </thead>
		    <tbody>
        @foreach($groups as $group)
			<tr>
                <td><a id="{{$group->name}}" href="/groups/{{ $group->id}}">{{ $group->name }}</a></td>
                <td>{{ $group->description }}</td>
                <td>{{ $group->users()->count()}} </td>
                <td>{{ $group->measures()->count()}} </td>
                <td>
                    <a class="btn btn-primary btn-sm" href='/groups/{{ $group->id }}/edit'>
                        <span class="bi bi-pencil-square"></span> {{ trans('common.edit') }}
                    </a>
                </td>
			</tr>
		@endforeach
			</tbody>
		</table>
		<br>
	</div>
</div></div>
</div>
</div>
@endsection
