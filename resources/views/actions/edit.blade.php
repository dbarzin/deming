@extends("layout")

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

<div data-role="panel" data-title-caption="{{ trans('cruds.action.edit') }}" data-collapsible="false" data-title-icon="<span class='mif-pending-actions'></span>">

@include('partials.errors')

<form method="POST" action="/action/save">
	@csrf
	<input type="hidden" name="id" value="{{ $action->id }}"/>

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans("cruds.action.fields.reference") }}</strong>
	    	</div>
            <div class="cell-6">
                <table>
                    <tr>
                        <td>
                            <input type="text" data-role="input" name="reference" value="{{ $action->reference }}" maxlength="32">
                        </td>
                        <td style="white-space: nowrap; padding-left: 10px; padding-right: 10px;">
                            <strong>{{ trans("cruds.action.fields.type") }}</strong>
                        </td>
                        <td>
                            <input type="text" name="type" data-role="input" autocomplete="on" maxlength="32"
                            value="{{ $action->type }}" data-autocomplete=" {{ implode(",",$types) }} "/>
                        </td>
                        <td style="white-space: nowrap; padding-left: 10px; padding-right: 10px;">
                            <strong>{{ trans('cruds.action.fields.due_date') }}</strong>
                        </td>
                        <td>
                            <input
                            data-role="calendarpicker"
                            name="due_date"
                            value="{{$action->due_date}}"
                            data-format="YYYY-MM-DD"/>
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
					    <option value="{{ $measure->id }}"
                            {{ in_array($measure->id, old("measures", $measures)) ? "selected" : "" }}>
                                {{ $measure->clause }}
                        </option>
				    @endforeach
				 </select>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans("cruds.action.fields.name") }}</strong>
    		</div>
    		<div class="cell-4">
				<input type="text" data-role="input" name="name" value="{{ $action->name }}" maxlength="255">
			</div>
    		<div class="cell-1" align="right">
	    		<strong>{{ trans("cruds.action.fields.scope") }}</strong>
            </div>
    		<div class="cell-1">
                <input type="text" name="scope" data-role="input" autocomplete="on" maxlength="32"
                value="{{ $action->scope }}" data-autocomplete=" {{ implode(",",$scopes) }} "/>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.cause') }}</strong>
	    	</div>
            <div class="cell-6">
            <textarea name="cause" class="easymde" id="cause">{{ $errors->has('cause') ?  old('cause') : $action->cause }}</textarea>
			</div>
		</div>

	<div class="row">
	</div>

	<div class="row">
		<div class="cell-1">
            <strong>{{ trans('cruds.action.fields.remediation') }}</strong>
    	</div>
		<div class="cell-6">
            <textarea name="remediation" class="easymde" id="remediation">{{ $errors->has('remediation') ?  old('remediation') : $action->remediation }}</textarea>
		</div>
	</div>

	<div class="row">
        <div class="cell-1">
            <strong>{{ trans('cruds.action.fields.status') }}</strong>
        </div>
        <div class="cell-3">
            <select data-role="select" name="status" id="status">
                <option value="0" {{ $action->status==0 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_open') }}</option>
                <option value="1" {{ $action->status==1 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_closed') }}</option>
                <option value="2" {{ $action->status==2 ? 'selected' : '' }}>{{ trans('cruds.action.fields.status_rejected') }}</option>
            </select>
        </div>
        <div class="cell-1" align="right">
            <strong>{{ trans('cruds.action.fields.close_date') }}</strong>
        </div>
        <div class="cell-2">
            <input
                data-role="calendarpicker"
                name="close_date"
                value="{{$action->close_date}}"
				data-format="YYYY-MM-DD"
				data-inputFormat="YYYY-MM-DD"
                />
        </div>
    </div>

	<div class="row">
		<div class="cell-1">
            <strong>{{ trans('cruds.action.fields.justification') }}</strong>
    	</div>
		<div class="cell-6">
            <textarea name="justification" class="easymde" id="justification">{{ $errors->has('justification') ?  old('justification') : $action->justification }}</textarea>
		</div>
	</div>

	<div class="row">
        <div class="cell-1">
            <strong>{{ trans('cruds.action.fields.owners') }}</strong>
        </div>
        <div class="cell-6">
            <select data-role="select" name="owners[]" id="owners" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (in_array($user->id, old('owners', [])) || $action->owners->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
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
