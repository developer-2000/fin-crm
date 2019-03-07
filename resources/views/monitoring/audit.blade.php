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
                        <li class="active"><a href="#audit" data-toggle="tab" aria-expanded="true">Статистика по операторам</a></li> 
                        <li><a onclick="location.href='{{ route('monitoring-agent') }}/'" href="#agents" data-toggle="tab" aria-expanded="false">Агенты</a></li>
                        <li><a onclick="location.href='{{ route('monitoring-companies') }}/'" href="#company" data-toggle="tab" aria-expanded="false">Компании</a></li>
                    </ul>
                    <div class='tab-content'>  
                        <div class="tab-pane fade active in" id="audit">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-center">ФИО</th>
                                        <th class="text-center">Вход в систему</th>
                                        <th class="text-center">Выход из системы</th>
                                        <th class="text-center">Кол-во входов</th>
                                        <th class="text-center">Время в системе</th>
                                        <th class="text-center">Время разговоров</th>
                                        <th class="text-center">Кол-во разговоров</th>
                                        <th class="text-center">Перерыв</th>
                                        <th class="text-center">Подтвержденные</th>
                                        <th class="text-center">Up Sell</th>
                                        <th class="text-center">Up Sell 2-го уровня</th>
                                        <th class="text-center">Cross Sell</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data)
                                        @foreach ($data as $d)
                                            <tr>
                                                <td>{{ $d->user_id }}</td>
                                                <td class="text-center">{{ $d->name }} {{ $d->surname }}</td>
                                                <td class="text-center">{{ date('Y-d-m-d H:i', (int)$d->login_start) }}</td>
                                                <td class="text-center">{{ date('Y-d-m-d H:i', (int)$d->login_end) }}</td>
                                                <td class="text-center">{{ $d->count_log }}</td>
                                                <td class="text-center">{{ date('H:i', (int)$d->login_time) }}</td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center">
                                                    {{ $d->break_pause > 3600 ? date('H:i:s', $d->break_pause) : date('i:s', $d->break_pause) }}
                                                    {{ $d->break_order > 3600 ? date('H:i:s', $d->break_order) : date('i:s', $d->break_order) }}
                                                    {{ $d->break_total > 3600 ? date('H:i:s', $d->break_total) : date('i:s', $d->break_total) }}
                                                </td>
                                                <td class="text-center">{{ $d->approved }}</td>
                                                <td class="text-center">{{ $d->up_sell }}</td>
                                                <td class="text-center">{{ $d->up_sell_level_two }}</td>
                                                <td class="text-center">{{ $d->cross_sell }}</td>
                                            </tr>
                                        @endforeach 
                                    @endif
                                </tbody>
                            </table> 
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
@stop