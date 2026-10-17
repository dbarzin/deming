@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-people"></i> {{ trans('cruds.group.add') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form method="POST" action="/groups">
	@csrf
		<div class="container-fluid px-0">
	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
                    <label>{{ trans('cruds.group.fields.name') }}</label>
		    	</div>
	    		<div class="col-lg-4 col-md-6">
                    <input type="text" class="{{ $errors->has('name') ? 'is-invalid' : ''}} form-control" name="name" value="{{ old('name') }}" maxsize='90' required>
				</div>
			</div>
	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
                    <label>{{ trans('cruds.group.fields.type') }}</label>
		    	</div>
	    		<div class="col-lg-4 col-md-6">
                    <select class="{{ $errors->has('type') ? 'is-invalid' : ''}} form-select" name="type" required>
                        @foreach(\App\Models\UserGroup::TYPES as $type)
                            <option value="{{ $type }}" {{ old('type', \App\Models\UserGroup::TYPE_BUSINESS) === $type ? 'selected' : '' }}>{{ trans('cruds.group.types.' . $type) }}</option>
                        @endforeach
                    </select>
				</div>
			</div>
	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
                    <label>{{ trans('cruds.group.fields.description') }}</label>
		    	</div>
                <div class="col-lg-8 col-md-10">
                    <textarea class="form-control" name="description" rows="5">{{  old('description') }}</textarea>
				</div>
			</div>
	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
                    <label>{{ trans('cruds.group.fields.users') }}</label>
				</div>
                <div class="col-lg-8 col-md-10">
                    <select class="form-select select2" name="users[]" multiple>
                        @foreach($all_users as $user)
                            <option value="{{ $user->id }}">
                                    {{ $user->name }}
                            </option>
					    @endforeach
					 </select>
				</div>
			</div>

			<div class="row">
		        <div class="col-lg-1 col-md-2">
		            <label>{{ trans('cruds.group.fields.controls') }}</label>
		    	</div>
		        <div class="col-lg-8 col-md-10">
		            <select class="form-select select2" name="measures[]" id="measures" multiple>
		                @foreach($all_measures as $measure)
							<option value="{{ $measure->id }}" {{ (in_array($measure->id, old('measures', []))) ? 'selected' : '' }}>{{ $measure->name }}</option>
		                @endforeach
		            </select>
		        </div>
		    </div>

	    	<div class="row">
                <br><br>
            </div>

		</div>
    	<div class="row">
            <div class="col-lg-8 col-md-10">
				<button type="submit" class="btn btn-success">
                    <span class="bi bi-floppy"></span> {{ trans('common.save') }}
				</button>
                <a href="/groups" class="btn btn-light border">
					<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
                </a>
			</div>
		</div>
	</form>
</div>
</div>

@endsection
