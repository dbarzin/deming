@extends("layout")
@section("style")
    <style>
        .disabled-editor {
            background-color: #f0f0f0; /* Couleur de fond pour l'état désactivé */
        }
        .CodeMirror {
            height: auto;
        }
    </style>
    @endsection
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
		    		<strong>{{ trans('cruds.control.fields.clauses') }}</strong>
		    	</div>
        		<div class="cell-4">
                    @foreach($control->measures as $measure)
                        <a href="/alice/show/{{ $measure->id }}">{{ $measure->clause }}</a>
                        @if(!$loop->last)
                        ,
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.name') }}</strong>
		    	</div>
	    		<div class="cell-5">
					{{ $control->name }}
				</div>
                @if ($control->scope!==null)
	    		<div class="cell-2">
		    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
		    		&nbsp;
	    			{{ $control->scope }}
	    		</div>
                @endif
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.objective') }}</strong>
		    	</div>
				<div class="cell-6">
                    {!! \Parsedown::instance()->text($control->objective) !!}
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.input') }}</strong>
		    	</div>
				<div class="cell-6">
                    {!! \Parsedown::instance()->text($control->input) !!}
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
                </div>
	    		<div class="cell-1" align="right">
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
                    <a target="_new" href="/bob/template/{{ $control->id }}" id="checklist-link">{{ trans('cruds.control.checklist') }}</a>
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
					<input type="text" data-role="spinner" name="note"  data-min-value="0" data-max-value="100"
                    value="{{ count($errors)>0 ?  old('note') : $control->note }}">
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
					<input type="radio" name="score" value="3" data-role="radio" {{ $control->score===3 ? "checked" : "" }} >
					<font color="green">{{ trans('common.green') }}</font> &nbsp;
					<input type="radio" name="score" value="2" data-role="radio" {{ $control->score===2 ? "checked" : "" }}>
					<font color="orange">{{ trans('common.orange') }}</font> &nbsp;
					<input type="radio" name="score" value="1" data-role="radio" {{ $control->score===1 ? "checked" : "" }}>
					<font color="red">{{ trans('common.red') }}</font>
				</div>
			</div>
            @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
	    	<div class="row">
                <div class="cell-1">
                </div>
                <div class="cell-3">
                    <input type="checkbox" name="add_action_plan" data-role="checkbox" id="toggleTextarea"/>
                    {{ trans('cruds.control.create_action') }}
                </div>
            </div>
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.control.fields.action_plan') }}</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="action_plan" id="mde1">{{ $errors->count()>0 ?  old('action_plan') : $control->action_plan }}</textarea>
				</div>
			</div>
            @endif
			@if ($control->periodicity!==0)
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
            @endif
			<div class="grid">
		    	<div class="row">
		    		&nbsp;
		    	</div>
		    </div>

			<div class="grid">
		    	<div class="row-12">
                    @if ($control->status===0)
                        @if ($control->canMake())
    					<button type="submit" class="button success">
    						<span class="mif-done"></span>
    						&nbsp;
    						{{ trans('common.make') }}
    					</button>
    					&nbsp;
                        @endif
                    @endif
                    @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
                        @if ($control->status===1)
    					<button type="submit" class="button success" onclick='this.form.action="/bob/accept"'>
    						<span class="mif-done"></span>
    						&nbsp;
    						{{ trans('common.accept') }}
    					</button>
    					&nbsp;
    					<button type="submit" class="button alert" onclick='this.form.action="/bob/reject"'>
    						<span class="mif-fire"></span>
    						&nbsp;
    						{{ trans('common.reject') }}
    					</button>
    					&nbsp;
                        @endif
                    @endif
                    @if ($control->canMake())
    					<button type="submit" class="button primary" onclick='this.form.action="/bob/draft"'>
    			            <span class="mif-floppy-disk"></span>
    			            &nbsp;
    						{{ trans('common.save') }}
    					</button>
    					&nbsp;
                    @endif
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

document.addEventListener('DOMContentLoaded', function () {

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
          	myDropzone.addFile(item.getAsFile())
        	}
      	})
    }

@if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
    const easyMDE = new EasyMDE({
        element: document.getElementById('mde1'),
        minHeight: "200px",
        maxHeight: "200px",
        status: false,
        spellChecker: false,
        });

    // Rendre l'éditeur en lecture seule par défaut
    easyMDE.codemirror.setOption("readOnly", true);
    easyMDE.codemirror.getWrapperElement().classList.add('disabled-editor');

    document.getElementById('toggleTextarea').addEventListener('change', function() {
            if (this.checked) {
                easyMDE.codemirror.setOption("readOnly", false);
                easyMDE.codemirror.getWrapperElement().classList.remove('disabled-editor');
            } else {
                easyMDE.codemirror.setOption("readOnly", true);
                easyMDE.codemirror.getWrapperElement().classList.add('disabled-editor');
            }
    });
@endif

    // Ajoute les observations en paramètre de la template de document
    
    let link = document.querySelector('#checklist-link');
    let textarea = document.querySelector('textarea[name="observations"]');

    link.addEventListener('click', function (event) {
        event.preventDefault(); // empêche l'ouverture immédiate

        let baseUrl = this.getAttribute('href');
        let observations = encodeURIComponent(textarea.value);
        let fullUrl = baseUrl + '?observations=' + observations;

        window.open(fullUrl, '_blank');
    });

});

</script>

@endsection
