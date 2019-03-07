@extends('layouts.app')

@section('title') @lang('general.order') # {{$order->id}} @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}"/>
    <style>
        .form-horizontal .control-label {
            text-align: left;
        }
    </style>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order-create.js') }}"></script>
    <script src="{{ URL::asset('js/orders/change-project.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><a href="{{route('order', $order->id)}}"> @lang('general.order') # {{$order->id}}</a></li>
                <li class="active"><span> @lang('orders.edit-project')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('orders.edit-project')</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <header class="main-box-header">
                        <h2> @lang('general.before')</h2>
                    </header>
                    <div class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-lg-6 control-label"> @lang('general.project')</label>
                            <div class="col-lg-6 control-label">{{$order->project ? $order->project->name : '-'}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-6 control-label"> @lang('general.subproject')</label>
                            <div class="col-sm-6 control-label">{{$order->subProject ? $order->subProject->name : '-'}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-6 control-label"> @lang('general.offer')</label>
                            <div class="col-sm-6 control-label">{{$order->offer ? $order->offer->name : '-'}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-6 control-label text-center "> @lang('general.products') :</label>
                        </div>
                        <div class="table-responsive">
                            <table class="table table_products">
                                <thead>
                                <tr>
                                    <th class="text-center"> @lang('general.product')</th>
                                    <th class="text-center">Цена</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ($order->products->isNotEmpty())
                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($order->products as $product)
                                        <tr @if ($product->pivot->disabled) class="warning" @endif>
                                            <td class="value">
                                                {{$product->title}}
                                            </td>
                                            <td class="text-center">
                                                {{$product->price}}
                                            </td>
                                            @php
                                                $total += $product->price;
                                            @endphp
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td class="value">
                                        Всего
                                    </td>
                                    <td class="text-center">
                                        {{$order->price_total}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        @php
                            $inputData = json_decode($order->input_data)
                        @endphp
                        @if ($inputData)
                            <div class="form-group">
                                <label class="col-sm-6 control-label text-center "> @lang('orders.incoming-information') :</label>
                            </div>
                            @foreach($inputData as $key => $value)
                                <div class="form-group">
                                    <label class="col-lg-6 control-label">{{$key}}</label>
                                    <div class="col-sm-6 control-label">
                                        @if (is_array($value) || is_object($value))
                                            @foreach($value as $v)
                                                @if (strlen($v) > 40)
                                                    {{substr($v, 0, 40)}}...
                                                @else
                                                    {{$v}}
                                                @endif
                                            @endforeach
                                        @else
                                            @if (strlen($value) > 40)
                                                {{substr($value, 0, 40)}}...
                                            @else
                                                {{$value}}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <header class="main-box-header">
                        <h2>Стало</h2>
                    </header>
                    <form class="form-horizontal" role="form" id="change_project">
                        <input type="hidden" name="partner_id" value="{{$order->partner_id}}">
                        <input type="hidden" name="id" value="{{$order->id}}">
                        <div class="form-group">
                            <label for="project_id" class="col-lg-6 control-label required"> @lang('general.project')</label>
                            <div class="col-lg-6">
                                <input type="text" id="project_id" name="project_id" class="select2">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sub_project_id" class="col-lg-6 control-label required"> @lang('general.subproject')</label>
                            <div class="col-lg-6">
                                <input type="text" id="sub_project_id" name="sub_project_id" class="select2">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="proc_status" class="col-lg-6 control-label"> @lang('general.status')</label>
                            <div class="col-lg-6">
                                <select id="proc_status" name="proc_status" class="select2">
                                    @if($procStatuses)
                                        @foreach($procStatuses as $status)
                                            <option value="{{$status->id}}" @if($order->proc_status == $status->id) selected @endif>{{$status->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="position: relative;">
                            <label class="col-sm-6 control-label text-center required"> @lang('general.product') :</label>
                            <div class="col-sm-6 control-label text-center">
                                <div class="filter-block" style="margin-left: 15px;margin-top: 0;">
                                    <div class="form-group pull-left">
                                        <input type="text" class="form-control search" id="search_product"
                                               placeholder="Search...">
                                        <i class="fa fa-search search-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive search_block" id="search_block" style="position:initial; max-height: 500px;"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table_products" id="products">
                                <thead>
                                <tr>
                                    <th class="text-center"> @lang('general.product')</th>
                                    <th class="text-center"> @lang('general.price')</th>
                                    <th style="width: 1px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="price_product">
                                    <td class="text-center value">
                                        @lang('general.total')
                                    </td>
                                    <td class="text-center" id="total_price">0</td>
                                    <td style="width: 1px"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-12">
                            <div class="error-messages"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-success" id="change"> @lang('general.save')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
