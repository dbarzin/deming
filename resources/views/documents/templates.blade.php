@extends("layout")

@section("title")
Modèles de document
@endsection

@section("content")

<div class="p-3">
    <div data-role="panel" data-title-caption="Documents" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

		@if(session('message'))
		<div class="remark success">
			<p>{{ session('message') }}</p>
		</div>	    
		@endif

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


		<form action="/doc/template" method="POST" role="form" enctype="multipart/form-data">
	    @csrf
			<ul>
				<li>
					<a href="/doc/template?id=1" target="_new">Modèle de Contrôle</a>
			        	<input type="file" data-role="file" name="template1"> <br>
				</li>

				<li>
					<a href="/doc/template?id=2" target="_new">Rapport de pilotage</a>
			        	<input type="file" data-role="file" name="template2"><br>
				</li>
			</ul>

			<button type="submit" class="button success"><span class="mif-ok"></span> Sauver</button>
		    <button type="submit" class="button cancel" onclick='this.form.method="GET";this.form.action="/";'><span class="mif-cancel"></span> Annuler</button>

		</form>

	</div>
</div>
@endsection