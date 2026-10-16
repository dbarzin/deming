@extends("layout")

@section("content")
    <div class="card">
    <div class="card-header"><i class="bi bi-person"></i> {{ trans('cruds.user.index') }}</div>
    <div class="card-body">

		<div class="container-fluid px-0">
			<div class="row">
				<div class="col-5">
				</div>

				<div class="col-7" align="right">
					@if (Auth::User()->role==1)
                    <a class="btn btn-primary" href="/users/create">
			            <span class="bi bi-plus-lg"></span> {{ trans('common.new') }}
                    </a>
					@endif
				</div>
			</div>

			<div class="row">
				<div class="col-12">

			<table data-order="[[0,&quot;asc&quot;]]" data-searching="false" data-page-length="10"
                id="users"
                class="table table-striped table-hover table-bordered datatable">
			    <thead>
			    <tr>
					<th
						width="50"
						>{{ trans('cruds.user.fields.login') }}</th>
					<th
						width="50"
						>{{ trans('cruds.user.fields.name') }}</th>
					<th
						width="50"
						>{{ trans('cruds.user.fields.role') }}</th>
					<th
						width="200">{{ trans('cruds.user.fields.email') }}</th>
					<th class="dt-hidden">{{ trans('cruds.user.fields.title') }}</th>
					<th class="dt-hidden">{{ trans('cruds.user.fields.language') }}</th>
    				@if (Auth::User()->role==1)
                    <th>
                    </th>
                    @endif
			    </tr>
			    </thead>
			    <tbody>
			@foreach($users as $user)
				<tr>
                    <td><a id="{{$user->login}}" href="/users/{{ $user->id}}">{{ $user->login==null ? "N/A" : $user->login }}</a></td>
					<td>{{ $user->name }}</td>
                    <td>
                    {{ $user->role==1 ? trans('cruds.user.roles.admin') : "" }}
		    		{{ $user->role==2 ? trans('cruds.user.roles.user') : "" }}
                    {{ $user->role==5 ? trans('cruds.user.roles.auditee') : "" }}
		    		{{ $user->role==3 ? trans('cruds.user.roles.auditor') : "" }}
		    		{{ $user->role==4 ? trans('cruds.user.roles.api') : "" }}
		    		{{ $user->role==0 ? trans('cruds.user.roles.disabled') : "" }}
                    </td>
					<td>{{ $user->email }}</td>
					<td>{{ $user->title }}</td>
					<td>{{ $user->language }}</td>
    				@if (Auth::User()->role==1)
                    <td>
                        <a class="btn btn-info btn-sm" href='/users/{{ $user->id }}/edit'>
        		            <span class="bi bi-pencil"></span> {{ trans('common.edit') }}
                        </a>
                    </td>
                    @endif
				</tr>
			@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>
</div>
@endsection
