@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption='{{ trans("cruds.log.index")}}' data-collapsible="true" data-title-icon="<span class='mif-log-file'></span>">

    <div class="grid">

    <table class="table striped row-hover cell-border"
       data-role="table"
       data-rows="100"
       data-show-activity="true"
       data-rownum="false"
       data-check="false"
       data-check-style="1"
       >
            <thead>
                <th class="sortable-column">
                    id
                </th>
                <th class="sortable-column">
                    {{ trans('cruds.log.action') }}
                </th>
                <th class="sortable-column">
                    {{ trans('cruds.log.subject_type') }}
                </th>
                <th class="sortable-column">
                    {{ trans('cruds.log.subject_id') }}
                </th>
                <th class="sortable-column">
                    {{ trans('cruds.log.user') }}
                </th>
                <th class="sortable-column">
                    {{ trans('cruds.log.host') }}
                </th>
                <th class="sortable-column sort-desc">
                    {{ trans('cruds.log.timestamp') }}
                </th>
                <th>
                </th>
        </thead>
        <tbody>
                @foreach($logs as $log)
                <tr data-entry-id="{{ $log->id }}">
                    <td>
                        <a href="/logs/show/{{ $log->id }}">
                        {{ $log->id }}
                        </a>
                    </td>
                    <td>
                        {{ $log->description }}
                    </td>
                    <td>
                        {{ $log->subject_type }}
                    </td>
                    <td>
                        @if ($log->subject_type=='App\\Models\\Measure')
                            <a href="/alice/show/{{ $log->subject_id }} ">
                                {{ $log->subject_id }}
                            </a>
                        @elseif ($log->subject_type=='App\\Models\\Control')
                            <a href="/bob/show/{{ $log->subject_id }} ">
                                {{ $log->subject_id }}
                            </a>
                        @elseif ($log->subject_type=='App\\Models\\Action')
                            <a href="/action/show/{{ $log->subject_id }} ">
                                {{ $log->subject_id }}
                            </a>
                        @elseif ($log->subject_type=='App\\Models\\User')
                            <a href="/user/show/{{ $log->subject_id }} ">
                                {{ $log->subject_id }}
                            </a>
                        @else
                            {{ $log->subject_id }}
                        @endif




                    </td>
                    <td>
                        <a href="{{ route('users.show', $log->user_id) }}">
                        {{ $log->name }}
                        </a>
                    </td>
                    <td>
                        {{ $log->host }}
                    </td>
                    <td>
                        {{ $log->created_at }}
                    </td>
                    <td nowrap>
                        <a class="button success" href="/logs/show/{{ $log->id }}">
                            {{ trans('common.show') }}
                        </a>
                        <a class="button info" href="/logs/history/{{ $log->id }}">
                            {{ trans('common.history') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>
        {{ $logs->links() }}
        </p>
    </div>
</div>
@endsection
