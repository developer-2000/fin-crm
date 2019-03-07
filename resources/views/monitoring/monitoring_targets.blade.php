@extends('layouts.app')

@section('title')Мониторинг целей@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/classie.js') }}"></script>
    <script src="{{ URL::asset('js/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/monitoring/monitoring-ws-client.js') }}"></script>
    <script src="{{ URL::asset('js/monitoring/monitoring_targets.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span>Мониторинг целей</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="main_container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-box clearfix">
                        <div class="main-box-body clearfix">
                            <div class="table-responsive">
                                <table class="table" id="orders">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Дата</th>
                                        <th class="text-center">ID</th>
                                        <th>Оператор</th>
                                        <th class="text-center">Страна</th>
                                        <th class="text-center">Оффер / Товары</th>
                                        <th class="text-center">Цель</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if ($orders)
                                        @foreach($orders as $order)
                                            <?
                                            $class = '';
                                            if ($order->proc_status == 3) {
                                                $class = 'info';
                                            }
                                            if ($order->target_status == 1) {
                                                $class = 'success';
                                            }
                                            if ($order->target_status == 2) {
                                                $class = 'danger';
                                            }
                                            if ($order->target_status == 3) {
                                                $class = 'warning';
                                            }
                                            if ($order->proc_callback_time) {
                                                $class = 'active';
                                            }
                                            if ($order->proc_status == 7) {
                                                $class = 'info';
                                            }
                                            $id = $order->id . '_' . $order->target_status . '_' . $order->proc_status;
                                            ?>
                                            <tr id="{{$id}}" class="{{$class}}">
                                                <td class="text-center">{{$order->time_modified}}</td>
                                                <td class="text-center">{{$order->id}}</td>
                                                <td>{{$order->name}} {{$order->surname}}</td>
                                                <td class="text-center">
                                                    <img class="country-flag"
                                                         src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}" />
                                                </td>
                                                <td class="text-center">
                                                    <b>{{$order->offer}}</b><br>
                                                    @if (isset($order->products))
                                                        @foreach($order->products as $product)
                                                            {{$product}}<br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (is_array($order->target_final) && ($order->target_status == 1 || $order->target_status == 3))

                                                        @if ($order->target_status == 1)
                                                            <h4>Подтвержден</h4>
                                                        @else
                                                            <h4>Аннулирован</h4>
                                                        @endif
                                                        @foreach ($order->target_final as $tf)
                                                            <div>{{ $tf->text }}</div>
                                                        @endforeach
                                                    @elseif ($order->target_final && $order->target_status == 2)
                                                        <h4>Отказ</h4>
                                                        Прична отказа - {{ $order->target_final }}
                                                    @elseif ($order->proc_callback_time)
                                                        <h4>Перезвонить</h4>
                                                        {{$order->proc_callback_time }}
                                                    @elseif ($order->proc_status)
                                                        @if ($order->proc_status == 3)
                                                            <h4>Заказ открылся</h4>
                                                        @endif
                                                        @if ($order->proc_status == 7)
                                                            <h4>Говорит на другом языке</h4>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('order', $order->id) }}" class="table-link">
                                                        <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                    </a>
                                                </td>
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
    </div>

@stop