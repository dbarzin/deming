@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.action.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		<div class="grid">
			<div class="row">
				<div class="cell">

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
				<th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.clause') }}</th>
				<th class="sortable-column sort-asc" width="60%">{{ trans('cruds.action.fields.action') }}</th>
				<th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.plan_date') }}</th>
				<th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.next_date') }}</th>
				<th class="sortable-column sort-asc" width="10%">{{ trans('cruds.action.fields.note') }}</th>
		    </tr>
		    </thead>
		    <tbody>
		@foreach($actions as $action)
			<tr>
				<td valign="top">
					<a href="/measures/{{$action->measure_id}}">
						{{ $action->clause }}
					</a>
				</td>
				<td>
					<b><a href="/action/{{ $action->id }}">{{ $action->name }}</a></b>
					<pre>{{ $action->action_plan }}</pre>
				</td>
				<td><a href="/controls/{{ $action->id }}">{{ $action->plan_date }}</a></td>
				<td><a href="/controls/{{ $action->next_id }}">{{ $action->next_date }}</a></td>
				<td>
	                @if ($action->score==1)
	                    &#128545;
	                @elseif ($action->score==2)
	                    &#128528;
	                @elseif ($action->score==3)
	                    <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
	                @else
	                    &#9675; <!-- &#9899; -->
	                @endif
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
			</div>
		</div>
	</div>
</div>
@endsection
