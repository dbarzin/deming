@extends("layout")

@section("content")
<?php
function bytesToHuman($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
    return round($bytes, 2) . ' ' . $units[$i];
}
?>

<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.document.title.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

    <ul>
        <li>
            {{ trans('cruds.document.count') }} : {{ $count }}
        </li>
        <li>
            {{ trans('cruds.document.total_size') }} : {{ bytesToHuman($sum) }} 
        </li>
    </ul>

    <br>
        <form action="/doc/check">
            <button type="submit" class="button success">{{ trans('common.check') }}</button>
        </form>
    </div>
</div>

@endsection