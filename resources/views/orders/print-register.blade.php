@extends('layouts.app')

@section('title')Печать заказов@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        .crm_id {
            color: #5ac9b9;
            display: inline-block;
            border-bottom: 1px dotted #5ac9b9;
            font-size: 14px;
            font-weight: 600;
        }

        .order_date {
            font-size: 13px;
            color: #2bb5fd;
            border-bottom: 1px dashed #2bb5fd;
            font-weight: 600;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/prints/index.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Печать заказов</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Печать заказов</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box no-header clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table id="orders" class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Проц.статус</th>
                                <th class="text-center">Дата</th>
                                <th class="text-center">Товар</th>
                                <th class="text-center">Сумма</th>
                                <th class="text-center">ЭН</th>
                                <th class="text-center"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($orders as $order)
                                <tr data-order= {{$order->id}}>
                                    <td class="text-center">
                                        <a class="crm_id" href="{{route('order-sending', $order->id)}}">
                                            {{$order->id}}
                                        </a>
                                        <div class="project_oid">{{$order->partner_oid}}</div>
                                    </td>
                                    <td class="text-center proc_status_name">
                                        <div class="proc_status">{{$order->procStatus->name}}</div>
                                    </td>
                                    <td class="text-center ">
                                        <div class="order_phone_block">
                                                    <span class="order_date">
                                                        {{\Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}
                                                    </span>
                                            <div class="project_oid">{{\Carbon\Carbon::parse($order->time_created)->format('d/m/Y')}}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @foreach($order->products as $product)
                                                {{$product->title}},<br>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center price_order">
                                        {{$order->price_total}}
                                        {{!empty($order->currency->currency) ? $order->currency->currency : ''}}
                                    </td>
                                    <td>
                                        {{$order->getTargetValue->track}}
                                    </td>
                                    <td>
                                        @if(View::exists('orders.prints.'.$order->getTargetValue->getTargetConfig->alias))
                                            @include('orders.prints.'.$order->getTargetValue->getTargetConfig->alias)
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        @if($orders[0]->getTargetValue->target_id == 1 && !empty($tracks))
                            @php
                                $integrationClass = \App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($orders[0]->getTargetValue->getTargetConfig->alias ?? '');
                            @endphp
                            <p>
                                @if($integrationClass::PRINT_NOTES)
                                    <a target="_blank" class="btn btn-success"
                                       href="{{route('delivery-note-print-all',  [$tracks, $targetConfig->alias ])}}">
                                        <i class="fa fa-print"></i>
                                        Печать всех накладных</a>
                                @endif
                                @if($integrationClass::PRINT_MARKINGS)
                                    <a target="_blank" class="btn btn-success"
                                       href="{{route('markings-print-all', [$tracks, $targetConfig->alias])}}">
                                        <i class="fa fa-print"></i>
                                        Печать всех маркировок</a>
                                @endif
                                @if($integrationClass::PRINT_MARKINGS_ZEBRA)
                                    <a target="_blank" class="btn btn-success"
                                       href="{{route('markings-zebra-print-all', [$tracks, $targetConfig->alias])}}">
                                        <i class="fa fa-print"></i>
                                        Печать всех маркировок (Zebra)</a>
                                @endif
                            </p>
                        @endif
                        <p>
                            <input type="hidden" name="ordersIds" id="ordersIds" value="{{$ordersIds}}">
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning dropdown-toggle"
                                    data-toggle="dropdown" aria-expanded="false">
                                Изменить статус заказов на: <span
                                        class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                @foreach($procStatuses as $procStatus)
                                    <li>
                                        <a class="change_all_orders_statuses" data-proc-status="{{$procStatus->id}}"
                                           href="#">
                                            {{$procStatus->name}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning dropdown-toggle"
                                    data-toggle="dropdown" aria-expanded="false">
                                Изменить подстатус заказов на: <span
                                        class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                @foreach($procStatuses as $procStatus)
                                    <li>
                                        <a class="change_all_orders_statuses" data-proc-status="{{$procStatus->id}}"
                                           href="#">
                                            {{$procStatus->name}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop