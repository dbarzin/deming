@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-clipboard-check"></i> {{ trans("cruds.measure.create")}}</div>
    <div class="card-body">

    @include('partials.errors')

	<form method="POST" action="/bob/store">
		@csrf

		<div class="container-fluid px-0">

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.measure.fields.clauses") }}</strong>
				</div>
	    		<div class="col-lg-6 col-md-8">
                    <select class="form-select select2" id="controls" name="controls[]" multiple >
						@foreach($all_controls as $control)
                            <option value="{{ $control->id }}"
                                {{ in_array($control->id, old('controls', [])) ? 'selected' : '' }}>
                                {{ $control->clause }} - {{ $control->name }}
                            </option>
					    @endforeach
					 </select>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.measure.fields.name") }}</strong>
	    		</div>
                <div class="col-lg-4 col-md-5">
					<input class="form-control" type="text" name="name" value="{{ old('name') }}" maxlength="255">
				</div>
                <div class="col-lg-1 col-md-1 text-md-end">
		    		<strong>{{ trans("cruds.measure.fields.scope") }}</strong>
                </div>
                <div class="col-lg-2 col-xl-1 col-md-2">
					<input class="form-control" type="text" name="scope" autocomplete="off" maxlength="32"
                    value="{{ old('scope') }}"/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.measure.fields.objective") }}</strong>
		    	</div>
				<div class="col-lg-6 col-md-8">
                    <textarea name="objective" class="easymde form-control" id="objective">{{ old('objective') }}</textarea>
				</div>
			</div>

			<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans('cruds.measure.fields.attributes') }}</strong>
		    	</div>
				<div class="col-lg-6 col-md-8">
                    <select class="form-select select2" id="attributes" name="attributes[]" multiple>
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
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.measure.fields.input") }}</strong>
		    	</div>
				<div class="col-lg-6 col-md-8">
                    <textarea name="input" class="easymde form-control" id="input">{{ old('input') }}</textarea>
				</div>
			</div>
			<div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.measure.fields.model') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <textarea class="form-control" name="model" rows="3">{{ old('model') }}</textarea>
                </div>
            </div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.measure.fields.plan_date") }}</strong>
		    	</div>
				<div class="col-lg-2 col-md-4">
					<input class="form-control" type="date"
						name="plan_date"
						value="{{ old('plan_date') }}"/>
				</div>
				<div class="col-lg-1 col-md-2">
                </div>
			</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans("cruds.measure.fields.action_plan") }}</strong>
		    	</div>
				<div class="col-lg-6 col-md-8">
                    <textarea name="action_plan" class="easymde form-control" id="action_plan">{{ old('action_plan') }}</textarea>
				</div>
			</div>


			<div class="row">
	    		<div class="col-lg-1 col-md-2">
		    		<strong>{{ trans('cruds.measure.fields.periodicity') }}</strong>
		    	</div>
				<div class="col-lg-2 col-md-4">
                    <select class="form-select select2" id='periodicity' name="periodicity">
					    <option value="0" {{ old("periodicity")=="0" ? "selected" : ""}}>{{ trans('common.once') }}</option>
					    <option value="-1" {{ old("periodicity")=="-1" ? "selected" : ""}}>{{ trans('common.weekly') }}</option>
					    <option value="1" {{ old("periodicity")=="1" ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
					    <option value="3" {{ old("periodicity")=="3" ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
					    <option value="6" {{ old("periodicity")=="6" ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
					    <option value="12" {{ (old("periodicity")==null||old("periodicity")=="12") ? "selected" : ""}}>{{ trans('common.annually') }}</option>
					 </select>
				</div>
			</div>

			<div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.measure.fields.owners') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <select class="form-select select2" name="owners[]" id="owners" multiple>
                        @foreach($owners as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, old('owners', [])) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

	    	<div class="row">
                <div class="col-lg-12 col-md-12">
    				<button type="submit" class="btn btn-success">
                        <span class="bi bi-floppy"></span> {{ trans("common.save") }}
    				</button>
    				&nbsp;
                    <a class="btn cancel btn-light border" href="/bob/index" role="button">
                        <span class="bi bi-x-circle"></span> {{ trans("common.cancel") }}
                    </a>
                </div>
    		</div>
    	</div>
    </form>
</div>
</div>
@endsection
