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
    <div data-role="panel" data-title-caption="{{ trans('cruds.document.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

    <div class="grid">
        <div class="row">
            <div class="cell-3">
            {{ trans('cruds.document.count') }} : {{ $count }}
            </div>
        </div>
        <div class="row">
            <div class="cell-3">
            {{ trans('cruds.document.total_size') }} : {{ bytesToHuman($sum) }} 
            </div>
        </div>
        <div class="row">
            <div class="cell-3">
                <form action="/doc/check">
                    <button type="submit" class="button success">{{ trans('common.check') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection