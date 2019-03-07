<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CRM | @yield('title')</title>

    <link type="image/x-icon" href="{{ URL::asset('favicon.png')}}" rel="shortcut icon"/>

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/theme_styles.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nanoscroller.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/select2.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/main.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/loader.css') }}"/>

    @yield('css')
    @yield('jsTop')

    <script src="{{ URL::asset('js/vendor/jquery.js')}}"></script>
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
</head>
<body class="theme-navyBlue fixed-header">
<div id="theme-wrapper">
    <header class="navbar" id="header-navbar">
        <div class="container">

            <a href="{{ route('index') }}" id="logo" class="navbar-brand {!! isset($_COOKIE['navHidden']) ? 'nav-small' : '' !!}">
                <img src="{{URL::asset('img/logo-small.png')}}" alt="" class="normal-logo logo-white"/>
            </a>
            <div class="clearfix">
                <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="fa fa-bars"></span>
                </button>
                <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
                    <ul class="nav navbar-nav pull-left">
                        <li>
                            <a class="btn" id="make-small-nav">
                                <i class="fa fa-bars"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-no-collapse pull-right" id="header-nav">
                    <ul class="nav navbar-nav pull-right">
                        <li class="dropdown language hidden-xs" id="main_languages">
                            @if (count($languages))
                                <a class="btn dropdown-toggle" data-toggle="dropdown">
                                    <img class="country-flag flag-selected"
                                         src="{{ URL::asset('img/flags/' . mb_strtoupper(auth()->user()->language) . '.png') }}"/>
                                    {{$languages[auth()->user()->language] ?? auth()->user()->language}}
                                    <i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach($languages as $code => $language)
                                        @if (auth()->user()->language != $code)
                                        <li class="item item-language" data-content="{{$code}}">
                                            <img src="{{URL::asset('img/flags/' . mb_strtoupper($code) . '.png')}}" class="country-flag item-icon-language">
                                            <a href="#">
                                                {{$language }}
                                            </a>
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                        <li class="dropdown profile-dropdown">
                            <a href="#" class="dropdown-toggle" id="user_id" data-toggle="dropdown"
                               data-id="{{auth()->user()->id}}">
                                <img src="{{ asset(auth()->user()->photo) }}" alt=""/>
                                <span class="hidden-xs">
                                    {{ auth()->user()->name }} {{(auth()->user()->sub_project_id > 0 && isset(auth()->user()->subProject)) ? '(' . auth()->user()->subProject->name . ')' : '' }}</span>
                                <b class="fa fa-caret-down"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{route('user', auth()->user()->id)}}"><i class="fa fa-user"></i> @lang('general.cabinet')</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div id="page-wrapper" class="container {!! isset($_COOKIE['navHidden']) ? 'nav-small' : '' !!}">
        <div class="row">
            <div id="nav-col">
                <section id="col-left" class="col-left-nano" >
                    <div id="col-left-inner" class="col-left-nano-content">
                        <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                            {!! $menu->render() !!}
                        </div>
                    </div>
                </section>
                <!--left buttons-->
                <div class="block_up">
                    <div class="wrapper_up">
                        <div class="button">
                            <i class="fa fa-angle-up"></i>
                        </div>
                    </div>
                </div>
                <div class="block_down">
                    <div class="wrapper_down">
                        <div class="button">
                            <i class="fa fa-angle-down"></i>
                        </div>
                    </div>
                </div>
                <!---->
                <div id="nav-col-submenu"></div>
            </div>
            <div id="content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('content')
                    </div>
                </div>
                <footer id="footer-bar" class="row">
                    <p id="footer-copyright" class="col-xs-12">
                        CRM
                    </p>
                </footer>
            </div>
        </div>
    </div>
    <!--right buttons-->
    <div class="block_buttons">
        <div class="button btn_up">
            <i class="fa fa-angle-up"></i>
        </div>
        <div class="button btn_down">
            <i class="fa fa-angle-down"></i>
        </div>
    </div>
    <!---->
    <!--messages for alerts-->
    <div class="hidden" id="messages_alert">
        <div id="processing">
            @lang('general.processing')
        </div>
        <div id="error">
            @lang('alerts.server-error')
        </div>
        <div id="field_required">
            @lang('alerts.field-required')
        </div>
    </div>
    <!---->
</div>
<script src="{{ URL::asset('js/vendor/bootstrap.js')}}"></script>
<script src="{{ URL::asset('js/vendor/jquery.nanoscroller.min.js')}}"></script>
<script src="{{ URL::asset('js/vendor/scripts.js')}}"></script>
<script src="{{ URL::asset('js/vendor/pace.min.js')}}"></script>
<script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
<script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
<script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
@yield('jsBottom')
<script src="{{ URL::asset('js/main.js') }}"></script>
{{-- <script type="text/javascript" src="https://uguide.ru/js/script/snowcursor.min.js"></script> script for NewYear --}}
{{-- <script src="https://uguide.ru/js/script/ok4.js" type="text/javascript"></script> script for NewYear  --}}
</body>
</html>
