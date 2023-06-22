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
    <div data-role="panel" data-title-caption="{{ trans('cruds.document.title.templates') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">


        <form action="/doc/template" method="POST" role="form" enctype="multipart/form-data">
        @csrf
            <div class="grid">
                <div class="row">
                    <div class="cell-4">
                        <a href="/doc/template?id=1" target="_new">{{ trans('cruds.document.model.control') }}</a>
                        <input type="file" data-role="file" name="template1">
                    </div>
                </div>
                <div class="row">
                    <div class="cell-4">
                        <a href="/doc/template?id=2" target="_new">{{ trans('cruds.document.model.report') }}</a>
                        <input type="file" data-role="file" name="template2">
                    </div>
                </div>
                <div class="row">
                    <div class="cell-4">

                    <button type="submit" class="button success"><span class="mif-ok"></span>
                        <span class="mif-floppy-disk"></span>
                        &nbsp;
                        {{ trans("common.save") }}
                    </button>
                    </form>
                    &nbsp;
                    <form action="/">
                        <button type="submit" class="button cancel" onclick='this.form.method="GET";this.form.action="/";'>
                            <span class="mif-cancel"></span>
                            &nbsp;
                            {{ trans("common.cancel") }}
                        </button>
                    </form>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.document.title.storage') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

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