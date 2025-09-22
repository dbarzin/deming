@extends("layout")

@section("content")
    <div data-role="panel" data-title-caption='{{ trans("cruds.control.list")}}' data-collapsible="false" data-title-icon="<span class='mif-paste'></span>">
    <div class="grid mb-2">
        <div class="row">
            <div class="cell-lg-2 cell-md-1">
                <select id='domain' name="domain_id" data-role="select">
                    <option value="0">-- {{ trans("cruds.control.fields.choose_domain")}} --</option>
                    @foreach ($domains as $domain)
                        <option value="{{ $domain->id }}"
                            @if (intval(Session::get("domain"))==$domain->id)
                                selected
                            @endif >
                            {{ $domain->title }} - {{ $domain->description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="cell-lg-2 cell-md-1">
                <select id='clause' name="clause" data-role="select">
                    <option value="none">-- {{ trans("cruds.control.fields.choose_clause")}} --</option>
                    @foreach ($clauses as $clause)
                        <option
                            @if (Session::get("clause")==trim($clause))
                                selected
                            @endif >
                            {{ $clause }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="cell-lg-2 cell-md-1">
                <select id='scope' name="scope" data-role="select">
                    <option value="none">-- {{ trans("cruds.control.fields.choose_scope")}} --</option>
                    @foreach ($scopes as $scope)
                        <option
                            @if (Session::get("scope")==$scope)
                                selected
                            @endif >
                            {{ $scope }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="cell-lg-2 cell-md-1">
                <select id='cur_period' name="period" data-role="select">
                    <option value="99"
                        @if (Session::get("period")==="99")
                            selected
                        @endif
                    >-- {{ trans("cruds.control.fields.choose_period") }} --</option>
                        @for ($i = -12; $i < 12; $i++)
                            <option value="{{ $i }}"
                            @if ((Session::get("period"))==strval($i))
                                selected
                            @endif
                            >
                            {{ now()->day(1)->addMonth($i)->format("M Y") }}
                            </option>
                        @endfor
                    </select>
                </div>
            <div class="cell-lg-3 cell-md-2 mt-2">
                 <input type="radio" data-role="radio" data-append="{{ trans("cruds.control.fields.status_all") }}" value="0" id="status0" {{ (Session::get("status")=="0") ? 'checked' : '' }}>
                 <input type="radio" data-role="radio" data-append="{{ trans("cruds.control.fields.status_done") }}" value="1" id="status1" {{ (Session::get("status")=="1") ? 'checked' : '' }}>
                 <input type="radio" data-role="radio" data-append="{{ trans("cruds.control.fields.status_todo") }}" value="2" id="status2" {{ (Session::get("status")=="2") ? 'checked' : '' }}>
            </div>
            <div class="cell-lg-1 cell-md-2" align="right">
			@if ((Auth::User()->role==1)||(Auth::User()->role==2))
				<button class="button primary" onclick="location.href = '/bob/create';">
		            <span class="mif-plus"></span>
		            &nbsp;
					{{ trans('common.new') }}
               </button>
            @endif
			</div>
        </div>
    </div>

    <table
        id="controls"
        class="table data-table striped row-hover cell-border"
        data-role="table"
        data-rows="100"
        data-show-activity="true"
        data-rownum="false"
        data-check="false"
        data-check-style="1"
        data-search="true"
       >
        <thead>
            <tr>
                <th class="sortable-column" width="5%">{{ trans("cruds.control.fields.clauses") }}</th>
                <th width="40%">{{ trans("cruds.control.fields.name") }}</th>
                <th class="sortable-column" width="10%">{{ trans("cruds.control.fields.scope") }}</th>
                <th class="sortable-column" width="5%">{{ trans("cruds.control.fields.score") }}</th>
                <th class="sortable-column sort-asc"  width="5%">{{ trans("cruds.control.fields.planned") }}</th>
                <th class="sortable-column sort-asc"  width="5%">{{ trans("cruds.control.fields.realized") }}</th>
                <th class="sortable-column"  width="5%">{{ trans("cruds.control.fields.next") }}</th>
            </tr>
        </thead>
        <tbody>
    @foreach($controls as $control)
        <tr>
            <td>
                @foreach($control->measures as $measure)
                <a id="{{ $measure['clause'] }}" href="/alice/show/{{ $measure['id'] }}">
                    {{ $measure['clause'] }}
                </a>
                @if (!$loop->last)
                ,
                @endif
                @endforeach
            </td>
            <td>
                    {{ $control->name }}
            </td>
            <td>
                    {{ $control->scope }}
            </td>
            <td>
                <center id="{{ $control->score }}">
                    @if ($control->action_id!=null)
                        <a href="/action/show/{{ $control->action_id }}" class="no-underline">
                    @endif
                    @if ($control->score==1)
                        &#128545;
                    @elseif ($control->score==2)
                        &#128528;
                    @elseif ($control->score==3)
                        <span style="filter: sepia(1) saturate(5) hue-rotate(70deg)">&#128512;</span>
                    @else
                        &#9675; <!-- &#9899; -->
                    @endif
                    @if ($control->action_id!=null)
                    </a>
                    @endif
                </center>
            </td>
            <td>
                <!-- format in red when month passed -->
                @if (($control->status === 0)||($control->status === 1))
                <a id="{{ $control->plan_date }}" href="/bob/show/{{$control->id}}">
                <b> @if (today()->lte($control->plan_date))
                        <font color="green">{{ $control->plan_date }}</font>
                    @else
                        <font color="red">{{ $control->plan_date }}</font>
                    @endif
                </b>
                </a>
                @else
                    {{ $control->plan_date }}
                @endif
            </td>
            <td>
                <b id="{{ $control->realisation_date }}">
                    <a href="/bob/show/{{$control->id}}">
                        {{ $control->realisation_date }}
                    </a>
                    @if ( ($control->status===1 )&& ((Auth::User()->role===1)||(Auth::User()->role===2)))
                        &nbsp;
                        <a href="/bob/make/{{ $control->id }}"><span class="mif-hour-glass"/></a>
                    @endif
                </b>
            </td>
            <td>
                <b id="{{ $control->next_date }}">
                    @if ($control->next_id!=null)
                    <a href="/bob/show/{{$control->next_id}}">
                        {{ $control->next_date }}
                    </a>
                    @endif
                </b>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const $ = (sel, root=document) => root.querySelector(sel);
  const getParam = (k) => new URLSearchParams(location.search).get(k) ?? '';

  // --- Attendre que Metro ait fini d'initialiser ses selects
  let ready = false;
  window.addEventListener('load', () => {
    // 2 rAF pour laisser Metro construire le DOM
    requestAnimationFrame(() => requestAnimationFrame(() => { ready = true; }));
  });

  // --- Snapshot de tous les filtres (UI -> fallback URL)
  function snapshotFilters() {
    const searchInput = $('.table-search-block input');
    return {
      domain : document.getElementById('domain')?.value ?? getParam('domain'),
      clause : (document.getElementById('clause')?.value ?? getParam('clause')).trim(),
      scope  : (document.getElementById('scope')?.value  ?? getParam('scope')).trim(),
      period : document.getElementById('cur_period')?.value ?? getParam('period'),
      status : (document.getElementById('status0')?.checked ? '0' :
               document.getElementById('status1')?.checked ? '1' :
               document.getElementById('status2')?.checked ? '2' : getParam('status')),
      search : (searchInput && searchInput.value !== '') ? searchInput.value : getParam('search')
    };
  }

  // --- Navigation: toujours poster TOUTES les valeurs
  function navigateWithAll(patch={}) {
    const cur  = new URL(location.href);
    const next = new URL(location.href);
    next.pathname = '/bob/index';

    const all = { ...snapshotFilters(), ...patch };
    for (const [k,v] of Object.entries(all)) {
      if (v == null || String(v) === '') next.searchParams.delete(k);
      else next.searchParams.set(k, String(v));
    }
    if (next.toString() !== location.href) location.assign(next.toString());
  }

  // ==================================================================

  // --- 2) lit la valeur dans l'URL
  const params = new URLSearchParams(location.search);
  const searchValue = params.get('search') || '';

  // --- 3) applique à l'API table (filtrage effectif)
  const applyToAPI = (val) => {
    const tableEl = document.getElementById('controls');
    const api = tableEl ? Metro.getPlugin(tableEl, 'table') : null;
    if (api && typeof api.search === 'function') {
      api.search(val);
      return true;
    }
    return false;
  };

  // --- 4) pose la valeur dans l'input quand il existe (et quand il est recréé)
  function setSearchInputWhenReady(val, timeoutMs = 3000) {
    if (!val) return;

    const trySet = () => {
      const input = $('.table-search-block input');
      if (!input) return false;
      if (input.value !== val) {
        input.value = val;
        input.dispatchEvent(new Event('input',  { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
      return true;
    };

    if (trySet()) return;

    // a) quelques frames
    let tries = 0, maxTries = 20;
    const raf = () => {
      if (trySet()) return;
      if (++tries >= maxTries) return;
      requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);

    // b) garde-fou si l’input apparaît plus tard (reconstruction Metro)
    const mo = new MutationObserver(() => {
      if (trySet()) mo.disconnect();
    });
    mo.observe(document.body, { childList: true, subtree: true });
    setTimeout(() => mo.disconnect(), timeoutMs);
  }

  // --- 5) exécution au chargement
  if (searchValue) {
    // filtre la table côté API (immédiat ou dès que prêt)
    if (!applyToAPI(searchValue)) {
      requestAnimationFrame(() => applyToAPI(searchValue));
    }
    // force l’affichage dans l’input dès qu’il existe
    setSearchInputWhenReady(searchValue);
  }


  // ==================================================================
  // --- Bind: ne PAS utiliser e.isTrusted (Metro déclenche un change programmatique)
  const bindChange = (id, key, coerce = v => v) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', () => {
      if (!ready) return;                      // on ignore les changements pendant l'init
      const newVal = coerce(el.value);
      navigateWithAll({ [key]: newVal });
    }, false);
  };

  bindChange('domain', 'domain', v => String(v));
  bindChange('clause', 'clause', v => String(v).trim());
  bindChange('scope',  'scope',  v => String(v).trim());
  bindChange('cur_period', 'period', v => String(v));

  // Radios status (OK de garder le flag ready)
  const bindStatus = (id, value) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', () => {
      if (!ready) return;
      navigateWithAll({ status: String(value) });
    }, false);
  };
  bindStatus('status0', 0);
  bindStatus('status1', 1);
  bindStatus('status2', 2);
}, false);
</script>
@endsection
