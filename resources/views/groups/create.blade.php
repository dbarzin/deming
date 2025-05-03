@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.group.add') }}" data-collapsible="true" data-title-icon="<span class='mif-users'></span>">

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

        <form method="POST" action="/groups">
		@csrf
			<div class="grid">
		    	<div class="row">
		    		<div class="cell-1">
                        <label>{{ trans('cruds.group.fields.name') }}</label>
			    	</div>
		    		<div class="cell-2">
                        <input type="text" class="input {{ $errors->has('name') ? 'is-danger' : ''}}" name="name" value="{{ old('name') }}" maxsize='90' required>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
                        <label>{{ trans('cruds.group.fields.description') }}</label>
			    	</div>
                    <div class="cell-6">
                        <textarea name="description" rows="5" data-role="textarea" data-clear-button="false">{{  old('description') }}</textarea>
					</div>
				</div>
		    	<div class="row">
		    		<div class="cell-1">
                        <label>{{ trans('cruds.group.fields.users') }}</label>
					</div>
                    <div class="cell-6">
                        <select data-role="select" name="users[]" multiple>
                            @foreach($all_users as $user)
                                <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                </option>
						    @endforeach
						 </select>
					</div>
				</div>
		    	<div class="row">
                    <br><br>
                </div>

			</div>
	    	<div class="row">
	    		<div class="cell-4">
					<button type="submit" class="button success">
			            <span class="mif-floppy-disk"></span>
						&nbsp;
						{{ trans('common.save') }}
					</button>
					<button type="submit" class="button" onclick="this.form.method='GET';">
						<span class="mif-cancel"></span>
						&nbsp;
						{{ trans('common.cancel') }}
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection
