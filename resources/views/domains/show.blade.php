@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.domain.show') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.domain.fields.name') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $domain->title }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.domain.fields.description') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $domain->description }}
	    		</div>
	    	</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
    		</div>
    	</div>
    	
		<div class="form-group">
		    <form action="/domains/{{ $domain->id }}/edit">
	           @csrf
		    	<button class="button primary">
		            <span class="mif-wrench"></span>
		            &nbsp;
		    		{{ trans('common.edit') }}
		    	</button>
		        &nbsp;
		    </form>
			<form action="/domains/{{ $domain->id }}" method="post">
	           {{ method_field('delete') }}
	           @csrf
		        <button class="button alert" type="submit">
					<span class="mif-fire"></span>
					&nbsp;
			        {{ trans('common.delete') }}
		    	</button>
        	</form>
			&nbsp;
			<form action="/domains">
	           @csrf
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