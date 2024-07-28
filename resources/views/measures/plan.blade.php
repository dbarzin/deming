@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.plan') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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

    <form method="POST" action="/alice/activate/{{ $measure->id }}">
	@csrf
	<div class="grid">

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
	    	</div>
    		<div class="cell-6">
				<select data-role="select" name="measures[]" multiple>
					@foreach($all_measures as $m)
					    <option
                            value="{{ $m->id }}"
                            {{ in_array($m->id, old("measures", $measures)) ? "selected" : "" }}
                                >{{ $m->clause }}</option>
				    @endforeach
				 </select>
            </div>
        </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
	    	</div>
			<div class="cell-4">
				<input type="text" class="input" name="name" value="{{ old('name', $measure->name) }}" maxlength='255'>
			</div>
    		<div class="cell-1" align="right">
	    		<strong>{{ trans('cruds.control.fields.scope') }}</strong>
	    	</div>
			<div class="cell-1">
				<input type="text" name="scope" data-role="input" autocomplete="on" size="32"
				value="{{ old('scope',$measure->scope) }}" data-autocomplete="{{ implode(",",$scopes) }}"/>
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
	    	</div>
			<div class="cell-6">
				<textarea name="objective" id="mde1">{{ old('objective', optional($measure)->objective) }}</textarea>
			</div>
	    </div>

			<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
		    	</div>
				<div class="cell-6">
					<select data-role="select" name="attributes[]" multiple>
						@foreach($values as $value)
					    <option {{ str_contains($measure->attributes, $value) ? "selected" : ""}} >{{$value}}</option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
					<strong>{{ trans("cruds.measure.fields.input") }}</strong>
				</div>
				<div class="cell-6">
                    <textarea name="input" id="mde2">{{ old('input', optional($measure)->input) }}</textarea>
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
					<textarea name="action_plan" id="mde3">{{ old('action_plan', optional($measure)->action_plan) }}</textarea>
				</div>
			</div>

		<div class="row">
			<div class="cell-1">
				<strong>{{ trans('cruds.control.fields.plan_date') }}</strong>
	    	</div>
			<div class="cell-3">
					<input type="text" data-role="calendarpicker" name="plan_date"
				value="" data-input-format="%Y-%m-%d">
			</div>
		</div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.periodicity') }}</strong>
	    	</div>
			<div class="cell-3">
				<select name="periodicity" data-role="select">
				    <option value="0" {{ $measure->periodicity==0 ? "selected" : ""}}>{{ trans('common.once') }}</option>
				    <option value="1" {{ $measure->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
				    <option value="3" {{ $measure->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
				    <option value="6" {{ $measure->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
				    <option value="12" {{ ($measure->periodicity==null) || ($measure->periodicity==12) ? "selected" : ""}}>{{ trans('common.annually') }}</option>
				 </select>
			</div>
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

	<div class="row">
		<div class="cell">
		</div>
	</div>

	<div class="form-group">
		@if (Auth::User()->role === 3)
	    </form>
	    @else
    	<button class="button success">
            <span class="mif-calendar"></span>
            &nbsp;
	    	{{ trans('common.plan') }}
    	</button>
	    </form>
	    @endif
	    &nbsp;
	    <form action="/alice/show/{{ $measure->id }}">
	    	<button class="button">
				<span class="mif-cancel"></span>
				&nbsp;
		    	{{ trans('common.cancel') }}
	    	</button>
		</form>
	</div>
</div>
</div>

<!------------------------------------------------------------------------------------->
<script type="text/javascript">
const mde1 = new EasyMDE({
    element: document.getElementById('mde1'),
    minHeight: "200px",
    maxHeight: "200px",
    status: false,
    });
const mde2 = new EasyMDE({
    element: document.getElementById('mde2'),
    minHeight: "200px",
    maxHeight: "200px",
    status: false,
    });
const mde3 = new EasyMDE({
    element: document.getElementById('mde3'),
    minHeight: "200px",
    maxHeight: "200px",
    status: false,
    });
</script>
@endsection
