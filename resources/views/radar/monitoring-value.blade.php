<p class="small text-muted mt-2 mb-0 monitoring-value" data-value="{{ $key }}">
    <i class="bi bi-lightbulb"></i>
    <strong>{{ trans('cruds.monitoring.' . ($label ?? 'value_label')) }}</strong>
    {{ trans('cruds.monitoring.value.' . $key) }}
</p>
