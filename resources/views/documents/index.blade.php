@extends("layout")

@section("content")
<?php
function bytesToHuman($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
    return round($bytes, 2) . ' ' . $units[$i];
}
?>
<div class="row">

{{-- Aide : balises du modèle du rapport de pilotage (à droite, en dessous sur mobile) --}}
<div class="col-md-5 col-xl-4 order-2">
    @include('partials.report-template-help')
</div>

<div class="col-md-7 col-xl-8 order-1">
<div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'documents'])<i class="bi bi-file-earmark-text"></i> {{ trans('cruds.document.title.templates') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form action="/doc/template" method="POST" role="form" enctype="multipart/form-data">
    @csrf
        <div class="container-fluid px-0">
            <div class="row">
                <div class="col-md-10 col-xl-8">
                    <a href="/doc/template?id=1" target="_new">{{ trans('cruds.document.model.control') }}</a>
                    @if (file_exists(storage_path('app/models/control_.docx')))
                        / <a href="/doc/template?id=2" target="_new">{{ trans('cruds.document.model.custom') }}</a>
                    @endif
                    <input class="form-control" type="file" name="template1"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-xl-8">
                    <a href="/doc/template?id=3" target="_new">{{ trans('cruds.document.model.report') }}</a>
                    @if (file_exists(storage_path('app/models/pilotage_.docx')))
                        / <a href="/doc/template?id=4" target="_new">{{ trans('cruds.document.model.custom') }}</a>
                    @endif
                    <input class="form-control {{ $errors->has('template2') ? 'is-invalid' : '' }}" type="file" name="template2" accept=".docx"/>
                    <span class="help-block">{{ trans('report_template.upload_helper') }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-xl-6">

                <button type="submit" class="btn btn-success">
                    <span class="bi bi-floppy"></span> {{ trans("common.save") }}
                </button>
                &nbsp;
                <a class="btn cancel btn-light border" href="/">
                    <span class="bi bi-x-circle"></span> {{ trans("common.cancel") }}
                </a>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
<div class="card">
    <div class="card-header"><i class="bi bi-file-earmark-text"></i> {{ trans('cruds.document.title.storage') }}</div>
    <div class="card-body">
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-md-6 col-xl-4">
            {{ trans('cruds.document.count') }} : {{ $count }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-4">
            {{ trans('cruds.document.total_size') }} : {{ bytesToHuman($sum) }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <a href="/doc/check" class="btn btn-success">
                    <span class="bi bi-check-all"></span> {{ trans('common.check') }}
                </a>
            </div>
        </div>
    </div>
</div>
</div>
<div class="card">
    <div class="card-header"><i class="bi bi-file-earmark-text"></i> {{ trans('cruds.document.title.cleanup') }}</div>
    <div class="card-body">
    <form id="cleanupForm" action="/doc/config" method="POST">
	@csrf
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-md-8 col-xl-6">
                {{ trans('cruds.document.title.cleanup_detail') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="input-group">
                <span class="input-group-text">{{ trans('cruds.document.month') }}</span>
                <select class="form-select select2" id="durationSelect" name="duration">
                    <option value="0" {{ ($duration=="0" || $duration==null)  ? 'selected' : '' }}>{{ trans('cruds.document.never') }}</option>
                    <option {{ $duration=="12" ? 'selected' : '' }}>12</option>
                    <option {{ $duration=="24" ? 'selected' : '' }}>24</option>
                    <option {{ $duration=="36" ? 'selected' : '' }}>36</option>
                    <option {{ $duration=="48" ? 'selected' : '' }}>48</option>
                    <option {{ $duration=="60" ? 'selected' : '' }}>60</option>
                </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 col-xl-8">
                <button type="submit" class="btn btn-success" name="action" value="save">
                    <span class="bi bi-floppy"></span> {{ trans("common.save") }}
                </button>
                &nbsp;
                <button type="submit" class="btn btn-info" name="action" value="test">
                    <span class="bi bi-eyedropper"></span> {{ trans("common.test") }}
                </button>
                &nbsp;
                <button type="submit" class="btn btn-danger" name="action" value="delete" id="deleteButton">
                    <span class="bi bi-trash"></span> {{ trans("common.delete") }}
                </button>
            </div>
        </div>
    </div>
    </form>
</div>
</div>

</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("deleteButton").addEventListener("click", function (event) {
        const select = document.getElementById("durationSelect");
        const selected = select.options[select.selectedIndex].value;
        if ((selected==0)||(!confirm("{{ trans('cruds.measure.confirm_delete') }}")))
            event.preventDefault();
    });
});
</script>

@endsection
