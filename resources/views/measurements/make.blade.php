@extends("layout")

@section("title")
Effectuer une mesure
@endsection

@section("content")

	@if (count($errors))
		@foreach ($errors->all() as $error)
		<div class="alert alert-danger" role="alert">{{ $error }}</div>
		@endforeach
	@endif

	<form method="POST" action="/measurement/make" enctype="multipart/form-data">
		@csrf
		<input type="hidden" name="id" value="{{ $measurement->id }}"/>

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Nom</strong>
		    	</div>
					{{ $measurement->clause }} - {{ $measurement->name }}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Objectif</strong>
		    	</div>
				<div class="cell-6">
					{{ $measurement->objective }}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Attributes</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $measurement->attributes }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Date de la mesure</strong>
		    	</div>
				<div class="cell-2">
					<input type="text" 
						data-role="calendarpicker" 
						name="realisation_date" 
						value="{{ 
							\Carbon\Carbon::now()
							->format('Y-m-d')
							}}" 
						data-input-format="%Y-%m-%d">
				</div>
				<div class="cell-2">
					<b>Date de planification</b>
				</div>
				<div class="cell-2">				
					<input type="text" 
						data-role="calendarpicker" 
						name="plan_date"
						value="{{ $measurement->plan_date }}" 
						data-input-format="%Y-%m-%d">

				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Observations</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="observations" rows="5" cols="80">{{ $errors->has('observations') ?  old('observations') : $measurement->observations }}</textarea>
				</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Rapport</strong>
		    		<br>
					<a target="_new" href="/measurement/template/{{ $measurement->id }}">Modèle</a>
		    	</div>
				<div class="cell-6">
					<div class="dropzone dropzone-previews" id="dropzoneFileUpload"></div>
				</div>
		    </div>


	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Fonction</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $measurement->model }}</pre>
				</div>
			</div>
			
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Note</strong>
		    	</div>
	    		<div class="cell-1">
					<input type="text" data-role="spinner" name="note" value="{{ $measurement->note }}">
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Echelle</strong>
		    	</div>
				<div class="cell">
					<pre>{{ $measurement->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">	    			
		    		<strong>
		    			Score
		    		</strong>
		    	</div>
				<div class="cell">
					<input type="radio" name="score" value="3" data-role="radio" {{ $measurement->score==3 ? "selected" : "" }} >
					<font color="green">Vert</font> &nbsp;
					<input type="radio" name="score" value="2" data-role="radio" {{ $measurement->score==2 ? "selected" : "" }}> 
					<font color="orange">Orange</font> &nbsp;
					<input type="radio" name="score" value="1" data-role="radio" {{ $measurement->score==1 ? "selected" : "" }}> 
					<font color="red">Rouge</font>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Plan d'action</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="action_plan" rows="5" cols="80">{{ $errors->has('action_plan') ?  old('action_plan') : $measurement->action_plan }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Prochaine revue</strong>
		    	</div>
				<div class="cell-2">
					<input type="text" 
						data-role="calendarpicker" 
						name="next_date" 
						value="{{ 
							$measurement->next_date==null ?
							\Carbon\Carbon::createFromFormat('Y-m-d',$measurement->plan_date)
								->addMonths($measurement->periodicity)
								->format('Y-m-d')
							: $measurement->next_date->format('Y-m-d')
							}}"
						data-input-format="%Y-%m-%d">
				</div>
				<div class="cell-1">
				(
				@if ($measurement->periodicity==1) Mensuel @endif
				@if ($measurement->periodicity==3) Triestriel @endif
				@if ($measurement->periodicity==4) Quadrimestriel @endif
				@if ($measurement->periodicity==6) Semestriel @endif
				@if ($measurement->periodicity==12) Annuel @endif
				)
				</div>
			</div>

		<div class="grid">
	    	<div class="row-12">
			<button type="submit" class="button primary" onclick='this.form.action="/measurement/save"'>Sauver</button>

			<button type="submit" class="button success">Faire</button>

    		<button type="submit" class="button" onclick='this.form.action="/measurements";this.form.method="GET";'>Cancel</button>
    		</div>
    	</div>

	</form>
<br>
<br>

<script>
Dropzone.options.dropzoneFileUpload = { 
            url: '/doc/store',
            headers: { 'x-csrf-token': '{{csrf_token()}}'},
            maxFilesize: 10,
            // acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            timeout: 50000,
            removedfile: function(file) 
            {
                console.log("remove file " + file.name + " " + file.id);
                $.ajax({
                    headers: {
                      'X-CSRF-TOKEN': '{{csrf_token()}}'
                       },
                    type: 'GET',
                    url: '{{ url( "/doc/delete" ) }}'+"/"+file.id,
                    success: function (data){
                        console.log("File has been successfully removed");
                    },
                    error: function(e) {
                        console.log("File not removed");
                        console.log(e);
                    }});
                    // console.log('{{ url( "/doc/delete" ) }}'+"/"+file.id+']');
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
       
            success: function(file, response) 
            {
                file.id=response.id;
                console.log("respose");
                console.log(response);
            },
            error: function(file, response)
            {
                console.log(response);
               return false;
            },
        init: function () {
            //Add existing files into dropzone            
            var existingFiles = [
            @foreach ($documents as $document) 
                { name: "{{ $document->filename }}", size: {{ $document->size }}, id: {{ $document->id }} },
            @endforeach
            ];
            for (i = 0; i < existingFiles.length; i++) {
                this.emit("addedfile", existingFiles[i]);                
                this.emit("complete", existingFiles[i]);                
            }            
        	}

    	}

</script>

@endsection