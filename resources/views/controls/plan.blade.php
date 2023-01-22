@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.control.plan') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

	@if (count($errors))
	<div class= “form-group”>
		<div class= “alert alert-danger”>
			<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif

	<form method="POST" action="/control/plan">
	@csrf

	<input type="hidden" name="id" value="{{ $control->id }}"/>

	<div class="grid">
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.control.fields.name') }}</strong>
	    	</div>
			<div class="cell">
	    		<a href="/measures/{{ $control->measure_id }}">{{ $control->clause }}</a> &nbsp; - &nbsp; {{ $control->name }}
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.control.fields.objective') }}</strong>
	    	</div>
			<div class="cell">
				{{ $control->objective }}
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>Dernière mesure</strong>
	    	</div>
			<div class="cell">
				...
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
	    	</div>
			<div class="cell">
				@if ($control->periodicity==1) {{ trans('common.monthly') }} @endif
				@if ($control->periodicity==3) {{ trans('common.quarterly') }} @endif
				@if ($control->periodicity==6) {{ trans('common.biannually') }} @endif
				@if ($control->periodicity==12) {{ trans('common.annually') }} @endif				
			</div>
		</div>
    	<div class="row">
    		<div class="cell-1">
				<strong>{{ trans('cruds.control.fields.plan_date') }}</strong>
	    	</div>
			<div class="cell-2">
					<input type="text" data-role="calendarpicker" name="plan_date" value="{{ 
				\Carbon\Carbon
				::createFromFormat('Y-m-d',$control->plan_date)
				->format('Y-m-d')
				}}" 
				data-input-format="%Y-%m-%d"> 
			</div>
		</div>
    	<div class="row">
    		<div class="cell-12">
				<button type="submit" class="button success">
					<span class="mif-calendar"></span>
					&nbsp;
					{{ trans("common.plan") }}
				</button>
				&nbsp;
				<button type="submit" class="button" onclick="this.form.action='/control/'.{{ $control->id }};">
					<span class="mif-cancel"></span>
					&nbsp;
					{{ trans("common.cancel") }}
			</button>
			</div>
		</div>
	</div>
	</form>
</div>
</div>
@endsection

