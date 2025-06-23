@extends('layout')
@section('content')
<div class="table-responsive" data-role="panel" data-title-caption="{{ trans('cruds.log.title') }}" data-collapsible="false" data-title-icon="<span class='mif-log-file'></span>">
        <table class="table table-border cell-border">
            <tbody>
                <tr>
                    <th style="width: 150px;">
                        {{ trans('cruds.log.subject_id') }}
                    </th>
                    <td>
                        @if ($auditLogs->first()->subject_type=='App\\Models\\Measure')
                            <a href="/alice/show/{{ $auditLogs->first()->subject_id }} ">
                                {{ $auditLogs->first()->subject_id }}
                            </a>
                        @elseif ($auditLogs->first()->subject_type=='App\\Models\\Control')
                            <a href="/bob/show/{{ $auditLogs->first()->subject_id }} ">
                                {{ $auditLogs->first()->subject_id }}
                            </a>
                        @elseif ($auditLogs->first()->subject_type=='App\\Models\\Action')
                            <a href="/action/show/{{ $auditLogs->first()->subject_id }} ">
                                {{ $auditLogs->first()->subject_id }}
                            </a>
                        @elseif ($auditLogs->first()->subject_type=='App\\Models\\User')
                            <a href="/user/show/{{ $auditLogs->first()->subject_id }} ">
                                {{ $auditLogs->first()->subject_id }}
                            </a>
                        @else
                            {{ $auditLogs->first()->subject_id }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>
                        id
                    </th>
                    @foreach($auditLogs as $auditLog)
                    <td>
                    <a href="/logs/show/{{ $auditLog->id }}">
                        {{ $auditLog->id }}
                    </a>
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <th>
                        {{ trans('cruds.log.action') }}
                    </th>
                    @foreach($auditLogs as $auditLog)
                    <td>
                    {{ $auditLog->description }}
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <th>
                        {{ trans('cruds.log.user') }}
                    </th>
                    @foreach($auditLogs as $auditLog)
                    <td>
                    <a href="/user/{{ $auditLog->user_id }}">
                        {{ $auditLog->name }}
                    </a>
                    </td>
                    @endforeach
                </tr>
                @foreach($auditLog->properties as $key => $value)
                <tr>
                        <th>[{{ $key }}]</th>
                        @php $previous = null; @endphp
                        @foreach($auditLogs as $auditLog2)
                            @php
                                if (property_exists($auditLog2->properties,$key))
                                    $value = $auditLog2->properties->{$key};
                                else
                                    $value = null;
                            @endphp
                        <td {!! (($loop->first)||($value==$previous)) ? "" : "class='info'" !!}>
                            @if ((gettype($value)=="string")&&(strlen($value)>100))
                                {{ substr($value,0,100) }}
                            @elseif ((gettype($value)=="array"))
                                @foreach($value as $v)
                                    {{ $v->name }}
                                @endforeach
                            @else
                                {{ $value }}
                            @endif
                        </td>
                            @php $previous = $value; @endphp
                        @endforeach
                </tr>
                @endforeach
                <tr>
                    <th>
                        {{ trans('cruds.log.host') }}
                    </th>
                    @foreach($auditLogs as $auditLog)
                    <td>
                        {{ $auditLog->host }}
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <th>
                        {{ trans('cruds.log.timestamp') }}
                    </th>
                    @foreach($auditLogs as $auditLog)
                    <td>
                        {{ $auditLog->created_at }}
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <br>
        <a class="button btn-default" href="/logs">
        {{ trans('common.cancel') }}
        </a>
    </div>
@endsection
@section('style')
    <style>
        .table-responsive {
            overflow-x: auto !important;
            border: 1px solid red; /* Pour voir les limites du conteneur */
        }
        .table {
            min-width: max-content;
        }
    </style>
@endsection
