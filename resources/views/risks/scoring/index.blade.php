@extends("layout")

@section("content")
<div data-role="panel" data-title-caption='{{ trans("cruds.risk_scoring.list") }}' data-collapsible="false" data-title-icon="<span class='mif-cog'></span>">

    {{-- Messages flash — même pattern que les autres pages Deming --}}
    @if (session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert">{{ session('error') }}</div>
    @endif

    <div class="grid mb-2">
        <div class="row">
            <div class="cell-lg-12" align="right">
                <button class="button primary" onclick="location.href='/risk/scoring/create'">
                    <span class="mif-plus"></span>
                    {{ trans('common.new') }}
                </button>
            </div>
        </div>
    </div>

    <table class="table striped row-hover border cell-border">
        <thead>
            <tr>
                <th width="4%"  align="center">{{ trans("common.active") }}</th>
                <th width="25%">{{ trans("cruds.risk_scoring.fields.name") }}</th>
                <th width="18%">{{ trans("cruds.risk_scoring.fields.formula") }}</th>
                <th width="28%">{{ trans("cruds.risk_scoring.fields.levels") }}</th>
                <th width="15%">{{ trans("cruds.risk_scoring.fields.thresholds") }}</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($configs as $config)
        <tr>
            {{-- Indicateur actif --}}
            <td align="center">
                @if ($config->is_active)
                    <span class="mif-checkmark fg-green" style="font-size:1.2rem"
                          title="{{ trans('common.active') }}"></span>
                @endif
            </td>

            {{-- Nom --}}
            <td>
                <b><a href="/risk/scoring/{{ $config->id }}/edit">{{ $config->name }}</a></b>
                @if ($config->is_active)
                    &nbsp;<span class="badge primary">{{ trans('common.active') }}</span>
                @endif
            </td>

            {{-- Formule --}}
            <td>{{ $formulas[$config->formula]['label'] ?? $config->formula }}</td>

{{-- Résumé des niveaux --}}
<td>
    @if ($config->usesLikelihood())
        <small>
            <b>{{ trans('cruds.risk.fields.exposure') }} :</b>
            {{ count($config->exposure_levels ?? []) }} {{ trans('cruds.risk_scoring.fields.levels') }}
        </small>
        <br>
        <small>
            <b>{{ trans('cruds.risk.fields.vulnerability') }} :</b>
            {{ count($config->vulnerability_levels ?? []) }} {{ trans('cruds.risk_scoring.fields.levels') }}
        </small>
    @else
        <small>
            <b>{{ trans('cruds.risk.fields.probability') }} :</b>
            {{ count($config->probability_levels ?? []) }} {{ trans('cruds.risk_scoring.fields.levels') }}
        </small>
    @endif
    <br>
    <small>
        <b>{{ trans('cruds.risk.fields.impact') }} :</b>
        {{ count($config->impact_levels ?? []) }} {{ trans('cruds.risk_scoring.fields.levels') }}
    </small>
</td>
            {{-- Seuils --}}
            <td>
                @foreach ($config->risk_thresholds as $i => $t)
                    @php $prevMax = $i > 0 ? ($config->risk_thresholds[$i-1]['max'] ?? null) : null; @endphp
                        <span class="badge"
                              data-role="hint"
                              data-hint-position="top"
                              data-hint-text="{{ $t['max'] !== null
                                  ? 'Score ≤ ' . $t['max']
                                  : 'Score > ' . ($prevMax ?? 0) }}"
                              style="font-size:.8rem;background:{{ $t['color'] }};color:#fff">
                            {{ $t['label'] }}
                        </span>
                @endforeach
            </td>

            {{-- Actions --}}
            <td align="center" style="white-space:nowrap">
                <a href="/risk/scoring/{{ $config->id }}/edit" class="button mini primary"
                   title="{{ trans('common.edit') }}">
                    <span class="mif-wrench"></span>
                </a>

                @unless ($config->is_active)
                    &nbsp;
                    {{-- Activation : POST avec CSRF --}}
                    <form action="/risk/scoring/{{ $config->id }}/activate"
                          method="POST" class="d-inline"
                          onsubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
                        @csrf
                        <button type="submit" class="button mini success"
                                title="{{ trans('cruds.risk_scoring.activate') }}">
                            <span class="mif-checkmark"></span>
                        </button>
                    </form>
                    &nbsp;
                    {{-- Suppression : GET, même pattern que /bob/delete/{id} --}}
                    <form action="/risk/scoring/{{ $config->id }}/delete"
                          class="d-inline"
                          onsubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}">
                        <button type="submit" class="button mini alert"
                                title="{{ trans('common.delete') }}">
                            <span class="mif-fire"></span>
                        </button>
                    </form>
                @endunless
            </td>
        </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    {{ trans('cruds.risk_scoring.empty') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="grid mt-2">
        <div class="row">
            <div class="cell-12">
                <a class="button" href="/risk/index">
                    <span class="mif-cancel"></span>
                    &nbsp;{{ trans("common.cancel") }}
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
