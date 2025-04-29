@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="Résultats de la recherche" data-collapsible="true" data-title-icon="<span class='mif-search'></span>">
	@foreach($results as $result)
	@if ($result['model']==='App\\Models\\Domain')
	   	<b>{{ trans("cruds.domain.title")}}</b> -
	   	<a href="/domains/{{ $result['id'] }}">{{ $result['title'] }}</a> : {{ $result['description'] }}
	@elseif ($result['model']==='App\\Models\\Measure')
	   	<b>{{ trans("cruds.measure.title")}}</b> -
		<a href="/alice/show/{{ $result['id'] }}">{{ $result['clause'] }}</a> : {{ $result["name"] }}
	@elseif ($result['model']==='App\\Models\\Control')
	   	<b>{{ trans("cruds.control.title_singular")}}</b> -
        {{ $result["name"] }} -
        <a href="/bob/show/{{ $result['id'] }}">{{ $result["plan_date"] }}</a>
	@endif
	<br>
	@endforeach
</div>
@endsection
