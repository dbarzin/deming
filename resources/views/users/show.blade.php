@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.user.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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

	    </div>

		<div class="form-group">
		    <form action="">
				@if ((Auth::User()->role==1)||($user->id==Auth::User()->id))
		    	<button class="button primary" onclick='this.form.action="/users/{{ $user->id }}/edit"';>{{ trans('common.edit') }}</button>
		    	@endif
		    	<button class="button" onclick='this.form.action="/users";'>{{ trans('common.cancel') }}</button>
			</form>
		</div>
	</div>
</div>


@endsection