@extends("layout")

@section("content")
<div data-role="panel" data-title-caption='{{ trans("cruds.risk.list") }}' data-collapsible="false" data-title-icon="<span class='mif-warning'></span>">

    <div class="grid mb-2">
        <div class="row">

            <div class="cell-lg-2 cell-md-3">
                <select id="filter-status" data-role="select">
                    <option value="none">-- {{ trans("cruds.risk.fields.choose_status") }} --</option>
                    @foreach (\App\Models\Risk::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" @if(Session::get('risk_status') === $value) selected @endif>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if (Auth::User()->role !== 3)
            <div class="cell-lg-2 cell-md-3">
                <select id="filter-owner" data-role="select">
                    <option value="none">-- {{ trans("cruds.risk.fields.choose_owner") }} --</option>
                    @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}" @if(Session::get('risk_owner') == $owner->id) selected @endif>
                            {{ $owner->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="cell-lg-2 cell-md-3 mt-2">
                <input type="radio" data-role="radio" data-append="{{ trans('cruds.risk.fields.overdue_all') }}"  value="0" id="overdue0" {{ Session::get('risk_overdue','0')==='0' ? 'checked' : '' }}>
                <input type="radio" data-role="radio" data-append="{{ trans('cruds.risk.fields.overdue_only') }}" value="1" id="overdue1" {{ Session::get('risk_overdue')==='1' ? 'checked' : '' }}>
            </div>

            <div class="cell-lg-5 cell-md-1">
            </div>

            <div class="cell-lg-1 cell-md-1">
                @if (Auth::User()->role === 1 || Auth::User()->role === 2)
                    <button class="button primary" onclick="location.href='/risk/create'">
                        <span class="mif-plus"></span>
                        {{ trans('common.new') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    <table
        id="risks-table"
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
                <th class="sortable-column">{{ trans("cruds.risk.fields.name") }}</th>
                <th class="sortable-column">{{ trans("cruds.risk.fields.owner") }}</th>

                {{-- Colonnes intermédiaires selon la config --}}
                @if ($scoringConfig->usesLikelihood())
                    <th class="sortable-column">{{ trans("cruds.risk.fields.likelihood") }}</th>
                    <th class="sortable-column">{{ trans("cruds.risk.fields.vulnerability") }}</th>
                @else
                    <th class="sortable-column">{{ trans("cruds.risk.fields.probability") }}</th>
                @endif

                <th class="sortable-column">{{ trans("cruds.risk.fields.impact") }}</th>
                <th class="sortable-column sort-desc">{{ trans("cruds.risk.fields.score") }}</th>
                <th class="sortable-column">{{ trans("cruds.risk.fields.status") }}</th>
                <th class="sortable-column">{{ trans("cruds.risk.fields.next_review") }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($risks as $risk)
        <tr>
            <td>
                <span style="display:none">{{ $risk->name }}</span>
                <a href="/risk/show/{{ $risk->id }}">{{ $risk->name }}</a>
            </td>
            <td>{{ $risk->owner?->name ?? '—' }}</td>

            {{-- Colonnes intermédiaires selon la config --}}
            @if ($scoringConfig->usesLikelihood())
                <td>{{ $risk->risk_likelihood }}</td>
                <td>{{ $risk->vulnerability }}</td>
            @else
                <td>{{ $risk->probability }}</td>
            @endif

            <td>{{ $risk->impact }}</td>

            {{-- Score délégué au modèle Risk --}}
            <td>
                @php
                    $score     = $risk->computedScore($scoringConfig);
                    $threshold = $scoringConfig->thresholdFor($score);
                @endphp
                <span style="display:none">{{ str_pad($score, 4, '0', STR_PAD_LEFT) }}</span>
                <span class="badge"
                      style="background:{{ $threshold['color'] }};color:#fff;padding:2px 8px;font-size:1rem">
                    {{ $score }}
                </span>
            </td>

            <td>
                <span class="badge {{ \App\Models\Risk::STATUS_COLORS[$risk->status] ?? 'secondary' }}">
                    {{ \App\Models\Risk::STATUS_LABELS[$risk->status] ?? $risk->status }}
                </span>
            </td>
            <td style="white-space:nowrap">
                @if ($risk->next_review_at)
                    @if ($risk->is_overdue)
                        <font color="red"><b>{{ $risk->next_review_at->format('Y-m-d') }}</b></font>
                    @else
                        <font color="green">{{ $risk->next_review_at->format('Y-m-d') }}</font>
                    @endif
                @else
                    —
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

</div>

{{-- Alignemet de la table --}}
<style>
#risks-table td:nth-child(5),
#risks-table td:nth-child(6),
#risks-table td:nth-child(7),
#risks-table th:nth-child(5),
#risks-table th:nth-child(6),
#risks-table th:nth-child(7) {
    text-align: center !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    let ready = false;
    window.addEventListener('load', () => {
        requestAnimationFrame(() => requestAnimationFrame(() => { ready = true; }));
    });

    const getParam = (k) => new URLSearchParams(location.search).get(k) ?? '';

    function snapshotFilters() {
        return {
            status  : document.getElementById('filter-status')?.value ?? getParam('status'),
            owner   : document.getElementById('filter-owner')?.value  ?? getParam('owner'),
            overdue : document.getElementById('overdue1')?.checked ? '1' : '0',
        };
    }

    function navigateWithAll(patch = {}) {
        const next = new URL('/risk/index', location.origin);
        const all  = { ...snapshotFilters(), ...patch };
        for (const [k, v] of Object.entries(all)) {
            if (v == null || String(v) === '' || v === 'none') next.searchParams.delete(k);
            else next.searchParams.set(k, String(v));
        }
        if (next.toString() !== location.href) location.assign(next.toString());
    }

    const bindChange = (id, key) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', () => {
            if (!ready) return;
            navigateWithAll({ [key]: el.value });
        });
    };

    bindChange('filter-status', 'status');
    bindChange('filter-owner',  'owner');

    ['overdue0', 'overdue1'].forEach((id, i) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', () => {
            if (!ready) return;
            navigateWithAll({ overdue: String(i) });
        });
    });
});
</script>
@endsection