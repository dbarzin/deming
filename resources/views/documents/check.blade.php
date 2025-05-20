@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.document.list') }}" data-collapsible="false" data-title-icon="<span class='mif-file-text'></span>">

<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>{{ trans('cruds.document.fields.control') }}</th>
        <th>{{ trans('cruds.document.fields.name') }}</th>
        <th>{{ trans('cruds.document.fields.size') }}</th>
        <th>{{ trans('cruds.document.fields.hash') }}</th>
    </tr>
    </thead>

    @foreach ($documents as $doc)
    <tr>
        <td>
            {{ $doc->id }}
        </td>
        <td>
            <a href="/bob/show/{{ $doc->control_id }}">{{ $doc->control->clause }}</a>
        </td>
        <td>
            <a href="/doc/show/{{ $doc->id }}">{{ substr($doc->filename,0,32) }}</a>
        </td>
        <td>
            {{ \Illuminate\Support\Number::fileSize($doc->size) }}
        </td>
        <td>
            {{ $doc->hash }}
            <br>
        </td>
        <td>
            <b>
            @if (file_exists(storage_path('docs/').$doc->id))
                @if ($doc->hash == hash_file("sha256", storage_path('docs/').$doc->id))
                    <font color="green">OK</font>
                @else
                    <font color="red">HASH FAILS</font>
                @endif
            @else
                    <font color="red">MISSING</font>
            @endif
            </b>
        </td>
    </tr>
    @endforeach
</table>
</div>

@endsection
