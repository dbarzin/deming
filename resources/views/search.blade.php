@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="Résultats de la recherche" data-collapsible="true" data-title-icon="<span class='mif-search'></span>">
		@foreach($results as $result)
		@if ($result['model']==='App\\Models\\Domain')
		   	<b>{{ trans("cruds.domain.title")}}</b> - 
		   	<a href="/domains/{{ $result['id'] }}">{{ $result['title'] }}</a> : {{ $result['description'] }}
		@elseif ($result['model']==='App\\Models\\Measure')
		   	<b>{{ trans("cruds.measure.title")}}</b> - 
			<a href="/measures/{{ $result['id'] }}">{{ $result['clause'] }}</a> : {{ $result["name"] }}
		@elseif ($result['model']==='App\\Models\\Control')
		   	<b>{{ trans("cruds.control.title_singular")}}</b> - 
		   	<a href="/controls/{{ $result['id'] }}">{{ $result["clause"] }}</a> : {{ $result["name"] }}
			@if (array_key_exists("realisation_date",$result))
				- {{ $result["realisation_date"] }}
			@endif
		@endif
		<br>
		@endforeach
	</div>
	</div>	
@endsection



