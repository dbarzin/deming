@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.exception.create') }}" data-collapsible="false" data-title-icon="<span class='mif-shield'></span>">

    @include('partials.errors')

    <form method="POST" action="/exception/store">
        @csrf

        <div class="grid">

            {{-- Nom --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.exception.fields.name') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <input type="text" data-role="input" name="name"
                        value="{{ old('name') }}" maxlength="255" required>
                </div>
            </div>

            {{-- Mesure liée --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.exception.fields.measure') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <select name="measure_id" data-role="select" data-filter="true">
                        <option value="">– {{ trans('cruds.exception.fields.no_measure') }} –</option>
                        @foreach($measures as $measure)
                            <option value="{{ $measure->id }}"
                                {{ old('measure_id') == $measure->id ? 'selected' : '' }}>
                                {{ $measure->clause }} – {{ $measure->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.exception.fields.description') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea name="description" rows="4"
                        data-role="textarea" data-clear-button="false">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Justification --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.exception.fields.justification') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea name="justification" rows="4"
                        data-role="textarea" data-clear-button="false">{{ old('justification') }}</textarea>
                </div>
            </div>

            {{-- Mesures compensatoires --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.exception.fields.compensating_controls') }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea name="compensating_controls" rows="3"
                        data-role="textarea" data-clear-button="false">{{ old('compensating_controls') }}</textarea>
                </div>
            </div>

            {{-- Période de validité --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans('cruds.exception.fields.start_date') }}</strong>
                </div>
                <div class="cell-lg-2 cell-md-3">
                    <input data-role="calendarpicker" data-format="YYYY-MM-DD"
                        name="start_date" value="{{ old('start_date') }}"/>
                </div>
                <div class="cell-lg-1 cell-md-2 text-right">
                    <strong>{{ trans('cruds.exception.fields.end_date') }}</strong>
                </div>
                <div class="cell-lg-2 cell-md-3">
                    <input data-role="calendarpicker" data-format="YYYY-MM-DD"
                        name="end_date" value="{{ old('end_date') }}" data-clear-button="true"/>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="row">
                <div class="cell-lg-12 cell-md-12">
                    <button type="submit" class="button success">
                        <span class="mif-floppy-disk2"></span>&nbsp;{{ trans('common.save') }}
                    </button>
                    &nbsp;
                    <a class="button" href="/exception/index" role="button">
                        <span class="mif-cancel"></span>&nbsp;{{ trans('common.cancel') }}
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
