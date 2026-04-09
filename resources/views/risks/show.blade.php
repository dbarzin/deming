@extends("layout")

@section('title', $risk->name)

@section("content")
<div data-role="panel" data-title-caption='{{ trans("cruds.risk.title_singular") }}' data-collapsible="false" data-title-icon="<span class='mif-warning'></span>">

<div class="grid">

    {{-- Nom --}}
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.name") }}</strong>
        </div>
        <div class="cell-lg-7 cell-md-9">
            {{ $risk->name }}
        </div>
    </div>

    {{-- Description --}}
    @if ($risk->description)
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.description") }}</strong>
        </div>
        <div class="cell-lg-7 cell-md-9">
            <pre>{{ $risk->description }}</pre>
        </div>
    </div>
    @endif

    {{-- Propriétaire --}}
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.owner") }}</strong>
        </div>
        <div class="cell-lg-3 cell-md-4">
            {{ $risk->owner?->name ?? '—' }}
        </div>
    </div>
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.review_frequency") }}</strong>
        </div>
        <div class="cell-lg-2 cell-md-3">
            {{ $risk->review_frequency }} {{ trans('common.months') }}
            @if ($risk->next_review_at)
                &nbsp;·&nbsp;
                @if ($risk->is_overdue)
                    <font color="red"><b>{{ $risk->next_review_at->format('Y-m-d') }}</b></font>
                    &nbsp;&#9888;
                @else
                    <font color="green">{{ $risk->next_review_at->format('Y-m-d') }}</font>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
    </div>

    {{-- Évaluation : probabilité --}}
    @if (!$scoringConfig->usesLikelihood())
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.probability") }}</strong>
        </div>
        <div class="cell-lg-1 cell-md-1">
            @php $probThreshold = $scoringConfig->thresholdFor($risk->probability * max($scoringConfig->levelValues('impact'))); @endphp
            <span class="badge" style="font-size:1.1rem;background:#7f8c8d;color:#fff">{{ $risk->probability }}</span>
            &nbsp;
            {{ $scoringConfig->levelLabel('probability', $risk->probability) }}
        </div>
        @if ($risk->probability_comment)
        <div class="cell-lg-5 cell-md-7">
            <small class="text-muted">{{ $risk->probability_comment }}</small>
        </div>
        @endif
    </div>
@endif
    {{-- Exposition + Vulnérabilité (formule likelihood_x_impact) --}}
    @if ($scoringConfig->usesLikelihood())
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.exposure") }}</strong>
        </div>
        <div class="cell-lg-1 cell-md-2">
            <span class="badge"style="font-size:1.1rem;background:#7f8c8d;color:#fff">{{ $risk->exposure ?? '—' }}</span>
            &nbsp;{{ $scoringConfig->levelLabel('exposure', $risk->exposure ?? 0) }}
        </div>
    </div>
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.vulnerability") }}</strong>
        </div>
        <div class="cell-lg-2 cell-md-3">
            <span class="badge" style="font-size:1.1rem;background:#7f8c8d;color:#fff">{{ $risk->vulnerability ?? '—' }}</span>
            &nbsp;{{ $scoringConfig->levelLabel('vulnerability', $risk->vulnerability ?? 0) }}
        </div>
    </div>
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.likelihood") }}</strong>
        </div>
        <div class="cell-lg-1 cell-md-1">
            <span class="badge" style="font-size:1.1rem;background:#7f8c8d;color:#fff">{{ $risk->risk_likelihood ?? '—' }}</span>
        </div>
    </div>
    @endif

    {{-- Impact --}}
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.impact") }}</strong>
        </div>
        <div class="cell-lg-1 cell-md-1">
            <span class="badge" style="font-size:1.1rem;background:#7f8c8d;color:#fff">{{ $risk->impact }}</span>
            &nbsp;
            {{ $scoringConfig->levelLabel('impact', $risk->impact) }}
        </div>
        @if ($risk->impact_comment)
        <div class="cell-lg-5 cell-md-7">
            <small class="text-muted">{{ $risk->impact_comment }}</small>
        </div>
        @endif
    </div>

    <div class="row">
    </div>

    {{-- Score calculé --}}
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.score") }}</strong>
        </div>
        <div class="cell-lg-6 cell-md-8">
            @php $scoreThreshold = $scoringConfig->thresholdFor($risk->risk_score); @endphp
            <span class="badge" style="font-size:1.1rem;background:{{ $scoreThreshold['color'] }};color:#fff">
                {{ $risk->risk_score }}
            </span>
            &nbsp;&mdash;&nbsp;
            <strong>{{ $scoreThreshold['label'] }}</strong>
        </div>
    </div>

    <div class="row">
    </div>

    {{-- Statut de traitement --}}
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.status") }}</strong>
        </div>
        <div class="cell-lg-2 cell-md-3">
            <span class="badge {{ \App\Models\Risk::STATUS_COLORS[$risk->status] ?? 'secondary' }}" style="font-size:1rem;">
                {{ \App\Models\Risk::STATUS_LABELS[$risk->status] ?? $risk->status }}
            </span>
        </div>
        @if ($risk->status_comment)
        <div class="cell-lg-4 cell-md-5">
            <small class="text-muted">{{ $risk->status_comment }}</small>
        </div>
        @endif
    </div>

    {{-- Mesures liés --}}
    @if ($risk->measures->isNotEmpty())
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.measures") }}</strong>
        </div>
        <div class="cell-lg-7 cell-md-9">
            @foreach ($risk->measures as $measure)
                <a href="/alice/show/{{ $measure->id }}">{{ $measure->name }}</a>
                @if (!$loop->last) , @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Plans d'action liés --}}
    @if ($risk->actions->isNotEmpty())
    <div class="row">
        <div class="cell-lg-1 cell-md-2">
            <strong>{{ trans("cruds.risk.fields.action_plan") }}</strong>
        </div>
        <div class="cell-lg-7 cell-md-9">
            @foreach ($risk->actions as $action)
                <a href="/action/show/{{ $action->id }}">{{ $action->name }}</a>
                @if (!$loop->last) , @endif
            @endforeach
        </div>
    </div>
    @endif

    <div class="row">
    </div>

    {{-- Boutons d'action --}}
    <div class="row">
        <div class="cell-12">

            @if (Auth::User()->role === 1 || Auth::User()->role === 2)
                <a href="/risk/edit/{{ $risk->id }}" class="button primary">
                    <span class="mif-wrench"></span>
                    &nbsp;{{ trans("common.edit") }}
                </a>
                &nbsp;
            @endif

            @if (Auth::User()->role === 1)
                <form action="/risk/delete/{{ $risk->id }}" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}" class="d-inline">
                    @csrf
                    <button class="button alert">
                        <span class="mif-fire"></span>
                        &nbsp;{{ trans("common.delete") }}
                    </button>
                </form>
                &nbsp;
                <a class="button" href="/logs/history/risk/{{ $risk->id }}">
                    <span class="mif-log-file"></span>
                    &nbsp;{{ trans("common.history") }}
                </a>
                &nbsp;
            @endif

            <a class="button" href="/risk/index">
                <span class="mif-cancel"></span>
                &nbsp;{{ trans("common.cancel") }}
            </a>

        </div>
    </div>

</div>
</div>
@endsection