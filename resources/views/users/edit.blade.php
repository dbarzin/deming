@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Modifier un utilisateur" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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

		<form method="POST" action="/users/{{ $user->id }}">
		@method("PATCH")
		@csrf
			<ul class="form-style-1">
				<li>
					<label>Login</label>
					<input type="text" class="input {{ $errors->has('login') ? 'is-danger' : ''}}" name="login" value="{{ $errors->has('login') ?  old('login') : $user->login }}" size='40'>
				</li>
				<li>
					<label>Nom</label>
					<input type="text" class="input {{ $errors->has('name') ? 'is-danger' : ''}}" name="name" value="{{ $errors->has('name') ?  old('name') : $user->name }}" size='80'>
				</li>
				<li>
					<label>Title</label>
					<input type="text" class="input {{ $errors->has('title') ? 'is-danger' : ''}}" name="title" value="{{ $errors->has('title') ?  old('title') : $user->title }}" size='80'>
				</li>
				@if (Auth::User()->role==1)
				<li>
					<label>Role</label>
					<select name="role" class="input">
					    <option value="1" {{ $user->role==1 ? "selected" : "" }}>CISO</option>
					    <option value="2" {{ $user->role==2 ? "selected" : "" }}>SecOps</option>
					    <option value="3" {{ $user->role==3 ? "selected" : "" }}>Auditor</option>
					</select>
				</li>
				@endif
				<li>
					<label class="label" for="description">eMail</label>
					<input type="text" name="email" class="input {{ $errors->has('email') ? 'is-danger' : ''}}" value="{{ $errors->has('email') ?  old('email') : $user->email }}" size="120">
				</li>
				<li>
					<label class="label" for="description">Mot de passe</label>
					<input type="password" name="password1"/>	
					<input type="password" name="password2"/>	
				</li>
				<li>
				</li>
			</ul>
			@if (Auth::User()->role==1)
				<button type="submit" class="button success">Sauver</button>
			@endif
			</form>
	        &nbsp;
			@if (Auth::User()->role==1)
			<form action="/users/{{ $user->id }}" method="post">
	               {{ method_field('delete') }}
	               @csrf
	            <button class="button alert" type="submit">Supprimer</button>
		        &nbsp;
	        </form>
	        @endif
			<form>
	    		<button type="submit" class="button" onclick='this.form.action="/users/{{ $user->id }}";this.form.method="GET";'><span class="mif-cancel"></span> Cancel</button>
			</form>
		</div>
	</div>	
@endsection



