@extends("layout")

@section("title")
Domaine
@endsection

@section("content")
	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Titre</strong>
	    	</div>
    		<div class="cell">
	    		{{ $domain->title }}
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>Description</strong>
	    	</div>
    		<div class="cell">
	    		{{ $domain->description }}
    		</div>
    	</div>

    </div>

	<div class="form-group">
	    <form action="">
	    	<button class="button primary" onclick='this.form.action="/domains/{{ $domain->id }}/edit"';>Edit</button>
	    	<button class="button" onclick='this.form.action="/domains";'>Cancel</button>
		</form>
	</div>


@endsection