@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Effectuer un contrôle" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	@if (count($errors))
		@foreach ($errors->all() as $error)
		<div class="alert alert-danger" role="alert">{{ $error }}</div>
		@endforeach
	@endif

	<form method="POST" action="/control/make" enctype="multipart/form-data">
		@csrf
		<input type="hidden" name="id" value="{{ $control->id }}"/>

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Nom</strong>
		    	</div>
					{{ $control->clause }} - {{ $control->name }}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Objectif</strong>
		    	</div>
				<div class="cell-6">
					{{ $control->objective }}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Attributes</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->attributes }}</pre>
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
						value="{{ count($errors)>0 ?  old('plan_date') : $control->plan_date }}" 
						data-input-format="%Y-%m-%d">

				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Observations</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="observations" rows="5" cols="80">{{ count($errors)>0 ?  old('observations') : $control->observations }}</textarea>
				</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Rapport</strong>
		    		<br>
					<a target="_new" href="/control/template/{{ $control->id }}">Modèle</a>
		    	</div>
				<div class="cell-6">
					<div class="dropzone dropzone-previews" id="dropzoneFileUpload"></div>
				</div>
		    </div>


	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Calcul</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->model }}</pre>
				</div>
			</div>
			
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Note</strong>
		    	</div>
	    		<div class="cell-1">
					<input type="text" data-role="spinner" name="note" value="{{ count($errors)>0 ?  old('note') : $control->note }}">
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Echelle</strong>
		    	</div>
				<div class="cell">
					<pre>{{ $control->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">	    			
		    		<strong>
		    			Score
		    		</strong>
		    	</div>
				<div class="cell">
					<input type="radio" name="score" value="3" data-role="radio" {{ $control->score==3 ? "selected" : "" }} >
					<font color="green">Vert</font> &nbsp;
					<input type="radio" name="score" value="2" data-role="radio" {{ $control->score==2 ? "selected" : "" }}> 
					<font color="orange">Orange</font> &nbsp;
					<input type="radio" name="score" value="1" data-role="radio" {{ $control->score==1 ? "selected" : "" }}> 
					<font color="red">Rouge</font>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Plan d'action</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="action_plan" rows="5" cols="80">{{ $errors->count()>0 ?  old('action_plan') : $control->action_plan }}</textarea>
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
							$control->next_date==null ?
							\Carbon\Carbon::createFromFormat('Y-m-d',$control->plan_date)
								->addMonths($control->periodicity)
								->format('Y-m-d')
							: $control->next_date->format('Y-m-d')
							}}"
						data-input-format="%Y-%m-%d">
				</div>
				<div class="cell-1">
				(
				@if ($control->periodicity==1) Mensuel @endif
				@if ($control->periodicity==3) Triestriel @endif
				@if ($control->periodicity==4) Quadrimestriel @endif
				@if ($control->periodicity==6) Semestriel @endif
				@if ($control->periodicity==12) Annuel @endif
				)
				</div>
			</div>

		<div class="grid">
	    	<div class="row-12">
			<button type="submit" class="button primary" onclick='this.form.action="/control/save"'>Sauver</button>

			<button type="submit" class="button success">Faire</button>

    		<button type="submit" class="button" onclick='this.form.action="/controls";this.form.method="GET";'>Cancel</button>
    		</div>
    	</div>

	</form>
</div>
</div>

<br>
<br>

<script>
Dropzone.options.dropzoneFileUpload = { 
            url: '/doc/store',
	    headers: { 'x-csrf-token': '{{csrf_token()}}' },
	    params: { 'control': '{{ $control->id }}' },
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
                console.log("success response");
                console.log(response);
            },
            error: function(file, response)
            {
                console.log("error response");
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

document.onpaste = function(event){
  const items = (event.clipboardData || event.originalEvent.clipboardData).items;
  for (let index in items) {
    const item = items[index];
    console.log("paste "+item.kind);
    if (item.kind === 'file') {
    // adds the file to your dropzone instance
       console.log("file: "+item.name);
      //Dropzone.options.dropzoneFileUpload.addFile(item.getAsFile())
    }
  }
}
</script>

@endsection
