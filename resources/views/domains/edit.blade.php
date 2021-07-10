@extends("layout")

@section("title")
Modifier un domaine
@endsection

@section("content")
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

	<form method="POST" action="/domains/{{ $domain->id }}">
	@method("PATCH")
	@csrf
		<ul class="form-style-1">
			<li>
				<label>Title</label>
				<input type="text" class="input {{ $errors->has('title') ? 'is-danger' : ''}}" name="title" value="{{ $errors->has('title') ?  old('title') : $domain->title }}" size='5'>
			</li>
			<li>
				<label class="label" for="description">Description</label>
				<textarea name="description" rows="5" cols="80">{{ $errors->has('description') ?  old('description') : $domain->description }}</textarea>
			</li>
			<li>
			</li>
		</ul>
		<button type="submit" class="button success">Sauver</button>
		</form>
        &nbsp;
		<form action="/domains/{{ $domain->id }}" method="post">
               {{ method_field('delete') }}
               @csrf
            <button class="button alert" type="submit">Supprimer</button>
        </form>
        &nbsp;
		<form>
    		<button type="submit" class="button" onclick='this.form.action="/domains/{{ $domain->id }}";this.form.method="GET";'>Cancel</button>
		</form>
@endsection



