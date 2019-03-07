@extends('layouts.app')

@section('title') @lang('orders.order-creating') @endsection

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order-create.js') }}"></script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order-create.css') }}"/>
    <style>
        .first_info .value {
            max-width: 30%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/"> @lang('general.main')</a></li>
                <li><a href="{{route('orders')}}"> @lang('orders.all-sendings')</a></li>
                <li class="active"><span> @lang('general.order-creating')</span></li>
            </ol>
            <h1> @lang('general.order-creating')</h1>
        </div>
    </div>
    <form id="create_order">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-push-4">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group ">
                                <label for="name_last" class="required">
                                    @lang('general.surname')
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="name_last"
                                       name="name_last"
                                       @if ($order) value="{{$order->name_last}}" @endif
                                       required>
                            </div>
                            <div class="form-group ">
                                <label for="name_first" class="required">
                                    @lang('general.first-name')
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="name_first"
                                       name="name_first"
                                       @if ($order) value="{{$order->name_first}}" @endif
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group ">
                                <label for="name_middle">
                                    @lang('general.middle-name')
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="name_middle"
                                       name="name_middle"
                                       @if ($order) value="{{$order->name_middle}}" @endif >
                            </div>
                            <div class="form-group ">
                                <label for="phone" class="required">
                                    @lang('general.phone')
                                </label>
                                <input type="text"
                                       class="form-control"
                                       name="phone"
                                       id="phone"
                                       @if ($order) value="{{$order->phone}}" @endif >
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group form-group-select2">
                                <label for="country" class="required">
                                    @lang('general.country')
                                </label>
                                <select name="country" id="country" class="select2">
                                    <option value=""></option>
                                    @if ($countries)
                                        @foreach ($countries as $country)
                                            <option data-currency="{{ $country->currency }}"
                                                    value="{{$country->code }}"
                                                    @if ($order && $order->geo ==  $country->code) selected @endif
                                            >

                                                @lang('countries.' . $country->code)
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="age"> @lang('general.age')</label>
                                <input type="text"
                                       class="form-control"
                                       name="age"
                                       id="age"
                                       @if ($order && $order->age) value="{{$order->age}}" @endif>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="gender"> @lang('general.gender')</label>
                                <select class="form-control" name="gender" id="gender">
                                    <option value=""></option>
                                    <option value="1" @if ($order && $order->gender == 1) selected @endif>
                                        @lang('general.male')
                                    </option>
                                    <option value="2" @if ($order && $order->gender == 2) selected @endif>
                                        @lang('general.female')
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-box clearfix" style="position:relative;">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left" style="color: #929292;">
                            @lang('general.products')
                        </h2>
                        <div class="filter-block pull-right">
                            <div class="form-group pull-left">
                                <input type="text" class="form-control search" id="search_product"
                                       placeholder=" @lang('general.search')...">
                                <i class="fa fa-search search-icon"></i>
                            </div>
                        </div>
                    </header>
                    <div class="table-responsive search_block" id="search_block">
                    </div>
                    <div class="table-responsive">
                        <table class="table table_products" id="products">
                            <thead>
                            <tr>
                                <th class="text-center"> @lang('general.name')</th>
                                <th class="text-center"> @lang('general.price')</th>
                                <th style="width: 1px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $totalPrice = 0;
                            @endphp
                            @if ($order && $order->products)
                                @foreach($order->products as $key => $product)
                                    @php
                                        $totalPrice += $product->price;
                                    @endphp
                                    <tr>
                                        <td class="value">
                                            {{$product->title}}
                                        </td>
                                        <td class="text-center">
                                            <input type="text"
                                                   style="width: 70px; display: inline-block;"
                                                   class="form-control product_price"
                                                   placeholder=" @lang('general.price')"
                                                   id="products[{{$key}}][product_price]"
                                                   name="products[{{$key}}][product_price]"
                                                   value="{{$product->price}}">
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="table-link danger delete_product">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                            </span>
                                            </a>
                                            <input type="hidden"
                                                   name="products[{{$key}}][product_id]"
                                                   value="{{$product->id}}"
                                                   id="products[{{$key}}][product_id]">
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr class="price_product">
                                <td class="text-center value">
                                    @lang('general.total')
                                </td>
                                <td class="text-center" id="total_price">{{$totalPrice}}</td>
                                <td style="width: 1px"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="result">
                    <div class="tabs-wrapper targets">
                        <ul class="nav nav-tabs">
                            <li class=" target  active " style="width: 100%;">
                                <a href="#approve"
                                   data-toggle="tab"
                                   class="approve ">
                                    @lang('general.delivery')
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade in active"
                                 id="approve">
                                <div class="main-box clearfix form-horizontal">
                                    <div class="main-box-body clearfix text-center"
                                         style="padding-top: 20px;">
                                        <div class="form-group">
                                            <label for="target_id"
                                                   class="col-md-4 control-label required">
                                                @lang('orders.change-target')
                                            </label>
                                            <div class="col-md-8">
                                                <input id="target_id"
                                                       class="select2"
                                                       name="target_id"
                                                       data-content="{{$order && $order->getTargetApprove ? json_encode(['id' => $order->getTargetApprove->id, 'text' => $order->getTargetApprove->name]) : ''}}"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="main-box-body clearfix target_block">
                                        <p class="text-center title_tab_content" style="padding-top: 0;">
                                            @lang('orders.fill-data-order')
                                        </p>
                                        <div id="target_block">
                                            @if ($order && $order->getTargetValue)
                                                {{renderTarget(json_decode($order->getTargetValue->values), 'approve[', ']', $order->getTargetApprove ? $order->getTargetApprove->alias : '')}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 col-md-pull-8">
                <div class="main-box-body clearfix">
                    <table class="table first_info">
                        {{--@if (!auth()->user()->project_id)--}}
                            {{--<tr class="form-group">--}}
                                {{--<td class="text-center key">--}}
                                    {{--<label for="partner_id" class=" control-label required">--}}
                                        {{--@lang('general.partner')--}}
                                    {{--</label>--}}
                                {{--</td>--}}
                                {{--<td class="value">--}}
                                    {{--<select name="partner_id"--}}
                                            {{--id="partner_id"--}}
                                            {{--class="select2">--}}
                                        {{--<option value=""></option>--}}

                                        {{--@if ($partners)--}}
                                            {{--@if ($order)--}}
                                                {{--@foreach($partners as $partner)--}}
                                                    {{--@if ($order->partner_id == $partner->id)--}}
                                                        {{--<option value="{{$partner->id}}"--}}
                                                                {{--selected>{{$partner->name}}</option>--}}
                                                    {{--@endif--}}
                                                {{--@endforeach--}}
                                            {{--@elseif(auth()->user()->project_id && auth()->user()->project && auth()->user()->project->partner)--}}
                                                {{--@foreach($partners as $partner)--}}
                                                    {{--@if (auth()->user()->project->partner->id == $partner->id)--}}
                                                        {{--<option value="{{$partner->id}}"--}}
                                                                {{--selected>{{$partner->name}}</option>--}}
                                                    {{--@endif--}}
                                                {{--@endforeach--}}
                                            {{--@elseif(!auth()->user()->project_id)--}}
                                                {{--@foreach($partners as $partner)--}}
                                                    {{--<option value="{{$partner->id}}">{{$partner->name}}</option>--}}
                                                {{--@endforeach--}}
                                            {{--@endif--}}
                                        {{--@endif--}}
                                    {{--</select>--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                        {{--@else--}}
                            {{--<input type="hidden" name="partner_id" value="{{auth()->user()->project->partner_id ?? 0}}">--}}
                        {{--@endif--}}
                        <tr class="form-group">
                            <td class="text-center key">
                                <label for="project_id" class="control-label required">
                                    @lang('general.project')
                                </label>
                            </td>
                            <td class="value">
                                @if (!auth()->user()->project_id)
                                    @php
                                        $content = auth()->user()->project ? json_encode(['id' => auth()->user()->project->id, 'text' => auth()->user()->project->name]) : '';
                                        if ($order && $order->project) {
                                            $content = json_encode(['id' => $order->project->id, 'text' => $order->project->name]);
                                        }
                                    @endphp
                                    <input name="project_id"
                                           id="project_id"
                                           class="select2"
                                           data-content="{{ $content }}">
                                @else
                                    {{auth()->user()->project->name ?? ''}}
                                    <input name="project_id" type="hidden" id="project_id"
                                           value="{{auth()->user()->project_id}}">
                                @endif
                            </td>
                        </tr>
                        <tr class="form-group">
                            <td class="text-center key">
                                <label for="sub_project_id" class="control-label required">
                                    @lang('general.subproject')
                                </label>
                            </td>
                            <td class="value">
                                @if (!auth()->user()->sub_project_id)
                                    <input name="sub_project_id"
                                           id="sub_project_id"
                                           class="select2"
                                           data-content="{{$order && $order->subProject ? json_encode(['id' => $order->subProject->id, 'text' => $order->subProject->name]) : ''}}"
                                    >
                                @else
                                    {{auth()->user()->subProject->name ?? ''}}
                                    <input name="sub_project_id" type="hidden"   id="sub_project_id"
                                           value="{{auth()->user()->sub_project_id}}">
                                @endif
                            </td>
                        </tr>
                        {{--<tr class="form-group">--}}
                            {{--<td class="text-center key">--}}
                                {{--<label for="offer_id" class="control-label">--}}
                                    {{--@lang('general.offer')--}}
                                {{--</label>--}}
                            {{--</td>--}}
                            {{--<td class="value">--}}
                                {{--<input name="offer_id"--}}
                                       {{--id="offer_id"--}}
                                       {{--class="select2"--}}
                                       {{--data-content="{{$order && $order->offer ? json_encode(['id' => $order->offer->id, 'text' => $order->offer->name]) : ''}}"--}}
                                {{-->--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        <tr class="form-group">
                            <td class="text-center key">
                                <label class="col-md-4 control-label" for="comment">
                                    @lang('general.additional-information')
                                </label>
                            </td>
                            <td class="value">
                                <textarea class="form-control" name="comment" id="comment"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="error-messages"></div>
            </div>
        </div>
        @if ($order)
            <input type="hidden" value="{{$order->id}}" name="parent_order_id">
            <div class="hidden order_id">{{ $order->id }}</div>
        @endif
        <div class="row">
            <button class="col-xs-12 btn btn-success" id="btn_create">
                @lang('general.save')
            </button>
        </div>
    </form>
@endsection
