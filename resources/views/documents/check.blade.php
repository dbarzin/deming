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
    @php
        $filePath = storage_path('docs/' . $doc->id);
        $fileExists = file_exists($filePath);
        $linkCount = 0;
        $hashValid = false;

        if ($fileExists) {
            $stats = stat($filePath);
            $linkCount = $stats['nlink'] ?? 0;
            $hashValid = ($doc->hash === hash_file('sha256', $filePath));
        }
    @endphp
    <tr>
        <td>
            {{ $doc->id }}
        </td>
        <td class="text-center">
            <a href="/bob/show/{{ $doc->control_id }}">{{ $doc->control_id }}</a>
        </td>
        <td>
            <a href="/doc/show/{{ $doc->id }}">{{ substr($doc->filename, 0, 32) }}</a>
        </td>
        <td>
            {{ \Illuminate\Support\Number::fileSize($doc->size) }}
        </td>
        <td>
            <small>{{ $doc->hash }}</small>
        </td>
        <td class="text-center">
            @if ($fileExists)
                @if ($linkCount > 1)
                    <span class="badge bg-blue">{{ $linkCount }}</span>
                @else
                    {{ $linkCount }}
                @endif
            @else
                -
            @endif
        </td>
        <td>
            <b>
            @if ($fileExists)
                @if ($hashValid)
                    <span style="color: green; ">OK</span>
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