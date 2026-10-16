@extends('layout')

@section('content')
<div class="card">
    <div class="card-header"><i class="bi bi-grid-3x3"></i> {{ trans('cruds.crosswalk.matrix') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form method="GET" action="{{ route('crosswalk.matrix') }}" class="mb-4">
        <div class="container-fluid px-0">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <label>{{ trans('cruds.crosswalk.source_framework') }}</label>
                    <select class="form-select select2" name="source_framework" required>
                        <option value="">-- {{ trans('cruds.crosswalk.source_framework') }} --</option>
                        @foreach($frameworks as $framework)
                            <option value="{{ $framework->code }}"
                                {{ ($filters['source_framework'] ?? '') === $framework->code ? 'selected' : '' }}>
                                {{ $framework->code }} — {{ $framework->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label>{{ trans('cruds.crosswalk.target_framework') }}</label>
                    <select class="form-select select2" name="target_framework" required>
                        <option value="">-- {{ trans('cruds.crosswalk.target_framework') }} --</option>
                        @foreach($frameworks as $framework)
                            <option value="{{ $framework->code }}"
                                {{ ($filters['target_framework'] ?? '') === $framework->code ? 'selected' : '' }}>
                                {{ $framework->code }} — {{ $framework->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label>{{ trans('cruds.crosswalk.fields.mapping_type') }}</label>
                    <select class="form-select select2" name="mapping_type">
                        <option value="">-- {{ trans('cruds.crosswalk.all') }} --</option>
                        @foreach(\App\Models\ControlMapping::DIRECTIONAL_MAPPING_TYPES as $mappingType)
                            <option value="{{ $mappingType }}"
                                {{ ($filters['mapping_type'] ?? '') === $mappingType ? 'selected' : '' }}>
                                {{ trans('cruds.crosswalk.mapping_types.' . $mappingType) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label>{{ trans('cruds.crosswalk.fields.coverage') }}</label>
                    <select class="form-select select2" name="coverage">
                        <option value="">-- {{ trans('cruds.crosswalk.all') }} --</option>
                        @foreach(\App\Models\ControlMapping::COVERAGE_LEVELS as $coverage)
                            <option value="{{ $coverage }}"
                                {{ ($filters['coverage'] ?? '') === $coverage ? 'selected' : '' }}>
                                {{ trans('cruds.crosswalk.coverage_levels.' . $coverage) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-info">
                        <span class="bi bi-funnel"></span> {{ trans('cruds.crosswalk.display') }}
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg-6 col-md-12">
                    <input type="hidden" name="unmapped" value="0">
                    <div class="form-check form-check-inline"><input id="unmapped" class="form-check-input" type="checkbox" name="unmapped" value="1"
                           {{ ($filters['unmapped'] ?? false) ? 'checked' : '' }}><label class="form-check-label" for="unmapped">{{ trans('cruds.crosswalk.show_unmapped') }}</label></div>
                </div>
                <div class="col-lg-6 col-md-12 text-end">
                    @if(($filters['source_framework'] ?? null) && ($filters['target_framework'] ?? null))
                        <a class="btn btn-light border" href="{{ route('crosswalk.matrix', array_merge($filters, [
                            'source_framework' => $filters['target_framework'],
                            'target_framework' => $filters['source_framework'],
                        ])) }}">
                            <span class="bi bi-arrow-repeat"></span> {{ trans('cruds.crosswalk.reverse') }}
                        </a>
                        <a class="btn btn-success" href="{{ route('crosswalk.export', array_merge($filters, ['directional' => 1])) }}">
                            <span class="bi bi-file-earmark-excel"></span> {{ trans('common.export') }}
                        </a>
                    @endif
                    <a class="btn btn-light border" href="{{ route('crosswalk.index') }}">
                        <span class="bi bi-list-ul"></span> {{ trans('cruds.crosswalk.list') }}
                    </a>
                </div>
            </div>
        </div>
    </form>

    @if(($filters['source_framework'] ?? null) === ($filters['target_framework'] ?? null)
        && ($filters['source_framework'] ?? null))
        <div class="alert alert-warning">{{ trans('cruds.crosswalk.distinct_frameworks') }}</div>
    @elseif($matrix === null)
        <div class="alert alert-info">{{ trans('cruds.crosswalk.select_pair') }}</div>
    @else
        <div class="alert alert-warning">{{ trans('cruds.crosswalk.documentary_notice') }}</div>

        <div class="container-fluid px-0">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="more-info-box bg-info text-white">
                        <div class="content"><span class="bi bi-book icon"></span>
                            <div class="text">{{ $matrix['stats']['source_total'] }}</div>
                            <div>{{ trans('cruds.crosswalk.source_total') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="more-info-box bg-success text-white">
                        <div class="content"><span class="bi bi-link icon"></span>
                            <div class="text">{{ $matrix['stats']['mapped_source'] }}</div>
                            <div>{{ trans('cruds.crosswalk.mapped_source') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="more-info-box bg-orange text-white">
                        <div class="content"><span class="bi bi-link-45deg icon"></span>
                            <div class="text">{{ $matrix['stats']['unmapped_source'] }}</div>
                            <div>{{ trans('cruds.crosswalk.unmapped_source') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="more-info-box bg-secondary text-white">
                        <div class="content"><span class="bi bi-shuffle icon"></span>
                            <div class="text">{{ $matrix['stats']['relations'] }}</div>
                            <div>{{ trans('cruds.crosswalk.relations') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-auto mt-4">
            <table data-order="[]" data-page-length="50" id="crosswalk-matrix" class="table table-striped table-hover table-bordered datatable">
                <thead>
                    <tr>
                        <th>{{ $filters['source_framework'] }}</th>
                        <th>{{ $filters['target_framework'] }}</th>
                        <th>{{ trans('cruds.crosswalk.fields.mapping_type') }}</th>
                        <th>{{ trans('cruds.crosswalk.fields.coverage') }}</th>
                        <th>{{ trans('cruds.crosswalk.stored_direction') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matrix['rows'] as $row)
                        @if($row['mappings']->isEmpty())
                            <tr>
                                <td>
                                    <a href="/alice/show/{{ $row['control']->id }}">
                                        {{ trim($row['control']->clause) }} — {{ $row['control']->name }}
                                    </a>
                                </td>
                                <td><em>
                                    {{ $row['has_any_mapping']
                                        ? trans('cruds.crosswalk.no_filtered_mapping')
                                        : trans('cruds.crosswalk.unmapped') }}
                                </em></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @else
                            @foreach($row['mappings'] as $item)
                                <tr>
                                    <td>
                                        <a href="/alice/show/{{ $row['control']->id }}">
                                            {{ trim($row['control']->clause) }} — {{ $row['control']->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="/alice/show/{{ $item['target_control']->id }}">
                                            {{ trim($item['target_control']->clause) }} — {{ $item['target_control']->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('crosswalk.show', $item['mapping']) }}">
                                            {{ trans('cruds.crosswalk.mapping_types.' . $item['mapping_type']) }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $item['mapping']->coverage
                                            ? trans('cruds.crosswalk.coverage_levels.' . $item['mapping']->coverage)
                                            : '–' }}
                                    </td>
                                    <td>
                                        {{ $item['reversed']
                                            ? trans('cruds.crosswalk.reversed')
                                            : trans('cruds.crosswalk.direct') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr><td>{{ trans('cruds.crosswalk.no_controls') }}</td><td></td><td></td><td></td><td></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
</div>
@endsection
