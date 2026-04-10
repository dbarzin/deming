@extends("layout")

@section("content")
<div data-role="panel"
     data-title-caption="{{ isset($config->id) ? trans('cruds.risk_scoring.edit') : trans('cruds.risk_scoring.create') }}"
     data-collapsible="false"
     data-title-icon="<span class='mif-cog'></span>">

    @include('partials.errors')

    <form method="POST"
          action="{{ isset($config->id) ? '/risk/scoring/'.$config->id.'/save' : '/risk/scoring/store' }}">
        @csrf

        <div class="grid">

            {{-- ================================================================
                 Section : Paramètres généraux
            ================================================================ --}}

            {{-- Nom --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2 pt-2">
                    <strong>{{ trans("cruds.risk_scoring.fields.name") }}</strong>
                </div>
                <div class="cell-lg-4 cell-md-6">
                    <input type="text" data-role="input" name="name"
                           value="{{ old('name', $config->name ?? '') }}" maxlength="255" required>
                </div>
            </div>

            {{-- Formule --}}
            <div class="row">
                <div class="cell-lg-1 cell-md-2 pt-2">
                    <strong>{{ trans("cruds.risk_scoring.fields.formula") }}</strong>
                </div>
                <div class="cell-lg-3 cell-md-4">
                    <select name="formula" id="formula-select" class="select"
                            style="width:100%;padding:6px 8px;border:1px solid #ccc;border-radius:4px">
                        @foreach ($formulas as $key => $info)
                            <option value="{{ $key }}"
                                    data-requires-exposure="{{ $info['requires_exposure'] ? '1' : '0' }}"
                                    data-description="{{ $info['description'] }}"
                                    {{ old('formula', $config->formula ?? 'probability_x_impact') === $key ? 'selected' : '' }}>
                                {{ $info['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="cell-lg-5 cell-md-5" style="padding-top:10px">
                    <small id="formula-description" class="text-muted"></small>
                </div>
            </div>

            {{-- ================================================================
                 Section : Niveaux
            ================================================================ --}}

            <div class="row">

                {{-- COL GAUCHE : Probabilité (masquée si likelihood) --}}
                <div class="cell-lg-6 cell-md-12 mb-4" id="probability-col">
                    <div class="level-section-header">
                        <span class="mif-chart-line"></span>
                        &nbsp;{{ trans("cruds.risk.fields.probability") }}
                        <small class="fg-white" style="opacity:.75;font-weight:400">
                            &nbsp;— {{ trans('cruds.risk_scoring.levels_hint') }}
                        </small>
                    </div>
                    <table class="table compact border" style="margin-top:0">
                        <thead>
                            <tr>
                                <th width="15%">{{ trans('cruds.risk_scoring.fields.value') }}</th>
                                <th width="30%">{{ trans('cruds.risk_scoring.fields.label') }}</th>
                                <th>{{ trans('cruds.risk_scoring.fields.description') }}</th>
                                <th width="36px"></th>
                            </tr>
                        </thead>
                        <tbody id="probability-body">
                        @foreach (old('probability_levels', $probLevels) as $idx => $level)
                        <tr class="level-row">
                            <td><input type="number" name="probability_levels[{{ $idx }}][value]" class="input" value="{{ $level['value'] }}" min="0" required style="width:80px; text-align:center;"></td>
                            <td><input type="text"   name="probability_levels[{{ $idx }}][label]" class="input" value="{{ $level['label'] }}" required></td>
                            <td><input type="text"   name="probability_levels[{{ $idx }}][description]" class="input" value="{{ $level['description'] ?? '' }}"></td>
                            <td><button type="button" class="button mini alert js-remove-level"><span class="mif-bin"></span></button></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="button mini success mt-1 js-add-level" data-field="probability">
                        <span class="mif-plus"></span> {{ trans('cruds.risk_scoring.add_level') }}
                    </button>
                </div>

                {{-- COL GAUCHE (alt) : Exposition (visible si likelihood) --}}
                <div class="cell-lg-6 cell-md-12 mb-4" id="exposure-col" style="display:none">
                    <div class="level-section-header" style="background:#5a7fb5">
                        <span class="mif-network"></span>
                        &nbsp;{{ trans("cruds.risk.fields.exposure") }}
                        <small class="fg-white" style="opacity:.75;font-weight:400">
                            &nbsp;— 0 = hors réseau &middot; 1 = interne &middot; 2 = Internet
                        </small>
                    </div>
                    <table class="table compact border" style="margin-top:0">
                        <thead>
                            <tr>
                                <th width="15%">{{ trans('cruds.risk_scoring.fields.value') }}</th>
                                <th width="30%">{{ trans('cruds.risk_scoring.fields.label') }}</th>
                                <th>{{ trans('cruds.risk_scoring.fields.description') }}</th>
                                <th width="36px"></th>
                            </tr>
                        </thead>
                        <tbody id="exposure-body">
                        @foreach (old('exposure_levels', $expLevels) as $idx => $level)
                        <tr class="level-row">
                            <td><input type="number" name="exposure_levels[{{ $idx }}][value]" class="input" value="{{ $level['value'] }}" min="0" style="width:80px; text-align:center;"></td>
                            <td><input type="text"   name="exposure_levels[{{ $idx }}][label]" class="input" value="{{ $level['label'] }}"></td>
                            <td><input type="text"   name="exposure_levels[{{ $idx }}][description]" class="input" value="{{ $level['description'] ?? '' }}"></td>
                            <td><button type="button" class="button mini alert js-remove-level"><span class="mif-bin"></span></button></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="button mini success mt-1 js-add-level" data-field="exposure">
                        <span class="mif-plus"></span> {{ trans('cruds.risk_scoring.add_level') }}
                    </button>
                </div>

                {{-- COL DROITE : Impact (toujours visible) --}}
                <div class="cell-lg-6 cell-md-12 mb-4">
                    <div class="level-section-header" style="background:#c0392b">
                        <span class="mif-warning"></span>
                        &nbsp;{{ trans("cruds.risk.fields.impact") }}
                    </div>
                    <table class="table compact border" style="margin-top:0">
                        <thead>
                            <tr>
                                <th width="15%">{{ trans('cruds.risk_scoring.fields.value') }}</th>
                                <th width="30%">{{ trans('cruds.risk_scoring.fields.label') }}</th>
                                <th>{{ trans('cruds.risk_scoring.fields.description') }}</th>
                                <th width="36px"></th>
                            </tr>
                        </thead>
                        <tbody id="impact-body">
                        @foreach (old('impact_levels', $impLevels) as $idx => $level)
                        <tr class="level-row">
                            <td><input type="number" name="impact_levels[{{ $idx }}][value]" class="input" value="{{ $level['value'] }}" min="1" required style="width:80px; text-align:center;"></td>
                            <td><input type="text"   name="impact_levels[{{ $idx }}][label]" class="input" value="{{ $level['label'] }}" required></td>
                            <td><input type="text"   name="impact_levels[{{ $idx }}][description]" class="input" value="{{ $level['description'] ?? '' }}"></td>
                            <td><button type="button" class="button mini alert js-remove-level"><span class="mif-bin"></span></button></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="button mini success mt-1 js-add-level" data-field="impact">
                        <span class="mif-plus"></span> {{ trans('cruds.risk_scoring.add_level') }}
                    </button>
                </div>

            </div>{{-- /row niveaux --}}

            {{-- Vulnérabilité : pleine largeur, visible si likelihood --}}
            <div class="row" id="vulnerability-row" style="display:none">
                <div class="cell-lg-12 cell-md-12 mb-4">
                    <div class="level-section-header" style="background:#7d3c98">
                        <span class="mif-bug"></span>
                        &nbsp;{{ trans("cruds.risk.fields.vulnerability") }}
                        <small class="fg-white" style="opacity:.75;font-weight:400">
                            &nbsp;— 1 = aucune &middot; 2 = connue &middot; 3 = exploitable interne &middot; 4 = exploitable externe
                        </small>
                    </div>
                    <table class="table compact border" style="margin-top:0">
                        <thead>
                            <tr>
                                <th width="8%">{{ trans('cruds.risk_scoring.fields.value') }}</th>
                                <th width="20%">{{ trans('cruds.risk_scoring.fields.label') }}</th>
                                <th>{{ trans('cruds.risk_scoring.fields.description') }}</th>
                                <th width="36px"></th>
                            </tr>
                        </thead>
                        <tbody id="vulnerability-body">
                        @foreach (old('vulnerability_levels', $vulnLevels) as $idx => $level)
                        <tr class="level-row">
                            <td><input type="number" name="vulnerability_levels[{{ $idx }}][value]" class="input" value="{{ $level['value'] }}" min="1" style="width:80px; text-align:center;"></td>
                            <td><input type="text"   name="vulnerability_levels[{{ $idx }}][label]" class="input" value="{{ $level['label'] }}"></td>
                            <td><input type="text"   name="vulnerability_levels[{{ $idx }}][description]" class="input" value="{{ $level['description'] ?? '' }}"></td>
                            <td><button type="button" class="button mini alert js-remove-level"><span class="mif-bin"></span></button></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="button mini success mt-1 js-add-level" data-field="vulnerability">
                        <span class="mif-plus"></span> {{ trans('cruds.risk_scoring.add_level') }}
                    </button>
                </div>
            </div>

            {{-- ================================================================
                 Section : Seuils de classification
            ================================================================ --}}
            <div class="row mt-2 mb-0">
                <div class="cell-lg-12">
                    <div class="section-header" style="background:#8059C8; margin-bottom:0;">
                        <span class="mif-equalizer"></span>
                        &nbsp;{{ trans("cruds.risk_scoring.fields.thresholds") }}
                        <small style="opacity:.75;font-weight:400">
                            &nbsp;— {{ trans('cruds.risk_scoring.thresholds_hint') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="row mt-0">
                <div class="cell-lg-8 cell-md-12 mb-4">
                    <table class="table compact border mt-1">
                        <thead>
                            <tr>
                                <th>{{ trans('cruds.risk_scoring.fields.level_key') }}</th>
                                <th>{{ trans('cruds.risk_scoring.fields.label') }}</th>
                                <th>{{ trans('cruds.risk_scoring.fields.score_max') }}</th>
                                <th>{{ trans('cruds.risk_scoring.fields.color') }}</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="thresholds-body">
                            @foreach (old('risk_thresholds', $thresholds) as $idx => $t)
                            <tr class="threshold-row">
                                <td><input type="text"   name="risk_thresholds[{{ $idx }}][level]" class="input" value="{{ $t['level'] }}" required></td>
                                <td><input type="text"   name="risk_thresholds[{{ $idx }}][label]" class="input js-threshold-label" value="{{ $t['label'] }}" required></td>
                                <td>
                                    <input type="number"
                                           name="risk_thresholds[{{ $idx }}][max]"
                                           class="input js-threshold-max"
                                           value="{{ isset($t['max']) && $t['max'] !== null ? $t['max'] : '' }}"
                                           placeholder="∞"
                                           min="1"
                                           style="width:80px; text-align:center;">
                                </td>
                                <td>
                                    <input type="color"
                                    style="width:40px;"
                                           name="risk_thresholds[{{ $idx }}][color]"
                                           class="js-color-input"
                                           value="{{ $t['color'] ?? '#cccccc' }}"
                                           data-role="color-selector"
                                           onchange="updatePreview(this)">
                                </td>
                                <td class="text-center">
                                    <span class="badge js-preview"
                                          style="background:{{ $t['color'] ?? '#cccccc' }};color:#fff">
                                        {{ $t['label'] }}
                                    </span>
                                </td>
                                <td><button type="button" class="button mini alert js-remove-threshold"><span class="mif-bin"></span></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="button mini success mt-1" id="js-add-threshold">
                        <span class="mif-plus"></span> {{ trans('cruds.risk_scoring.add_threshold') }}
                    </button>
                </div>
            </div>

            {{-- ================================================================
                 Boutons d'action
            ================================================================ --}}
            <div class="row mt-2">
                <div class="cell-lg-12 cell-md-12">
                    <button type="submit" class="button success">
                        <span class="mif-floppy-disk2"></span>
                        &nbsp;{{ trans("common.save") }}
                    </button>
                    &nbsp;
                    <a class="button" href="/risk/scoring" role="button">
                        <span class="mif-cancel"></span>
                        &nbsp;{{ trans("common.cancel") }}
                    </a>
                </div>
            </div>

        </div>{{-- /grid --}}
    </form>
</div>

<style>
/*
 * section-header : barre de titre générique (même style que le titre du panel MetroUI)
 * Reprend la couleur de fond du panel-title de MetroUI (#323232 / #3d3d3d)
 */
.section-header {
    background : #3d3d3d;
    color      : #fff;
    padding    : 8px 14px;
    font-size  : 14px;
    font-weight: 600;
    border-radius: 3px;
    margin-bottom: 12px;
}

/*
 * level-section-header : variante colorée pour les blocs Probabilité / Exposition / Impact / Vulnérabilité
 * — fond par défaut bleu-gris (Probabilité), surchargé en ligne pour les autres
 */
.level-section-header {
    background   : #2c6e9c;   /* Probabilité — bleu */
    color        : #fff;
    padding      : 7px 12px;
    font-size    : 13px;
    font-weight  : 600;
    border-radius: 3px 3px 0 0;
    margin-bottom: 0;
}

/* Colle le header à la table qui suit */
.level-section-header + .table {
    border-top: none;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // =========================================================================
    // Formule
    // =========================================================================
    const formulaSelect = document.getElementById('formula-select');

    function updateFormulaUI() {
        const opt      = formulaSelect.options[formulaSelect.selectedIndex];
        const needsExp = opt ? opt.dataset.requiresExposure === '1' : false;
        const desc     = opt ? opt.dataset.description : '';

        document.getElementById('formula-description').textContent = desc;
        document.getElementById('probability-col').style.display   = needsExp ? 'none' : '';
        document.getElementById('exposure-col').style.display      = needsExp ? ''     : 'none';
        document.getElementById('vulnerability-row').style.display = needsExp ? ''     : 'none';
    }

    formulaSelect.addEventListener('change', updateFormulaUI);
    updateFormulaUI();

    // =========================================================================
    // Aperçu badge couleur
    // =========================================================================
    window.updatePreview = function (inputEl) {
        const row     = inputEl.closest('tr');
        const preview = row.querySelector('.js-preview');
        const label   = row.querySelector('.js-threshold-label');
        if (!preview) return;
        preview.style.background = inputEl.value;
        preview.style.color      = '#fff';
        if (label) preview.textContent = label.value || '—';
    };

    document.getElementById('thresholds-body').addEventListener('input', function (e) {
        if (!e.target.classList.contains('js-threshold-label')) return;
        const row     = e.target.closest('tr');
        const preview = row.querySelector('.js-preview');
        if (preview) preview.textContent = e.target.value || '—';
    });

    // =========================================================================
    // Suppression de lignes
    // =========================================================================
    document.addEventListener('click', function (e) {

        const removeLevel = e.target.closest('.js-remove-level');
        if (removeLevel) {
            const tbody = removeLevel.closest('tbody');
            if (tbody.querySelectorAll('tr.level-row').length > 2) {
                removeLevel.closest('tr').remove();
            }
            return;
        }

        const removeThr = e.target.closest('.js-remove-threshold');
        if (removeThr) {
            const tbody = document.getElementById('thresholds-body');
            if (tbody.querySelectorAll('tr.threshold-row').length > 2) {
                removeThr.closest('tr').remove();
            }
        }
    });

    // =========================================================================
    // Ajout dynamique de niveaux
    // =========================================================================
    const counters = {
        probability   : {{ count(old('probability_levels', $probLevels)) }},
        impact        : {{ count(old('impact_levels', $impLevels)) }},
        exposure      : {{ count(old('exposure_levels', $expLevels)) }},
        vulnerability : {{ count(old('vulnerability_levels', $vulnLevels)) }},
    };

    document.querySelectorAll('.js-add-level').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const field = btn.dataset.field;
            const idx   = counters[field]++;
            const tbody = document.getElementById(field + '-body');
            const tr    = document.createElement('tr');
            tr.className = 'level-row';
            tr.innerHTML =
                '<td><input type="number" name="' + field + '_levels[' + idx + '][value]" class="input" min="0" style="width:80px; text-align:center;" required></td>' +
                '<td><input type="text"   name="' + field + '_levels[' + idx + '][label]" class="input" required></td>' +
                '<td><input type="text"   name="' + field + '_levels[' + idx + '][description]" class="input"></td>' +
                '<td><button type="button" class="button mini alert js-remove-level"><span class="mif-bin"></span></button></td>';
            tbody.appendChild(tr);
        });
    });

    // =========================================================================
    // Ajout dynamique de seuils
    // =========================================================================
    let thrIdx = {{ count(old('risk_thresholds', $thresholds)) }};

    document.getElementById('js-add-threshold').addEventListener('click', function () {
        const tbody  = document.getElementById('thresholds-body');
        const tr     = document.createElement('tr');
        tr.className = 'threshold-row';

        tr.innerHTML =
            '<td><input type="text"   name="risk_thresholds[' + thrIdx + '][level]" class="input" required></td>' +
            '<td><input type="text"   name="risk_thresholds[' + thrIdx + '][label]" class="input js-threshold-label" required></td>' +
            '<td><input type="number" name="risk_thresholds[' + thrIdx + '][max]"   class="input" placeholder="∞" min="1" style="width:80px; text-align:center;"></td>' +
            '<td><input type="color" name="risk_thresholds[' + thrIdx + '][color]" class="js-color-input"' +
                ' value="#cccccc" data-role="color-selector" onchange="updatePreview(this)"></td>' +
            '<td><span class="badge js-preview" style="background:#cccccc;color:#fff">—</span></td>' +
            '<td><button type="button" class="button mini alert js-remove-threshold"><span class="mif-bin"></span></button></td>';

        tbody.appendChild(tr);

        // Initialise le composant MetroUI sur le nouvel input
        if (window.Metro) {
            Metro.makePlugin(tr.querySelector('[data-role="color-selector"]'), 'color-selector');
        }

        thrIdx++;
    });

});
</script>
@endsection