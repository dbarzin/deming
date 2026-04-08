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
            {{--
            <div class="cell-lg-1 cell-md-1" align="right">
                @if (Auth::User()->role === 1 || Auth::User()->role === 2)
                    <button class="button" onclick="location.href='/risk/matrix'">
                        <span class="mif-chart-bars"></span>
                        {{ trans('cruds.risk.matrix') }}
                    </button>
                @endif
            </div>

            <div class="cell-lg-1 cell-md-1" align="right">
                @if (Auth::User()->role === 1)
                    <button class="button" onclick="location.href='/risk/export'">
                        <span class="mif-file-excel"></span>
                        {{ trans('common.export') }}
                    </button>
                @endif
            </div>
            --}}
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
                <th class="sortable-column" width="25%">{{ trans("cruds.risk.fields.name") }}</th>
                <th class="sortable-column" width="10%">{{ trans("cruds.risk.fields.owner") }}</th>
                <th class="sortable-column" width="5%"  align="center">{{ trans("cruds.risk.fields.probability") }}</th>
                <th class="sortable-column" width="5%"  align="center">{{ trans("cruds.risk.fields.impact") }}</th>
                <th class="sortable-column" width="5%"  align="center">{{ trans("cruds.risk.fields.score") }}</th>
                <th class="sortable-column" width="10%">{{ trans("cruds.risk.fields.status") }}</th>
                <th class="sortable-column sort-asc" width="8%">{{ trans("cruds.risk.fields.next_review") }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($risks as $risk)
        <tr>
            <td>
                <a href="/risk/show/{{ $risk->id }}">{{ $risk->name }}</a>
            </td>
            <td>{{ $risk->owner?->name ?? '—' }}</td>
            <td align="center">{{ $risk->probability }}</td>
            <td align="center">{{ $risk->impact }}</td>
            <td align="center">
                @php $score = $risk->risk_score; $color = $risk->risk_level_color; @endphp
                <span class="badge {{ $color }}">{{ $score }}</span>
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
        const next = new URL('/risks', location.origin);
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