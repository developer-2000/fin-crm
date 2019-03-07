@extends('layouts.app')

@section('title') @lang('orders.order-create') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/incoming_line.css') }}" />
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
    <script src="{{ URL::asset('js/incoming-calls/incoming_call.js') }}"></script>
@stop

@section('content')
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="content-header" class="clearfix">
                            <div class="pull-left">
                                <ol class="breadcrumb">
                                    <li> @lang('orders.incoming-line')</li>
                                    <li class="active"><span> @lang('orders.order-by-number')</span></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-box clearfix">
                        <header class="main-box-header clearfix">
                            <div class="filter-block pull-left custom_filter_block">
                                <div class="form-group pull-left">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" class="form-control" id="phone_search" name="phone" placeholder=" @lang('general.search')...">
                                    </div>
                                    <i class="fa fa-search search-icon"></i>
                                </div>
                            </div>
                            <div class="filter-block pull-right">
                                <a href="{{route('incoming-call-create-order')}}?phone={{$phone}}" class="btn btn-primary pull-right">
                                    <i class="fa fa-plus-circle fa-lg"></i> @lang('orders.order-create')
                                </a>
                                <div id="incoming_phone" class="hidden">{{$phone}}</div>
                            </div>
                        </header>
                        <div class="main-box-body clearfix">
                            <div class="table-responsive">
                                <table id="orders" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center"> @lang('general.id')</th>
                                        <th class="text-center"> @lang('general.country')</th>
                                        <th class="text-center"> @lang('general.date')</th>
                                        <th class="text-center"> @lang('general.data')</th>
                                        <th class="text-center"> @lang('general.offer')/@lang('general.products')</th>
                                        <th class="text-center"> @lang('general.sum')</th>
                                        <th colspan="5" class="text-center"> @lang('general.process')/@lang('general.target')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if ($orders)
                                            @foreach($orders as $order)
                                                <?
                                                $status = '';
                                                switch($order->proc_status) {
                                                    case 1:
                                                        $status = 'В обработке';
                                                        break;
                                                    case 2: {
                                                        $status = 'В наборе';
                                                        break;
                                                    }
                                                    case 3: {
                                                        $status = 'Контакт';
                                                        break;
                                                    }
                                                    case 4: {
                                                        $status = 'Повтор';
                                                        break;
                                                    }
                                                    case 5: {
                                                        $status = 'Недозвон';
                                                        break;
                                                    }
                                                    case 6: {
                                                        $status = 'Некорректный номер';
                                                        break;
                                                    }
                                                    case 7: {
                                                        $status = 'Другой язык';
                                                        break;
                                                    }
                                                    case 8: {
                                                        $status = 'Ошибка';
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                            <span class="crm_id">
                                                                {{$order->id}}
                                                            </span>
                                                        <div class="project_oid">{{$order->partner_oid}}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        <img class="country-flag"
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}" />
                                                    </td>
                                                    <td class="text-center ">
                                                        <div class="order_phone_block">
                                                            <a href="#" class="pop">
                                                                    <span class="order_date">
                                                                       {{ \Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}
                                                                    </span>
                                                            </a>
                                                            <div class="data_popup">
                                                                <div class="arrow"></div>
                                                                <h3 class="title"> @lang('general.date-created')</h3>
                                                                <div class="content">{{ \Carbon\Carbon::parse($order->time_created)->format('H:i:s d/m/y')}}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="input-data">
                                                        <div class="value"> @lang('general.first-name') : <span>{{$order->name}}</span> </div>
                                                        <div class="value"> @lang('general.surname') : <span>{{$order->surname}}</span> </div>
                                                        <div class="value"> @lang('general.middle-name') : <span>{{$order->middle}}</span> </div>
                                                        <div class="value"> @lang('general.phone') : <span>{{$order->phone}}</span> </div>
                                                    </td>
                                                    <td class="offer_products">
                                                        <div class="value"> @lang('general.offer') : <span>{{$order->offer_name}}</span></div>
                                                        <div class="value"> @lang('general.products') :
                                                            @if ($order->products)
                                                                @foreach($order->products as $product)
                                                                    <div class="product">
                                                                        {{$product->name}}
                                                                        @if ($product->type == 1)
                                                                            <span class="label label-success "> @lang('general.up-sell')</span>
                                                                        @elseif ($product->type == 2)
                                                                            <span class="label label-primary "> @lang('general.up-sell') 2</span>
                                                                        @elseif ($product->type == 4)
                                                                            <span class="label label-info "> @lang('general.cross-sell')</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-center price_order">
                                                        {{$order->price_total}} {{$order->currency}}
                                                        <div class=" border_proc @if ($order->target_status) border @endif"></div>
                                                    </td>
                                                    @if ($order->target_status)
                                                        <?
                                                        $target = '';
                                                        $classLabel = '';
                                                        $classRow = '';
                                                        $classBtn = '';
                                                        switch($order->target_status) {
                                                            case 1: {
                                                                $target = 'Подтвержден';
                                                                $classLabel = 'label-primary';
                                                                $classRow = 'success';
                                                                break;
                                                            }
                                                            case 2: {
                                                                $target = 'Отказ';
                                                                $classLabel = 'label-danger';
                                                                $classRow = 'danger';
                                                                $classBtn = 'custom_danger';
                                                                break;
                                                            }
                                                            case 3: {
                                                                $target = 'Аннулирован';
                                                                $classLabel = 'label-warning';
                                                                $classRow = 'warning';
                                                                $classBtn = 'custom_warning';
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <td class="text-center {{$classRow}}">
                                                            {{$order->operSurname}} {{$order->operName}}
                                                        </td>
                                                            <td class="text-center {{$classRow }}">
                                                                <div class="proc_label">
                                                                    @lang('general.target')
                                                                </div>
                                                                <span class="badge {{$classLabel}}">{{$target}}</span>
                                                            </td>
                                                            <td class="text-center {{$classRow }}" colspan="2">
                                                                @if ($order->target_status != 1)
                                                                    <div class="proc_label">
                                                                        @lang('general.cause')
                                                                    </div>
                                                                    <div class="order_comment">
                                                                        {{$order->cause}}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="{{$classRow }}">
                                                                <a href="{{ route('order', $order->id) }}/" class="table-link {{$classBtn}}">
                                                                    <span class="fa-stack">
                                                                        <i class="fa fa-square fa-stack-2x "></i>
                                                                        <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                                                </a>
                                                            </td>

                                                    @else
                                                            <td class="text-center" style="font-size: 13px;">
                                                                {{$order->campaign}}
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="proc_label">
                                                                    @lang('general.status')
                                                                </div>
                                                                <?
                                                                $status = '';
                                                                $class = '';
                                                                switch($order->proc_status) {
                                                                    case 1:
                                                                        $status = 'В обработке';
                                                                        $class = 'label-default';
                                                                        break;
                                                                    case 2: {
                                                                        $status = 'В наборе';
                                                                        $class = 'label-success';
                                                                        break;
                                                                    }
                                                                    case 3: {
                                                                        $status = 'Контакт';
                                                                        $class = 'label-primary';
                                                                        break;
                                                                    }
                                                                    case 4: {
                                                                        $status = 'Повтор';
                                                                        $class = 'label-warning';
                                                                        break;
                                                                    }
                                                                    case 5: {
                                                                        $status = 'Недозвон';
                                                                        $class = 'label-danger';
                                                                        break;
                                                                    }
                                                                    case 6: {
                                                                        $status = 'Некоректный номер';
                                                                        $class = 'label-danger';
                                                                        break;
                                                                    }
                                                                    case 7: {
                                                                        $status = 'Другой язык';
                                                                        $class = 'label-info';
                                                                        break;
                                                                    }
                                                                    case 8: {
                                                                        $status = 'Ошибка';
                                                                        $class = 'label-danger';
                                                                        break;
                                                                    }
                                                                    case 9: {
                                                                        $status = 'Завершен';
                                                                        $class = 'label-default';
                                                                        break;
                                                                    }
                                                                    case 13: {
                                                                        $status = 'Ошибка';
                                                                        $class = 'label-danger';
                                                                        break;
                                                                    }
                                                                }
                                                                ?>
                                                                <span class="label {{$class}}">{{$status}}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="proc_label">
                                                                    @lang('general.count')
                                                                </div>
                                                                <span class="badge badge-primary">{{$order->proc_stage}}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="order_phone_block">
                                                                    <div class="proc_label">
                                                                        @lang('general.call-back')
                                                                    </div>
                                                                    <a href="#" class="pop">
                                                            <span class="order_date @if (!$order->proc_callback_time) proc_time @endif">
                                                                {{ \Carbon\Carbon::parse($order->proc_time)->format('H:i:s')}}
                                                            </span>
                                                                    </a>
                                                                    <div class="data_popup">
                                                                        <div class="arrow"></div>
                                                                        <h3 class="title"> @lang('general.call-back')</h3>
                                                                        <div class="content">{{ \Carbon\Carbon::parse($order->proc_time)->format('H:i:s d/m/y')}}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('order', $order->id) }}/" class="table-link custom_badge">
                                                        <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x "></i>
                                                            <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                                </a>
                                                            </td>
                                                    @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center" colspan="11"> @lang('orders.order-by-number') "{{$phone}}" @lang('orders.not-found')</td>
                                            </tr>
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
