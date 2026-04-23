@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.exception.list') }}" data-collapsible="false" data-title-icon="<span class='mif-cross'></span>">

    <div class="grid mb-2">
        <div class="row">
            {{-- Filtre statut --}}
            <div class="cell-lg-2 cell-md-2">
                <select id="filter-status" name="status" data-role="select">
                    <option value="">-- {{ trans('cruds.exception.fields.choose_status') }} --</option>
                    @foreach(\App\Models\Exception::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" {{ (string)($filters['status'] ?? '') === (string)$value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre mesure --}}
            <div class="cell-lg-3 cell-md-3">
                <select id="filter-measure" name="measure_id" data-role="select">
                    <option value="">-- {{ trans('cruds.exception.fields.choose_measure') }} --</option>
                    @foreach($measures as $measure)
                        <option value="{{ $measure->id }}"
                            {{ (string)($filters['measure_id'] ?? '') === (string)$measure->id ? 'selected' : '' }}>
                            {{ $measure->clause }} – {{ $measure->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre expirées --}}
            <div class="cell-lg-2 cell-md-2 mt-2">
                <input type="checkbox" id="filter-expired" data-role="checkbox"
                    data-caption="{{ trans('cruds.exception.fields.expired_only') }}"
                    {{ ($filters['expired'] ?? false) ? 'checked' : '' }}>
            </div>

            {{-- Bouton Nouveau --}}
            <div class="cell-lg-5 cell-md-5 text-right">
                <button class="button primary" onclick="location.href='/exception/create';">
                    <span class="mif-plus"></span>&nbsp;{{ trans('common.new') }}
                </button>
            </div>
        </div>
    </div>

    <table
        id="exceptions-table"
        class="table data-table striped row-hover cell-border"
        data-role="table"
        data-rows="50"
        data-show-activity="true"
        data-rownum="false"
        data-check="false"
        data-search="true"
    >
        <thead>
            <tr>
                <th class="sortable-column" width="30%">{{ trans('cruds.exception.fields.name') }}</th>
                <th class="sortable-column" width="20%">{{ trans('cruds.exception.fields.measure') }}</th>
                <th class="sortable-column" width="10%">{{ trans('cruds.exception.fields.start_date') }}</th>
                <th class="sortable-column" width="10%">{{ trans('cruds.exception.fields.end_date') }}</th>
                <th class="sortable-column" width="10%">{{ trans('cruds.exception.fields.status') }}</th>
                <th class="sortable-column" width="20%">{{ trans('cruds.exception.fields.created_by') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($exceptions as $exception)
        <tr onclick="location.href='/exception/show/{{ $exception->id }}'" style="cursor:pointer;">
            <td><a href="/exception/show/{{ $exception->id }}">{{ $exception->name }}</a></td>
            <td>
                @if($exception->measure)
                    <a href="/alice/show/{{ $exception->measure->id }}" onclick="event.stopPropagation();">
                        {{ $exception->measure->clause }}
                    </a>
                @else
                    –
                @endif
            </td>
            <td>
                {{-- Tri caché sur date ISO --}}
                <span style="display:none;">{{ $exception->start_date?->format('Y-m-d') ?? '0000-00-00' }}</span>
                {{ $exception->start_date?->format('Y-m-d') ?? '–' }}
            </td>
            <td>
                <span style="display:none;">{{ $exception->end_date?->format('Y-m-d') ?? '9999-99-99' }}</span>
                @if($exception->end_date)
                    @if($exception->end_date->isPast())
                        <font color="#D94F45"><strong>{{ $exception->end_date->format('Y-m-d') }}</strong></font>
                    @else
                        {{ $exception->end_date->format('Y-m-d') }}
                    @endif
                @else
                    –
                @endif
            </td>
            <td>
                @php
                    $colors = [
                        \App\Models\Exception::STATUS_DRAFT     => '#888888',
                        \App\Models\Exception::STATUS_SUBMITTED => '#3A72C4',
                        \App\Models\Exception::STATUS_APPROVED  => '#3AB87A',
                        \App\Models\Exception::STATUS_REJECTED  => '#D94F45',
                        \App\Models\Exception::STATUS_EXPIRED   => '#E09B1A',
                    ];
                    $color = $colors[$exception->status] ?? '#888888';
                @endphp
                <span style="color:{{ $color }}; font-weight:bold;">
                    {{ $exception->status_label }}
                </span>
            </td>
            <td>{{ $exception->createdBy?->name ?? '–' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-2">
        {{ $exceptions->withQueryString()->links() }}
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let ready = false;
    window.addEventListener('load', () => {
        requestAnimationFrame(() => requestAnimationFrame(() => { ready = true; }));
    });

    function navigate(patch = {}) {
        const url = new URL('/exception/index', location.origin);
        const cur = new URLSearchParams(location.search);

        const params = {
            status:     document.getElementById('filter-status')?.value     ?? cur.get('status')     ?? '',
            measure_id: document.getElementById('filter-measure')?.value    ?? cur.get('measure_id') ?? '',
            expired:    document.getElementById('filter-expired')?.checked  ? '1' : '0',
            ...patch
        };

        for (const [k, v] of Object.entries(params)) {
            if (v === '' || v === null) url.searchParams.delete(k);
            else url.searchParams.set(k, v);
        }
        if (url.toString() !== location.href) location.assign(url.toString());
    }

    const bindChange = (id) => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', () => { if (ready) navigate(); });
    };

    bindChange('filter-status');
    bindChange('filter-measure');
    bindChange('filter-expired');
});
</script>
@endsection
