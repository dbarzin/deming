@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.measure.plan') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
    <form method="POST" action="/measure/activate/{{ $measure->id }}">
	@csrf
	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.domain') }}</strong>
	    	</div>
			<div class="cell">
				<a href="/domains/{{$measure->domain_id}}">
				{{ $measure->domain->title }}
				</a>
				-
				{{ $measure->domain->description }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
	    	</div>
			<div class="cell">
				{{ $measure->clause }} - {{ $measure->name }}
			</div>
	    </div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
	    	</div>
			<div class="cell-6">
				<pre>{!! $measure->objective !!}</pre>
			</div>
	    </div>

		<div class="row">
			<div class="cell-1">
				<strong>{{ trans('cruds.control.fields.plan_date') }}</strong>
	    	</div>
			<div class="cell-2">
					<input type="text" data-role="calendarpicker" name="plan_date" 
					value="{{ $measure->planDate() }}" data-input-format="%Y-%m-%d"> 
			</div>
		</div>

    	<div class="row">
			<div class="cell-1">
	    		<strong>{{ trans('cruds.measure.fields.periodicity') }}</strong>
	    	</div>
			<div class="cell-2">
				<select name="periodicity" data-role="select">
				    <option value="1" {{ $measure->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
				    <option value="3" {{ $measure->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
				    <option value="6" {{ $measure->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
				    <option value="12" {{ $measure->periodicity==12 ? "selected" : ""}}>{{ trans('common.annually') }}</option>
				 </select>
			</div>
		</div>
    </div>

	<div class="row">
		<div class="cell-1">
			<strong>{{ trans('cruds.control.fields.owners') }}</strong>
    	</div>
		<div class="cell-4">
            <select data-role="select" name="owners[]" id="owners" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (in_array($user->id, old('owners', [])) || $measure->control()->owners->contains($user->id)) ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
			
		</div>
	</div>

	<div class="row">
		<div class="cell">
		</div>
	</div>

	<div class="form-group">
    	<button class="button success">
            <span class="mif-calendar"></span>
            &nbsp;
	    	{{ trans('common.plan') }}
    	</button>
	    </form>
		@if ($measure->planDate()!==null)
		&nbsp;
	    <form action="/measure/unplan/{{ $measure->id }}">
	    	<button class="button alert">
	            <span class="mif-calendar"></span>
	            &nbsp;
		    	{{ trans('common.unplan') }}
	    	</button>
	    </form>
		@endif
	    &nbsp;
	    <form action="/measures">
	    	<button class="button">
				<span class="mif-cancel"></span>
				&nbsp;
		    	{{ trans('common.cancel') }}
	    	</button>
		</form>
	</div>
</div>
</div>

@endsection

