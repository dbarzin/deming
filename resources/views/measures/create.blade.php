@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.create') }}" data-collapsible="true" data-title-icon="<span class='mif-pencil'></span>">
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

	<form method="POST" action="/alice/store">
	@csrf
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.measure.fields.domain") }}</strong>
		    	</div>
				<div class="cell-6">
					<select data-role="select" name="domain_id" value="{{ old('domain_id') }}" size="1" width='10'>
					    <option value="">-- {{ trans("cruds.domain.choose") }} --</option>
						@foreach ($domains as $domain)
					    	<option value="{{ $domain->id }}" {{ old('domain_id', optional($measure)->domain_id)==$domain->id ? "selected" : "" }} >
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
					<input type="text" class="input" name="clause" value="{{ old('clause', optional($measure)->clause) }}" maxlength='32'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.measure.fields.name") }}</strong>
		    	</div>
				<div class="cell-6">
					<input type="text" class="input" name="name" value="{{ old('name', optional($measure)->name) }}" maxlength='255'>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.objective") }}</strong>
				</div>
				<div class="cell-6">
                    <textarea name="objective" class="easymde" id="objetive">{{ old('objective', optional($measure)->objective) }}</textarea>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
		    	</div>
				<div class="cell-6">
					<select data-role="select" name="attributes[]" multiple>
						@foreach($values as $value)
					    <option oncl>{{$value}}</option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.input") }}</strong>
				</div>
				<div class="cell-6">
                    <textarea name="input" class="easymde" id="input">{{ old('input', optional($measure)->input) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.model") }}</strong>
				</div>
				<div class="cell-6">
					<textarea name="model" rows="3" data-role="textarea" data-clear-button="false">{{ old('model', optional($measure)->model) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.indicator") }}</strong>
				</div>
				<div class="cell-6">
					<textarea name="indicator" rows="3" data-role="textarea" data-clear-button="false">{{ old('indicator', optional($measure)->indicator) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.action_plan") }}</strong>
				</div>
				<div class="cell-6">
                    <textarea name="action_plan" class="easymde" id="action_plan">{{ old('action_plan', optional($measure)->action_plan) }}</textarea>
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
					<button type="button" class="button" onclick="location.href = '/alice/index'">
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
