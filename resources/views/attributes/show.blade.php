@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.attribute.show') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">
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
		    <form action="">
		    	<button class="button primary" onclick='this.form.action="/attributes/{{ $attribute->id }}/edit"';>
		            <span class="mif-wrench"></span>
		            &nbsp;
		    		{{ trans('common.edit') }}
		    	</button>
				&nbsp;
		    	<button class="button" onclick='this.form.action="/attributes";'>
					<span class="mif-cancel"></span>
					&nbsp;
		    		{{ trans('common.cancel') }}
		    	</button>
			</form>
		</div>
	</div>
</div>
@endsection