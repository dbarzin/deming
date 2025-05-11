@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.domain.show') }}" data-collapsible="true" data-title-icon="<span class='mif-library'></span>">
    @include('partials.errors')
	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.domain.fields.framework') }}</strong>
	    	</div>
            <div class="cell-6">
	    		{{ $domain->framework }}
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.domain.fields.name') }}</strong>
	    	</div>
            <div class="cell-6">
	    		{{ $domain->title }}
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.domain.fields.description') }}</strong>
	    	</div>
            <div class="cell-6">
	    		{{ $domain->description }}
    		</div>
    	</div>
    </div>

	<div class="row">
		<div class="cell-1">
		</div>
	</div>

	<div class="row">
        <div class="cell-6">
    			@if (Auth::User()->role==1)
                <a href="/domains/{{ $domain->id }}/edit" class="button primary">
    		            <span class="mif-wrench"></span>
    		            &nbsp;
    		    		{{ trans('common.edit') }}
                </a>
    			<form action="/domains/{{ $domain->id }}" class="d-inline" method="post" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
    	           {{ method_field('delete') }}
    	           @csrf
    		        <button class="button alert" type="submit">
    					<span class="mif-fire"></span>
    					&nbsp;
    			        {{ trans('common.delete') }}
    		    	</button>
            	</form>
    			@endif
                <a href="/domains" class="button">
					<span class="mif-cancel"></span>
					&nbsp;
		    		{{ trans('common.cancel') }}
                </a>
    	</div>
    </div>
</div>
@endsection
