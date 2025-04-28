@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.attribute.show') }}" data-collapsible="true" data-title-icon="<span class='mif-tags'></span>">
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.attribute.fields.name') }}</strong>
		    	</div>
	    		<div class="cell">
		    		{{ $attribute->name }}
	    		</div>
		    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans('cruds.attribute.fields.values') }}</strong>
		    	</div>
	    		<div class="cell-6">
		    		{{ $attribute->values }}
	    		</div>
	    	</div>
	    </div>

    	<div class="row">
    		<div class="cell-1">
    		</div>
    	</div>

		<div class="form-group">
			@if (Auth::User()->role==1)
		    <form action="/attributes/{{ $attribute->id }}/edit">
		    	<button class="button primary" type="submit">
		            <span class="mif-wrench"></span>
		            &nbsp;
		    		{{ trans('common.edit') }}
		    	</button>
		    </form>
	        &nbsp;
			<form action="/attributes/{{ $attribute->id }}" method="post" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
	           {{ method_field('delete') }}
	           @csrf
		        <button class="button alert" type="submit">
					<span class="mif-fire"></span>
					&nbsp;
			        {{ trans('common.delete') }}
			    </button>
	        </form>
			&nbsp;
			@endif
			<form action="/attributes">
		    	<button class="button" type="submit">
					<span class="mif-cancel"></span>
					&nbsp;
		    		{{ trans('common.cancel') }}
		    	</button>
			</form>
		</div>
	</div>
</div>
@endsection
