@extends('layouts.app')

@section('title') @lang('prints.queued-to-print')@stop

@section('css')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        .delivery_note label {
            display: block;
            border: 0;
            background-color: #7FC8BA;
            padding: 5px;
            padding-top: 7%;
            border-radius: 50% !important;
            margin-right: 4px;
            height: 34px;
            width: 34px;
            color: #ffffff;
        }

        .delivery_note label:hover {
            margin-right: 3px;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/snap.svg-min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order.js?a=1') }}"></script>
    <script src="{{ URL::asset('js/orders/print-register.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/prints/index.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main') </a></li>
                <li class="active"><span> @lang('prints.queued-to-print')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">@lang('prints.queued-to-print')(<span class="badge">{{$ordersCount}}</span>)
                </h1>
            </div>
        </div>
    </div>
    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            <div class="tabs-wrapper">
                <ul class="nav nav-tabs">
                    <li class="">
                        <a href="{{route('orders')}}">
                            @lang('general.orders')
                        </a>
                    </li>
                    <li class="active">
                        <a href="#">
                            @lang('prints.queued-to-print')
                        </a>
                    </li>
                </ul>
                <div class="main-box clearfix">
                    <div class="row">
                        <div class="main-box no-header clearfix">
                            <div class="main-box-body clearfix">
                                <br>
                                <div class="table-responsive">
                                    <div class="row">
                                        {{--<div class="col-lg-12 text-center">--}}
                                        {{--<span style="font-size: 18px;font-weight: bold; color: #0c4539; text-decoration: underline">  {{isset($orders[0]->project->name) ? $orders[0]->project->name : ''}}--}}
                                        {{--</span>--}}
                                        {{--</div>--}}
                                    </div>
                                    @foreach($orders as $ordersToPrintByPostCollection)
                                        <div class="post-block">
                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                                <span style="font-size: 18px; font-weight: bold; color: #1ABC9C; text-decoration: underline">  {{isset($ordersToPrintByPostCollection[0]->getTargetApprove->name) ? $ordersToPrintByPostCollection[0]->getTargetApprove->name : ''}}
                                                                </span>
                                                </div>
                                            </div>
                                            <table id="orders" class="table table-striped table-hover">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">
                                                        @lang('general.id')
                                                    </th>
                                                    <th class="text-center">
                                                        @lang('general.processing-status')
                                                    </th>
                                                    <th class="text-center">
                                                        @lang('general.date-created')
                                                    </th>
                                                    <th class="text-center">
                                                        @lang('general.products')
                                                    </th>
                                                    <th class="text-center">
                                                        @lang('general.order-price')
                                                    </th>
                                                    <th class="text-center">
                                                        @lang('general.track')
                                                    </th>
                                                    <th class="text-center"></th>
                                                </tr>
                                                </thead>
                                                <tbody
                                                        {{--data-project="{{$key}}"--}}
                                                >
                                                @php
                                                    $orderIds = [];
                                                @endphp
                                                @foreach($ordersToPrintByPostCollection as $printOrder)
                                                    @php
                                                        $orderIds[] = $printOrder->id;
                                                    @endphp
                                                    <tr class="order" data-order= {{$printOrder->id}}>
                                                        <td class="text-center">
                                                            <a class="crm_id"
                                                               href="{{route('order-sending', $printOrder->id)}}">
                                                                {{$printOrder->id}}
                                                            </a>
                                                            <div class="project_oid">
                                                                {{$printOrder->partner_oid}}
                                                            </div>
                                                        </td>
                                                        <td class="text-center proc_status_name">
                                                            <div class="proc_status">
                                                                {{$printOrder->procStatus->name}}
                                                            </div>
                                                        </td>
                                                        <td class="text-center ">
                                                            <div class="order_phone_block">
                                                                <span class="order_date">
                                                    {{\Carbon\Carbon::parse($printOrder->time_created)->format('H:i:s')}}
                                                                </span>
                                                                <div class="project_oid">
                                                                    {{\Carbon\Carbon::parse($printOrder->time_created)->format('d/m/Y')}}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                @foreach($printOrder->products as $product)
                                                                    {{$product->title}},<br>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td class="text-center price_order">
                                                            {{$printOrder->price_total}}
                                                            {{!empty($printOrder->currency->currency) ? $printOrder->currency->currency : ''}}
                                                        </td>
                                                        <td>
                                                            {{$printOrder->getTargetValue->track}}
                                                        </td>
                                                        <td>
                                                            @if(View::exists('orders.prints.'.$printOrder->getTargetValue->getTargetConfig->alias))
                                                                @include('orders.prints.'.$printOrder->getTargetValue->getTargetConfig->alias)
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="7">
                                                        <div class="text-center">
                                                            <button type="button"
                                                                    id="sent"
                                                                    class="btn btn-warning dropdown-toggle sent"
                                                                    data-toggle="dropdown"
                                                                    data-proc-status="{{$ordersToPrintByPostCollection[0]->procStatus->id}}"
                                                                    aria-expanded="false">
                                                                &raquo; {{$ordersToPrintByPostCollection[0]->procStatus->name}}
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            @if(!empty($ordersToPrintByPostCollection->tracks))
                                                <div class="text-center">
                                                    @php
                                                        $alias = $ordersToPrintByPostCollection[0]->getTargetValue->getTargetConfig->alias;
                                                        $className = studly_case($alias ?? '');
                                                        $integrationClass = 'App\Models\Api\Posts\\'.$className;
                                                        $tracksTarget = implode(",", $ordersToPrintByPostCollection->tracks);
                                                   $ordersIdsString = implode(",", $orderIds);
                                                    @endphp
                                                    @if (class_exists($integrationClass))
                                                        <p>
                                                            @if($integrationClass::PRINT_NOTES && !empty($tracksTarget))
                                                                <a target="_blank" class="btn btn-success"
                                                                   href="{{route('delivery-note-print-all',  [$tracksTarget, $alias, $ordersIdsString])}}">
                                                                    <i class="fa fa-print"></i>
                                                                    @lang('prints.print-all')
                                                                </a>
                                                            @endif
                                                            @if($integrationClass::PRINT_MARKINGS)
                                                                <a target="_blank" class="btn btn-success"
                                                                   href="{{route('markings-print-all', [$tracksTarget, $alias])}}">
                                                                    <i class="fa fa-print"></i>
                                                                    @lang('prints.print-all-markings')
                                                                </a>
                                                            @endif
                                                            @if($integrationClass::PRINT_MARKINGS_ZEBRA)
                                                                <a target="_blank" class="btn btn-success"
                                                                   href="{{route('markings-zebra-print-all', [$tracksTarget, $alias])}}">
                                                                    <i class="fa fa-print"></i>
                                                                    @lang('prints.Print all markings (Zebra)')
                                                                </a>
                                                            @endif
                                                            @if(!empty($integrationClass::PRINT_REGISTRY)){{--костыль--}}
                                                            <a download class="btn btn-success"
                                                               href="{{route('kazpost-get-registry', ['orders' => implode(',', $orderIds)])}}">
                                                                <i class="fa fa-print"></i>
                                                                @lang('prints.registry')
                                                            </a>
                                                            @endif
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <br>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
    @include('orders.print_error_messages')
@stop
