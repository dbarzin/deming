@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.user.index') }}" data-collapsible="true" data-title-icon="<span class='mif-users'></span>">

		<div class="grid">
			<div class="row">
				<div class="cell-5">
				</div>

				<div class="cell-7" align="right">
					@if (Auth::User()->role==1)
                    <a class="button primary" href="/users/create">
			            <span class="mif-plus"></span>
			            &nbsp;
						{{ trans('common.new') }}
                    </a>
					@endif
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
					<th
						data-sortable="true"
						data-sort-dir="asc"
						data-format="string"
						width="50"
						>{{ trans('cruds.user.fields.login') }}</th>
					<th
						data-sortable="true"
						data-sort-dir="asc"
						data-format="string"
						width="50"
						>{{ trans('cruds.user.fields.name') }}</th>
					<th
						data-sortable="true"
						data-sort-dir="asc"
						data-format="string"
						width="50"
						>{{ trans('cruds.user.fields.role') }}</th>
					<th
						data-sortable="true"
						data-format="string"
						data-format="string"
						width="200">{{ trans('cruds.user.fields.email') }}</th>
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
                    </td>
					<td>{{ $user->email }}</td>
    				@if (Auth::User()->role==1)
                    <td>
                        <a class="button primary small" href='/users/{{ $user->id }}/edit'>
        		            <span class="mif-wrench"></span>
        		            &nbsp;
        		    		{{ trans('common.edit') }}
                        </a>
                    </td>
                    @endif
				</tr>
			@endforeach
				</tbody>
			</table>
			<br>
		</div>
	</div>
</div></div>
@endsection
