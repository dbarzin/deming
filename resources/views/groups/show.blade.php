@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.group.show') }}" data-collapsible="true" data-title-icon="<span class='mif-group'></span>">

		<div class="grid">

	    	<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans('cruds.group.fields.name') }}</strong>
		    	</div>
	    		<div class="cell">
                    {{ $group->name }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans('cruds.group.fields.description') }}</strong>
		    	</div>
	    		<div class="cell">
                    {!! $group->description !!}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans('cruds.group.fields.users') }}</strong>
		    	</div>
            </div>
	    	<div class="row">
	    		<div class="cell-8">
				    <table class="table striped row-hover cell-border row-border"
				       data-role="table"
				       data-rows="25"
				       data-show-activity="true"
				       data-rownum="false"
				       data-check="false">
				        <thead>
				            <tr>
                                <th class="sortable-column">{{ trans("cruds.user.fields.name") }}</th>
                                <th class="sortable-column">{{ trans("cruds.user.fields.email") }}</th>
                                <th class="sortable-column">{{ trans("cruds.user.fields.role") }}</th>
				            </tr>
				        </thead>
				        <tbody>
                    @foreach($group->users as $user)
	    				<tr>
				            <td>
                                <a id="{{ $user->name }}" href="/users/{{$user->id}}">
                                    {{ $user->name }}
                                </a>
                            </td>
				            <td>
                                {{ $user->email }}
				            </td>
                            <td>
            		    		{{ $user->role==1 ? trans('cruds.user.roles.admin') : "" }}
            		    		{{ $user->role==2 ? trans('cruds.user.roles.user') : "" }}
                                {{ $user->role==5 ? trans('cruds.user.roles.auditee') : "" }}
            		    		{{ $user->role==3 ? trans('cruds.user.roles.auditor') : "" }}
            		    		{{ $user->role==4 ? trans('cruds.user.roles.api') : "" }}
                            </td>
		    			</tr>
	    			@endforeach
	    			</table>
	    		</div>
	    	</div>


	    	<div class="row">
                <div class="cell-2">
                    <strong>{{ trans('cruds.group.fields.controls') }}</strong>
		    	</div>
            </div>
	    	<div class="row">
	    		<div class="cell-8">
				    <table class="table striped row-hover cell-border row-border"
				       data-role="table"
				       data-rows="25"
				       data-show-activity="true"
				       data-rownum="false"
				       data-check="false">
				        <thead>
				            <tr>
                                <th class="sortable-column">{{ trans("cruds.control.fields.clauses") }}</th>
                                <th class="sortable-column">{{ trans("cruds.control.fields.name") }}</th>
                                <th class="sortable-column">{{ trans("cruds.control.fields.scope") }}</th>
                                <th class="sortable-column">{{ trans("cruds.control.fields.planned") }}</th>
				            </tr>
				        </thead>
				        <tbody>
                    @foreach($group->controls as $control)
	    				<tr>
				            <td>
                                @foreach($control->measures as $measure)
                                <a id="{{ $measure['clause'] }}" href="/alice/show/{{ $measure['id'] }}">
                                    {{ $measure['clause'] }}
                                </a>
                                @if (!$loop->last)
                                ,
                                @endif
                                @endforeach
                            </td>
				            <td>
                                {{ $control->name }}
                            </td>
				            <td>
                                {{ $control->scope }}
				            </td>
                            <td>
                                <a id="{{ $control->plan_date }}" href="/bob/show/{{$control->id}}">
				                <b>
				                    @if( strtotime($control->plan_date) >= strtotime('now') )
				                        <font color="green">{{ $control->plan_date }}</font>
				                    @else
				                        <font color="red">{{ $control->plan_date }}</font>
				                    @endif
				                </b>
                                </a>
                            </td>
		    			</tr>
	    			@endforeach
	    			</table>
	    		</div>
	    	</div>

	    	<div class="row">
	    		<div class="cell">
		    	</div>
            </div>


	    </div>

		<div class="form-group">
		    <form>
				@if (Auth::User()->role==1)
                <button class="button primary" onclick='this.form.action="/groups/{{ $group->id }}/edit"'>
		            <span class="mif-wrench"></span>
		            &nbsp;
		    		{{ trans('common.edit') }}
		    	</button>
		    </form>
		    	&nbsp;
		    	@endif
				@if (Auth::User()->role==1)
                <form action="/groups/{{ $group->id }}" method="post" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
	               {{ method_field('delete') }}
	               @csrf
		            <button class="button alert" type="submit">
						<span class="mif-fire"></span>
						&nbsp;
		            	{{ trans('common.delete') }}
		            </button>
		        </form>
		        &nbsp;
		        @endif
		        <form>
		    	<button class="button" onclick='this.form.action="/users";'>
					<span class="mif-cancel"></span>
					&nbsp;
		    		{{ trans('common.cancel') }}
		    	</button>
			</form>
		</div>
	</div>
</div>


@endsection
