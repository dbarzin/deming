@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption='{{ trans("cruds.control.edit")}}' data-collapsible="true" data-title-icon="<span class='mif-pencil'></span>">
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
			    		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
					</div>
		    		<div class="cell-6">
						<select data-role="select" name="measures[]" multiple>
							@foreach($all_measures as $measure)
							    <option value="{{ $measure->id }}"
                                    {{ in_array($measure->id, old("measures", $measures)) ? "selected" : "" }}>
                                        {{ $measure->clause }}
                                </option>
						    @endforeach
						 </select>
					</div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.name") }}</strong>
		    		</div>
		    		<div class="cell-4">
						<input type="text" data-role="input" name="name" value="{{ $control->name }}" maxlength="255">
					</div>
		    		<div class="cell-1" align="right">
			    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
                    </div>
		    		<div class="cell-1">
						<input type="text" name="scope" data-role="input" autocomplete="off" maxlength="32"
						value="{{ $control->scope }}" data-autocomplete=" {{ implode(",",$scopes) }} "/>
					</div>
				</div>


		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.objective") }}</strong>
			    	</div>
					<div class="cell-6">
						<textarea name="objective" id="mde1">{{ $errors->has('objective') ?  old('objective') : $control->objective }}</textarea>
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
                        <textarea name="input" id="mde2">{{ $errors->has('input') ?  old('input') : $control->input }}</textarea>
					</div>
				</div>
				<div class="row">
                    <div class="cell-1">
                        <strong>{{ trans('cruds.control.fields.model') }}</strong>
                    </div>
                    <div class="cell-6">
                        <textarea class="textarea" name="model" rows="3" data-role="textarea" data-clear-button="false">{{ $errors->has('model') ?  old('model') : $control->model }}</textarea>
                    </div>
                </div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.plan_date") }}</strong>
			    	</div>
					<div class="cell-2">
						<input type="text" data-role="calendarpicker" name="plan_date" value="{{$control->plan_date}}" data-input-format="%Y-%m-%d">
					</div>
					<div class="cell-1">
                    </div>
		    		<div class="cell-1" align="right">
			    		<strong>{{ trans("cruds.control.fields.realisation_date") }}</strong>
			    	</div>
					<div class="cell-2">
						<input type="text" data-role="calendarpicker" name="realisation_date" data-clear-button="true"
                            value="{{$control->realisation_date}}" data-input-format="%Y-%m-%d" />
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
						<input type="text" data-role="spinner" name="note" data-min-value="0" data-max-value="100"
                        value="{{ count($errors)>0 ?  old('note') : $control->note }}">
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
						<textarea name="action_plan" id="mde3">{{ $errors->has('action_plan') ?  old('action_plan') : $control->action_plan }}</textarea>
					</div>
				</div>


				<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
			    	</div>
					<div class="cell-2">
						<select data-role="select" name="periodicity">
						    <option value="0" {{ $control->periodicity==0 ? "selected" : ""}}>{{ trans('common.once') }}</option>
						    <option value="1" {{ $control->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
						    <option value="3" {{ $control->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
						    <option value="6" {{ $control->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
						    <option value="12" {{ $control->periodicity==12 ? "selected" : ""}}>{{ trans('common.annually') }}</option>
						 </select>
					</div>
		    		<div class="cell-1" align="right">
			    		<strong>Status</strong>
		    		</div>
					<div class="cell-1">
						<select data-role="select" name="status">
							<option value="0" {{ $control->status==0 ? "selected" : ""}}>Todo</option>
							<option value="1" {{ $control->status==1 ? "selected" : ""}}>Proposed</option>
							<option value="2" {{ $control->status==2 ? "selected" : ""}}>Done</option>
						 </select>
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

				<div class="row">
                    <div class="cell-1">
                        <strong>{{ trans('cruds.control.fields.owners') }}</strong>
                    </div>
                    <div class="cell-4">
                        <select data-role="select" name="owners[]" id="owners" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (in_array($user->id, old('owners', [])) || $control->owners->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
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
document.addEventListener("DOMContentLoaded", function () {

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

    const mde1 = new EasyMDE({
        element: document.getElementById('mde1'),
        minHeight: "200px",
        maxHeight: "200px",
        status: false,
        spellChecker: false,
        });

    const mde2 = new EasyMDE({
        element: document.getElementById('mde2'),
        minHeight: "200px",
        maxHeight: "200px",
        status: false,
        spellChecker: false,
        });

    const mde3 = new EasyMDE({
        element: document.getElementById('mde3'),
        minHeight: "200px",
        maxHeight: "200px",
        status: false,
        spellChecker: false,
        });
});
</script>
@endsection
