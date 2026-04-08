@extends("layout")

@section("content")
<div data-role="panel" data-title-caption='{{ trans("cruds.risk.matrix") }}' data-collapsible="false" data-title-icon="<span class='mif-warning'></span>">

<div class="grid">

{{-- Compteurs résumé --}}
{{--
<div class="row">
    <div class="cell-lg-2 cell-md-6 mt-2">
        <div class="more-info-box fg-white" style="background-color:#D94F45 !important">
            <div class="content">
                <h2 class="text-bold mb-0">{{ $stats['critical'] }}</h2>
                <div>{{ trans('cruds.risk.levels.critical') }}</div>
            </div>
            <div class="icon"><span class="mif-warning"></span></div>
            <a href="/risk/index?level=critical" class="more">{{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-2 cell-md-6 mt-2">
        <div class="more-info-box fg-white" style="background-color:#E8731A !important">
            <div class="content">
                <h2 class="text-bold mb-0">{{ $stats['high'] }}</h2>
                <div>{{ trans('cruds.risk.levels.high') }}</div>
            </div>
            <div class="icon"><span class="mif-warning"></span></div>
            <a href="/risk/index?level=high" class="more">{{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-2 cell-md-6 mt-2">
        <div class="more-info-box fg-white" style="background-color:#E09B1A !important">
            <div class="content">
                <h2 class="text-bold mb-0">{{ $stats['medium'] }}</h2>
                <div>{{ trans('cruds.risk.levels.medium') }}</div>
            </div>
            <div class="icon"><span class="mif-clock"></span></div>
            <a href="/risk/index?level=medium" class="more">{{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-2 cell-md-6 mt-2">
        <div class="more-info-box fg-white" style="background-color:#3AB87A !important">
            <div class="content">
                <h2 class="text-bold mb-0">{{ $stats['low'] }}</h2>
                <div>{{ trans('cruds.risk.levels.low') }}</div>
            </div>
            <div class="icon"><span class="mif-checkmark"></span></div>
            <a href="/risk/index?level=low" class="more">{{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-2 cell-md-6 mt-2">
        <div class="more-info-box fg-white" style="background-color:#D94F45 !important">
            <div class="content">
                <h2 class="text-bold mb-0">{{ $stats['total'] }}</h2>
                <div>{{ trans('cruds.risk.fields.total') }}</div>
            </div>
            <div class="icon"><span class="mif-report"></span></div>
            <a href="/risk/index" class="more">{{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
        </div>
    </div>

    <div class="cell-lg-2 cell-md-6 mt-2">
        <div class="more-info-box fg-grey" style="background-color:#ffffff !important">
            <div class="content">
                <h2 class="text-bold mb-0">{{ $stats['overdue'] }}</h2>
                <div>{{ trans('cruds.risk.fields.overdue') }}</div>
            </div>
            <div class="icon"><span class="mif-access-time"></span></div>
            <a href="/risk/index?overdue=1" class="more">{{ trans('common.more_info') }} <span class="mif-arrow-right"></span></a>
        </div>
    </div>

</div>
-- }}
    {{-- Matrice --}}
    <div class="row">
        <div class="cell-lg-10 cell-md-12">
            <div class="overflow-auto">
            <table class="table border text-center" style="table-layout:fixed; min-width:400px">
                <thead>
                    <tr>
                        <th style="width:110px"></th>
                        @foreach ($xAxis as $impact)
                        <th>
                            {{ trans('cruds.risk.fields.impact') }} {{ $impact['value'] }}
                            <br><small class="text-muted">{{ $impact['label'] }}</small>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach (array_reverse($yAxis) as $yLevel)
                <tr>
                    <th class="text-right" style="font-size:.85rem">
                        @if ($scoringConfig->usesLikelihood())
                            {{ trans('cruds.risk.fields.likelihood') }} {{ $yLevel['value'] }}
                        @else
                            {{ trans('cruds.risk.fields.probability') }} {{ $yLevel['value'] }}
                            <br><small class="text-muted">{{ $yLevel['label'] }}</small>
                        @endif
                    </th>
                    @foreach ($xAxis as $impact)
                        @php
                            $cell      = $matrix[$yLevel['value']][$impact['value']] ?? [];
                            $count     = count($cell);
                            $score     = $yLevel['value'] * $impact['value'];
                            $threshold = $scoringConfig->thresholdFor($score);
                            $bgColor   = $threshold['color'];
                            $txtColor  = '#fff';
                        @endphp
                        <td style="background:{{ $bgColor }};color:{{ $txtColor }};padding:10px;vertical-align:middle;{{ $count > 0 ? 'cursor:pointer' : '' }}"
                            @if($count > 0)
                                onclick="location.href='/risk/index?score={{ $score }}'"
                                data-role="hint"
                                data-hint-position="top"
                                data-hint-text="{{ collect($cell)->pluck('name')->take(5)->implode(', ') }}{{ $count > 5 ? ' …' : '' }}"
                            @endif>
                            @if ($count > 0)
                                <div style="font-size:1.5rem;font-weight:700;line-height:1">{{ $count }}</div>
                                <small>{{ $count === 1 ? trans('cruds.risk.singular') : trans('cruds.risk.plural') }}</small>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>

            {{-- Légende --}}
            <div class="mt-2 d-flex gap-2">
                @foreach ($scoringConfig->risk_thresholds as $i => $t)
                    @php
                        $prevMax = $i > 0 ? $scoringConfig->risk_thresholds[$i-1]['max'] + 1 : 1;
                    @endphp
                    <span class="badge" style="background:{{ $t['color'] }};color:#fff">
                        {{ $t['label'] }}
                        @if ($t['max']) {{ $prevMax }}–{{ $t['max'] }}
                        @else &gt; {{ $scoringConfig->risk_thresholds[$i-1]['max'] ?? 0 }} @endif
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Répartition par statut --}}
        <div class="cell-lg-2 cell-md-12">
            <table class="table compact border mt-2">
            <tr>
                <td colspan=3>
            <strong>{{ trans('cruds.risk.fields.by_risks') }}</strong>
            </td>
            </tr>
                @foreach ($scoringConfig->risk_thresholds as $i => $t)
                <tr>
                    <td class="text-right">
                    <span class="badge" style="background:{{ $t['color'] }};color:#fff">
                        {{ $t['label'] }}
                    </span>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach
                <tr>
                    <td class="text-right">
                    <b>{{ trans('cruds.risk.fields.total') }}</b>
                    </td>
                    <td class="text-right">
                    <b>{{ $stats['total'] }}</b>
                    </td>
                    <td>
                        @if($stats['total'] > 0)
                        <a href="/risk/index" class="no-underline">
                            <span class="mif-chevron-right"></span>
                        </a>
                        @endif
                    </td>
                </tr>
            <tr>
                <td colspan="3">
            <strong>{{ trans('cruds.risk.fields.by_status') }}</strong>
            </td>
            </tr>

                @foreach (\App\Models\Risk::STATUS_LABELS as $status => $label)
                <tr>
                    <td class="text-right">
                    <a href="/risk/index?status={{ $status }}" class="no-underline">
                        <span class="badge {{ \App\Models\Risk::STATUS_COLORS[$status] }}">
                            {{ $label }}
                        </span>
                    </a>
                    </td>
                    <td class="text-right">
                        <b>{{ $stats['by_status'][$status] ?? 0 }}</b>
                    </td>
                    <td>
                        @if(($stats['by_status'][$status] ?? 0) > 0)
                        <a href="/risk/index?status={{$status}}" class="no-underline">
                                <span class="mif-chevron-right"></span>
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    {{-- Navigation
    <div class="row mt-4">
        <div class="cell-12">
            <a class="button" href="/risk/index">
                <span class="mif-cancel"></span>
                &nbsp;{{ trans("common.cancel") }}
            </a>
        </div>
    </div>
    --}}

</div>
</div>
@endsection