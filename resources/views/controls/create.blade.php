@extends("layout")

@section("content")
<div data-role="panel" data-title-caption='{{ trans("cruds.control.create")}}' data-collapsible="false" data-title-icon="<span class='mif-paste'></span>">

    @include('partials.errors')

	<form method="POST" action="/bob/store">
		@csrf

		<div class="grid">

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
				</div>
	    		<div class="cell-lg-6 cell-md-8">
                    <select data-role="select" id="measures" name="measures[]" multiple>
						@foreach($all_measures as $measure)
                            <option value="{{ $measure->id }}"
                                {{ in_array($measure->id, old('measures', [])) ? 'selected' : '' }}>
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
                <div class="cell-lg-4 cell-md-5">
					<input type="text" data-role="input" name="name" value="{{ old('name') }}" maxlength="255">
				</div>
                <div class="cell-lg-1 cell-md-1" align="right">
		    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
                </div>
                <div class="cell-lg-1 cell-md-2">
					<input type="text" name="scope" data-role="input" autocomplete="off" maxlength="32"
                    value="{{ old('scope') }}" data-autocomplete=" {{ $scopes->implode(",") }} "/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.objective") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-8">
                    <textarea name="objective" class="easymde" id="objective">{{ old('objective') }}</textarea>
				</div>
			</div>

			<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans('cruds.control.fields.attributes') }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-8">
                    <select data-role="select" id="attributes" name="attributes[]" data-filter="true" multiple>
						@foreach($attributes as $attribute)
							@if (strlen($attribute)>0)
							    <option {{ in_array($attribute, old("attributes",[])) ? "selected" : "" }}>
                                    {{ $attribute }}
                                </option>
                            @endif
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.input") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-8">
                    <textarea name="input" class="easymde" id="input">{{ old('input') }}</textarea>
				</div>
			</div>
			<div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.control.fields.model') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea class="textarea" name="model" rows="3" data-role="textarea" data-clear-button="false">{{ old('model') }}</textarea>
                </div>
            </div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.plan_date") }}</strong>
		    	</div>
				<div class="cell-lg-2 cell-md-4">
					<input
						data-role="calendarpicker"
						data-format="YYYY-MM-DD"
						data-inputFormat="YYYY-MM-DD"
						name="plan_date"
						value="{{ old('plan_date') }}"/>
				</div>
				<div class="cell-lg-1 cell-md-2">
                </div>
			</div>

	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
		    	</div>
				<div class="cell-lg-6 cell-md-8">
                    <textarea name="action_plan" class="easymde" id="action_plan">{{ old('action_plan') }}</textarea>
				</div>
			</div>


			<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
		    		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
		    	</div>
				<div class="cell-lg-2 cell-md-4">
                    <select data-role="select" id='periodicity' name="periodicity">
					    <option value="0" {{ old("periodicity")=="0" ? "selected" : ""}}>{{ trans('common.once') }}</option>
					    <option value="1" {{ old("periodicity")=="1" ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
					    <option value="3" {{ old("periodicity")=="3" ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
					    <option value="6" {{ old("periodicity")=="6" ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
					    <option value="12" {{ (old("periodicity")==null||old("periodicity")=="12") ? "selected" : ""}}>{{ trans('common.annually') }}</option>
					 </select>
				</div>
			</div>

			<div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.control.fields.owners') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <select data-role="select" name="owners[]" id="owners" multiple>
                        @foreach($owners as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, old('owners', [])) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

	    	<div class="row">
                <div class="cell-lg-12 cell-md-12">
    				<button type="submit" class="button success">
                        <span class="mif-floppy-disk2"></span>
    		            &nbsp;
    					{{ trans("common.save") }}
    				</button>
    				&nbsp;
                    <a class="button cancel" href="/bob/index" role="button">
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
