@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.measure.create') }}" data-collapsible="false" data-title-icon="<span class='mif-books'></span>">
    @include('partials.errors')

	<form method="POST" action="/alice/store">
	@csrf
		<div class="grid">
	    	<div class="row">
                <div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.measure.fields.domain") }}</strong>
		    	</div>
                <div class="cell-lg-6 cell-md-9">
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
                <div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.measure.fields.clause") }}</strong>
		    	</div>
                <div class="cell-lg-2 cell-md-4">
                    <input type="text" class="input" name="clause" value="{{ old('clause', optional($measure)->clause) }}" maxlength='32'/>
				</div>
			</div>

	    	<div class="row">
                <div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.measure.fields.name") }}</strong>
		    	</div>
                <div class="cell-lg-6 cell-md-9">
                    <input type="text" class="input" name="name" value="{{ old('name', optional($measure)->name) }}" maxlength='255'/>
				</div>
			</div>

	    	<div class="row">
                <div class="cell-lg-1 cell-md-2">
					<strong>{{ trans("cruds.measure.fields.objective") }}</strong>
				</div>
                <div class="cell-lg-6 cell-md-9">
                    <textarea name="objective" class="easymde" id="objetive">{{ old('objective', optional($measure)->objective) }}</textarea>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
		    	</div>
                    <div class="cell-lg-6 cell-md-9">
					<select data-role="select" name="attributes[]" data-filter="true" multiple>
						@foreach($values as $value)
					    <option value="{{ $value }}"
                            @if(in_array($value, old('attributes', $selectedAttributes ?? []))) selected @endif>
                            {{$value}}
                        </option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
					<strong>{{ trans("cruds.measure.fields.input") }}</strong>
				</div>
                <div class="cell-lg-6 cell-md-9">
                    <textarea name="input" class="easymde" id="input">{{ old('input', optional($measure)->input) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
					<strong>{{ trans("cruds.measure.fields.model") }}</strong>
				</div>
				<div class="cell-lg-6 cell-md-9">
					<textarea name="model" rows="3" data-role="textarea" data-clear-button="false">{{ old('model', optional($measure)->model) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
					<strong>{{ trans("cruds.measure.fields.indicator") }}</strong>
				</div>
				<div class="cell-lg-6 cell-md-9">
					<textarea name="indicator" rows="3" data-role="textarea" data-clear-button="false">{{ old('indicator', optional($measure)->indicator) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
					<strong>{{ trans("cruds.measure.fields.action_plan") }}</strong>
				</div>
				<div class="cell-lg-6 cell-md-9">
                    <textarea name="action_plan" class="easymde" id="action_plan">{{ old('action_plan', optional($measure)->action_plan) }}</textarea>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-5">
					<button type="submit" class="button success">
                        <span class="mif-floppy-disk2"></span>
						&nbsp;
						{{ trans("common.save") }}
					</button>
                    <a class="button" href="/alice/index">
						<span class="mif-cancel"></span>
						&nbsp;
						{{ trans("common.cancel") }}
                    </a>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection
