@extends('layouts.app')
@section('title')Редактирование интеграции  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel=" stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <style>
        body {
            color: grey;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('integrations')}}"><span>Все интеграции</span></a></li>
                <li class="active"><span>Редактировать интеграцию</span></li>
            </ol>
            {{--<div class="clearfix">--}}
                {{--<h1 class="pull-left">Редактирование</h1>--}}
                {{--@if (isset($permissions['integrations_keys_create']))--}}
                    {{--<div class="pull-right top-page-ui">--}}
                        {{--<button data-modal="form_block"--}}
                                {{--class=" md-trigger btn btn-primary pull-right mrg-b-lg create-key">--}}
                            {{--<i class="fa fa-plus-circle fa-lg"></i> Добавить ключ--}}
                        {{--</button>--}}
                    {{--</div>--}}
                {{--@endif--}}
            {{--</div>--}}
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="">
                            <a href="{{route('integrations-edit' , Request::segment(2))}}">Все ключи</a>
                        </li>
                        <li class="">
                            <a href="{{route('novaposhta-senders',  Request::segment(2))}}">Отправители</a>
                        </li>

                        <li class="active">
                            <a href="{{route('integration-codes-statuses',  Request::segment(2))}}">Коды/Статусы</a>
                        </li>
                    </ul>
                    @include('integrations.codes-statuses', ['codesStatuses', $codesStatuses])
                </div>
            </div>
        </div>
    </div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
@stop


