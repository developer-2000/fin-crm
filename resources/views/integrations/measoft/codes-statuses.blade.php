@extends('layouts.app')
@section('title')Редактирование интеграции  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel=" stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <style>
        body {
            color: grey;
        }

        .ns-box {
            z-index: 5000
        }
    </style>
@stop
@section('content')
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('integrations')}}"><span>Все интеграции</span></a></li>
                <li class="active"><span>Редактировать интеграцию</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Редактирование</h1>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li >
                            <a href="{{route('integrations-edit', Request::segment(2))}}">Отправители</a>
                        </li>
                        <li class="active">
                            <a href="{{route('integration-codes-statuses', Request::segment(2))}}">Коды/Статусы</a>
                        </li>
                    </ul>
                    @include('integrations.codes-statuses', ['codesStatuses', $codesStatuses])
                </div>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.datetimepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/integrations/viettel/index.js') }}"></script>

@stop


