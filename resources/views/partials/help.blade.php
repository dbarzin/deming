{{-- Bouton discret vers la page de documentation correspondante (resources/lang/*/help.php) --}}
<a href="{{ trans('help.url') . trans('help.pages.' . $page) }}" target="_blank" rel="noopener"
   class="btn btn-help float-end ms-2">{{ trans('help.button') }}</a>
