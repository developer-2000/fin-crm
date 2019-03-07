<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title> @lang('general.enter-crm')</title>
{{-- Вход в CRM систему --}}
    <link type="image/x-icon" href="{{ URL::asset('favicon.png')}}" rel="shortcut icon"/>

    <!-- CSS Start -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/main.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/theme_styles.css')}}"/>
    <link type="image/x-icon" href="{{ URL::asset('favicon.png')}}" rel="shortcut icon"/>
    <!-- CSS End -->

    <!-- JS Start -->
    <script src="{{ URL::asset('js/vendor/jquery.js')}}"></script>
    <script src="{{ URL::asset('js/users/authorization.js')}}"></script>
    <!-- JS End -->

    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>
<body id="login-page">
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div id="login-box">
                <div id="login-box-holder">
                    <div class="row">
                        <div class="col-xs-12">
                            <header id="login-header">
                                <div id="login-logo">
                                    @lang('general.crm')
                                </div>
                            </header>
                            <div id="login-box-inner">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input id="login" type="login"
                                               class="form-control{{ $errors->has('login') ? ' is-invalid' : '' }}"
                                               name="login" value="{{ old('login') }}" required autofocus>

                                    </div>
                                    @if ($errors->has('login'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('login') }}</strong>
                                    </span>
                                    @endif
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                        <input id="password" type="password"
                                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                               name="password" required>
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                    @if(Session::has('sessions'))
                                        <div class=" alert-block alert-danger">
                                            <h4> @lang('general.session-not-end')</h4>
                                            {{-- Не законченная сессия! --}}
                                            <p> @lang('general.close-old-session')</p>
                                            {{-- Закрыть старую и начать новую? --}}
                                            <a class="label label-success" id="new_session" href="#"> @lang('general.yes')</a>
                                            <a class="label label-default" href="{{route('login')}}"> @lang('general.not')</a>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <button type="submit" class="btn btn-success col-xs-12 authorization">
                                                @lang('general.enter')
                                            </button>
                                        </div>
                                    </div>
                                    @if(Session::has('message'))
                                        <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('message') }}</p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
