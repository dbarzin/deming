@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption='{{ trans("cruds.control.edit")}}' data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		@if (count($errors))
		<div class="grid">
		    <div class="cell-3 bg-red fg-white">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
		</div>
		@endif

		<form method="POST" action="/bob/save">
			@csrf
			<input type="hidden" name="id" value="{{ $control->id }}"/>

			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.name") }}</strong>
		    		</div>
		    		<div class="cell-4">
			    		{{ $control->clause }} &nbsp;
						<input type="text" data-role="input" name="name" value="{{ $control->name }}" size="30">
					</div>
		    		<div class="cell-2">
			    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
			    	&nbsp;
						<input type="text" name="scope" data-role="input" autocomplete="off" size="5"
						value="{{ $control->scope }}" data-autocomplete=" {{ implode(",",$scopes) }} "/>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.objective") }}</strong>
			    	</div>
					<div class="cell-6">
						<textarea name="objective" rows="5" data-role="textarea" data-clear-button="false">{{ $errors->has('objective') ?  old('objective') : $control->objective }}</textarea>
					</div>
				</div>

				<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.control.fields.attributes') }}</strong>
			    	</div>
					<div class="cell-6">
						<select data-role="select" name="attributes[]" multiple>
							@foreach($attributes as $attribute)
								@if (strlen($attribute)>0)
							    <option {{ str_contains($control->attributes, $attribute) ? "selected" : ""}}>{{$attribute}}</option>
							    @endif
						    @endforeach
						 </select>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.input") }}</strong>
			    	</div>
					<div class="cell-6">
						<textarea name="input" rows="5" data-role="textarea" data-clear-button="false">{{ $errors->has('input') ?  old('input') : $control->input }}</textarea>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.plan_date") }}</strong>
			    	</div>
					<div class="cell-2">
						<input type="text" data-role="calendarpicker" name="plan_date" value="{{$control->plan_date}}"
						data-input-format="%Y-%m-%d">
					</div>
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.realisation_date") }}</strong>
			    	</div>
					<div class="cell-2">
						<input type="text" data-role="calendarpicker" name="realisation_date" value="{{$control->realisation_date}}"
					data-input-format="%Y-%m-%d">
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.observations") }}</strong>
			    	</div>
					<div class="cell-6">
						<textarea name="observations" rows="5" data-role="textarea" data-clear-button="false">{{ $errors->has('observations') ?  old('observations') : $control->observations }}</textarea>
					</div>
			    </div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.evidence") }}</strong>
			    	</div>
					<div class="cell-6">
						<div class="dropzone dropzone-previews" id="dropzoneFileUpload"></div>
					</div>
			    </div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.note") }}</strong>
			    	</div>
		    		<div class="cell-1">
						<input type="text" data-role="spinner" name="note" value="{{ count($errors)>0 ?  old('note') : $control->note }}">
		    		</div>
			    </div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.indicator") }}</strong>
			    	</div>
					<div class="cell">
						<pre>{{ $control->indicator }}</pre>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.score") }}</strong>
			    	</div>
					<div class="cell">
						<input type="radio" name="score" value="3" data-role="radio" {{ ($control->score==3) ? 'checked' : '' }}>
						<font color="green">{{ trans("common.green") }}</font> &nbsp;
						<input type="radio" name="score" value="2" data-role="radio" {{ ($control->score==2) ? 'checked' : '' }}>
						<font color="orange">{{ trans("common.orange") }}</font> &nbsp;
						<input type="radio" name="score" value="1" data-role="radio" {{ ($control->score==1) ? 'checked' : '' }}>
						<font color="red">{{ trans("common.red") }}</font>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
			    	</div>
					<div class="cell-6">
						<textarea name="action_plan" rows="5" data-role="textarea" data-clear-button="false">{{ $errors->has('action_plan') ?  old('action_plan') : $control->action_plan }}</textarea>
					</div>
				</div>


				<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
			    	</div>
					<div class="cell-3">
						<select data-role="select" name="periodicity">
						    <option value="1" {{ $control->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
						    <option value="3" {{ $control->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
						    <option value="6" {{ $control->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
						    <option value="12" {{ $control->periodicity==12 ? "selected" : ""}}>{{ trans('common.annually') }}</option>
						 </select>
					</div>
		    		<div class="cell-1">
		    		</div>
		    		<div class="cell-1" align="right">
			    		<strong>Next ID</strong>
			    	</div>
					<div class="cell-1">
						<select data-role="select" name="next_id">
							<option></option>
							@foreach($ids as $id)
							    <option {{ $control->next_id === $id ? "selected" : ""}}>{{$id}}</option>
						    @endforeach
						 </select>
					</div>
				</div>

		    	<div class="row-12">
		    		<div><br></div>
		    	</div>

		    	<div class="row-12">
					<button type="submit" class="button success">
			            <span class="mif-floppy-disk"></span>
			            &nbsp;
						{{ trans("common.save") }}
					</button>
					</form>
					&nbsp;
		    		<form action="/bob/show/{{$control->id}}">
			    		<button type="submit" class="button cancel">
			    			<span class="mif-cancel"></span>
			    			&nbsp;
			    			{{ trans("common.cancel") }}
			    		</button>
			    	</form>
	    		</div>
	    	</div>
	    </div>
	</form>
</div>

<script>
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone("div#dropzoneFileUpload", {
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
	            url: '/doc/delete/'+file.id,
	            success: function (data) {
	                console.log("File has been successfully removed");
	            	},
	            error: function(e) {
	                console.log("File not removed");
	                console.log(e);
	            	}
	            });
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
	});

    document.onpaste = function(event) {
      const items = (event.clipboardData || event.originalEvent.clipboardData).items;
      items.forEach((item) => {
      	console.log(item.kind);
        if (item.kind === 'file') {
          	// adds the file to your dropzone instance
          	myDropzone.addFile(item.getAsFile())
        	}
      	})
    }
</script>
@endsection
