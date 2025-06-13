<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="metro:smooth_scroll" content="true">
    <title>Deming - ISMS Controls Made Easy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
    @if (!app()->environment('production'))
    <style>
    .navview-content {
        padding-top: 50px;
    }
    .navview-pane {
        padding-top: 50px;
    }
    </style>
    @endif
</head>
<body class="cloak">
@if (!app()->environment('production'))
<div class="app-bar pos-fixed bg-orange fg-white" data-role="appbar">
      <div class="app-bar-section">
        <span class="mif-warning"></span> &nbsp; {{ app()->environment() }} - {{ trans('menu.test') }}
    </div>
</div>
@endif
<div id="navview" data-role="navview" data-expand-point="md">
    <div class="navview-pane">
        <div class="logo-container">
            <button class="pull-button">
                <span class="mif-menu"></span>
            </button>
           <a href="/" class="d-flex flex-align-center bg-transparent">
                <div class="enlarge-2x text-weight-9">Deming</div>
            </a>
        </div>

        <form id="search-form" action="/global-search" method="GET">
            <div class="suggest-box">
                <input type="text" data-role="input" name="search" value="{{ $search ?? '' }}" id="search" data-clear-button="false" data-search-button="true">
                <button class="holder">
                    <span class="mif-search"></span>
                </button>
            </div>
        </form>

        <ul class="navview-menu pad-second-level" id="side-menu">
            @if (Auth::User()->role <= 3)
            <li class="{{ request()->is('/') ? 'active': '' }}">
                <a href="/">
                    <span class="icon mif-home"></span>
                    <span class="caption">{{ trans("menu.home") }}</span>
                </a>
            </li>
            <li class="{{
                    request()->is('alice*') && !request()->is('alice/import')
                    ? 'active': '' }}">
                <a href="/alice/index">
                    <span class="icon mif-books"></span>
                    <span class="caption">{{ trans("menu.measures") }}</span>
                </a>
            </li>
            @endif
            <li class="{{ request()->is('bob/index') ? 'active': '' }}">
                <a href="/bob/index">
                    <span class="icon mif-paste"></span>
                    <span class="caption">{{ trans("menu.controls") }}</span>
                </a>
            </li>
            @if (Auth::User()->role <= 3)
            <li class="{{ request()->is('bob/history') ? 'active': '' }}">
                <a href="/bob/history">
                    <span class="icon mif-calendar"></span>
                    <span class="caption">{{ trans("menu.planning") }}</span>
                </a>
            </li>
            <li class="{{ request()->is('action*') ? 'active': '' }}">
                <a href="/actions">
                    <span class="icon mif-pending-actions"></span>
                    <span class="caption">{{ trans("menu.action_plan") }}</span>
                </a>
            </li>
            <li class="{{ request()->is('radar/*') ? 'active': '' }}">
                <a id="nav-radar" href="#" class="dropdown-toggle">
                    <span class="icon mif-meter"></span>
                    <span class="caption">{{ trans("menu.radar") }}</span>
                </a>
                <ul class="navview-menu"
                    data-role="collapse"
                    data-collapsed="{{ request()->is('radar/*') ? 'false': 'true' }}">
                    <li class="{{ request()->is('radar/domains') ? 'active': '' }}">
                        <a href="/radar/domains">
                        <span class="icon mif-stacked-bar-chart"></span>
                        <span class="caption">{{ trans("menu.radar_by_domains") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('radar/bob') ? 'active': '' }}">
                        <a href="/radar/bob">
                        <span class="icon mif-timeline"></span>
                        <span class="caption">{{ trans("menu.radar_by_measure") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('radar/alice') ? 'active': '' }}">
                        <a href="/radar/alice">
                        <span class="icon mif-pie-chart"></span>
                        <span class="caption">{{ trans("menu.radar_by_controls") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('radar/attributes') ? 'active': '' }}">
                        <a href="/radar/attributes">
                        <span class="icon mif-pie-chart"></span>
                        <span class="caption">{{ trans("menu.radar_by_attributes") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('radar/actions') ? 'active': '' }}">
                        <a href="/radar/actions">
                        <span class="icon mif-stacked-bar-chart"></span>
                        <span class="caption">{{ trans("menu.radar_by_actions") }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="{{ request()->is('reports*') ? 'active': '' }}">
                <a href="/reports">
                    <span class="icon mif-file-text"></span>
                    <span class="caption">{{ trans("menu.configuration.reports") }}</span>
                </a>
            </li>


            <li class="{{
                (
                    request()->is('attributes*')||
                    request()->is('domains*')||
                    request()->is('users*')||
                    request()->is('group*')||
                    request()->is('alice/import*')||
                    request()->is('doc*')||
                    request()->is('config*')||
                    request()->is('logs*')
                ) ? 'active': '' }}">
                <a href="#" class="dropdown-toggle open">
                    <span class="icon mif-cog"></span>
                    <span class="caption">{{ trans("menu.configuration.title") }}</span>
                </a>
                <ul class="navview-menu"
                    data-role="collapse"
                    data-collapsed="{{
                        (
                            request()->is('attributes*')||
                            request()->is('domains*')||
                            request()->is('users*')||
                            request()->is('group*')||
                            request()->is('alice/import*')||
                            request()->is('doc*')||
                            request()->is('config*')||
                            request()->is('logs*')
                        ) ? 'false': 'true' }}">
                    <li  class="{{ request()->is('attributes*') ? 'active': '' }}">
                        <a href="/attributes">
                            <span class="icon mif-tags"></span>
                            <span class="caption">{{ trans("menu.attributes") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('domains*') ? 'active': '' }}">
                        <a href="/domains">
                            <span class="icon mif-library"></span>
                            <span class="caption">{{ trans("menu.domains") }}</span>
                        </a>
                    </li>
                    @if (Auth::User()->role==1)
                    <li class="{{ request()->is('users*') ? 'active': '' }}">
                        <a href="/users">
                        <span class="icon mif-person"></span>
                        <span class="caption">{{ trans("menu.configuration.users") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('group*') ? 'active': '' }}">
                        <a href="/groups">
                        <span class="icon mif-group"></span>
                        <span class="caption">{{ trans("menu.configuration.groups") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('alice/import*') ? 'active': '' }}">
                        <a href="/alice/import">
                        <span class="icon mif-import"></span>
                        <span class="caption">{{ trans("menu.configuration.import") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('doc*') ? 'active': '' }}">
                        <a href="/doc">
                        <span class="icon mif-file-text"></span>
                        <span class="caption">{{ trans("menu.configuration.documents") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('config*') ? 'active': '' }}">
                        <a href="/config">
                        <span class="icon mif-alarm"></span>
                        <span class="caption">{{ trans("menu.configuration.notifications") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('logs*') ? 'active': '' }}">
                        <a href="/logs">
                        <span class="icon mif-log-file"></span>
                        <span class="caption">Logs</span>
                        </a>
                    </li>
                    @endif
                    </ul>
                </li>
                @endif
                <li>
                    <a class="dropdown-item" href="/logout"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        <span class="icon mif-exit"></span>
                        <span class="caption">{{ trans("menu.logout") }}</span>

                    </a>
                    <form id="logout-form" action="/logout" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        <div class="w-100 text-center text-small data-box p-2 border-top bd-grayMouse" style="position: absolute; bottom: 0">
            Version {{ $appVersion }}
        </div>
    </div>

    <div class="navview-content">
        <div data-role="appbar" class="bg-reserve-steppe border-bottom bd-default" data-expand-point="fs">
            <div class="app-bar-item-static d-none-fs d-flex-md">
                <div class="text-bold enlarge-2" id="content-title">
                @yield('title')
                </div>
            </div>

            <ul class="app-bar-menu ml-auto">

                <a href="/bob/index?attribute=none&period=0&scope=none&domain=0&status=2">
                    <span class="mif-mail-outline mif-2x"></span>
                    @if (Session::get("planed_controls_this_month_count")!=null)
                    <span class="badge bg-green fg-white mt-2 mr-1">{{Session::get("planed_controls_this_month_count")}}</span>
                    @endif
                </a>
                <a href="/bob/index?attribute=none&period=99&scope=none&domain=0&status=1&late=1">
                    <span class="mif-notifications mif-2x"></span>
                    @if (Session::get("late_controls_count")!=null)
                    <span class="badge bg-red fg-white mt-2 mr-1">{{Session::get("late_controls_count")}}</span>
                    @endif
                </a>
                <a href="/actions">
                    <span class="mif-flag mif-2x"></span>
                    @if (Session::get("action_plans_count")!=null)
                    <span class="badge bg-blue fg-white mt-2 mr-1">{{Session::get("action_plans_count")}}</span>
                    @endif
                </a>
                <li>
                    <a href="/users/{{ Auth::User()->id }}/edit">
                        <span class="mif-person mif-2x"></span>
                        <span class="badge bg-black fg-white mt-2 mr-1">{{ Auth::User()->initiales() }}</span>
                    </a>
                </li>
                <li>
                    <a href="/about">
                        <span class="mif-help-outline mif-2x"></span>
                    </a>
                </li>
            </ul>

            <div class="app-bar-item-static">
                <input type="checkbox" data-role="theme-switcher" />
            </div>
        </div>

        <main id="page-content">
@yield('content')
        </main>
    </div>
</div>
</body>
</html>
