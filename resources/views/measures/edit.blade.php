@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.edit') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	@if (count($errors))
	<div class= “form-group”>
		<div class= “remark alert alert-danger”>
			<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif

	<style type="text/css">
	form, table {
	     display:inline;
	     margin:0px;
	     padding:0px;
	}
	</style>

	<form method="POST" action="/measures/{{ $measure->id }}">
		@method("PATCH")
		@csrf
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.domain') }}</strong>
		    	</div>
				<div class="cell-5">
				<select name="domain_id" size="1" width='10'>
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
				<div class="cell-5">
					<input type="text" name="clause" 
					value="{{ $errors->has('clause') ?  old('clause') : $measure->clause }}" 
					size='60'>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
		    	</div>
				<div class="cell-5">
					<input type="text" name="name" 
						value="{{ $errors->has('name') ?  old('name') : $measure->name }}" 
						size='60'>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
		    	</div>
				<div class="cell-7">
					<select data-role="select" name="attributes" multiple>
						@foreach($values as $value)
					    <option>{{$value}}</option>
					    @endforeach
					 </select>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
		    	</div>
				<div class="cell-5">
					<textarea name="objective" rows="3" cols="80">{{ $errors->has('objective') ?  old('objective') : $measure->objective }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.input') }}</strong>
		    	</div>
				<div class="cell-5">
					<textarea name="input" rows="3" cols="80">{{ $errors->has('input') ?  old('input') : $measure->input }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.model') }}</strong>
		    	</div>
				<div class="cell-5">
					<textarea class="textarea" name="model" rows="3" cols="80">{{ $errors->has('model') ?  old('model') : $measure->model }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.indicator') }}</strong>
		    	</div>
				<div class="cell-5">
					<textarea name="indicator" rows="3" cols="80">{{ $errors->has('indicator') ?  old('indicator') : $measure->indicator }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.action_plan') }}</strong>
		    	</div>
				<div class="cell-5">
					<textarea name="action_plan" rows="3" cols="80">{{ $errors->has('action_plan') ?  old('action_plan') : $measure->action_plan }}</textarea>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.owner') }}</strong>
		    	</div>
				<div class="cell-2">
				<input name="owner" type="text"
					value="{{ $errors->has('owner') ?  old('owner') : $measure->owner }}" 
					size='20'>
				</div>
			</div>
			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.periodicity') }}</strong>
		    	</div>
				<div class="cell-2">
					<select name="periodicity" data-role="select">
					    <option value="1" {{ $measure->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
					    <option value="3" {{ $measure->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
					    <option value="6" {{ $measure->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
					    <option value="12" {{ $measure->periodicity==12 ? "selected" : ""}}>{{ trans('common.annually') }}</option>
					 </select>
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
					<form action="/measures/{{ $measure->id }}">
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
@endsection

