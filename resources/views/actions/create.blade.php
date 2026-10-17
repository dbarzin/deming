@extends("layout")

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

    <div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'actions.create'])<i class="bi bi-list-check"></i> {{ trans('cruds.action.create') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form method="POST" action="/action/store">
		@csrf

		<div class="container-fluid px-0">
        	<div class="row">
        		<div class="col-md-2 col-lg-1">
                    <strong>{{ trans("cruds.action.fields.reference") }}</strong>
    	    	</div>
                <div class="col-md-2 col-lg-2 col-xl-1">
                    <input class="form-control" type="text" name="reference" value="{{ old('reference') }}" maxlength="32">
                </div>
                <div class="col-md-2 col-lg-1 text-md-end">
                    <strong>{{ trans("cruds.action.fields.type") }}</strong>
                </div>
                <div class="col-md-4 col-lg-2">
                    <select class="form-select select2" name="type">
                        <option></option>
                        <option value="1" {{ old('type')==1 ? 'selected' : '' }}>{{ trans('cruds.action.types.major') }}</option>
                        <option value="2" {{ old('type')==2 ? 'selected' : '' }}>{{ trans('cruds.action.types.minor') }}</option>
                        <option value="3" {{ old('type')==3 ? 'selected' : '' }}>{{ trans('cruds.action.types.observation') }}</option>
                        <option value="4" {{ old('type')==4 ? 'selected' : '' }}>{{ trans('cruds.action.types.opportunity') }}</option>
                    </select>
                </div>
                <div class="col-md-2 col-lg-1 text-md-end">
                    <strong>{{ trans('cruds.action.fields.due_date') }}</strong>
                </div>
                <div class="col-md-2 col-lg-2 col-xl-1">
                        <input class="form-control" type="date"
                            name="due_date"
                            value="{{ old('due_date') }}"/>
    			</div>
            </div>

			<div class="row">
	    		<div class="col-md-2 col-lg-1">
                    <strong>{{ trans('cruds.action.fields.clauses') }}</strong>
		    	</div>
                <div class="col-md-12 col-lg-11">
                    <select class="form-select select2" name="measures[]" multiple>
                        @foreach($all_measures as $measure)
						    <option
                                value="{{ $measure->id }}"
                                {{ ((old('measures')!=null) && in_array($measure->id, old('measures'))) ? 'selected' : '' }}>
                                {{ $measure->clause }} - {{ $measure->name }}
                            </option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
                    <strong>{{ trans("cruds.action.fields.name") }}</strong>
	    		</div>
	    		<div class="col-md-5 col-lg-6">
                    <input class="form-control" type="text" name="name" value="{{ old('name') }}" maxlength="255" required>
				</div>
	    		<div class="col-md-1 col-lg-1 text-md-end">
		    		<strong>{{ trans("cruds.action.fields.scope") }}</strong>
                </div>
	    		<div class="col-md-4 col-lg-4">
                    <input class="form-control" type="text" name="scope" autocomplete="on" maxlength="32"
                    value="{{ old('scope') }}"/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-md-2 col-lg-1">
                    <strong>{{ trans('cruds.action.fields.cause') }}</strong>
		    	</div>
                <div class="col-md-12 col-lg-11">
                <textarea name="cause" class="easymde form-control" id="cause">{{ old('cause') }}</textarea>
				</div>
			</div>

    	<div class="row">
		</div>

    	<div class="row">
    		<div class="col-md-2 col-lg-1">
                <strong>{{ trans('cruds.action.fields.remediation') }}</strong>
	    	</div>
			<div class="col-md-12 col-lg-11">
                <textarea name="remediation" class="easymde form-control" id="remediation">{{ old('remediation') }}</textarea>
			</div>
		</div>

		<div class="row">
            <div class="col-md-2 col-lg-1">
                <strong>{{ trans('cruds.action.fields.owners') }}</strong>
            </div>
            <div class="col-md-12 col-lg-11">
                <select class="form-select select2" name="owners[]" id="owners" multiple>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id, old('owners', [])) ? 'selected' : ''  }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
    		<div class="col-md-2 col-lg-1">

            </div>
        </div>
		<div class="container-fluid px-0">
	    	<div class="mt-2">
				<button type="submit" class="btn btn-success">
                    <span class="bi bi-floppy"></span> {{ trans('common.save') }}
				</button>
				&nbsp;
                <a class="btn btn-light border" href="/actions">
	    			<span class="bi bi-x-circle"></span> {{ trans("common.cancel") }}
                </a>
    		</div>
		</div>
	</div>
	</form>
</div>
</div>
@endsection
