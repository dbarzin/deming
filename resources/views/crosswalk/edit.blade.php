@extends('layout')

@section('content')
<div class="card">
    <div class="card-header"><i class="bi bi-shuffle"></i> {{ trans('cruds.crosswalk.edit') }}</div>
    <div class="card-body">
    <form method="POST" action="{{ route('crosswalk.update', $mapping) }}">
        @csrf
        @method('PUT')
        @include('crosswalk._form')
    </form>
</div>
</div>
@endsection
