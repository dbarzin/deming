@extends("layout")

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

    <div data-role="panel" data-title-caption="{{ trans('cruds.action.create') }}" data-collapsible="false" data-title-icon="<span class='mif-pending-actions'></span>">

    @include('partials.errors')

    <form method="POST" action="/action/store">
		@csrf

		<div class="grid">
        	<div class="row">
        		<div class="cell-1">
                    <strong>{{ trans("cruds.action.fields.reference") }}</strong>
    	    	</div>
                <div class="cell-1">
                    <input type="text" data-role="input" name="reference" value="{{ old('reference') }}" maxlength="32">
                </div>
                <div class="cell-1 text-right">
                    <strong>{{ trans("cruds.action.fields.type") }}</strong>
                </div>
                <div class="cell-2">
                    <select name="type" data-role="select">
                        <option></option>
                        <option value="1" {{ old('type')==1 ? 'selected' : '' }}>{{ trans('cruds.action.types.major') }}</option>
                        <option value="2" {{ old('type')==2 ? 'selected' : '' }}>{{ trans('cruds.action.types.minor') }}</option>
                        <option value="3" {{ old('type')==3 ? 'selected' : '' }}>{{ trans('cruds.action.types.observation') }}</option>
                        <option value="4" {{ old('type')==4 ? 'selected' : '' }}>{{ trans('cruds.action.types.opportunity') }}</option>
                    </select>
                </div>
                <div class="cell-1 text-right">
                    <strong>{{ trans('cruds.action.fields.due_date') }}</strong>
                </div>
                <div class="cell-1">
                    <td>
                        <input type="text"
                            data-role="calendarpicker"
                            name="due_date"
                            value=" {{ old('due_date') }}"
    						data-format="YYYY-MM-DD"/>
                    </td>
    			</div>
            </div>

			<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans('cruds.action.fields.clauses') }}</strong>
		    	</div>
                <div class="cell-4">
                    <select data-role="select" name="measures[]" multiple>
                        @foreach($all_measures as $measure)
						    <option
                                value="{{ $measure->id }}"
                                {{ ((old('measures')!=null) && in_array($measure->id, old('measures'))) ? 'selected' : '' }}
                                    >{{ $measure->clause }}</option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans("cruds.action.fields.name") }}</strong>
	    		</div>
	    		<div class="cell-4">
                    <input type="text" data-role="input" name="name" value="{{ old('name') }}" maxlength="255" required>
				</div>
	    		<div class="cell-1" align="right">
		    		<strong>{{ trans("cruds.action.fields.scope") }}</strong>
                </div>
	    		<div class="cell-1">
                    <input type="text" name="scope" data-role="input" autocomplete="on" maxlength="32"
                    value="{{ old('scope') }}" data-autocomplete=" {{ implode(",",$scopes) }} "/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans('cruds.action.fields.cause') }}</strong>
		    	</div>
                <div class="cell-6">
                <textarea name="cause" class="easymde" id="cause">{{ old('cause') }}</textarea>
				</div>
			</div>

    	<div class="row">
		</div>

    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.remediation') }}</strong>
	    	</div>
			<div class="cell-6">
                <textarea name="remediation" class="easymde" id="remediation">{{ old('remediation') }}</textarea>
			</div>
		</div>

		<div class="row">
            <div class="cell-1">
                <strong>{{ trans('cruds.action.fields.owners') }}</strong>
            </div>
            <div class="cell-6">
                <select data-role="select" name="owners[]" id="owners" multiple>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id, old('owners', [])) ? 'selected' : ''  }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
    		<div class="cell-1">

            </div>
        </div>
		<div class="grid">
	    	<div class="row-12">
				<button type="submit" class="button success">
                    <span class="mif-floppy-disk2"></span>
		            &nbsp;
					{{ trans('common.save') }}
				</button>
				&nbsp;
                <a class="button dafault" href="/actions">
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
