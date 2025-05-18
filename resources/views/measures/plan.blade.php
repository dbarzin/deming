@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.measure.plan') }}" data-collapsible="false" data-title-icon="<span class='mif-paste'></span>">

@include('partials.errors')

<form method="POST" action="/alice/activate/{{ $measure->id }}">
@csrf
    <div class="grid">

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
        	</div>
    		<div class="cell-lg-6 cell-md-8">
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
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
        	</div>
            <div class="cell-lg-4 cell-md-5">
    			<input type="text" class="input" name="name" value="{{ old('name', $measure->name) }}" maxlength='255'>
    		</div>
            <div class="cell-lg-1 cell-md-1" align="right">
        		<strong>{{ trans('cruds.control.fields.scope') }}</strong>
        	</div>
    		<div class="cell-lg-1 cell-md-2">
    			<input type="text" name="scope" data-role="input" autocomplete="on" size="32"
    			value="{{ old('scope',$measure->scope) }}" data-autocomplete="{{ implode(",",$scopes) }}"/>
    		</div>
        </div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
        	</div>
            <div class="cell-lg-6 cell-md-8">
                <textarea name="objective" class="easymde" id="objetive">{{ old('objective', optional($measure)->objective) }}</textarea>
    		</div>
        </div>

    		<div class="row">
        		<div class="cell-lg-1 cell-md-2">
    	    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
    	    	</div>
                <div class="cell-lg-6 cell-md-8">
    				<select data-role="select" name="attributes[]" data-filter="true" multiple>
    					@foreach($values as $value)
    				    <option {{ str_contains($measure->attributes, $value) ? "selected" : ""}} >{{$value}}</option>
    				    @endforeach
    				 </select>
    			</div>
    		</div>

        	<div class="row">
        		<div class="cell-lg-1 cell-md-2">
    				<strong>{{ trans("cruds.measure.fields.input") }}</strong>
    			</div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea name="input" class="easymde" id="input">{{ old('input', optional($measure)->input) }}</textarea>
    			</div>
    		</div>

        	<div class="row">
        		<div class="cell-lg-1 cell-md-2">
    				<strong>{{ trans("cruds.measure.fields.model") }}</strong>
    			</div>
    			<div class="cell-lg-6 cell-md-8">
    				<textarea name="model" rows="3" data-role="textarea" data-clear-button="false">{{ old('model', optional($measure)->model) }}</textarea>
    			</div>
    		</div>

        	<div class="row">
        		<div class="cell-lg-1 cell-md-2">
    				<strong>{{ trans("cruds.measure.fields.indicator") }}</strong>
    			</div>
    			<div class="cell-lg-6 cell-md-8">
    				<textarea name="indicator" rows="3" data-role="textarea" data-clear-button="false">{{ old('indicator', optional($measure)->indicator) }}</textarea>
    			</div>
    		</div>

        	<div class="row">
        		<div class="cell-lg-1 cell-md-2">
    				<strong>{{ trans("cruds.measure.fields.action_plan") }}</strong>
    			</div>
    			<div class="cell-lg-6 cell-md-8">
                    <textarea name="action_plan" class="easymde" id="action_plan">{{ old('action_plan', optional($measure)->action_plan) }}</textarea>
    			</div>
    		</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
    			<strong>{{ trans('cruds.control.fields.plan_date') }}</strong>
        	</div>
    		<div class="cell-lg-3 cell-md-4">
    			<input type="text" data-role="calendarpicker" name="plan_date"
                    value=""
					data-format="YYYY-MM-DD"
					data-inputFormat="YYYY-MM-DD"/>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.measure.fields.periodicity') }}</strong>
        	</div>
    		<div class="cell-lg-3 cell-md-4">
    			<select name="periodicity" data-role="select">
    			    <option value="0" {{ $measure->periodicity==0 ? "selected" : ""}}>{{ trans('common.once') }}</option>
    			    <option value="1" {{ $measure->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
    			    <option value="3" {{ $measure->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
    			    <option value="6" {{ $measure->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
    			    <option value="12" {{ ($measure->periodicity==null) || ($measure->periodicity==12) ? "selected" : ""}}>{{ trans('common.annually') }}</option>
    			 </select>
    		</div>
    	</div>

    <div class="row">
    	<div class="cell-lg-1 cell-md-2">
    		<strong>{{ trans('cruds.control.fields.owners') }}</strong>
    	</div>
    	<div class="cell-lg-5 cell-md-7">
            <select data-role="select" name="owners[]" id="owners" multiple>
                @foreach($owners as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, old('owners', [])) ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
    	</div>
    </div>

    <div class="row">
    	<div class="cell">
    	</div>
    </div>

    <div class="row">
        <div class="cell-6">
            @if (Auth::User()->role !== 3)
        	<button class="button success">
                <span class="mif-calendar"></span>
                &nbsp;
            	{{ trans('common.plan') }}
        	</button>
            @endif
            &nbsp;
            <a href="/alice/show/{{ $measure->id }}" class="button">
        			<span class="mif-cancel"></span>
        			&nbsp;
        	    	{{ trans('common.cancel') }}
            </a>
        </div>
    </div>
    </form>
</div>
@endsection
