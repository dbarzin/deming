@php
    $controlsByFramework = $controls->groupBy(fn ($control) => $control->domain->framework ?? '');
@endphp

@include('partials.errors')

<div class="container-fluid px-0">
    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.source') }}</strong>
        </div>
        <div class="col-lg-8 col-md-9">
            <select class="form-select select2" name="source_control_id" required>
                <option value="">-- {{ trans('cruds.crosswalk.choose_control') }} --</option>
                @foreach($controlsByFramework as $framework => $frameworkControls)
                    <optgroup label="{{ $framework }}">
                        @foreach($frameworkControls as $control)
                            <option value="{{ $control->id }}"
                                {{ (string) old('source_control_id', $mapping->source_control_id) === (string) $control->id ? 'selected' : '' }}>
                                {{ trim($control->clause) }} – {{ $control->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.target') }}</strong>
        </div>
        <div class="col-lg-8 col-md-9">
            <select class="form-select select2" name="target_control_id" required>
                <option value="">-- {{ trans('cruds.crosswalk.choose_control') }} --</option>
                @foreach($controlsByFramework as $framework => $frameworkControls)
                    <optgroup label="{{ $framework }}">
                        @foreach($frameworkControls as $control)
                            <option value="{{ $control->id }}"
                                {{ (string) old('target_control_id', $mapping->target_control_id) === (string) $control->id ? 'selected' : '' }}>
                                {{ trim($control->clause) }} – {{ $control->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.fields.mapping_type') }}</strong>
        </div>
        <div class="col-lg-3 col-md-4">
            <select class="form-select select2" name="mapping_type" required>
                @foreach(\App\Models\ControlMapping::MAPPING_TYPES as $mappingType)
                    <option value="{{ $mappingType }}"
                        {{ old('mapping_type', $mapping->mapping_type) === $mappingType ? 'selected' : '' }}>
                        {{ trans('cruds.crosswalk.mapping_types.' . $mappingType) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-2 text-end">
            <strong>{{ trans('cruds.crosswalk.fields.coverage') }}</strong>
        </div>
        <div class="col-lg-3 col-md-3">
            <select class="form-select select2" name="coverage">
                <option value="">–</option>
                @foreach(\App\Models\ControlMapping::COVERAGE_LEVELS as $coverage)
                    <option value="{{ $coverage }}"
                        {{ old('coverage', $mapping->coverage) === $coverage ? 'selected' : '' }}>
                        {{ trans('cruds.crosswalk.coverage_levels.' . $coverage) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.fields.confidence') }}</strong>
        </div>
        <div class="col-lg-2 col-md-3">
            <input class="form-control" type="number" name="confidence" min="0" max="100" step="0.01"
                   value="{{ old('confidence', $mapping->confidence) }}">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.fields.rationale') }}</strong>
        </div>
        <div class="col-lg-8 col-md-9">
            <textarea class="form-control" name="rationale" rows="5">{{ old('rationale', $mapping->rationale) }}</textarea>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.fields.source_reference') }}</strong>
        </div>
        <div class="col-lg-8 col-md-9">
            <input class="form-control" type="text" name="source_reference" maxlength="255"
                   value="{{ old('source_reference', $mapping->source_reference) }}">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.fields.source_url') }}</strong>
        </div>
        <div class="col-lg-8 col-md-9">
            <input class="form-control" type="url" name="source_url" maxlength="2048"
                   value="{{ old('source_url', $mapping->source_url) }}">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-3">
            <strong>{{ trans('cruds.crosswalk.fields.validated') }}</strong>
        </div>
        <div class="col-lg-8 col-md-9">
            <input type="hidden" name="validated" value="0">
            <input class="form-check-input" type="checkbox" name="validated" value="1"
                   {{ old('validated', $mapping->validated) ? 'checked' : '' }}>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-success">
                <span class="bi bi-floppy"></span> {{ trans('common.save') }}
            </button>
            <a class="btn btn-light border" href="{{ route('crosswalk.index') }}">
                <span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
            </a>
        </div>
    </div>
</div>
