@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.log.title') }}" data-collapsible="true" data-title-icon="<span class='mif-log-file'></span>">

    <table class="table table-border cell-border">
        <tbody>
            <tr>
                <th>
                    id
                </th>
                <td>
                    {{ $auditLog->id }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.log.subject_type') }}
                </th>
                <td>
                    {{ $auditLog->subject_type }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.log.subject_id') }}
                </th>
                <td>
                    @if ($auditLog->subject_type=='App\\Models\\Measure')
                        <a href="/alice/show/{{ $auditLog->subject_id }} ">
                            {{ $auditLog->subject_id }}
                        </a>
                    @elseif ($auditLog->subject_type=='App\\Models\\Control')
                        <a href="/bob/show/{{ $auditLog->subject_id }} ">
                            {{ $auditLog->subject_id }}
                        </a>
                    @elseif ($auditLog->subject_type=='App\\Models\\Action')
                        <a href="/action/show/{{ $auditLog->subject_id }} ">
                            {{ $auditLog->subject_id }}
                        </a>
                    @elseif ($auditLog->subject_type=='App\\Models\\User')
                        <a href="/users/{{ $auditLog->subject_id }} ">
                            {{ $auditLog->subject_id }}
                        </a>
                    @else
                        {{ $auditLog->subject_id }}
                    @endif

                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.log.action') }}
                </th>
                <td>
                    {{ $auditLog->description }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.log.user') }}
                </th>
                <td>
                <a href="/users/{{ $auditLog->user_id }}">
                    {!! $auditLog->user->name !!}
                </a>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.log.properties') }}
                </th>
                <td>
                    {{ $auditLog->properties }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.log.host') }}
                </th>
                <td>
                    {{ $auditLog->host }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.log.timestamp') }}
                </th>
                <td>
                    {{ $auditLog->created_at }}
                </td>
            </tr>
        </tbody>
    </table>
    <div>
        <br>
        <a class="button btn-default" href="/logs">
        {{ trans('common.cancel') }}
        </a>
    </div>
</div>
@endsection
