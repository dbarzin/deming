@extends("layout")

@section("content")
<div class="card">
    <div class="card-header">@include('partials.help', ['page' => 'exceptions.create'])<i class="bi bi-x-octagon"></i> {{ trans('cruds.exception.create') }}</div>
    <div class="card-body">

    @include('partials.errors')

    <form method="POST" action="/exception/store">
        @csrf

        <div class="container-fluid px-0">

            {{-- Nom --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.name') }}</strong>
                </div>
                <div class="col-lg-11 col-md-10">
                    <input class="form-control" type="text" name="name"
                        value="{{ old('name') }}" maxlength="255" required>
                </div>
            </div>

            {{-- Mesure liée --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.measure') }}</strong>
                </div>
                <div class="col-lg-11 col-md-10">
                    <select class="form-select select2" name="measure_id">
                        <option value="">– {{ trans('cruds.exception.fields.no_measure') }} –</option>
                        @foreach($controls as $control)
                            <option value="{{ $control->id }}"
                                {{ old('measure_id') == $control->id ? 'selected' : '' }}>
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
                <div class="col-lg-11 col-md-10">
                    <textarea class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Justification --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.justification') }}</strong>
                </div>
                <div class="col-lg-11 col-md-10">
                    <textarea class="form-control" name="justification" rows="4">{{ old('justification') }}</textarea>
                </div>
            </div>

            {{-- Mesures compensatoires --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.compensating_controls') }}</strong>
                </div>
                <div class="col-lg-11 col-md-10">
                    <textarea class="form-control" name="compensating_controls" rows="3">{{ old('compensating_controls') }}</textarea>
                </div>
            </div>

            {{-- Période de validité --}}
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <strong>{{ trans('cruds.exception.fields.start_date') }}</strong>
                </div>
                <div class="col-lg-2 col-md-3">
                    <input class="form-control" type="date"
                        name="start_date" value="{{ old('start_date') }}"/>
                </div>
                <div class="col-lg-1 col-md-2 text-md-end">
                    <strong>{{ trans('cruds.exception.fields.end_date') }}</strong>
                </div>
                <div class="col-lg-2 col-md-3">
                    <input class="form-control" type="date"
                        name="end_date" value="{{ old('end_date') }}"/>
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
