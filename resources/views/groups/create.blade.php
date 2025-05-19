@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.group.add') }}" data-collapsible="false" data-title-icon="<span class='mif-group'></span>">

    @include('partials.errors')

    <form method="POST" action="/groups">
	@csrf
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
                    <label>{{ trans('cruds.group.fields.name') }}</label>
		    	</div>
	    		<div class="cell-lg-4 cell-md-6">
                    <input type="text" class="input {{ $errors->has('name') ? 'is-danger' : ''}}" name="name" value="{{ old('name') }}" maxsize='90' required>
				</div>
			</div>
	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
                    <label>{{ trans('cruds.group.fields.description') }}</label>
		    	</div>
                <div class="cell-lg-8 cell-md-10">
                    <textarea name="description" rows="5" data-role="textarea" data-clear-button="false">{{  old('description') }}</textarea>
				</div>
			</div>
	    	<div class="row">
	    		<div class="cell-lg-1 cell-md-2">
                    <label>{{ trans('cruds.group.fields.users') }}</label>
				</div>
                <div class="cell-lg-8 cell-md-10">
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
		        <div class="cell-lg-1 cell-md-2">
		            <label>{{ trans('cruds.group.fields.controls') }}</label>
		    	</div>
		        <div class="cell-lg-8 cell-md-10">
		            <select data-role="select" data-filter="true" name="controls[]" id="controls" multiple>
		                @foreach($all_controls as $control)
							<option value="{{ $control->id }}" {{ (in_array($control->id, old('controls', []))) ? 'selected' : '' }}>{{ $control->name }}</option>
		                @endforeach
		            </select>
		        </div>
		    </div>

	    	<div class="row">
                <br><br>
            </div>

		</div>
    	<div class="row">
            <div class="cell-lg-8 cell-md-10">
				<button type="submit" class="button success">
                    <span class="mif-floppy-disk2"></span>
					&nbsp;
					{{ trans('common.save') }}
				</button>
                <a href="/groups" class="button">
					<span class="mif-cancel"></span>
					&nbsp;
					{{ trans('common.cancel') }}
                </a>
			</div>
		</div>
	</form>
</div>

@endsection
