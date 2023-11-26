@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.make') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	@if (count($errors))
		@foreach ($errors->all() as $error)
		<div class="remark alert" role="alert">{{ $error }}</div>
		@endforeach
	@endif

	<form method="POST" action="/bob/make" enctype="multipart/form-data">
		@csrf
		<input type="hidden" name="id" value="{{ $control->id }}"/>

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.name') }}</strong>
		    	</div>
	    		<div class="cell-5">
					{{ $control->clause }} - {{ $control->name }}
				</div>
	    		<div class="cell-2">
		    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
		    		&nbsp;
	    			{{ $control->scope }}
	    		</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.objective') }}</strong>
		    	</div>
				<div class="cell-6">
                    {!! Michelf\Markdown::defaultTransform($control->objective) !!}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.input') }}</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->input }}</pre>
				</div>
			</div>

            <div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.model') }}</strong>
		    	</div>
				<div class="cell-6">
					<pre>{{ $control->model }}</pre>
				</div>
			</div>

            <div class="row">

				<div class="cell-1">
					<b>{{ trans('cruds.control.fields.plan_date') }}</b>
				</div>
				<div class="cell-2">
                    @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
    					<input type="text"
    						data-role="calendarpicker"
    						name="plan_date"
    						value="{{ count($errors)>0 ?  old('plan_date') : $control->plan_date }}"
    						data-input-format="%Y-%m-%d" />
                    @else
                        {{ $control->plan_date }}
                    @endif
				</div>
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.realisation_date') }}</strong>
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
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.observations') }}</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="observations" rows="5" data-role="textarea" data-clear-button="false">{{ count($errors)>0 ?  old('observations') : $control->observations }}</textarea>
				</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.evidence') }}</strong>
		    		<br>
					<a target="_new" href="/bob/template/{{ $control->id }}">{{ trans('cruds.control.checklist') }}</a>
		    	</div>
				<div class="cell-6">
					<div class="dropzone dropzone-previews" id="dropzoneFileUpload"></div>
				</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.note') }}</strong>
		    	</div>
	    		<div class="cell-1">
					<input type="text" data-role="spinner" name="note" value="{{ count($errors)>0 ?  old('note') : $control->note }}">
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.indicator') }}</strong>
		    	</div>
				<div class="cell">
					<pre>{{ $control->indicator }}</pre>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>
		    			{{ trans('cruds.control.fields.score') }}
		    		</strong>
		    	</div>
				<div class="cell">
					<input type="radio" name="score" value="3" data-role="radio" {{ $control->score==3 ? "selected" : "" }} >
					<font color="green">{{ trans('common.green') }}</font> &nbsp;
					<input type="radio" name="score" value="2" data-role="radio" {{ $control->score==2 ? "selected" : "" }}>
					<font color="orange">{{ trans('common.orange') }}</font> &nbsp;
					<input type="radio" name="score" value="1" data-role="radio" {{ $control->score==1 ? "selected" : "" }}>
					<font color="red">{{ trans('common.red') }}</font>
				</div>
			</div>
            @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.action_plan') }}</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="action_plan" id="mde1">{{ $errors->count()>0 ?  old('action_plan') : $control->action_plan }}</textarea>
				</div>
			</div>
            @endif
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.next') }}</strong>
		    	</div>
				<div class="cell-2">
                    @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
    					<input type="text"
                            lenght="12"
    						data-role="calendarpicker"
    						name="next_date"
    						value="{{ $next_date }}"
    						data-input-format="%Y-%m-%d"/>
                    </div>
                    <div class="cell-2">
                    @else
                        {{ $next_date }}
                    @endif
				(
				@if ($control->periodicity==1)
					{{ trans("common.monthly") }}
				@elseif ($control->periodicity==3)
					{{ trans("common.quarterly") }}
				@elseif ($control->periodicity==6)
					{{ trans("common.biannually") }}
				@elseif ($control->periodicity==12)
					{{ trans("common.annually") }}
				@else
					{{ $control->periodicity }}
				@endif
				)
				</div>
			</div>
			<div class="grid">
		    	<div class="row">
		    		&nbsp;
		    	</div>
		    </div>

			<div class="grid">
		    	<div class="row-12">
					<button type="submit" class="button success">
						<span class="mif-done"></span>
						&nbsp;
						{{ trans('common.make') }}
					</button>
					&nbsp;
					<button type="submit" class="button primary" onclick='this.form.action="/bob/draft"'>
			            <span class="mif-floppy-disk"></span>
			            &nbsp;
						{{ trans('common.save') }}
					</button>
					&nbsp;
					</form>
					<form action="/bob/show/{{ $control->id }}">
			    		<button type="submit" class="button">
							<span class="mif-cancel"></span>
							&nbsp;
			    			{{ trans('common.cancel') }}
			    		</button>
		    		</form>
	    		</div>
	    	</div>

		</form>
	</div>
</div>

<br>
<br>

<script>
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone("div#dropzoneFileUpload", {
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
                    url: '/doc/delete/'+file.id,
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
    );

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

@if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
    const mde1 = new EasyMDE({
        element: document.getElementById('mde1'),
        minHeight: "200px",
        maxHeight: "200px",
        status: false,
        spellChecker: false,
        });
@endif
</script>

@endsection
