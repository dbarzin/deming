@extends("layout")
@section("styles")
@endsection
@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.measure.edit') }}" data-collapsible="false" data-title-icon="<span class='mif-books'></span>">

@include('partials.errors')

<form method="POST" action="/alice/save/{{ $measure->id }}">
	@csrf
	<div class="grid">
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.domain') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
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
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.clause') }}</strong>
	    	</div>
			<div class="cell-lg-3 cell-md-5">
				<input type="text" name="clause" data-role="input"
				value="{{ $errors->has('clause') ?  old('clause') : $measure->clause }}"
				size='60'>
			</div>
		</div>

		<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
				<input type="text" name="name" data-role="input"
					value="{{ $errors->has('name') ?  old('name') : $measure->name }}"
					size='60'>
			</div>
		</div>

		<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
                <textarea name="objective" class="easymde" id="mde1">{{ $errors->has('objective') ?  old('objective') : $measure->objective }}</textarea>
			</div>
		</div>

		<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
				<select data-role="select" name="attributes[]" multiple>
					@foreach($values as $value)
				    <option {{ old('attributes') ? (in_array($value, old("attributes")) ? "selected" : "") : (str_contains($measure->attributes,$value) ? "selected" : "")}}>{{$value}}</option>
				    @endforeach
				 </select>
			</div>
		</div>

		<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.input') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
                <textarea name="input" class="easymde" id="input">{{ $errors->has('input') ?  old('input') : $measure->input }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.model') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
				<textarea class="textarea" name="model" rows="3" data-role="textarea" data-clear-button="false">{{ $errors->has('model') ?  old('model') : $measure->model }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.indicator') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
				<textarea name="indicator" rows="3" data-role="textarea" data-clear-button="false">{{ $errors->has('indicator') ?  old('indicator') : $measure->indicator }}</textarea>
			</div>
		</div>
		<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.action_plan') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-8">
                <textarea name="action_plan" class="easymde" id="action_plan">{{ $errors->has('action_plan') ?  old('action_plan') : $measure->action_plan }}</textarea>
			</div>
		</div>

    	<div class="row">
			<div class="cell-lg-1 cell-md-2">
			</div>
		</div>

		<div class="row">
    		<div class="cell-lg-8 cell-md-12">
				<button type="submit" class="button success">
                    <span class="mif-floppy-disk2"></span>
		            &nbsp;
					{{ trans('common.save') }}
				</button>
				&nbsp;
                <a type="button" class="button" href="/alice/show/{{ $measure->id }}">
					<span class="mif-cancel"></span>
					&nbsp;
					{{ trans('common.cancel') }}
                </a>
			</div>
		</div>
	</div>
</form>
</div>
@endsection
