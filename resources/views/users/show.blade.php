@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.user.show') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

		<div class="grid">

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.login') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $user->login }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.name') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $user->name }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.title') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $user->title }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.language') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $user->language }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.role') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $user->role==1 ? trans('cruds.user.roles.admin') : "" }}
		    		{{ $user->role==2 ? trans('cruds.user.roles.user') : "" }}
		    		{{ $user->role==3 ? trans('cruds.user.roles.auditor') : "" }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.user.fields.email') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $user->email }}
	    		</div>
	    	</div>

	    	<div class="row">
	    		<div class="cell-2">
		    		<strong>{{ trans('cruds.user.fields.controls') }}</strong>
		    	</div>
		    </div>
	    	<div class="row">
	    		<div class="cell-8">
    				<table class="table">
	    			@foreach($user->controls as $control)
	    				@if($control->realisation_date==null)
	    				<tr>
		    				<td>
					    		<a href="/measures/{{ $control->measure_id }}">{{ $control->clause }}</a> &nbsp; - &nbsp; {{ $control->name }}		    					
		    				</td>
		    				<td>
				                <a id="{{ $control->plan_date }}" href="/control/show/{{$control->id}}">
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
		    			@endif
	    			</tr>
	    			@endforeach
	    			</table>
	    		</div>
	    	</div>

	    </div>

		<div class="form-group">
		    <form action="">
				@if ((Auth::User()->role==1)||($user->id==Auth::User()->id))
		    	<button class="button primary" onclick='this.form.action="/users/{{ $user->id }}/edit"';>
		            <span class="mif-wrench"></span>
		            &nbsp;
		    		{{ trans('common.edit') }}
		    	</button>
		    	&nbsp;
		    	@endif
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