@extends("layout")

@section("content")
<style type="text/css">
form, table {
     display:inline;
     margin:0px;
     padding:0px;
}
</style>

<div data-role="panel" data-title-caption="{{ trans('cruds.action.show') }}" data-collapsible="true" data-title-icon="<span class='mif-pending-actions'></span>">

    @include('partials.errors')

    <form method="POST" action="/action/update">
		@csrf
		<input type="hidden" name="id" value="{{ $action->id }}"/>

		<div class="grid">
        	<div class="row">
        		<div class="cell-1">
                    <strong>{{ trans("cruds.action.fields.reference") }}</strong>
    	    	</div>
                <div class="cell-2">
                    {{ $action->reference }}
                </div>
                <div class="cell-1" align="right">
                    <strong>{{ trans("cruds.action.fields.type") }}</strong>
    	    	</div>
                <div class="cell-1">
                    {{ $action->type }}
                </div>
                <div class="cell-1" align="right">
                    <strong>{{ trans('cruds.action.fields.due_date') }}</strong>
    	    	</div>
                <div class="cell-2">
                    {{$action->due_date}}
    			</div>
            </div>

        	<div class="row">
        		<div class="cell-1">
                    <strong>{{ trans("cruds.action.fields.clauses") }}</strong>
    	    	</div>
        		<div class="cell-4">
                    @foreach($action->measures as $measure)
                        <a href="/alice/show/{{ $measure->id }}">{{ $measure->clause }}</a>
                        @if(!$loop->last)
                        ,
                        @endif
                    @endforeach
                </div>
            </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.action.fields.name') }}</strong>
		    	</div>
                <div class="cell-5">
                    @if ($action->control_id!==null)
                    <a href="/bob/show/{{ $action->control_id }}">
					    {{ $action->name }}
                    </a>
                    @else
					    {{ $action->name }}
                    @endif
				</div>
                @if ($action->scope!==null)
                <div class="cell-2">
		    		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
		    		&nbsp;
	    			{{ $action->scope }}
	    		</div>
                @endif
			</div>

	    	<div class="row">
	    		<div class="cell-1">
                    <strong>{{ trans('cruds.action.fields.cause') }}</strong>
		    	</div>
				<div class="cell-5">
                    <pre>{{ $action->cause }}</pre>
				</div>
			</div>

    	<div class="row">
		</div>

    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.remediation') }}</strong>
	    	</div>
			<div class="cell-6">
                @if ($action->status==0)
                    <textarea name="remediation" class="easymde" id="remediation">{{ $errors->has('remediation') ?  old('remediation') : $action->remediation }}</textarea>
                @else
                    {!! \Parsedown::instance()->text($action->remediation) !!}
                @endif
			</div>
		</div>
        @if ($action->status!=0)
    	<div class="row">
			<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.status') }}</strong>
	    	</div>
            <div class="cell-4">
                @if ($action->status==0)
                    {{ trans('cruds.action.fields.status_open') }}
                @elseif ($action->status==1)
                    {{ trans('cruds.action.fields.status_closed') }}
                @elseif ($action->status==2)
                    {{ trans('cruds.action.fields.status_rejected') }}
                @else
                    {{ $action->status }}
                @endif

	    	</div>
			<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.close_date') }}</strong>
	    	</div>
			<div class="cell-1">
                {{ $action->close_date}}
            </div>
        </div>
    	<div class="row">
    		<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.justification') }}</strong>
	    	</div>
			<div class="cell-6">
                {!! \Parsedown::instance()->text($action->justification) !!}
			</div>
		</div>
        @endif

    	<div class="row">
			<div class="cell-1">
                <strong>{{ trans('cruds.action.fields.owners') }}</strong>
	    	</div>
			<div class="cell">
                @foreach($action->owners as $owner)
					{{ $owner->name }}
                    @if ($action->owners->last()!=$owner)
					,
					@endif
				@endforeach
			</div>
	    </div>

        <div class="row">
    		<div class="cell-1">

            </div>
        </div>
		<div class="grid">
	    	<div class="row-12">
                @if ($action->status==0)
				<button type="submit" class="button success">
                    <span class="mif-floppy-disk2"></span>
		            &nbsp;
					{{ trans('common.save') }}
				</button>
	            &nbsp;
                    <a class="button info" href="/action/close/{{ $action->id }}">
                        <span class="mif-done"></span>
    					&nbsp;
                        {{ trans("common.close") }}
                    </a>
	            &nbsp;
                @endif
                <a class="button primary" href="/action/edit/{{ $action->id }}">
    		            <span class="mif-wrench"></span>
    		            &nbsp;
    			    	{{ trans('common.edit') }}
                </a>
				&nbsp;
                <button class="button alert" type="submit" onclick='this.form.action="/action/delete"'
                    onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
					<span class="mif-fire"></span>
					&nbsp;
				    {{ trans('common.delete') }}
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
