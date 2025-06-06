@extends("layout")

@section("content")
    <div data-role="panel" data-title-caption="{{ trans('cruds.user.show') }}" data-collapsible="false" data-title-icon="<span class='mif-person'></span>">
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.login') }}</strong>
		    	</div>
                <div class="cell-6">
		    		{{ $user->login }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.name') }}</strong>
		    	</div>
                <div class="cell-6">
		    		{{ $user->name }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.title') }}</strong>
		    	</div>
                <div class="cell-6">
		    		{{ $user->title }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.language') }}</strong>
		    	</div>
                <div class="cell-6">
		    		{{ $user->language }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.role') }}</strong>
		    	</div>
                <div class="cell-6">
		    		{{ $user->role==1 ? trans('cruds.user.roles.admin') : "" }}
		    		{{ $user->role==2 ? trans('cruds.user.roles.user') : "" }}
                    {{ $user->role==5 ? trans('cruds.user.roles.auditee') : "" }}
		    		{{ $user->role==3 ? trans('cruds.user.roles.auditor') : "" }}
		    		{{ $user->role==4 ? trans('cruds.user.roles.api') : "" }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.email') }}</strong>
		    	</div>
                <div class="cell-6">
		    		{{ $user->email }}
	    		</div>
	    	</div>

	    	<div class="row">
                <div class="cell-1">
                    <strong>{{ trans('cruds.user.fields.groups') }}</strong>
		    	</div>

	    		<div class="cell-8">
                    @foreach($user->groups as $group)
                        <a href='/groups/{{ $group->id }}'> {{ $group->name }}</a>
                        @if (!$loop->last)
                        ,
                        @endif
                    @endforeach
	    		</div>
	    	</div>

	    	<div class="row">
	    		<div class="cell-2">
		    		<strong>{{ trans('cruds.user.fields.controls') }}</strong>
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
				                <th class="sortable-column" width="5%">{{ trans("cruds.control.fields.clauses") }}</th>
				                <th width="50%">{{ trans("cruds.control.fields.name") }}</th>
                                <th width="50%">{{ trans("cruds.control.fields.scope") }}</th>
				                <th class="sortable-column sort-asc"  width="5%">{{ trans("cruds.control.fields.planned") }}</th>
				            </tr>
				        </thead>
				        <tbody>
	    			@foreach($user->lastControls as $control)
	    				<tr>
				            <td>
                                @foreach($control->measures as $measure)
                                    <a href="/alice/show/{{ $measure->id }}">{{ $measure->clause }}</a>
                                    @if(!$loop->last)
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
	    </div>

    	<div class="row">
            <div class="cell-6">
			@if ((Auth::User()->role==1)||($user->id==Auth::User()->id))
		    <form>
		    	<button class="button primary" onclick='this.form.action="/users/{{ $user->id }}/edit"'>
		            <span class="mif-wrench"></span>
		            &nbsp;
		    		{{ trans('common.edit') }}
		    	</button>
		    </form>
            @endif
			@if (Auth::User()->role==1)
			<form action="/users/{{ $user->id }}" method="post" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
               {{ method_field('delete') }}
               @csrf
	            <button class="button alert" type="submit">
					<span class="mif-fire"></span>
					&nbsp;
	            	{{ trans('common.delete') }}
	            </button>
	        </form>
	        @endif
            <a class="button" href="/users">
				<span class="mif-cancel"></span>
				&nbsp;
	    		{{ trans('common.cancel') }}
            </a>
		</div>
	</div>
@endsection
