@if ($group->type === \App\Models\UserGroup::TYPE_SECURITY)
<span class="badge" style="background-color:#3A72C4;color:{{ contrast_color('#3A72C4') }}">{{ trans('cruds.group.types.security') }}</span>
@else
<span class="badge text-bg-secondary">{{ trans('cruds.group.types.business') }}</span>
@endif
