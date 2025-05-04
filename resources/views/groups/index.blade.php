@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.group.index') }}" data-collapsible="true" data-title-icon="<span class='mif-group'></span>">

		<div class="grid">
			<div class="row">
				<div class="cell-5">
				</div>

				<div class="cell-7" align="right">
					@if (Auth::User()->role==1)
                    <a class="button primary" href="/groups/create">
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
                        >{{ trans('cruds.group.fields.name') }}
                    </th>
					<th
						data-sortable="true"
						data-sort-dir="asc"
						data-format="string"
						width="50"
                        >{{ trans('cruds.group.fields.description') }}
                    </th>
					<th
						data-sortable="true"
						data-sort-dir="asc"
						data-format="string"
						width="50"
                        ># {{ trans('cruds.group.fields.users') }}
                    </th>
					<th
						data-sortable="true"
						data-sort-dir="asc"
						data-format="string"
						width="50"
                        ># {{ trans('cruds.group.fields.controls') }}
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
                    <td>{{ $group->controls()->count()}} </td>
                    <td>
                        <a class="button primary small" href='/groups/{{ $group->id }}/edit'>
                            <span class="mif-wrench"></span>
        		            &nbsp;
        		    		{{ trans('common.edit') }}
                        </a>
                    </td>
				</tr>
			@endforeach
				</tbody>
			</table>
			<br>
		</div>
	</div>
</div></div>
@endsection
