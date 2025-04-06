<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Deming - ISMS Controls Made Easy</title>

    <link rel="stylesheet" href="/css/all.css" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <script src="/js/all.js"></script>
    <script src="/js/easymde.min.js"></script>
    @yield('style')

</head>

<body class="m4-cloak h-vh-100">
<div data-role="navview" data-toggle="#paneToggle" data-expand="xl" data-compact="lg" data-active-state="true">
    <div class="navview-pane">
        <div class="d-flex flex-align-center">
            <button class="pull-button m-0 ">
                <span class="mif-menu fg-black"></span>
            </button>
            <a href="/" class="d-block fg-black text-medium no-decor">
                <h2 class="text-medium m-0 fg-black pl-7" style="line-height: 52px">Deming</h2>
            </a>
        </div>
        <div class="suggest-box">
            <form id="search-form" action="/global-search" method="GET">
                <input type="text" data-role="input" name="search" value="{{ $search ?? '' }}" id="search" data-clear-button="false" data-search-button="true">
                <button class="holder">
                    <span class="mif-search fg-white"></span>
                </button>
            </form>
        </div>

        <ul class="navview-menu mt-4" id="side-menu">
            @if ((Auth::User()->role === 1)||(Auth::User()->role === 2)||(Auth::User()->role === 3))
                <li>
                    <a href="/">
                        <span class="icon"><span class="mif-home"></span></span>
                        <span class="caption">{{ trans("menu.home") }}</span>
                    </a>
                </li>

                <li class="{{
                        request()->is('alice/index') ||
                        request()->is('alice/show/*') ||
                        request()->is('alice/*/edit') ||
                        request()->is('alice/plan/*')
                        ? 'bg-gray': '' }}">
                    <a href="/alice/index">
                        <span class="icon"><span class="mif-books"></span></span>
                        <span class="caption">{{ trans("menu.measures") }}</span>
                    </a>
                </li>


                <li class="{{
                    request()->is('bob/index') ||
                    request()->is('bob/show/*') ||
                    request()->is('bob/edit/*') ||
                    request()->is('bob/plan/*') ||
                    request()->is('bob/make/*')
                    ? 'bg-gray': '' }}">
                    <a href="/bob/index">
                        <span class="icon"><span class="mif-paste"></span></span>
                        <span class="caption">{{ trans("menu.controls") }}</span>
                        <span class="badges ml-auto mr-3">
                        <!-- TODO
                            <span class="badge inline bg-cyan fg-white">17</span>
                            <span class="badge inline bg-green fg-white">4</span>
                            <span class="badge inline bg-orange fg-white">3</span>
                            <span class="badge inline bg-red fg-white">7</span>
                        -->
                        </span>
                    </a>
                </li>

                <li class="{{ request()->is('bob/history*') ? 'bg-gray': '' }}">
                    <a href="/bob/history">
                        <span class="icon"><span class="mif-calendar"></span></span>
                        <span class="caption">{{ trans("menu.planning") }}</span>
                    </a>
                </li>

                <li class="{{ request()->is('action*') ? 'bg-gray': '' }}">
                    <a href="/actions">
                        <span class="icon"><span class="mif-open-book"></span></span>
                        <span class="caption">{{ trans("menu.action_plan") }}</span>
                    </a>
                </li>

                <li>
                    <a href="#" class="dropdown-toggle">
                        <span class="icon"><span class="mif-meter"></span></span>
                        <span class="caption">{{ trans("menu.radar") }}</span>
                    </a>
                    <ul class="navview-menu stay-open {{ request()->is('radar*') ? 'open' : '' }}" data-role="dropdown" >
                        <li class="{{ request()->is('radar/domains') ? 'bg-gray': '' }}">
                            <a href="/radar/domains">
                            <span class="caption">{{ trans("menu.radar_by_domains") }}</span>
                            </a>
                        <li class="{{ request()->is('radar/temp') ? 'bg-gray': '' }}">
                            <a href="/radar/bob">
                            <span class="caption">{{ trans("menu.radar_by_measure") }}</span>
                        </a></li>
                        </li>
                        <li class="{{ request()->is('radar/alice') ? 'bg-gray': '' }}">
                            <a href="/radar/alice">
                            <span class="caption">{{ trans("menu.radar_by_controls") }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('radar/attributes') ? 'bg-gray': '' }}">
                            <a href="/radar/attributes">
                            <span class="caption">{{ trans("menu.radar_by_attributes") }}</span>
                        </a></li>
                    </ul>
                </li>

                <li class="{{ request()->is('reports') ? 'bg-gray': '' }}">
                    <a href="/reports">
                        <span class="icon"><span class="mif-file-text"></span></span>
                        <span class="caption">{{ trans("menu.configuration.reports") }}</span>
                    </a>
                </li>

                <li>
                    <a href="#" class="dropdown-toggle">
                        <span class="icon"><span class="mif-cog"></span></span>
                        <span class="caption">{{ trans("menu.configuration.title") }}</span>
                    </a>
                    <ul class="navview-menu stay-open
                        {{  (
                            request()->is('attributes*') ||
                            request()->is('domains*') ||
                            request()->is('users*') ||
                            request()->is('alice/import*') ||
                            request()->is('doc*') ||
                            request()->is('logs*') ||
                            request()->is('config*')
                            )
                            ? 'open' : '' }}" data-role="dropdown" >
                    <li class="{{ request()->is('attributes*') ? 'bg-gray': '' }}">
                        <a href="/attributes">
                            <span class="icon"><span class="mif-tags"></span></span>
                            <span class="caption">{{ trans("menu.attributes") }}</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('domains*') ? 'bg-gray': '' }}">
                        <a href="/domains">
                            <span class="icon"><span class="mif-books"></span></span>
                            <span class="caption">{{ trans("menu.domains") }}</span>
                        </a>
                    </li>
                    @if (Auth::User()->role==1)
                    <li class="{{ request()->is('users*') ? 'bg-gray': '' }}">
                        <a href="/users">
                        <span class="icon"><span class="mif-users"></span></span>
                        <span class="caption">{{ trans("menu.configuration.users") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('alice/import*') ? 'bg-gray': '' }}">
                        <a href="/alice/import">
                        <span class="icon"><span class="mif-file-excel"></span></span>
                        <span class="caption">{{ trans("menu.configuration.import") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('doc*') ? 'bg-gray': '' }}">
                        <a href="/doc">
                        <span class="icon"><span class="mif-file-text"></span></span>
                        <span class="caption">{{ trans("menu.configuration.documents") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('config*') ? 'bg-gray': '' }}">
                        <a href="/config">
                        <span class="icon"><span class="mif-alarm"></span></span>
                        <span class="caption">{{ trans("menu.configuration.notifications") }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('logs*') ? 'bg-gray': '' }}">
                        <a href="/logs">
                        <span class="icon"><span class="mif-log-file"></span></span>
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
                        <span class="icon"><span class="mif-switch"></span></span>
                        <span class="caption">{{ trans("menu.logout") }}</span>

                    </a>
                    <form id="logout-form" action="/logout" method="POST" style="display: none;">
                        @csrf
                    </form>
                    </a>
                </li>
            </ul>
        <div class="w-100 text-center text-small data-box p-2 border-top bd-grayMouse" style="position: absolute; bottom: 0">
            <div>Github <a href="https://github.com/dbarzin/deming" class="no-decor">dbarzin/deming</a></div>
            <div>Version 2025-02-R1</div>
        </div>
    </div>

    <div class="navview-content h-100">
        <div data-role="appbar" class="pos-absolute bg-chem fg-black">
            @if ((Auth::User()->role === 1)||(Auth::User()->role === 2)||(Auth::User()->role === 3)||(Auth::User()->role === 5))

            <a href="#" class="app-bar-item d-block d-none-lg" id="paneToggle"><span class="mif-menu"></span></a>

            <div class="app-bar-container ml-auto">
                <a href="/bob/index?attribute=none&period=0&scope=none&domain=0&status=2" class="app-bar-item">
                    <span class="mif-envelop"></span>
                    @if (Session::get("planed_controls_this_month_count")!=null)
                    <span class="badge bg-green fg-white mt-2 mr-1">{{Session::get("planed_controls_this_month_count")}}</span>
                    @endif
                </a>
                <a href="/bob/index?attribute=none&period=99&scope=none&domain=0&status=1&late=1" class="app-bar-item">
                    <span class="mif-bell"></span>
                    @if (Session::get("late_controls_count")!=null)
                    <span class="badge bg-red fg-white mt-2 mr-1">{{Session::get("late_controls_count")}}</span>
                    @endif
                </a>
                <a href="/actions" class="app-bar-item">
                    <span class="mif-flag"></span>
                    @if (Session::get("action_plans_count")!=null)
                    <span class="badge bg-blue fg-white mt-2 mr-1">{{Session::get("action_plans_count")}}</span>
                    @endif
                </a>
                <a href="/users/{{ Auth::User()->id }}/edit" class="app-bar-item">
                    <span class="mif-cogs"></span>
                </a>
                <a href="/about" class="app-bar-item">
                    <span class="mif-help2"></span>
                </a>
            </div>
            @endif
        </div>
        <div id="content-wrapper" class="h-100" style="overflow-y: auto">
        @yield('content')
        </div>
    </div>
</div>

</body>
</html>
