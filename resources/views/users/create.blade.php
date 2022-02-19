@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Ajouter un utilisateur" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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
			<ul class="form-style-1">
				<li>
					<label>Login</label>
					<input type="text" class="input {{ $errors->has('login') ? 'is-danger' : ''}}" name="login" value="{{ old('login') }}" size='40'>
				</li>
				<li>
					<label>Nom</label>
					<input type="text" class="input {{ $errors->has('name') ? 'is-danger' : ''}}" name="name" value="{{ old('name') }}" size='40'>
				</li>
				<li>
					<label>Title</label>
					<input type="text" class="input {{ $errors->has('title') ? 'is-danger' : ''}}" name="title" value="{{ old('title') }}" size='80'>
				</li>
				<li>
					<label>Role</label>
					<select name="role" class="input">
						<option></option>
					    <option value="1" {{ old('role')==1 ? "selected" : "" }}>Admin</option>
					    <option value="2" {{ old('role')==2 ? "selected" : "" }}>User</option>
					    <option value="3" {{ old('role')==3 ? "selected" : "" }}>Auditor</option>
					</select>
				</li>
				<li>
					<label class="label" for="description">eMail</label>
					<input type="text" name="email" class="input {{ $errors->has('email') ? 'is-danger' : ''}}" value="{{ old('email') }}" size="120">
				</li>
				<li>
					<label class="label" for="description">Mot de passe</label>
					<input type="password" name="password1"/>	
					<input type="password" name="password2"/>	
				</li>
				<li>
				</li>
			</ul>

	    	<div class="row">
	    		<div class="cell-5">
					<button type="submit" class="button success">Sauver</button>
					<button type="submit" class="button" onclick='this.form.action="/users";'>Annuler</button>
				</div>
			</div>		
		</form>
	</div>
</div>

@endsection

