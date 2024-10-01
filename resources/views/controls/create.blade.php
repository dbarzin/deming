@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption='{{ trans("cruds.control.create")}}' data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
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

		<form method="POST" action="/bob/store">
			@csrf

			<div class="grid">

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
					</div>
		    		<div class="cell-6">
						<select data-role="select" name="measures[]" multiple>
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
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.name") }}</strong>
		    		</div>
		    		<div class="cell-4">
						<input type="text" data-role="input" name="name" value="{{ old('name') }}" maxlength="255">
					</div>
		    		<div class="cell-1" align="right">
			    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
                    </div>
		    		<div class="cell-1">
						<input type="text" name="scope" data-role="input" autocomplete="off" maxlength="32"
						value="{{ old('scope') }}" data-autocomplete=" {{ implode(",",$scopes) }} "/>
					</div>
				</div>


		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.objective") }}</strong>
			    	</div>
					<div class="cell-6">
						<textarea name="objective" id="mde1">{{ old('objective') }}</textarea>
					</div>
				</div>

				<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.control.fields.attributes') }}</strong>
			    	</div>
					<div class="cell-6">
						<select data-role="select" name="attributes[]" multiple>
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
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.input") }}</strong>
			    	</div>
					<div class="cell-6">
                        <textarea name="input" id="mde2">{{ old('input') }}</textarea>
					</div>
				</div>
				<div class="row">
                    <div class="cell-1">
                        <strong>{{ trans('cruds.control.fields.model') }}</strong>
                    </div>
                    <div class="cell-6">
                        <textarea class="textarea" name="model" rows="3" data-role="textarea" data-clear-button="false">{{ old('model') }}</textarea>
                    </div>
                </div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.plan_date") }}</strong>
			    	</div>
					<div class="cell-2">
						<input type="text" data-role="calendarpicker" name="plan_date" value="{{ old('plan_date') }}" data-input-format="%Y-%m-%d">
					</div>
					<div class="cell-1">
                    </div>
				</div>

		    	<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans("cruds.control.fields.action_plan") }}</strong>
			    	</div>
					<div class="cell-6">
						<textarea name="action_plan" id="mde3">{{ old('action_plan') }}</textarea>
					</div>
				</div>


				<div class="row">
		    		<div class="cell-1">
			    		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
			    	</div>
					<div class="cell-2">
						<select data-role="select" name="periodicity">
						    <option value="0" {{ old("periodicity")=="0" ? "selected" : ""}}>{{ trans('common.once') }}</option>
						    <option value="1" {{ old("periodicity")=="1" ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
						    <option value="3" {{ old("periodicity")=="3" ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
						    <option value="6" {{ old("periodicity")=="6" ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
						    <option value="12" {{ (old("periodicity")==null||old("periodicity")=="12") ? "selected" : ""}}>{{ trans('common.annually') }}</option>
						 </select>
					</div>
				</div>

				<div class="row">
                    <div class="cell-1">
                        <strong>{{ trans('cruds.control.fields.owners') }}</strong>
                    </div>
                    <div class="cell-4">
                        <select data-role="select" name="owners[]" id="owners" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, old('owners', [])) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

		    	<div class="row-12">
		    		<div><br></div>
		    	</div>

		    	<div class="row-12">
					<button type="submit" class="button success">
			            <span class="mif-floppy-disk"></span>
			            &nbsp;
						{{ trans("common.save") }}
					</button>
					&nbsp;
		    		<button type="button" class="button cancel" onclick="location.href = '/bob/index';">
		    			<span class="mif-cancel"></span>
		    			&nbsp;
		    			{{ trans("common.cancel") }}
		    		</button>
	    		</div>
	    	</div>
	    </form>
	</div>
</div>

<script>
const mde1 = new EasyMDE({
    element: document.getElementById('mde1'),
    minHeight: "200px",
    maxHeight: "200px",
    status: false,
    spellChecker: false,
    });

const mde2 = new EasyMDE({
    element: document.getElementById('mde2'),
    minHeight: "200px",
    maxHeight: "200px",
    status: false,
    spellChecker: false,
    });

const mde3 = new EasyMDE({
    element: document.getElementById('mde3'),
    minHeight: "200px",
    maxHeight: "200px",
    status: false,
    spellChecker: false,
    });
</script>
@endsection
