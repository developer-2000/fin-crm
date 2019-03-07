@extends('x_layout/layout')

@section('title')Процесинг операторов@stop

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
    <script src="{{ URL::asset('js/monitoring/monitoring_calls.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span>Мониторинг операторов</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-12">
            @if ($orders)
                <table border="1">
                    <tr>
                        <th>Оператор</th>
                        <th>Всего</th>
                        <th>Без цели</th>
                        <th>Подтверждено</th>
                        <th>Отказ</th>
                        <th>Аннулировано</th>
                        <th>Недозвон</th>
                    </tr>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{$order['name']}}</td>
                            <td>{{$order['all']}}</td>
                            <td>{{$order['not_target']}}</td>
                            <td>{{$order['approve']}}</td>
                            <td>{{$order['fail']}}</td>
                            <td>{{$order['annulled']}}</td>
                            <td>{{$order['not_call']}}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
@stop