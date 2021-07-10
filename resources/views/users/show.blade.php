@extends("layout")

@section("title")
Utilisateur
@endsection

@section("content")
	<div class="grid">

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Login</strong>
	    	</div>
    		<div class="cell">
	    		{{ $user->login }}
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Nom</strong>
	    	</div>
    		<div class="cell">
	    		{{ $user->name }}
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Title</strong>
	    	</div>
    		<div class="cell">
	    		{{ $user->title }}
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Role</strong>
	    	</div>
    		<div class="cell">
	    		{{ $user->role==1 ? "CISO" : "" }}
	    		{{ $user->role==2 ? "Security" : "" }}
	    		{{ $user->role==3 ? "Auditor" : "" }}
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>eMail</strong>
	    	</div>
    		<div class="cell">
	    		{{ $user->email }}
    		</div>
    	</div>

    </div>

	<div class="form-group">
	    <form action="">
			@if ((Auth::User()->role==1)||($user->id==Auth::User()->id))
	    	<button class="button primary" onclick='this.form.action="/users/{{ $user->id }}/edit"';>Edit</button>
	    	@endif
	    	<button class="button" onclick='this.form.action="/users";'>Cancel</button>
		</form>
	</div>


@endsection