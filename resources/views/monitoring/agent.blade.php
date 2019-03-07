@extends('x_layout/layout')

@section('title')Мониторинг@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/theme_styles.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/select2.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/main.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/demo-skin-changer.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nanoscroller.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/scripts.js') }}"></script>
    <script src="{{ URL::asset('js/monitoring/monitoring_agent.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span>Мониторинг</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row"  style="opacity: 1;">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li><a onclick="location.href='{{ route('monitoring-audit') }}/'" href="#audit" data-toggle="tab" aria-expanded="false">Статистика по операторам</a></li>
                        <li class="active"><a href="#agents" data-toggle="tab" aria-expanded="true">Агенты</a></li>
                        <li><a onclick="location.href='{{ route('monitoring-companies') }}/'" href="#tab-newsfeed" data-toggle="tab" aria-expanded="false">Компании</a></li>
                    </ul>
                    <div class='tab-content'>  
                        <div class="tab-pane fade active in" id="agents">
                            @if ($operators)
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>ФИО</th>
                                            <th>Статус</th>
                                            <th class="text-center">Всего вызовов</th>
                                            <th class="text-center">Время в системе</th>
                                            <th class="text-center">Время разговоров</th>
                                            <th class="text-center">Перерыв<br />Пауза</th>
                                            <th class="text-center">Перерыв<br />Оформление</th>
                                            <th class="text-center">Перерыв<br />Всего</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($operators as $operator) 
                                            <tr class="operator_tr" id="{{ $operator->id }}" style="display: none">
                                                <td>{{ $operator->id }}</td>
                                                <td>{{ $operator->name }} {{ $operator->surname }}</td>
                                                <td class="text-left" id="status-{{ $operator->id }}"></td>
                                                <td class="text-center" id="calls-{{ $operator->id }}"></td>
                                                <td class="text-center" id="time-login-{{ $operator->id }}"></td>
                                                <td class="text-center" id="time-talk-{{ $operator->id }}"></td>
                                                <td class="text-center" id="break-pause-{{ $operator->id }}"></td>
                                                <td class="text-center" id="break-order-{{ $operator->id }}"></td>
                                                <td class="text-center" id="break-total-{{ $operator->id }}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table> 
                            @endif
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
@stop