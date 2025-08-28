@extends("layout")

@section("content")
<div data-role="panel" data-title-caption='{{ trans("cruds.control.edit")}}' data-collapsible="false" data-title-icon="<span class='mif-paste'></span>">

    @include('partials.errors')

	<form method="POST" action="/bob/save">
		@csrf
		<input type="hidden" name="id" value="{{ $control->id }}"/>

		<div class="grid">

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
				</div>
	    		<div class="cell-lg-6 cell-md-10">
                    <select id="measures" name="measures[]" data-role="select" data-filter="true" multiple>
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
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.name") }}</strong>
	    		</div>
                <div class="cell-lg-4 cell-md-6">
					<input type="text" data-role="input" name="name" value="{{ $control->name }}" maxlength="255">
				</div>
                <div class="cell-lg-1 cell-md-1" align="right">
		    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
                </div>
                <div class="cell-lg-1 cell-md-3">
					<input type="text" name="scope" data-role="input" autocomplete="off" maxlength="32"
					value="{{ $control->scope }}" data-autocomplete=" {{ implode(",",$scopes) }} "/>
				</div>
			</div>


	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.objective") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-10">
                    <textarea name="objective" class="easymde" id="objective">{{ $errors->has('objective') ?  old('objective') : $control->objective }}</textarea>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans('cruds.control.fields.attributes') }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-10">
                    <select data-role="select" id="attributes" data-filter="true" name="attributes[]" multiple>
						@foreach($attributes as $attribute)
							@if (strlen($attribute)>0)
						    <option {{ str_contains($control->attributes, $attribute) ? "selected" : ""}}>{{$attribute}}</option>
						    @endif
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.input") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-10">
                    <textarea name="input" class="easymde" id="input">{{ $errors->has('input') ?  old('input') : $control->input }}</textarea>
				</div>
			</div>
			<div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.control.fields.model') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-10">
                    <textarea class="textarea" name="model" rows="3" data-role="textarea" data-clear-button="false">{{ $errors->has('model') ?  old('model') : $control->model }}</textarea>
                </div>
            </div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.plan_date") }}</strong>
		    	</div>
				<div class="cell-lg-2 cell-md-3">
					<input
					data-role="calendarpicker"
					data-format="YYYY-MM-DD"
					name="plan_date"
					value="{{ $control->plan_date }}"
					/>
				</div>
                <div class="cell-lg-1 cell-md-2">
                </div>
                <div class="cell-lg-1 cell-md-2" align="right">
		    		<strong>{{ trans("cruds.control.fields.realisation_date") }}</strong>
		    	</div>
				<div class="cell-lg-2 cell-md-3">
					<input
					data-role="calendarpicker"
					data-format="YYYY-MM-DD"
					name="realisation_date"
					value="{{ $control->realisation_date == '0000-00-00' ? null : $control->realisation_date}}"
					data-clear-button="true"
					/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.observations") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-10">
					<textarea name="observations" rows="5" data-role="textarea" data-clear-button="false">{{ $errors->has('observations') ?  old('observations') : $control->observations }}</textarea>
				</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.evidence") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-10">
					<div class="dropzone dropzone-previews" id="dropzoneFileUpload"></div>
				</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.note") }}</strong>
		    	</div>
                <div class="cell-lg-2 cell-md-2">
                    <input
                        type="number"
                        id="note"
                        name="note"
                        value="{{ count($errors)>0 ?  old('note') : $control->note }}"
                        min="0"
                        max="100"
                        step="0.01"
                        placeholder="0.00"
                        data-role="spinner"
                    />
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.indicator") }}</strong>
		    	</div>
                <div class="cell-lg-6 cell-md-10">
					<pre>{{ $control->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.score") }}</strong>
		    	</div>
                <div class="cell-lg-6 cell-md-10">
					<input type="radio" name="score" value="3" data-role="radio" data-append="<font color='green'>{{ trans('common.green') }}</font>" {{ ($control->score==3) ? 'checked' : '' }}/>
					<input type="radio" name="score" value="2" data-role="radio" data-append="<font color='orange'>{{ trans('common.orange') }}</font>" {{ ($control->score==2) ? 'checked' : '' }}/>
					<input type="radio" name="score" value="1" data-role="radio" data-append="<font color='red'>{{ trans('common.red') }}</font>" {{ ($control->score==1) ? 'checked' : '' }}/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-10">
                    <textarea name="action_plan" class="easymde" id="action_plan">{{ $errors->has('action_plan') ?  old('action_plan') : $control->action_plan }}</textarea>
				</div>
			</div>


			<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
		    	</div>
                <div class="cell-lg-2 cell-md-2">
                    <select data-role="select" id="periodicity" name="periodicity">
					    <option value="0" {{ $control->periodicity==0 ? "selected" : ""}}>{{ trans('common.once') }}</option>
					    <option value="1" {{ $control->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
					    <option value="3" {{ $control->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
					    <option value="6" {{ $control->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
					    <option value="12" {{ $control->periodicity==12 ? "selected" : ""}}>{{ trans('common.annually') }}</option>
					 </select>
				</div>
                <div class="cell-lg-1 cell-md-2" align="right">
		    		<strong>Status</strong>
	    		</div>
				<div class="cell-lg-1 cell-md-2">
                    <select data-role="select" id="status" name="status">
						<option value="0" {{ $control->status==0 ? "selected" : ""}}>Todo</option>
						<option value="1" {{ $control->status==1 ? "selected" : ""}}>Proposed</option>
						<option value="2" {{ $control->status==2 ? "selected" : ""}}>Done</option>
					 </select>
	    		</div>
                <div class="cell-lg-1 cell-md-2" align="right">
		    		<strong>Next ID</strong>
		    	</div>
				<div class="cell-lg-1 cell-md-2">
                    <select data-role="select" id="next_id" name="next_id">
						<option></option>
						@foreach($ids as $id)
						    <option {{ $control->next_id === $id ? "selected" : ""}}>{{$id}}</option>
					    @endforeach
					 </select>
				</div>
			</div>

			<div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.control.fields.owners') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-10">
                    <select data-role="select" name="owners[]" id="owners" multiple>
                        @foreach($owners as $id => $name)
                            <option
                                value="{{ $id }}"
                                {{ (in_array($id, old('owners', []))) ||
                                    (
                                        (str_starts_with($id,'USR_') && $control->users->contains(intval(substr($id, 4)))) ||
                                        (str_starts_with($id,'GRP_') && $control->groups->contains(intval(substr($id, 4))))
                                    )
                                    ? 'selected' : '' }}>
                            {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

	    	<div class="row">
                <div class="col-12">
    				<button type="submit" class="button success">
    					<span class="mif-floppy-disk2"></span>
    		            &nbsp;
    					{{ trans("common.save") }}
    				</button>
    				&nbsp;
                    <a class="button" href="/bob/show/{{$control->id}}" role="button">
                        <span class="mif-cancel"></span>
                        &nbsp;
                        {{ trans("common.cancel") }}
                    </a>
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
        params: function params(files, xhr, chunk) { return { 'control': '{{ $control->id }}' }; },
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
});
</script>
@endsection
