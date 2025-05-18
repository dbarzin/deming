@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.control.plan') }}" data-collapsible="false" data-title-icon="<span class='mif-paste'></span>">

    @include('partials.errors')

    <form method="POST" action="/bob/plan">
    @csrf

    <input type="hidden" name="id" value="{{ $control->id }}"/>

    <div class="grid">
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
        	</div>
    		<div class="cell-lg-4 cell-md-5">
                @foreach($control->measures as $measure)
                    <a href="/alice/show/{{ $measure->id }}">{{ $measure->clause }}</a>
                    @if(!$loop->last)
                    ,
                    @endif
                @endforeach
            </div>
        </div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.control.fields.name') }}</strong>
        	</div>
    		<div class="cell-lg-4 cell-md-5">
        		{{ $control->name }}
    		</div>
            @if ($control->scope!==null)
    		<div class="cell-lg-1 cell-md-2" align="right">
        		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
        	</div>
    		<div class="cell-lg-1 cell-md-2">
                <a href="/bob/index?scope={{ $control->scope }}">
    			{{ $control->scope }}
                </a>
    		</div>
            @endif
    	</div>
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.control.fields.objective') }}</strong>
        	</div>
            <div class="cell-lg-6 cell-md-10">
                {!! \Parsedown::instance()->text($control->objective) !!}
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
    			<strong>{{ trans('cruds.control.fields.plan_date') }}</strong>
        	</div>
    		<div class="cell-lg-2 cell-md-3">
    			<input type="text" data-role="calendarpicker" name="plan_date" value="{{
    			\Carbon\Carbon
    			::createFromFormat('Y-m-d',$control->plan_date)
    			->format('Y-m-d')
    			}}"
				data-format="YYYY-MM-DD"
				data-inputFormat="YYYY-MM-DD"
                />
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
        	</div>
    		<div class="cell-lg-2 cell-md-3">
    			<select name="periodicity" data-role="select">
    			    <option value="1" {{ $control->periodicity==0 ? "selected" : ""}}>{{ trans('common.once') }}</option>
    			    <option value="1" {{ $control->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
    			    <option value="3" {{ $control->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
    			    <option value="6" {{ $control->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
    			    <option value="12" {{ $control->periodicity==12 ? "selected" : ""}}>{{ trans('common.annually') }}</option>
    			 </select>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
    			<strong>{{ trans('cruds.control.fields.owners') }}</strong>
        	</div>
            <div class="cell-lg-4 cell-md-8">
                <select data-role="select" name="owners[]" id="owners" multiple>
                    @foreach($owners as $id => $name)
                        <option
                            value="{{ $id }}"
                            {{ (in_array($id, old('owners', []))) ||
                                (
                                    (str_starts_with($id,'USR_') && $control->users->contains(intval(substr($id, 4)))) ||
                                    (str_starts_with($id,'GRP_') && $control->groups->contains(intval(substr($id, 4))))
                                )
                                ? 'selected' : '' }}>
                        {{ $name }}
                        </option>
                    @endforeach
                </select>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-12">
    		@if (Auth::User()->role !== 3)
    			<button type="submit" class="button success">
    				<span class="mif-calendar"></span>
    				&nbsp;
    				{{ trans("common.plan") }}
    			</button>
    			</form>
    			&nbsp;
    		@endif
    		@if (Auth::User()->role !== 3)
                <form action="/bob/unplan" method="POST" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}" class="d-inline">
    				@csrf
    				<input type="hidden" name="id" value="{{ $control->id }}"/>
    	            <button class="button alert" type="submit">
    					<span class="mif-fire"></span>
    					&nbsp;
    					{{ trans("common.unplan") }}
    				</button>
    			</form>
    			&nbsp;
    		@endif
            <a href="/bob/show/{{$control->id}}" class="button">
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
