@extends("layout")

@section("content")
<div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'search'])<i class="bi bi-search"></i> {{ trans("common.search_results") }}</div>
    <div class="card-body">
	@foreach($results as $result)
	@if ($result['model']==='App\\Models\\Domain')
	   	<b>{{ trans("cruds.domain.title")}}</b> -
	   	<a href="/domains/{{ $result['id'] }}">{{ $result['title'] }}</a> : {{ $result['description'] }}
	@elseif ($result['model']==='App\\Models\\Control')
	   	<b>{{ trans("cruds.control.title")}}</b> -
		<a href="/alice/show/{{ $result['id'] }}">{{ $result['clause'] }}</a> : {{ $result["name"] }}
	@elseif ($result['model']==='App\\Models\\Measure')
	   	<b>{{ trans("cruds.measure.title_singular")}}</b> -
        {{ $result["name"] }}
        @if ($result["scope"]!==null)
        - {{ $result["scope"] }} -
        @endif
        <a href="/bob/show/{{ $result['id'] }}">{{ $result["plan_date"] }}</a>
	@endif
	<br>
	@endforeach
</div>
</div>
@endsection
