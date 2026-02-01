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
        <th>Links</th>
        <th>Status</th>
    </tr>
    </thead>

    @foreach ($documents as $doc)
    <tr>
        <td>{{ $doc->id }}</td>
        <td class="text-center">
            <a href="/bob/show/{{ $doc->control_id }}">{{ $doc->control_id }}</a>
        </td>
        <td>
            <a href="/doc/show/{{ $doc->id }}">{{ substr($doc->filename, 0, 32) }}</a>
        </td>
        <td>{{ \Illuminate\Support\Number::fileSize($doc->size) }}</td>
        <td><small>{{ $doc->hash }}</small></td>
        <td class="text-center">
            @if ($doc->file_exists)
                @if ($doc->link_count > 1)
                    <span class="badge bg-blue">{{ $doc->link_count }}</span>
                @else
                    {{ $doc->link_count }}
                @endif
            @else
                -
            @endif
        </td>
        <td>
            <b>
            @if ($doc->file_exists)
                @if ($doc->hash_valid)
                    <span style="color: green;">OK</span>
                @else
                    <span style="color: red">HASH FAILS</span>
                @endif
            @else
                <span style="color: red">MISSING</span>
            @endif
            </b>
        </td>
    </tr>
    @endforeach
</table>
</div>

@endsection