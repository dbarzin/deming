<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Metro 4 -->
    <link rel="stylesheet" href="/vendors/metro4/css/metro-all.min.css">
    <link rel="stylesheet" href="/css/index.css">

    <!-- Dropzone -->
    <!-- https://rawgit.com/enyo/dropzone/master/dist/dropzone.js -->
    <script src="/js/dropzone.js"></script>
    <!-- https://rawgit.com/enyo/dropzone/master/dist/dropzone.css -->    
    <link rel="stylesheet" href="/css/dropzone.css">
    <!--------------->

    <title>Deming - ISMS Controls Made Easy</title>

    <script>
        window.on_page_functions = [];
    </script>
</head>
<body class="m4-cloak h-vh-100">
<div data-role="navview" data-toggle="#paneToggle" data-expand="xl" data-compact="lg" data-active-state="true">
    <div class="navview-pane">
        
        <div class="d-flex flex-align-center">
            <button class="pull-button m-0 bg-chem-hover">
                <span class="mif-menu fg-white"></span>
            </button>
            <a href="/" class="d-block fg-white text-medium no-decor">
                <h2 class="text-medium m-0 fg-white pl-7" style="line-height: 52px">Deming</h2>
            </a>
        </div>
        
        <div class="suggest-box">
            <div class="data-box">                
                @if (Auth::User()->profile_image)
                    <img src="{{asset('/storage/avatar/'.Auth::user()->id)}}" class="avatar">
                @else
                    <img src="/images/user.jpeg" class="avatar">
                @endif
                <div class="ml-4 avatar-title flex-column">
                    <a href="/profile" class="d-block fg-white text-medium no-decor">
                        <span class="reduce-1">{{ Auth::User()->name }}<br>{{ Auth::User()->title }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="suggest-box">            
            <!-- Search engine -->
            <input type="text" data-role="input" data-clear-button="false" data-search-button="true" id="search">
            <button class="holder">
                <span class="mif-search fg-white"></span>
            </button>
            
            <!-- search engine 
                <select class="searchable-field form-control" id="search">
                </select>
            -->
        </div>

        <ul class="navview-menu mt-4" id="side-menu">
            <li> </li>
            <!--
            <li class="item-header">MAIN NAVIGATION</li>
            <li>
                <a href="#dashboard">
                    <span class="icon"><span class="mif-meter"></span></span>
                    <span class="caption">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#widgets">
                    <span class="icon"><span class="mif-widgets"></span></span>
                    <span class="caption">Widgets</span>
                </a>
            </li>
            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-versions"></span></span>
                    <span class="caption">Sample Pages</span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown">
                    <li class="item-header">Pages</li>
                    <li><a href="login.html">
                        <span class="icon"><span class="mif-lock"></span></span>
                        <span class="caption">Login</span>
                    </a></li>
                    <li><a href="register.html">
                        <span class="icon"><span class="mif-user-plus"></span></span>
                        <span class="caption">Register</span>
                    </a></li>
                    <li><a href="lockscreen.html">
                        <span class="icon"><span class="mif-key"></span></span>
                        <span class="caption">Lock screen</span>
                    </a></li>
                    <li><a href="#profile">
                        <span class="icon"><span class="mif-profile"></span></span>
                        <span class="caption">Profile</span>
                    </a></li>
                    <li><a href="preloader.html">
                        <span class="icon"><span class="mif-spinner"></span></span>
                        <span class="caption">Preloader</span>
                    </a></li>
                    <li><a href="404.html">
                        <span class="icon"><span class="mif-cancel"></span></span>
                        <span class="caption">404 Page</span>
                    </a></li>
                    <li><a href="500.html">
                        <span class="icon"><span class="mif-warning"></span></span>
                        <span class="caption">500 Page</span>
                    </a></li>
                    <li><a href="#product-list">
                        <span class="icon"><span class="mif-featured-play-list"></span></span>
                        <span class="caption">Product list</span>
                    </a></li>
                    <li><a href="#product">
                        <span class="icon"><span class="mif-rocket"></span></span>
                        <span class="caption">Product page</span>
                    </a></li>
                    <li><a href="#invoice">
                        <span class="icon"><span class="mif-open-book"></span></span>
                        <span class="caption">Invoice</span>
                    </a></li>
                    <li><a href="#orders">
                        <span class="icon"><span class="mif-table"></span></span>
                        <span class="caption">Orders</span>
                    </a></li>
                    <li><a href="#order-details">
                        <span class="icon"><span class="mif-library"></span></span>
                        <span class="caption">Order details</span>
                    </a></li>
                    <li><a href="#price-table">
                        <span class="icon"><span class="mif-table"></span></span>
                        <span class="caption">Price table</span>
                    </a></li>
                    <li><a href="maintenance.html">
                        <span class="icon"><span class="mif-cogs"></span></span>
                        <span class="caption">Maintenance</span>
                    </a></li>
                    <li><a href="coming-soon.html">
                        <span class="icon"><span class="mif-watch"></span></span>
                        <span class="caption">Coming soon</span>
                    </a></li>
                    <li>
                        <a href="help-center.html">
                            <span class="icon"><span class="mif-help"></span></span>
                            <span class="caption">Help center</span>
                        </a>
                    </li>
                </ul>
            -->


                <li>
                    <a href="/">
                        <span class="icon"><span class="mif-home"></span></span>
                        <span class="caption">Home</span>
                    </a>
                </li>

                <li>
                    <a href="/domains">
                        <span class="icon"><span class="mif-books"></span></span>
                        <span class="caption">Domaines</span>
                    </a>
                </li>

                <li>
                    <a href="/measures">
                        <span class="icon"><span class="mif-event-available"></span></span>
                        <span class="caption">Mesures</span>
                    </a>
                </li>


                <li>
                    <a href="/controls">
                        <span class="icon"><span class="mif-table"></span></span>
                        <span class="caption">Contrôles</span>
                    </a>
                </li>

                <li>
                    <a href="/control/history">
                        <span class="icon"><span class="mif-calendar"></span></span>
                        <span class="caption">Planning</span>
                    </a>
                </li>


                <li>
                    <a href="/control/radar">
                        <span class="icon"><span class="mif-meter"></span></span>
                        <span class="caption">Radar</span>
                    </a>
                </li>
<!--
                <li>
                    <a href="#" class="dropdown-toggle">
                        <span class="icon"><span class="mif-paste"></span></span>
                        <span class="caption">Controles</span>
                    </a>
                    <ul class="navview-menu stay-open" data-role="dropdown" >
                        <li><a href="/control/radar">
                            <span class="icon"><span class="mif-meter"></span></span>
                            <span class="caption">Radar</span>
                        </a></li>
                        <li><a href="/control/history">
                            <span class="icon"><span class="mif-calendar"></span></span>
                            <span class="caption">Planning</span>
                        </a></li>
                        <li><a href="/controls">
                            <span class="icon"><span class="mif-table"></span></span>
                            <span class="caption">Inventaire</span>
                        </a></li>
                    </ul>
                </li>
-->
                <li>
                    <a href="/actions">
                        <span class="icon"><span class="mif-open-book"></span></span>
                        <span class="caption">Plans d'action</span>
                    </a>
                </li>

                <li>
                    <a href="#" class="dropdown-toggle">
                        <span class="icon"><span class="mif-cog"></span></span>
                        <span class="caption">Configuration</span>
                    </a>
                    <ul class="navview-menu stay-open" data-role="dropdown" >
                        @if (Auth::User()->role==1)
                        <li><a href="/users">
                            <span class="icon"><span class="mif-users"></span></span>
                            <span class="caption">Utilisateurs</span>
                        </a></li>
                        @endif
                        <li><a href="/exports">
                            <span class="icon"><span class="mif-download"></span></span>
                            <span class="caption">Rapports</span>
                        </a></li>
                        <li><a href="/doc/stats"> 
                            <span class="icon"><span class="mif-file-text"></span></span>
                            <span class="caption">Documents</span>
                        </a></li>
                        <li><a href="/doc/templates"> 
                            <span class="icon"><span class="mif-file-text"></span></span>
                            <span class="caption">Modèles</span>
                        </a></li>
                    </ul>
                </li>

<!-- ------------------------------------------------------------------------------  
            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-devices"></span></span>
                    <span class="caption">Forms</span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown" >
                    <li class="item-header">Forms</li>
                    <li><a href="#forms-basic">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Basic elements</span>
                    </a></li>
                    <li><a href="#forms-extended">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Extended elements</span>
                    </a></li>
                    <li><a href="#forms-layouts">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Layouts</span>
                    </a></li>
                    <li><a href="#forms-validating">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Validating</span>
                    </a></li>
                </ul>
            </li>
- ------------------------------------------------------------------------------  
            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-table"></span></span>
                    <span class="caption">Tables</span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown" >
                    <li class="item-header">Tables</li>
                    <li><a href="#table-classes">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Table classes</span>
                    </a></li>
                    <li><a href="#table-component">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Table component</span>
                    </a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-air"></span></span>
                    <span class="caption">UI Elements</span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown">
                    <li class="item-header">UI Elements</li>
                    <li>
                        <a href="#colors">
                            <span class="icon"><span class="mif-paint"></span></span>
                            <span class="caption">Colors</span>
                        </a>
                    </li>
                    <li><a href="#typography">
                        <span class="icon"><span class="mif-bold"></span></span>
                        <span class="caption">Typography</span>
                    </a></li>
                    <li><a href="#buttons">
                        <span class="icon"><span class="mif-apps"></span></span>
                        <span class="caption">Buttons</span>
                    </a></li>
                    <li><a href="#tabs">
                        <span class="icon"><span class="mif-open-book"></span></span>
                        <span class="caption">Accordion &amp; Tabs</span>
                    </a></li>
                    <li><a href="#tiles">
                        <span class="icon"><span class="mif-dashboard"></span></span>
                        <span class="caption">Tiles</span>
                    </a></li>
                    <li><a href="#treeview">
                        <span class="icon"><span class="mif-tree"></span></span>
                        <span class="caption">TreeView</span>
                    </a></li>
                    <li><a href="#listview">
                        <span class="icon"><span class="mif-list"></span></span>
                        <span class="caption">ListView</span>
                    </a></li>
                    <li><a href="#progress">
                        <span class="icon"><span class="mif-spinner5"></span></span>
                        <span class="caption">Progress & activities</span>
                    </a></li>
                    <li><a href="#list">
                        <span class="icon"><span class="mif-list2"></span></span>
                        <span class="caption">List component</span>
                    </a></li>
                    <li><a href="#splitter">
                        <span class="icon"><span class="mif-table"></span></span>
                        <span class="caption">Splitter</span>
                    </a></li>
                    <li><a href="#calendar">
                        <span class="icon"><span class="mif-calendar"></span></span>
                        <span class="caption">Calendar</span>
                    </a></li>
                    <li><a href="#countdown">
                        <span class="icon"><span class="mif-watch"></span></span>
                        <span class="caption">Countdown</span>
                    </a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-play"></span></span>
                    <span class="caption">Media</span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown" >
                    <li class="item-header">Media</li>
                    <li><a href="#video">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Video player</span>
                    </a></li>
                    <li><a href="#audio">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Audio</span>
                    </a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-comment"></span></span>
                    <span class="caption">Information</span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown" >
                    <li class="item-header">Information</li>
                    <li><a href="#windows">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Windows</span>
                    </a></li>
                    <li><a href="#dialogs">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Dialogs</span>
                    </a></li>
                    <li><a href="#info-boxes">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">InfoBox</span>
                    </a></li>
                    <li><a href="#hints">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Hints</span>
                    </a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-envelop"></span></span>
                    <span class="caption">Mailbox</span>
                    <span class="badges ml-auto mr-3">
                        <span class="badge inline bg-cyan fg-white">17</span>
                        <span class="badge inline bg-red fg-white">7</span>
                        <span class="badge inline bg-green fg-white">4</span>
                        <span class="badge inline bg-orange fg-white">3</span>
                    </span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown" >
                    <li class="item-header">Mailbox</li>
                    <li>
                        <a href="#inbox">
                            <span class="icon"><span class="mif-mail"></span></span>
                            <span class="caption">Inbox</span>
                        </a>
                    </li>
                    <li>
                        <a href="#inbox2">
                            <span class="icon"><span class="mif-mail"></span></span>
                            <span class="caption">Inbox2</span>
                        </a>
                    </li>
                    <li>
                        <a href="#compose">
                            <span class="icon"><span class="mif-mail-read"></span></span>
                            <span class="caption">Compose</span>
                        </a>
                    </li>
                    <li>
                        <a href="#read-email">
                            <span class="icon"><span class="mif-mail-read"></span></span>
                            <span class="caption">Read email</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="#chat">
                    <span class="icon"><span class="mif-bubbles"></span></span>
                    <span class="caption">Chat</span>
                    <span class="badges ml-auto mr-3">
                        <span class="badge inline bg-red fg-white">7</span>
                        <span class="badge inline bg-green fg-white">4</span>
                        <span class="badge inline bg-orange fg-white">3</span>
                    </span>
                </a>
            </li>

            <li>
                <a href="#" class="dropdown-toggle">
                    <span class="icon"><span class="mif-magic-wand"></span></span>
                    <span class="caption">Wizards</span>
                </a>
                <ul class="navview-menu stay-open" data-role="dropdown" >
                    <li class="item-header">Wizards</li>
                    <li><a href="#master">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Master</span>
                    </a></li>
                    <li><a href="#wizard">
                        <span class="icon"><span class="mif-spinner2"></span></span>
                        <span class="caption">Wizard</span>
                    </a></li>
                </ul>
            </li>
 ---------------------------------------------------------------------   -->
<!--
            <li class="item-header">Documentation</li>
            <li>
                <a href="https://metroui.org.ua/intro.html">
                    <span class="icon"><span class="mif-brightness-auto fg-red"></span></span>
                    <span class="caption">Metro 4</span>
                </a>
            </li>
        </ul>
-->
        <div class="w-100 text-center text-small data-box p-2 border-top bd-grayMouse" style="position: absolute; bottom: 0">
            <!-- <div>&copy; 2020 <a href="mailto:a@b.com" class="text-muted fg-white-hover no-decor">name</a></div>-->
            <div>Created with <a href="https://laravel.com" class="text-muted fg-white-hover no-decor">Laravel</a></div>
        </div>       
    </div>

    <div class="navview-content h-100">
        <div data-role="appbar" class="pos-absolute bg-chem fg-white">

            <a href="#" class="app-bar-item d-block d-none-lg" id="paneToggle"><span class="mif-menu"></span></a>

            <div class="app-bar-container ml-auto">
                <a href="/controls?period=0&domain=0&status=2" class="app-bar-item">
                    <span class="mif-envelop"></span>
                    @if (Session::get("planed_controls_this_month_count")!=null)
                    <span class="badge bg-green fg-white mt-2 mr-1">{{Session::get("planed_controls_this_month_count")}}</span>
                    @endif
                </a>
                <a href="/controls?period=99&domain=0&status=1&late=1" class="app-bar-item">
                    <span class="mif-bell"></span>
                    @if (Session::get("late_controls_count")!=null)                    
                    <span class="badge bg-orange fg-white mt-2 mr-1">{{Session::get("late_controls_count")}}</span>
                    @endif
                </a>
                <a href="/actions" class="app-bar-item">
                    <span class="mif-flag"></span>
                    @if (Session::get("action_plans_count")!=null)
                    <span class="badge bg-red fg-white mt-2 mr-1">{{Session::get("action_plans_count")}}</span>
                    @endif
                </a>
                <div class="app-bar-container">
                    <a href="#" class="app-bar-item">
                        @if (Auth::User()->profile_image)
                            <img src="{{asset('/storage/avatar/'.Auth::user()->id)}}" class="avatar">
                        @else
                            <img src="/images/user.jpeg" class="avatar">
                        @endif
                        <span class="ml-2 app-bar-name">{{ Auth::User()->name }}</span>
                    </a>
                    <div class="user-block shadow-1" data-role="collapse" data-collapsed="true">
                        <div class="bg-chem fg-white p-2 text-center">
                            @if (Auth::User()->profile_image)
                                <img src="{{asset('/storage/avatar/'.Auth::user()->id)}}" class="avatar">
                            @else
                                <img src="/images/user.jpeg" class="avatar">
                            @endif
                            <div class="h4 mb-0">{{ Auth::User()->name }}</div>
                            <div>{{ Auth::User()->title }}</div>
                        </div>
                        <!--
                        <div class="bg-white d-flex flex-justify-between flex-equal-items p-2">
                            <button class="button flat-button">Followers</button>
                            <button class="button flat-button">Sales</button>
                            <button class="button flat-button">Friends</button>
                        </div>
                        -->
                        <form action="">@csrf
                        <div class="bg-white d-flex flex-justify-between flex-equal-items p-2 bg-light">
                            <button class="button mr-1" type="submit" onclick="this.form.action='/profile'">Profile</button>
                            <button class="button ml-1" onclick='this.form.method="POST";this.form.action="/logout"'>Sign out</button>
                        </div>
                        </form>
                    </div>
                </div>
                <a href="#" class="app-bar-item">
                    <span class="mif-cogs"></span>
                </a>
            </div>
        </div>
        <!--
        <div id="content-wrapper" class="content-inner h-100" style="overflow-y: auto">
        </div>
    -->

<!------------------------------------------------------------------------------------------>

<div id="content-wrapper" class="h-100" style="overflow-y: auto">

<div class="row border-bottom bd-lightGray m-2">
    <div class="cell-md-8 d-flex flex-align-center">
        <h3 class="dashboard-section-title  text-center text-left-md w-600">
            Information Security Management System
        </h3>
    </div>

    <div class="cell-md-4 d-flex flex-justify-center flex-justify-end-md flex-align-center">
        <ul class="breadcrumbs bg-transparent">
            <li class="page-item">
                <a href="/" class="page-link"><span class="mif-meter"></span></a>
            </li>
            <li class="page-item"><a href="#" class="page-link">@yield('title')</a></li>
        </ul>
    </div>
</div>

<!------------------------------------------------------------------------------------------>

<div class="p-3">
    <div data-role="panel" data-title-caption="@yield('title')" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">@yield('content')
    </div>
</div>

<!--
<div class="m-3">
<div class="row mt-2">
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-cyan">
            <div class="icon bg-cyan fg-white"><span class="mif-cog"></span></div>
            <div class="content p-4">
                <div class="text-upper">cpu traffic</div>
                <div class="text-upper text-bold text-lead">90%</div>
            </div>
        </div>
    </div>
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-red">
            <div class="icon bg-red fg-white"><span class="mif-google-plus"></span></div>
            <div class="content p-4">
                <div class="text-upper">likes</div>
                <div class="text-upper text-bold text-lead">41,410</div>
            </div>
        </div>
    </div>
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-green">
            <div class="icon bg-green fg-white"><span class="mif-cart"></span></div>
            <div class="content p-4">
                <div class="text-upper">sales</div>
                <div class="text-upper text-bold text-lead">1024</div>
            </div>
        </div>
    </div>
    <div class="cell-lg-3 cell-sm-6 mt-2">
        <div class="icon-box border bd-orange">
            <div class="icon bg-orange fg-white"><span class="mif-users"></span></div>
            <div class="content p-4">
                <div class="text-upper">new members</div>
                <div class="text-upper text-bold text-lead">3,300</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="cell-lg-3 cell-md-6 mt-2">
        <div class="more-info-box bg-cyan fg-white">
            <div class="content">
                <h2 class="text-bold mb-0">150</h2>
                <div>New Orders</div>
            </div>
            <div class="icon">
                <span class="mif-cart"></span>
            </div>
            <a href="#" class="more"> More info <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-3 cell-md-6 mt-2">
        <div class="more-info-box bg-green fg-white">
            <div class="content">
                <h2 class="text-bold mb-0">53%</h2>
                <div>Bounce Rate</div>
            </div>
            <div class="icon">
                <span class="mif-chart-bars"></span>
            </div>
            <a href="#" class="more"> More info <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-3 cell-md-6 mt-2">
        <div class="more-info-box bg-orange fg-white">
            <div class="content">
                <h2 class="text-bold mb-0">44</h2>
                <div>New Registrations</div>
            </div>
            <div class="icon">
                <span class="mif-user-plus"></span>
            </div>
            <a href="#" class="more"> More info <span class="mif-arrow-right"></span></a>
        </div>
    </div>
    <div class="cell-lg-3 cell-md-6 mt-2">
        <div class="more-info-box bg-red fg-white">
            <div class="content">
                <h2 class="text-bold mb-0">10,000</h2>
                <div>Unique Visitors</div>
            </div>
            <div class="icon">
                <span class="mif-user-check"></span>
            </div>
            <a href="#" class="more"> More info <span class="mif-arrow-right"></span></a>
        </div>
    </div>
</div>

<div data-role="panel" data-title-caption="Monthly Recap Report" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>" class="mt-4">
    <div class="row">
        <div class="cell-md-8 p-10">
            <h5 class="text-center">Sales: 1 Jan, 2014 - 30 Jul, 2014</h5>
            <canvas id="dashboardChart1"></canvas>
        </div>
        <div class="cell-md-4 p-10">
            <h5 class="text-center">Goal Completion</h5>
            <div class="mt-6">
                <div class="clear">
                    <div class="place-left">Add Products to Cart</div>
                    <div class="place-right"><strong>160</strong>/200</div>
                </div>
                <div data-role="progress" data-value="35" data-cls-bar="bg-cyan"></div>
            </div>
            <div class="mt-6">
                <div class="clear">
                    <div class="place-left">Complete Purchase</div>
                    <div class="place-right"><strong>310</strong>/400</div>
                </div>
                <div data-role="progress" data-value="35" data-cls-bar="bg-red"></div>
            </div>
            <div class="mt-6">
                <div class="clear">
                    <div class="place-left">Visit Premium Page</div>
                    <div class="place-right"><strong>480</strong>/800</div>
                </div>
                <div data-role="progress" data-value="35"></div>
            </div>
            <div class="mt-6">
                <div class="clear">
                    <div class="place-left">Send Inquiries</div>
                    <div class="place-right"><strong>250</strong>/500</div>
                </div>
                <div data-role="progress" data-value="35" data-cls-bar="bg-orange"></div>
            </div>
            <div class="mt-6">
                <p class="text-small">Cum brodium resistere, omnes spatiies perdere varius, magnum lanistaes.</p>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="cell-lg-3 cell-sm-6 text-center mt-4">
            <div class="fg-green"><span class="mif-arrow-drop-up"></span>17%</div>
            <div class="text-bold">$35,210.43</div>
            <div class="text-upper">TOTAL REVENUE</div>
        </div>
        <div class="cell-lg-3 cell-sm-6 text-center mt-4">
            <div class="fg-orange"><span class="">=</span>0</div>
            <div class="text-bold">$10,390.90</div>
            <div class="text-upper">TOTAL COST</div>
        </div>
        <div class="cell-lg-3 cell-sm-6 text-center mt-4">
            <div class="fg-green"><span class="mif-arrow-drop-up"></span>20%</div>
            <div class="text-bold">$24,813.53</div>
            <div class="text-upper">TOTAL PROFIT</div>
        </div>
        <div class="cell-lg-3 cell-sm-6 text-center mt-4">
            <div class="fg-red"><span class="mif-arrow-drop-down"></span>18%</div>
            <div class="text-bold">1,200</div>
            <div class="text-upper">GOAL COMPLETIONS</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="cell-md-7">
        <div data-role="panel" data-title-caption="Staff salary" data-collapsible="true" data-title-icon="<span class='mif-table'></span>" class="mt-4">
            <div class="p-4">
                <table class="table striped table-border mt-4"
                       data-role="table"
                       data-cls-table-top="row"
                       data-cls-search="cell-md-6"
                       data-cls-rows-count="cell-md-6"
                       data-rows="5"
                       data-rows-steps="5, 10"
                       data-show-activity="false"
                       data-source="data/table.json"
                       data-horizontal-scroll="true"
                >
                </table>
            </div>
        </div>
    </div>

    <div class="cell-md-5">
        <div data-role="panel" data-title-caption="New members" data-collapsible="true" data-title-icon="<span class='mif-users'></span>" class="mt-4">
            <ul class="user-list">
                <li>
                    <img src="images/user1-128x128.jpg" class="avatar">
                    <div class="text-ellipsis">Sergey</div>
                    <div class="text-small text-muted">Today</div>
                </li>
                <li>
                    <img src="images/user2-160x160.jpg" class="avatar">
                    <div class="text-ellipsis">Alex</div>
                    <div class="text-small text-muted">Yesterday</div>
                </li>
                <li>
                    <img src="images/user3-128x128.jpg" class="avatar">
                    <div class="text-ellipsis">Norma</div>
                    <div class="text-small text-muted">Yesterday</div>
                </li>
                <li>
                    <img src="images/user4-128x128.jpg" class="avatar">
                    <div class="text-ellipsis">Katty</div>
                    <div class="text-small text-muted">11 Jan</div>
                </li>
                <li>
                    <img src="images/user5-128x128.jpg" class="avatar">
                    <div class="text-ellipsis">Julia</div>
                    <div class="text-small text-muted">11 Jan</div>
                </li>
                <li>
                    <img src="images/user6-128x128.jpg" class="avatar">
                    <div class="text-ellipsis">Mark</div>
                    <div class="text-small text-muted">11 Jan</div>
                </li>
                <li>
                    <img src="images/user7-128x128.jpg" class="avatar">
                    <div class="text-ellipsis">Marta</div>
                    <div class="text-small text-muted">11 Jan</div>
                </li>
                <li>
                    <img src="images/user8-128x128.jpg" class="avatar">
                    <div class="text-ellipsis">Ustas</div>
                    <div class="text-small text-muted">11 Jan</div>
                </li>
            </ul>
            <div class="p-2 border-top bd-default text-center">
                <a href="#">View all users</a>
            </div>
        </div>
    </div>
</div>
!-->
</div>

</div>

<!------------------------------------------------------------------------------------------>

    </div>
</div>

<!-- jQuery first, then Metro UI JS -->
<script src="/vendors/jquery/jquery-3.4.1.min.js"></script>
<script src="/vendors/chartjs/Chart.bundle.min.js"></script>
<script src="/vendors/qrcode/qrcode.min.js"></script>
<script src="/vendors/jsbarcode/JsBarcode.all.min.js"></script>
<script src="/vendors/ckeditor/ckeditor.js"></script>
<script src="/vendors/metro4/js/metro.min.js"></script>
<script src="/js/index.js"></script>

<!-- search engine 
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>

    <script>
        $(document).ready(function() {
    $('#search').select2({
        minimumInputLength: 3,
        ajax: {
            url: '{{ route("globalSearch") }}',
            dataType: 'json',
            type: 'GET',
            delay: 200,
            data: function (term) {
                return {
                    search: term
                };
            },
            results: function (data) {
                return {
                    data
                };
            }
        },
        escapeMarkup: function (markup) { return markup; },
        templateResult: formatItem,
        templateSelection: formatItemSelection,
        placeholder : '{{ trans('global.search') }}...',
        language: {
            inputTooShort: function(args) {
                var remainingChars = args.minimum - args.input.length;
                var translation = '{{ trans('global.search_input_too_short') }}';

                return translation.replace(':count', remainingChars);
            },
            errorLoading: function() {
                return '{{ trans('global.results_could_not_be_loaded') }}';
            },
            searching: function() {
                return '{{ trans('global.searching') }}';
            },
            noResults: function() {
                return '{{ trans('global.no_results') }}';
            },
        }

    });
    function formatItem (item) {
        if (item.loading) {
            return '{{ trans('global.searching') }}...';
        }
        var markup = "<div class='searchable-link' href='" + item.url + "'>";
        markup += "<div class='searchable-title'>" + item.model + "</div>";
        $.each(item.fields, function(key, field) {
            markup += "<div class='searchable-fields'>" + item.fields_formated[field] + " : " + item[field] + "</div>";
        });
        markup += "</div>";

        return markup;
    }

    function formatItemSelection (item) {
        if (!item.model) {
            return '{{ trans('global.search') }}...';
        }
        return item.model;
    }
    $(document).delegate('.searchable-link', 'click', function() {
        var url = $(this).attr('href');
        window.location = url;
    });
});

    </script>
-->


</body>
</html>