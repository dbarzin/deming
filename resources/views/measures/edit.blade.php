@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.edit') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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
	<form method="POST" action="/alice/save/{{ $measure->id }}">
		@csrf
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.domain') }}</strong>
		    	</div>
				<div class="cell-6">
				<select name="domain_id" data-role="select">
					    <option value="">--{{ trans('cruds.domain.choose') }}--</option>
						@foreach ($domains as $domain)
					    	<option value="{{ $domain->id }}"
					    	{{ $domain->id==$measure->domain_id ? "selected" : ""}} >
					    	{{ $domain->title }} - {{ $domain->description }}
							</option>
					    @endforeach
					</select>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.clause') }}</strong>
		    	</div>
				<div class="cell-3">
					<input type="text" name="clause" data-role="input"
					value="{{ $errors->has('clause') ?  old('clause') : $measure->clause }}"
					size='60'>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
		    	</div>
				<div class="cell-6">
					<input type="text" name="name" data-role="input"
						value="{{ $errors->has('name') ?  old('name') : $measure->name }}"
						size='60'>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="objective" id="mde1">{{ $errors->has('objective') ?  old('objective') : $measure->objective }}</textarea>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
		    	</div>
				<div class="cell-6">
					<select data-role="select" name="attributes[]" multiple>
						@foreach($values as $value)
					    <option {{ old('attributes') ? (in_array($value, old("attributes")) ? "selected" : "") : (str_contains($measure->attributes,$value) ? "selected" : "")}}>{{$value}}</option>
					    @endforeach
					 </select>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.input') }}</strong>
		    	</div>
				<div class="cell-6">
                    <textarea name="input" id="mde2">{{ $errors->has('input') ?  old('input') : $measure->input }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.model') }}</strong>
		    	</div>
				<div class="cell-6">
					<textarea class="textarea" name="model" rows="3" data-role="textarea" data-clear-button="false">{{ $errors->has('model') ?  old('model') : $measure->model }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.indicator') }}</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="indicator" rows="3" data-role="textarea" data-clear-button="false">{{ $errors->has('indicator') ?  old('indicator') : $measure->indicator }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.action_plan') }}</strong>
		    	</div>
				<div class="cell-6">
					<textarea name="action_plan" id="mde3">{{ $errors->has('action_plan') ?  old('action_plan') : $measure->action_plan }}</textarea>
				</div>
			</div>

	    	<div class="row">
				<div class="cell-1">
				</div>
			</div>

			<div class="row">
	    		<div class="cell-5">
					<button type="submit" class="button success">
			            <span class="mif-floppy-disk"></span>
			            &nbsp;
						{{ trans('common.save') }}
					</button>
					</form>
					&nbsp;
					<form action="/alice/show/{{ $measure->id }}">
						<button type="submit" class="button">
							<span class="mif-cancel"></span>
							&nbsp;
							{{ trans('common.cancel') }}
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!------------------------------------------------------------------------------------->
<script type="text/javascript" defer>
    const mde1 = new EasyMDE({
        element: document.getElementById('mde1'),
        minHeight: "100px",
        maxHeight: "100px",
        status: false,
        spellChecker: false,
        });
    const mde2 = new EasyMDE({
        element: document.getElementById('mde2'),
        minHeight: "100px",
        maxHeight: "100px",
        status: false,
        spellChecker: false,
        });
    const mde3 = new EasyMDE({
        element: document.getElementById('mde3'),
        minHeight: "100px",
        maxHeight: "100px",
        status: false,
        spellChecker: false,
        });
</script>
@endsection
