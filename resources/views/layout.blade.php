<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Metro 4 -->
    <link rel="stylesheet" href="/vendors/metro4/css/metro-all.min.css">
    <link rel="stylesheet" href="/css/index.css">

    <!-- Calendar -->
    <link rel="stylesheet" href="/css/calendar.css">

    <!-- Dropzone -->
    <!-- https://rawgit.com/enyo/dropzone/master/dist/dropzone.js -->
    <script src="/js/dropzone.js"></script>
    <!-- https://rawgit.com/enyo/dropzone/master/dist/dropzone.css -->    
    <link rel="stylesheet" href="/css/dropzone.css">

    <title>Deming - ISMS Controls Made Easy</title>

    <script>
        window.on_page_functions = [];
    </script>
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
            <form id="search-form" action="{{ route("globalSearch") }}" method="GET">
                <input type="text" data-role="input" data-clear-button="false" data-search-button="true" id="search">
                <button class="holder">
                    <span class="mif-search fg-white"></span>
                </button>
            </form>            
        </div>

        <ul class="navview-menu mt-4" id="side-menu">
                <li>
                    <a href="/">
                        <span class="icon"><span class="mif-home"></span></span>
                        <span class="caption">Home</span>
                    </a>
                </li>

                <li>
                    <a href="/domains">
                        <span class="icon"><span class="mif-library"></span></span>
                        <span class="caption">Domaines</span>
                    </a>
                </li>

                <li>
                    <a href="/measures">
                        <span class="icon"><span class="mif-books"></span></span>
                        <span class="caption">Mesures</span>
                    </a>
                </li>


                <li>
                    <a href="/controls">
                        <span class="icon"><span class="mif-paste"></span></span>
                        <span class="caption">Contrôles</span>
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

                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        <span class="icon"><span class="mif-switch"></span></span>
                        <span class="caption">Quitter</span>

                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    </a>
                </li>
            </ul>
        <div class="w-100 text-center text-small data-box p-2 border-top bd-grayMouse" style="position: absolute; bottom: 0">
            <div>Github <a href="https://github.com/dbarzin/deming" class="no-decor">dbarzin/deming</a></div>
            <div>Created with <a href="https://laravel.com" class="no-decor">Laravel</a></div>
        </div>
    </div>

    <div class="navview-content h-100">
        <div data-role="appbar" class="pos-absolute bg-chem fg-black">

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
                <a href="#" class="app-bar-item">
                    <span class="mif-cogs"></span>
                </a>
            </div>
        </div>
    <div id="content-wrapper" class="h-100" style="overflow-y: auto">
    @yield('content')
    </div>
</div>
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

<!-- search engine  -->
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
        placeholder : '',
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
            return '';
        }
        return item.model;
    }
    $(document).delegate('.searchable-link', 'click', function() {
        var url = $(this).attr('href');
        window.location = url;
    });
});

    </script>

</body>
</html>
