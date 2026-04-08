@extends("layout")

@section("content")
<div data-role="panel" data-title-caption='{{ trans("cruds.risk.create") }}' data-collapsible="false" data-title-icon="<span class='mif-warning'></span>">

    @include('partials.errors')

    <form method="POST" action="/risk/store">
        @csrf

        <div class="grid">

            {{-- Nom --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans("cruds.risk.fields.name") }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <input type="text" data-role="input" name="name"
                           value="{{ old('name') }}" maxlength="255" required>
                </div>
            </div>

            {{-- Description --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans("cruds.risk.fields.description") }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea name="description" rows="3" data-role="textarea"
                              data-clear-button="false">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Propriétaire + Fréquence de revue --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans("cruds.risk.fields.owner") }}</strong>
                </div>
                <div class="cell-lg-3 cell-md-5">
                    <select name="owner_id" data-role="select" data-filter="true">
                        <option value="">— {{ trans('cruds.risk.fields.no_owner') }} —</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans("cruds.risk.fields.review_frequency") }}</strong>
                </div>
                <div class="cell-lg-1 cell-md-2">
                    <input data-role="spinner" name="review_frequency"
                           value="{{ old('review_frequency', 12) }}" min="1" max="60">
                </div>
                <div class="cell-lg-1 cell-md-1">
                    {{ trans('common.months') }}
                </div>
            </div>

            <div class="row"></div>

            {{-- Section probabilité (masquée si formule likelihood) --}}
            <div id="probability-section" @if($scoringConfig->usesLikelihood()) style="display:none" @endif>
                <div class="row">
                    <div class="cell-lg-1 cell-md-2">
                        <strong>{{ trans("cruds.risk.fields.probability") }}</strong>
                    </div>
                    <div class="cell-lg-6 cell-md-8">
                        @foreach ($scoringConfig->probability_levels ?? [] as $level)
                        <input type="radio" name="probability" value="{{ $level['value'] }}"
                               data-role="radio"
                               data-append="<b>{{ $level['value'] }}</b> — {{ $level['label'] }}{{ isset($level['description']) && $level['description'] ? ' <small class=\'text-muted\'>('.$level['description'].')</small>' : '' }}"
                               {{ old('probability', 1) == $level['value'] ? 'checked' : '' }}/>
                        <br>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="cell-lg-1 cell-md-2"></div>
                    <div class="cell-lg-6 cell-md-8">
                        <textarea name="probability_comment" rows="2" data-role="textarea"
                                  data-clear-button="false"
                                  placeholder="{{ trans('cruds.risk.fields.probability_comment') }}">{{ old('probability_comment') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Section exposition + vulnérabilité (formule likelihood_x_impact) --}}
            <div id="likelihood-section" @if(!$scoringConfig->usesLikelihood()) style="display:none" @endif>
                <div class="row">
                    <div class="cell-lg-1 cell-md-2">
                        <strong>{{ trans("cruds.risk.fields.exposure") }}</strong>
                    </div>
                    <div class="cell-lg-6 cell-md-8">
                        @foreach ($scoringConfig->exposure_levels ?? [] as $level)
                        <input type="radio" name="exposure" value="{{ $level['value'] }}"
                               data-role="radio"
                               data-append="<b>{{ $level['value'] }}</b> — {{ $level['label'] }}"
                               {{ old('exposure', 0) == $level['value'] ? 'checked' : '' }}/>
                        <br>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="cell-lg-1 cell-md-2">
                        <strong>{{ trans("cruds.risk.fields.vulnerability") }}</strong>
                    </div>
                    <div class="cell-lg-6 cell-md-8">
                        @foreach ($scoringConfig->vulnerability_levels ?? [] as $level)
                        <input type="radio" name="vulnerability" value="{{ $level['value'] }}"
                               data-role="radio"
                               data-append="<b>{{ $level['value'] }}</b> — {{ $level['label'] }}"
                               {{ old('vulnerability', 1) == $level['value'] ? 'checked' : '' }}/>
                        <br>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row"></div>

            {{-- Impact --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans("cruds.risk.fields.impact") }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    @foreach ($scoringConfig->impact_levels ?? [] as $level)
                    <input type="radio" name="impact" value="{{ $level['value'] }}"
                           data-role="radio"
                           data-append="<b>{{ $level['value'] }}</b> — {{ $level['label'] }}{{ isset($level['description']) && $level['description'] ? ' <small class=\'text-muted\'>('.$level['description'].')</small>' : '' }}"
                           {{ old('impact', 1) == $level['value'] ? 'checked' : '' }}/>
                        <br>
                    @endforeach
                </div>
            </div>
            <div class="row">
                <div class="cell-lg-1 cell-md-2"></div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea name="impact_comment" rows="2" data-role="textarea"
                              data-clear-button="false"
                              placeholder="{{ trans('cruds.risk.fields.impact_comment') }}">{{ old('impact_comment') }}</textarea>
                </div>
            </div>

            {{-- Score calculé (dynamique) --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans("cruds.risk.fields.score") }}</strong>
                </div>
                <div class="cell-lg-6 cell-md-8">
                    <span id="score-badge" class="badge secondary" style="font-size:1.1rem">—</span>
                    &nbsp;
                    <span id="score-label" class="text-muted"></span>
                    &nbsp;
                    <small id="likelihood-display" class="text-muted"></small>
                </div>
            </div>

            <div class="row"></div>

            {{-- Statut de traitement --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2">
                    <strong>{{ trans("cruds.risk.fields.status") }}</strong>
                </div>
                <div class="cell-lg-2 cell-md-3">
                    <select name="status" id="risk-status" data-role="select">
                        @foreach (\App\Models\Risk::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ old('status', 'not_evaluated') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="cell-lg-1 cell-md-2"></div>
                <div class="cell-lg-6 cell-md-8">
                    <textarea name="status_comment" rows="2" data-role="textarea"
                              data-clear-button="false"
                              placeholder="{{ trans('cruds.risk.fields.status_comment') }}">{{ old('status_comment') }}</textarea>
                </div>
            </div>

            {{-- Contrôles liés (visible si mitigated) --}}
            <div id="controls-section" style="display:none">
                <div class="row">
                    <div class="cell-lg-1 cell-md-2">
                        <strong>{{ trans("cruds.risk.fields.controls") }}</strong>
                        <br><small class="text-muted">{{ trans('cruds.risk.fields.controls_hint') }}</small>
                    </div>
                    <div class="cell-lg-6 cell-md-8">
                        <select name="control_ids[]" data-role="select" data-filter="true" multiple>
                            @foreach ($controls as $control)
                                <option value="{{ $control->id }}"
                                    {{ in_array($control->id, old('control_ids', [])) ? 'selected' : '' }}>
                                    {{ $control->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Plans d'action liés (visible si not_accepted) --}}
            <div id="actions-section" style="display:none">
                <div class="row">
                    <div class="cell-lg-1 cell-md-2">
                        <strong>{{ trans("cruds.risk.fields.action_plan") }}</strong>
                        <br><small class="text-muted">{{ trans('cruds.risk.fields.actions_hint') }}</small>
                    </div>
                    <div class="cell-lg-6 cell-md-8">
                        <select name="action_ids[]" data-role="select" data-filter="true" multiple>
                            @foreach ($actions as $action)
                                <option value="{{ $action->id }}"
                                    {{ in_array($action->id, old('action_ids', [])) ? 'selected' : '' }}>
                                    {{ $action->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row"></div>

            {{-- Boutons --}}
            <div class="row">
                <div class="cell-lg-12 cell-md-12">
                    <button type="submit" class="button success">
                        <span class="mif-floppy-disk2"></span>
                        &nbsp;{{ trans("common.save") }}
                    </button>
                    &nbsp;
                    <a class="button cancel" href="/risk/index" role="button">
                        <span class="mif-cancel"></span>
                        &nbsp;{{ trans("common.cancel") }}
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

@include('risks._scoring_script')
@endsection