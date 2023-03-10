@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.create') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
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


	<form method="POST" action="/measures">
	@csrf
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.measure.fields.domain") }}</strong>
		    	</div>
				<div class="cell-5">
					<select data-role="select" name="domain_id" value="{{ old('domain_id') }}" size="1" width='10'>
					    <option value="">-- {{ trans("cruds.domain.choose") }} --</option>
						@foreach ($domains as $domain)
					    	<option value="{{ $domain->id }}" {{ old('domain_id')==$domain->id ? "selected" : "" }} >
					    		{{ $domain->title }} - {{ $domain->description }}
					    	</option>
					    @endforeach
					</select>
				</div>
			</div>
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.measure.fields.clause") }}</strong>
		    	</div>
				<div class="cell-2">
					<input type="text" class="input" name="clause" value="{{ old('clause') }}" size='60'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.measure.fields.name") }}</strong>
		    	</div>
				<div class="cell-5">
					<input type="text" class="input" name="name" value="{{ old('name') }}" size='60'>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
		    	</div>
				<div class="cell-5">
					<select data-role="select" name="values[]" multiple>
						@foreach($values as $value)
					    <option {{ str_contains(old("vaues"),$value) ? "selected" : ""}}>{{$value}}</option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.objective") }}</strong>
				</div>
				<div class="cell-5">
					<textarea class="textarea" name="objective" rows="3" cols="80">{{ old('objective') }}</textarea>
				</div> 	
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.input") }}</strong>
				</div>
				<div class="cell-5">			
					<textarea class="textarea" name="input" rows="3" cols="80">{{ old('input') }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.model") }}</strong>
				</div>
				<div class="cell-5">			
					<textarea class="textarea" name="model" rows="3" cols="80">{{ old('model') }}</textarea>
				</div> 	
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.indicator") }}</strong>
				</div>
				<div class="cell-5">			
					<textarea class="textarea" name="indicator" rows="3" cols="80">{{ old('indicator') }}</textarea>
				</div> 	
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.action_plan") }}</strong>
				</div>
				<div class="cell-5">
					<textarea class="textarea" name="action_plan" rows="3" cols="80">{{ old('action_plan') }}</textarea>
				</div> 	
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.owner") }}</strong>
				</div>
				<div class="cell-5">
					<input type="text" class="input" name="owner" value="{{ old('owner') }}" size='20'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.periodicity") }}</strong>
				</div>
				<div class="cell-2">
					<select data-role="select" name="periodicity" size="1" width='20'>
					    <option value="0" {{ old('periodicity')==0 ? 'selected' : ''}} ></option>
					    <option value="1" {{ old('periodicity')==1 ? 'selected' : ''}}>{{ trans("common.monthly") }}</option>
					    <option value="3" {{ old('periodicity')==3 ? 'selected' : ''}}>{{ trans("common.quarterly") }}</option>
					    <option value="6" {{ old('periodicity')==6 ? 'selected' : ''}}>{{ trans("common.biannually") }}</option>
					    <option value="12" {{ old('periodicity')==12 ? 'selected' : ''}}>{{ trans("common.annually") }}</option>
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-5">
					<button type="submit" class="button success">
			            <span class="mif-floppy-disk"></span>
						&nbsp;
						{{ trans("common.save") }}
					</button>
					&nbsp;
					<button type="submit" onclick="this.form.method='GET';"class="button">
						<span class="mif-cancel"></span> 
						&nbsp;
						{{ trans("common.cancel") }}
					</button>
				</div>
			</div>
		</div>
	</form>
</div>

@endsection

