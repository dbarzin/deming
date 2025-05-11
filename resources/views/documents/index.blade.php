@extends("layout")

@section("content")
<?php
function bytesToHuman($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
    return round($bytes, 2) . ' ' . $units[$i];
}
?>
<div data-role="panel" data-title-caption="{{ trans('cruds.document.title.templates') }}" data-collapsible="true" data-title-icon="<span class='mif-file-text'></span>">

    <form action="/doc/template" method="POST" role="form" enctype="multipart/form-data">
    @csrf
        <div class="grid">
            <div class="row">
                <div class="cell-4">
                    <a href="/doc/template?id=1" target="_new">{{ trans('cruds.document.model.control') }}</a>
                    @if (file_exists(storage_path('app/models/control_.docx')))
                        / <a href="/doc/template?id=2" target="_new">{{ trans('cruds.document.model.custom') }}</a>
                    @endif
                    <!-- input type="file" data-role="file" name="template1"/-->
                    <br><br><input type="file" name="template1"/>
                </div>
            </div>
            <div class="row">
                <div class="cell-4">
                    <a href="/doc/template?id=3" target="_new">{{ trans('cruds.document.model.report') }}</a>
                    @if (file_exists(storage_path('app/models/pilotage_.docx')))
                        / <a href="/doc/template?id=4" target="_new">{{ trans('cruds.document.model.custom') }}</a>
                    @endif
                    <!-- input type="file" data-role="file" name="template2"/-->
                    <br><br><input type="file" name="template2"/>
                </div>
            </div>
            <div class="row">
                <div class="cell-4">

                <button type="submit" class="button success"><span class="mif-ok"></span>
                    <span class="mif-floppy-disk2"></span>
                    &nbsp;
                    {{ trans("common.save") }}
                </button>
                &nbsp;
                <a class="button cancel" href="/">
                    <span class="mif-cancel"></span>
                    &nbsp;
                    {{ trans("common.cancel") }}
                </a>
                </div>
            </div>
        </div>
    </form>
</div>

<div data-role="panel" data-title-caption="{{ trans('cruds.document.title.storage') }}" data-collapsible="true" data-title-icon="<span class='mif-file-text'></span>">

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
            <a href="/doc/check" class="button success">
                <span class="mif-done-all"></span>
                &nbsp;
                {{ trans('common.check') }}
            </a>
        </div>
    </div>
</div>
@endsection
