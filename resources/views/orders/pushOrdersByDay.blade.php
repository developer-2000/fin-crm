@extends('layouts.app')

@section('title')Дожимы @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/wizard.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/pushOrder.css') }}" />
    <style>
        .value {
            color: #929292;
            font-size: 12px;
            font-weight: 600;
        }
        .value span {
            color: #000000;
        }
        .input_data {
            background-color: #fff6db;
        }
    </style>

    {{--50%/кол-во запросов - кол-во звонков по активному запросу * 50%/кол-во запросов--}}
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js?x=1') }}"></script>
    <script src="{{ URL::asset('js/orders/pushOrder.js') }}"></script>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Все дожимы</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Все дожимы(<span class="badge">1</span>)</h1>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                            <div class="table-responsive">
                                <table id="orders" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">ID<br>дожима</th>
                                        <th class="text-center">ID<br>заказа</th>
                                        <th class="text-center">Данные</th>
                                        <th class="text-center">Вероятность дожима%</th>
                                        <th class="text-center">Запросы</th>
                                        <th class="text-center">Этап</th>
                                        <th class="text-center">Цель</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr >
                                        <td class="text-center">1</td>
                                        <td class="text-center">
                                                <span class="crm_id">
                                                    123
                                                </span>
                                            <div class="project_oid">321</div>
                                        </td>
                                        <td  class="input_data">
                                            <div class="value">
                                                Оффер : <span>Парфюм кз </span>
                                            </div>
                                            <div class="value">
                                                Телефон : <span>12312313</span>
                                            </div>
                                            <div class="value">
                                                Страна : <span>Украина</span>
                                            </div>
                                            <div class="value">
                                                Стоимость : <span>1000</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="proc_label">
                                                100%
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center" style="font-size: 13px;">
                                            <div>
                                                <span class="label label-warning">Отказ 1</span>
                                            </div>
                                            <div>
                                                <span class="label label-success">подтввержденные 2 </span>
                                            </div>
                                            <div>
                                                <span class="label label-default">active 1</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div>
                                                <span class="label label-success">На отделении</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div>
                                                <span class="label label-success">Подтвержден</span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('push-orders-id', 1) }}" class="table-link custom_badge">
                                                        <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x "></i>
                                                            <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                        </span>
                                            </a>
                                        </td>

                                    </tbody>
                                </table>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop