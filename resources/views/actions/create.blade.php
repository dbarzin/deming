@extends("layout")

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.action.create') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	@if (count($errors))
	<div class= “form-group”>
		<div class= “alert alert-danger”>
			<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif

    <form method="POST" action="/action/store">
		@csrf

		<div class="grid">
        	<div class="row">
        		<div class="cell-1">
                    <strong>{{ trans("cruds.action.fields.reference") }}</strong>
    	    	</div>
                <div class="cell-6">
                    <table>
                        <tr>
                            <td>
                                <input type="text" data-role="input" name="reference" value="{{ old('reference') }}" maxlength="32">
                            </td>
                            <td style="white-space: nowrap; padding-left: 10px; padding-right: 10px;">
                                <strong>{{ trans("cruds.action.fields.type") }}</strong>
                            </td>
                            <td>
                                <input type="text" name="type" data-role="input" autocomplete="on" maxlength="32"
                                value="{{ old('type') }}" data-autocomplete=" {{ implode(",",$types) }} "/>
                            </td>
                            <td style="white-space: nowrap; padding-left: 10px; padding-right: 10px;">
                                <strong>{{ trans('cruds.action.fields.due_date') }}</strong>
                            </td>
                            <td>
                                <input type="text" data-role="calendarpicker" name="due_date" value=" {{ old('due_date') }}" data-input-format="%Y-%m-%d">
                            </td>
                        </tr>
                    </table>
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
                <textarea name="cause" id="mde1">{{ old('cause') }}</textarea>
				</div>
			</div>

    	<div class="row">
		</div>

    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.remediation') }}</strong>
	    	</div>
			<div class="cell-6">
                <textarea name="remediation" id="mde2">{{ old('remediation') }}</textarea>
			</div>
		</div>

		<div class="row">
            <div class="cell-1">
                <strong>{{ trans('cruds.action.fields.owners') }}</strong>
            </div>
            <div class="cell-4">
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
		            <span class="mif-floppy-disk"></span>
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
    minHeight: "400px",
    maxHeight: "400px",
    status: false,
    spellChecker: false,
    });
</script>

@endsection
