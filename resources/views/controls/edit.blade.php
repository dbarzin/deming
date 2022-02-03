@extends("layout")

@section("title")
Edit Measurement
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

	<form method="POST" action="/control/save">
		@csrf
		<input type="hidden" name="id" value="{{ $control->id }}"/>

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Nom</strong>
		    	</div>
				<div class="cell-6">
		    		<a href="/controls/{{ $control->measure_id}}">{{ $control->clause }}</a>
					<input type="text" name="name" value="{{ $control->name }}">
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Objectif</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="objective" rows="5" cols="80">{{ $errors->has('objective') ?  old('objective') : $control->objective }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Attributs</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="attributes" rows="5" cols="80">{{ $errors->has('attributes') ?  old('attributes') : $control->attributes }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Date de planification</strong>
		    	</div>
				<div class="cell-2">
					<input type="text" data-role="calendarpicker" name="plan_date" value="{{$control->plan_date}}"
					data-input-format="%Y-%m-%d"> 
				</div>
	    		<div class="cell-1">
		    		<strong>Date de réalisation</strong>
		    	</div>
				<div class="cell-2">
					<input type="text" data-role="calendarpicker" name="realisation_date" value="{{$control->realisation_date}}" 
				data-input-format="%Y-%m-%d"> 
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Observations</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="observations" rows="5" cols="80">{{ $errors->has('observations') ?  old('observations') : $control->observations }}</textarea>
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
		    		<strong>Note</strong>
		    	</div>
	    		<div class="cell-2">
					<input type="text" data-role="spinner" name="note" value="{{ $control->note }}">
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Fonction</strong>
		    	</div>
				<div class="cell">
					<pre>{{ $control->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Score</strong>
		    	</div>
				<div class="cell">
					<input type="radio" name="score" value="3" data-role="radio" {{ ($control->score==3) ? 'checked' : '' }}> 
					<font color="green">Vert</font> &nbsp;
					<input type="radio" name="score" value="2" data-role="radio" {{ ($control->score==2) ? 'checked' : '' }}> 
					<font color="orange">Orange</font> &nbsp;
					<input type="radio" name="score" value="1" data-role="radio" {{ ($control->score==1) ? 'checked' : '' }}> 
					<font color="red">Rouge</font>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Plan d'action</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="action_plan" rows="5" cols="80">{{ $errors->has('action_plan') ?  old('action_plan') : $control->action_plan }}</textarea>
				</div>
			</div>
<!--
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>Prochaine revue</strong>
		    	</div>
				<div class="cell-3">
					<input data-role="datepicker" name="next_date" value="{{ 
				\Carbon\Carbon
				::createFromFormat('Y-m-d',$control->plan_date)
				->addMonths($control->periodicity)
				->format('Y-m-d')
				}}" 
				data-input-format="%d/%m/%y">
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
-->
		<div class="grid">
	    	<div class="row-12">
			<button type="submit" class="button success">Save</button>

    		<button type="submit" class="button cancel" onclick='this.form.action="/controls";this.form.method="GET";'><span class="mif-cancel"></span> Cancel</button>
    		</div>
    	</div>

	</form>

<script>
Dropzone.options.dropzoneFileUpload = { 
            url: '/doc/store',
            headers: { 'x-csrf-token': '{{csrf_token()}}'},
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
