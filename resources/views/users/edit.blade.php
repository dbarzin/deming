@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.user.edit') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

		@if (count($errors))
		<div class="grid">
		    <div class="cell-3 bg-red fg-white">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
		</div>
		@endif

		<form method="POST" action="/users/{{ $user->id }}">
		@method("PATCH")
		@csrf
		<div class="grid">

	    	<div class="row">
	    		<div class="cell-1">
					<label>{{ trans('cruds.user.fields.login') }}</label>
				</div>
	    		<div class="cell-3">
					<input type="text" class="input {{ $errors->has('login') ? 'is-danger' : ''}}" name="login" value="{{ $errors->has('login') ?  old('login') : $user->login }}" size='40'>
				</div>
			</div>


	    	<div class="row">
	    		<div class="cell-1">
					<label>{{ trans('cruds.user.fields.name') }}</label>
				</div>
	    		<div class="cell-3">
					<input type="text" class="input {{ $errors->has('name') ? 'is-danger' : ''}}" name="name" value="{{ $errors->has('name') ?  old('name') : $user->name }}" size='80'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<label>{{ trans('cruds.user.fields.title') }}</label>
				</div>
	    		<div class="cell-3">
					<input type="text" class="input {{ $errors->has('title') ? 'is-danger' : ''}}" name="title" value="{{ $errors->has('title') ?  old('title') : $user->title }}" size='80'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<label>{{ trans('cruds.user.fields.language') }}</label>
				</div>
	    		<div class="cell-3">
	    			<select name='language' value='language'>
	    				<option {{ (count($errors)>0 ?  old('language') : $user->language) =='en' ? 'selected' : ''}} >en</option>
                        <option {{ (count($errors)>0 ?  old('language') : $user->language) =='fr' ? 'selected' : ''}} >fr</option>
	    			</select>
				</div>
			</div>
			@if (Auth::User()->role==1)
	    	<div class="row">
	    		<div class="cell-1">
					<label>{{ trans('cruds.user.fields.role') }}</label>
				</div>
	    		<div class="cell-3">
					<select name="role" class="input">
					    <option value="1" {{ $user->role==1 ? "selected" : "" }}>{{ trans('cruds.user.roles.admin') }}</option>
					    <option value="2" {{ $user->role==2 ? "selected" : "" }}>{{ trans('cruds.user.roles.user') }}</option>
                        <option value="5" {{ $user->role==5 ? "selected" : "" }}>{{ trans('cruds.user.roles.auditee') }}</option>
					    <option value="3" {{ $user->role==3 ? "selected" : "" }}>{{ trans('cruds.user.roles.auditor') }}</option>
					    <option value="4" {{ $user->role==4 ? "selected" : "" }}>{{ trans('cruds.user.roles.api') }}</option>
					</select>
				</div>
			</div>
			@endif
	    	<div class="row">
	    		<div class="cell-1">
					<label class="label" for="description">{{ trans('cruds.user.fields.email') }}</label>
				</div>
	    		<div class="cell-3">
					<input type="text" name="email" class="input {{ $errors->has('email') ? 'is-danger' : ''}}" value="{{ $errors->has('email') ?  old('email') : $user->email }}" size="120">
				</div>
			</div>

	        @if (Config::get('app.ldap_domain') === null)
	    	<div class="row">
	    		<div class="cell-1">
					<label class="label" for="description">{{ trans('cruds.user.fields.password') }}</label>
				</div>
	    		<div class="cell-5">
					<input type="password" name="password1"/>
					<input type="password" name="password2"/>
				</div>
			</div>
			@endif

		</div>
        @if ((Auth::User()->role === 1)||(Auth::User()->role === 2)) 
    	<div class="row">
    		<div class="cell-2">
	    		<strong>{{ trans('cruds.user.fields.controls') }}</strong>
	    	</div>
	    </div>

    	<div class="row">
    		<div class="cell-8">
	            <select data-role="select" name="controls[]" id="controls" multiple>
	                @foreach($controls as $control)
	                       <option value="{{ $control->id }}" {{ (in_array($control->id, old('controls', [])) || ($user->controls->contains($control->id))) ? 'selected' : '' }}>{{ $control->clause }}</option>
	                @endforeach
	            </select>

    		</div>
    	</div>
        @endif
    	<div class="row">
    		<div class="cell-1">
    		</div>
    	</div>
			@if ((Auth::User()->role==1)||(Auth::User()->id===$user->id))
				<button type="submit" class="button success">
		            <span class="mif-floppy-disk"></span>
					&nbsp;
					{{ trans('common.save') }}
				</button>
		        &nbsp;
			@endif
			</form>
			<form action="/users/{{ $user->id }}">
	    		<button type="submit" class="button">
	    			<span class="mif-cancel"></span>
	    			&nbsp;
	    			{{ trans('common.cancel') }}</button>
			</form>
		</div>
	</div>
@endsection
