@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

			<div class="grid">
				<div class="row">
					<div class="cell-5">
						<select id='domain_id' name="domain_id" data-role="select">
						    <option value="0">-- {{ trans('cruds.domain.choose') }} --</option>
							@foreach ($domains as $domain)
						    	<option value="{{ $domain->id }}"
									@if (((int)Session::get("domain"))==$domain->id)
										selected
									@endif >
						    		{{ $domain->title }}
						    	</option>
						    @endforeach
						</select>
					</div>
					<div class="cell-7" align="right">
						<button class="button primary" onclick="location.href = '/alice/create';">
    			            <span class="mif-plus"></span>
    			            &nbsp;
    						{{ trans('common.new') }}
		               </button>
					</div>
				</div>

			<script>
				window.addEventListener('load', function(){
			    var select = document.getElementById('domain_id');

			    select.addEventListener('change', function(){
			        window.location = '/alice/index?domain=' + this.value;
			    }, false);
			}, false);
			</script>

				<div class="row">
					<div class="cell">

			<table class="table striped row-hover cell-border"
		       data-role="table"
		       data-rows="100"
		       data-show-activity="true"
		       data-rownum="false"
		       data-check="false"
		       data-check-style="1">
			   <thead>
				    <tr>
						<th class="sortable-column sort-asc" width="10%">{{ trans('cruds.measure.fields.domain') }}</th>
						<th class="sortable-column sort-asc" width="10%">{{ trans('cruds.measure.fields.clause') }}</th>
						<th class="sortable-column sort-asc" width="60%">{{ trans('cruds.measure.fields.name') }}</th>
						<th class="sortable-column sort-asc" data-cls-column="text-center" width="10%"># {{ trans('cruds.control.title') }}</th>
						<th width="10%"></th>
				    </tr>
			    </thead>
			    <tbody>
			@foreach($measures as $measure)
				<tr>
					<td>
						<a id="{{ $measure->title }}" href="/domains/{{$measure->domain_id}}">
							{{ $measure->title }}
						</a>
					</td>
					<td>
                        <a id="{{ $measure->clause }}" href="/alice/show/{{ $measure->id }}">
						@if (strlen($measure->clause)==0)
							None
						@else
							{{ $measure->clause }}
						@endif
						</a>
					</td>
					<td>{{ $measure->name }}</td>
					<td id="{{ $measure->control_count }}">
						@if ($measure->control_count>0)
						<a href="/bob/index?period=99&domain=0&scope=none&status=2&measure={{ $measure->id }}">
							{{ $measure->control_count }}
						</a>
						@else
						0
						@endif
					</td>
					<td>
					    <form action="/alice/plan/{{ $measure->id }}">
					    	<button class="button info">
					            <span class="mif-calendar"></span>
					            &nbsp;
						    	{{ trans('common.plan') }}
					    	</button>
					    </form>
					</td>
				</tr>
			@endforeach
			</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
