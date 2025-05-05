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
</head>
<body class="cloak">
<div id="navview" data-role="navview" data-expand-point="md">
    <div class="navview-pane">
        <div class="logo-container">
            <button class="pull-button">
                <span class="mif-menu"></span>
            </button>
           <a href="/" class="d-flex flex-align-center bg-transparent">
                <div class="enlarge-4 ml-2 text-weight-9">Deming</div>
            </a>
        </div>

        <div class="suggest-box">
            <input type="text" data-role="input" data-clear-button="false" data-search-button="true">
            <button class="holder">
                <span class="mif-search"></span>
            </button>
        </div>

        <ul class="navview-menu pad-second-level" id="side-menu">
            <li class="{{ request()->is('/') ? 'active': '' }}">
                <a href="/">
                    <span class="icon mif-home"></span>
                    <span class="caption">{{ trans("menu.home") }}</span>
                </a>
            </li>
            <li class="{{ request()->is('alice*') ? 'active': '' }}">
                <a href="/alice/index">
                    <span class="icon mif-books"></span>
                    <span class="caption">{{ trans("menu.measures") }}</span>
                </a>
            </li>
            <li class="{{ request()->is('bob/index') ? 'active': '' }}">
                <a href="/bob/index">
                    <span class="icon mif-paste"></span>
                    <span class="caption">{{ trans("menu.controls") }}</span>
                </a>
            </li>
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
            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon mif-meter"></span>
                    <span class="caption">{{ trans("menu.radar") }}</span>
                </a>
                <ul class="navview-menu " data-role="collapse" data-collapsed="true">
                    <li>
                        <a href="/radar/domains">
                        <span class="icon mif-stacked-bar-chart"></span>
                        <span class="caption">{{ trans("menu.radar_by_domains") }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="/radar/bob">
                        <span class="caption">{{ trans("menu.radar_by_measure") }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="/radar/alice">
                        <span class="caption">{{ trans("menu.radar_by_controls") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('radar/attributes') ? 'bg-gray': '' }}">
                        <a href="/radar/attributes">
                        <span class="caption">{{ trans("menu.radar_by_attributes") }}</span>
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


            <li>
                <a href="#" class="dropdown-toggle open">
                    <span class="icon mif-cog"></span>
                    <span class="caption">{{ trans("menu.configuration.title") }}</span>
                </a>
                <ul class="navview-menu " data-role="collapse" data-collapsed="true">
                    <li  class="{{ request()->is('attributes*') ? 'active': '' }}">
                        <a href="/attributes">
                            <span class="icon mif-tags"></span>
                            <span class="caption">{{ trans("menu.attributes") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('domains*') ? 'bg-gray': '' }}">
                        <a href="/domains">
                            <span class="icon mif-books"></span>
                            <span class="caption">{{ trans("menu.domains") }}</span>
                        </a>
                    </li>
                    @if (Auth::User()->role==1)
                    <li class="{{ request()->is('users*') ? 'bg-gray': '' }}">
                        <a href="/users">
                        <span class="icon mif-person"></span>
                        <span class="caption">{{ trans("menu.configuration.users") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('group*') ? 'bg-gray': '' }}">
                        <a href="/groups">
                        <span class="icon mif-group"></span>
                        <span class="caption">{{ trans("menu.configuration.groups") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('alice/import*') ? 'bg-gray': '' }}">
                        <a href="/alice/import">
                        <span class="icon mif-import"></span>
                        <span class="caption">{{ trans("menu.configuration.import") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('doc*') ? 'bg-gray': '' }}">
                        <a href="/doc">
                        <span class="icon mif-file-text"></span>
                        <span class="caption">{{ trans("menu.configuration.documents") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('config*') ? 'bg-gray': '' }}">
                        <a href="/config">
                        <span class="icon mif-alarm"></span>
                        <span class="caption">{{ trans("menu.configuration.notifications") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('logs*') ? 'bg-gray': '' }}">
                        <a href="/logs">
                        <span class="icon mif-log-file"></span>
                        <span class="caption">Logs</span>
                        </a>
                    </li>
                    @endif
                    </ul>
                </li>
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
    </div>
        <div class="w-100 text-center text-small data-box p-2 border-top bd-grayMouse" style="position: absolute; bottom: 0">
            <div>Version 2025-02-R1</div>
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
