@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.user.add') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

		@if (count($errors))
		<div class= “form-group”>
			<div class= “alert alert-danger”>
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
		</div>
		@endif

		<form method="POST" action="/users">
		@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.user.fields.login') }}</label>
			    	</div>
		    		<div class="cell-2">
						<input type="text" class="input {{ $errors->has('login') ? 'is-danger' : ''}}" name="login" value="{{ old('login') }}" size='40'>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.user.fields.name') }}</label>
			    	</div>
		    		<div class="cell-3">
						<input type="text" class="input {{ $errors->has('name') ? 'is-danger' : ''}}" name="name" value="{{ old('name') }}" size='40'>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.user.fields.title') }}</label>
					</div>
		    		<div class="cell-3">
						<input type="text" class="input {{ $errors->has('title') ? 'is-danger' : ''}}" name="title" value="{{ old('title') }}" size='80'>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.user.fields.language') }}</label>
					</div>
		    		<div class="cell-3">
		    			<select name='language' value='language'>
		    				<option {{ old('language')=='en' ? 'selected' : ''}} >en</option>
		    				<option {{ old('language')=='fr' ? 'selected' : ''}} >fr</option>
		    			</select>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label>{{ trans('cruds.user.fields.role') }}</label>
					</div>
		    		<div class="cell-3">
						<select name="role" class="input">
							<option></option>
						    <option value="1" {{ old('role')==1 ? "selected" : "" }}>{{ trans('cruds.user.roles.admin') }}</option>
						    <option value="2" {{ old('role')==2 ? "selected" : "" }}>{{ trans('cruds.user.roles.user') }}</option>
						    <option value="3" {{ old('role')==3 ? "selected" : "" }}>{{ trans('cruds.user.roles.auditor') }}</option>
						    <option value="4" {{ old('role')==4 ? "selected" : "" }}>{{ trans('cruds.user.roles.api') }}</option>
						</select>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
						<label class="label" for="description">{{ trans('cruds.user.fields.email') }}</label>
					</div>
		    		<div class="cell-3">
						<input type="text" name="email" class="input {{ $errors->has('email') ? 'is-danger' : ''}}" value="{{ old('email') }}" size="120">
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
	    	<div class="row">
	    		<div class="cell-4">
					<button type="submit" class="button success">
			            <span class="mif-floppy-disk"></span>
						&nbsp;
						{{ trans('common.save') }}
					</button>
					<button type="submit" class="button" onclick="this.form.method='GET';">
						<span class="mif-cancel"></span> 
						&nbsp;
						{{ trans('common.cancel') }}
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection

