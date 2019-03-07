@extends('layouts.app')
@section('title')Редактирование интеграции  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel=" stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        body {
            color: grey;
        }

        .md-show {
            height: 100%;
            overflow-y: auto;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span></span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Редактирование</h1>
                @if (isset($permissions['ninjaxpress_generate_token']))
                    <div class="pull-right top-page-ui"  style="margin-left: 10px">
                        <input type="hidden" id="ninjaxpress_key_id" name="ninjaxpress_key_id"
                               value="{{!empty($keyData->id) ? $keyData->id : ''}}">
                        <button class="btn btn-primary generate-access-token">
                            Сгенерировать токен
                        </button>
                    </div>
                @endif
                @if (isset($permissions['ninjaxpress_generate_hmac']))
                    <div class="pull-right top-page-ui">
                        <button class="btn btn-primary generate-hmac">
                            Сгенерировать hmac
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#block_senders">Отправители</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped ninjaxpress_key_data">
                                                <thead>
                                                <tr>
                                                    <th>Client ID</th>
                                                    <th>Client Secret</th>
                                                    <th>Access Token</th>
                                                    <th>Expires</th>
                                                    <th>Token type</th>
                                                    <th>Expires In</th>
                                                    <th>Создан</th>
                                                    <th>Обновлен</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if ($keyData)
                                                    @include('integrations.ninjaxpress.edit-key-table')
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/integrations/ninjaxpress/index.js') }}"></script>
@stop


