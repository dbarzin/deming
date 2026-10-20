@extends("layout")

@section("content")
<div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'groups'])<i class="bi bi-people"></i> {{ trans('cruds.group.edit') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form method="POST" action="/groups/{{ $group->id }}">
	@method("PATCH")
	@csrf
	<div class="container-fluid px-0">

    	<div class="row">
    		<div class="col-lg-1 col-md-2">
                <label>{{ trans('cruds.group.fields.name') }}</label>
			</div>
            <div class="col-lg-11 col-md-10">
                <input type="text" class="{{ $errors->has('name') ? 'is-invalid' : ''}} form-control" name="name" value="{{ old('name', $group->name) }}" maxlength='90' required>
			</div>
		</div>

	    	<div class="row">
	    		<div class="col-lg-1 col-md-2">
                    <label>{{ trans('cruds.group.fields.type') }}</label>
		    	</div>
	    		<div class="col-lg-11 col-md-10">
                    <select class="{{ $errors->has('type') ? 'is-invalid' : ''}} form-select" name="type" required>
                        @foreach(\App\Models\UserGroup::TYPES as $type)
                            <option value="{{ $type }}" {{ old('type', $group->type) === $type ? 'selected' : '' }}>{{ trans('cruds.group.types.' . $type) }}</option>
                        @endforeach
                    </select>
				</div>
			</div>

    	<div class="row">
    		<div class="col-lg-1 col-md-2">
                <label>{{ trans('cruds.group.fields.description') }}</label>
			</div>
            <div class="col-lg-11 col-md-10">
                <textarea class="form-control" name="description" rows="5">{{ old('description', $group->description) }}</textarea>
			</div>
		</div>

	<div class="row">
        <div class="col-lg-1 col-md-2">
            <strong>{{ trans('cruds.group.fields.users') }}</strong>
    	</div>
        <div class="col-lg-11 col-md-10">
            <select class="form-select select2" name="users[]" id="users" multiple>
                @foreach($all_users as $user)
                    <option value="{{ $user->id }}" {{ (in_array($user->id, old('users', [])) || $group->users->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

	<div class="row">
        <div class="col-lg-1 col-md-2">
            <strong>{{ trans('cruds.group.fields.controls') }}</strong>
    	</div>
        <div class="col-lg-11 col-md-10">
            <select class="form-select select2" name="measures[]" id="measures" multiple>
                @foreach($all_measures as $measure)
                    <option value="{{ $measure->id }}" {{ (in_array($measure->id, old('measures', [])) || $group->measures->contains($measure->id)) ? 'selected' : '' }}>{{ $measure->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

	<div class="row">
		<div class="col-lg-1 col-md-2">
		</div>
	</div>

	<div class="row">
        <div class="col-lg-12 col-md-12">

    		<button type="submit" class="btn btn-success">
                <span class="bi bi-floppy"></span> {{ trans('common.save') }}
    		</button>
            &nbsp;
            <a href="/groups/{{ $group->id }}" class="btn btn-light border">
        		<span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}</button>
            </a>
        </div>
    </div>
</form>
</div>
</div>
</div>
@endsection
