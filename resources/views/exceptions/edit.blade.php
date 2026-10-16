@extends("layout")

@section("content")
<div class="card">
    <div class="card-header"><i class="bi bi-x-octagon"></i> {{ trans('cruds.exception.create') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form method="POST" action="/exception/save">
        @csrf
        <input type="hidden" name="id" value="{{ $exception->id }}">

        <div class="container-fluid px-0">

            {{-- Nom --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.name') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <input class="form-control" type="text" name="name"
        				value="{{ old('name', $exception->name) }}"
                        maxlength="255" required>
                </div>
            </div>

            {{-- Mesure liée --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.measure') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <select class="form-select select2" name="measure_id">
                        <option value="">– {{ trans('cruds.exception.fields.no_measure') }} –</option>
                        @foreach($controls as $control)
                            <option value="{{ $control->id }}"
                                {{ (old('measure_id', $exception->control_id)) == $control->id ? 'selected' : '' }}>
                                {{ $control->clause }} – {{ $control->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.description') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <textarea class="form-control" name="description" rows="4">{{ old('description', $exception->description) }}</textarea>
                </div>
            </div>

            {{-- Justification --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.justification') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <textarea class="form-control" name="justification" rows="4">{{ old('justification', $exception->justification) }}</textarea>
                </div>
            </div>

            {{-- Mesures compensatoires --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.compensating_controls') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <textarea class="form-control" name="compensating_controls" rows="3">{{ old('compensating_controls', $exception->compensating_controls) }}</textarea>
                </div>
            </div>

            @if (Auth::user()->isAdmin())
            {{-- Statut (Administrateur uniquement) --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.status') }}</strong>
                </div>
                <div class="col-lg-6 col-md-8">
                    <select class="form-select" name="status" style="width: 100%; height: 36px; padding: 4px 8px; border: 1px solid #ababab; border-radius: 2px; background-color: #fff;">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ ((int) old('status', $exception->status)) === $value ? 'selected' : '' }}>
                                {{ trans($label) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            {{-- Période de validité --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.start_date') }}</strong>
                </div>
                <div class="col-lg-2 col-md-3">
                    <input class="form-control" type="date"
                        name="start_date" value="{{ old('start_date', $exception->start_date) }}"/>
                </div>
                <div class="col-lg-1 col-md-2 text-md-end">
                    <strong>{{ trans('cruds.exception.fields.end_date') }}</strong>
                </div>
                <div class="col-lg-2 col-md-3">
                    <input class="form-control" type="date"
                        name="end_date" value="{{ old('end_date', $exception->end_date) }}"/>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <button type="submit" class="btn btn-success">
                        <span class="bi bi-floppy"></span> {{ trans('common.save') }}
                    </button>
                    &nbsp;
                    <a class="btn btn-light border" href="/exception/index" role="button">
                        <span class="bi bi-x-circle"></span> {{ trans('common.cancel') }}
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
</div>
@endsection
