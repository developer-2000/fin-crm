@extends('layouts.app')

@section('title') @lang('general.order') # {{ $orderOne->id }} @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>


    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    {{--<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/tabs.css') }}"/>--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/tabs.sideways.css') }}"/>--}}
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.vertical-tabs.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/moderator-panel.css') }}"/>

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
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nouislider.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js?x=1') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/feedback-add.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nouislider.js') }}"></script>
    <script src="{{ URL::asset('js/orders/moderator-panel.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order-one-monitoring-call.js') }}"></script>
@stop

@section('content')

    <?
    $statusInfo = '';
    switch ($orderOne->proc_status) {
        case 1:
            {
                $statusInfo = 'В обработке';
                break;
            }
        case 2:
            {
                $statusInfo = 'В наборе';
                break;
            }
        case 3:
            {
                $statusInfo = 'Контакт';
                break;
            }
        case 4:
            {
                $statusInfo = 'Повтор';
                break;
            }
        case 5:
            {
                $statusInfo = 'Недозвон';
                break;
            }
        case 6:
            {
                $statusInfo = 'Некорректный номер';
                break;
            }
        case 7:
            {
                $statusInfo = 'Другой язык';
                break;
            }
        case 8:
            {
                $statusInfo = 'Ошибка';
                break;
            }
        case 9:
            {
                $statusInfo = 'Завершен';
                break;
            }
        case 10:
            {
                $statusInfo = 'Подозрительный заказ';
                break;
            }
        case 11:
            {
                $statusInfo = 'Приостановлен';
                break;
            }
        case 13:
            {
                $statusInfo = 'Сбой';
                break;
            }
    }
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div id="content-header" class="clearfix">
                <div class="pull-left">
                    <?
                    $statusInfo = '';
                    switch ($orderOne->proc_status) {
                        case 1:
                            {
                                $statusInfo = 'В обработке';
                                break;
                            }
                        case 2:
                            {
                                $statusInfo = 'В наборе';
                                break;
                            }
                        case 3:
                            {
                                $statusInfo = 'Контакт';
                                break;
                            }
                        case 4:
                            {
                                $statusInfo = 'Повтор';
                                break;
                            }
                        case 5:
                            {
                                $statusInfo = 'Недозвон';
                                break;
                            }
                        case 6:
                            {
                                $statusInfo = 'Некорректный номер';
                                break;
                            }
                        case 7:
                            {
                                $statusInfo = 'Другой язык';
                                break;
                            }
                        case 8:
                            {
                                $statusInfo = 'Ошибка';
                                break;
                            }
                        case 9:
                            {
                                $statusInfo = 'Завершен';
                                break;
                            }
                        case 10:
                            {
                                $statusInfo = 'Подозрительный заказ';
                                break;
                            }
                        case 11:
                            {
                                $statusInfo = 'Приостановлен';
                                break;
                            }
                        case 13:
                            {
                                $statusInfo = 'Сбой';
                                break;
                            }
                    }
                    ?>
                    <ol class="breadcrumb">
                        {{--<li class="active"><span>Заказ #<span--}}
                        {{--class='order_id'>{{ $orderOne->id }}</span><span--}}
                        {{--class="status_info">({{ $statusInfo }})</span></span>--}}
                        {{--</li>--}}
                        <li class="active"><span style="color: white"> @lang('orders.panel-medarator')</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="order">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 main-box clearfix">
                            <h3><span> @lang('orders.incoming-information')</span></h3>

                            <div class="">
                                <table class="table first_info">
                                    <tr>
                                        <td class="text-center key"> @lang('general.offer')</td>
                                        <td class=" value">
                                            {{$orderOne->offer_name}}
                                        </td>
                                    </tr>
                                    @if ($orderOne->source_url)
                                        <tr>
                                            <td class="text-center key"> @lang('general.source')</td>
                                            <td class=" value">
                                                                    <span>  <a target="_blank"
                                                                               href="{{$orderOne->source_url}}">
                                                                        @if (strlen($orderOne->source_url) > 40)
                                                                                {{substr($orderOne->source_url, 0, 40)}}
                                                                                ...
                                                                            @else
                                                                                {{$orderOne->source_url}}
                                                                            @endif
                                                                    </a></span>

                                            </td>
                                        </tr>
                                    @endif
                                    @if ($orderOne->input_data)
                                        <? $inputData = json_decode($orderOne->input_data, true); ?>
                                        @foreach ($inputData as $inDataKey => $inDataValue)
                                            <tr>
                                                <td class="text-center key">
                                                    {{ $inDataKey }}
                                                </td>
                                                <td class=" value">
                                                    @if (is_array($inDataValue))
                                                        @foreach($inDataValue as $value)
                                                            <span>{{ $value }}</span><br>
                                                        @endforeach
                                                    @else
                                                        <span>{{ $inDataValue }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td class="text-center key">
                                            @lang('general.ip-address')
                                        </td>
                                        <td class=" value">
                                            {{ $orderOne->host }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center key"> @lang('general.country')</td>
                                        <td class=" value">
                                            {{$orderOne->country}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center key"> @lang('general.date-created')</td>
                                        <td class="value">{{date('H:i:s d/m/y', $orderOne->time_created)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center key"> @lang('orders.date-set-target')</td>
                                        <td class="value">{{date('H:i:s d/m/y', $orderOne->time_modified)}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="">
                                @if ($recommended_products)
                                    @foreach($recommended_products as $type)
                                        <table class="table product_offer">
                                            <thead>
                                            <tr>
                                                <th class="text-left">
                                                    @if ($type[0]->type == 0)
                                                        @lang('general.products')
                                                    @elseif ($type[0]->type == 1)
                                                        @lang('general.up-sell')
                                                    @elseif($type[0]->type == 2)
                                                        @lang('general.up-sell') 2
                                                    @elseif($type[0]->type == 4)
                                                        @lang('general.cross-sell')
                                                    @endif
                                                </th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($type as $product)
                                                <tr>
                                                    <td class="value">{{$product->name}}</td>
                                                    <td class="text-right ">
                                                        <input type="text" style="width: 60%; display: inline-block;"
                                                               class="form-control price_offer_add"
                                                               data-value="{{$product->price}}"
                                                               value="{{$product->price}}" placeholder=" @lang('general.price')">
                                                        <span class="offer_currency">
                                                    {{$orderOne->currency}}
                                                </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="#" class="table-link">
                                                <span class="fa-stack add_product" data-id="{{$product->product_id}}">
                                                    <i class="fa  fa-plus-square fa-stack-2x"></i>
                                                </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endforeach
                                @endif
                            </div>
                            <div class=" comments_block">
                                <header class="main-box-header clearfix">
                                    <h3 style="border-bottom: none;"><span
                                                style="border-bottom: none;"> @lang('orders.order-comment')</span>
                                    </h3>
                                </header>
                                <div class="main-box-body clearfix" style="padding: 0">
                                    <div class="conversation-wrapper">
                                        <div class="conversation-new-message">
                                            <form onsubmit="return false;">
                                                <div class="form-group">
                                        <textarea class="form-control field_comment" rows="2"
                                                  placeholder="Комментарий..." style="resize:vertical;"></textarea>
                                                </div>
                                                <div class="clearfix text-center">
                                                    <button type="submit" class="btn btn-success add_comment ">
                                                    @lang('general.comment-leave')
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="conversation-content ">
                                            <div class="conversation-inner">
                                                @if ($comments)
                                                    @foreach ($comments as $co)
                                                        <div class="conversation-item item-left clearfix">
                                                            <div class="conversation-user">
                                                                <img src="{{$co->photo}}"
                                                                     alt=""/>
                                                            </div>
                                                            <div class="conversation-body">
                                                                <div class="company_user">{{$co->company}}</div>
                                                                <div class="name" style="max-width: 50%;">
                                                                    {{ $co->name }} ({{ $co->login }})
                                                                </div>
                                                                <div class="time hidden-xs" style="max-width: 50%;">
                                                                    {{ date('Y/m/d H:i:s', (int)$co->date) }}
                                                                </div>
                                                                <div class="text">
                                                                    {{ $co->text }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            <div class="row" style="display: flex; flex-wrap: wrap">
                                <div class="col-lg-11 col-md-11">
                                    <div class="row">
                                        <div class="moderator-panel" style="color: grey;">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="main-box clearfix">
                                                    <div class="main-box-body clearfix">
                                                        {{--<div class="profile-box-header blue-bg clearfix"--}}
                                                        {{--style="height: 5em">--}}
                                                        {{--<h2 style="color: white">Панель модератора</h2>--}}
                                                        {{--</div>--}}
                                                        <div class="order-details">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                <div class="row">
                                                                    <div class="col-sm-4"> @lang('general.order') #</div>
                                                                    <div class="col-sm-8 data-list">{{$orderOne->id}}</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-4"> @lang('general.date'):</div>
                                                                    <div class="col-sm-8 data-list">  {{date('H:i:s d/m/y', $orderOne->time_created)}}</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-4"> @lang('general.status'):</div>
                                                                    <div class="col-sm-8 data-list moderator_block_status">  {{$statusInfo}}</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-4" style="padding-top: 5px">
                                                                        @lang('general.country'):
                                                                    </div>
                                                                    <div class="col-sm-8 data-list">
                                                                        <img class="country-flag"
                                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($orderOne->geo) . '.png')  }}" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                <div class="row">
                                                                    <div class="col-sm-5"> @lang('general.queue'):</div>
                                                                    <div class="col-sm-7 data-list">   {{!empty($orderOne->campaign->name) ? $orderOnee->campaign : 'N/A'}}</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-5"> @lang('orders.count-calls'):</div>
                                                                    <div class="col-sm-7 data-list">    {{$orderOne->proc_stage}}</div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-5"> @lang('orders.next-call'):</div>
                                                                    <div class="col-sm-7 data-list">
                                                                        {{ date('Y/m/d H:i:s', (int)$orderOne->proc_callback_time) }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="main-box clearfix">
                                                    <div class="main-box-body clearfix">
                                                        <div class="" style="padding: 10px">
                                                            <div>
                                                                <div class="" style="padding: 10px;">
                                                                    @php
                                                                        $status = '';
                                                                        $value = NULL;
                                                                        $action = NULL;
                                                                        if($orderOne->proc_status == 2){
                                                                            $status = 'checked';
                                                                            $value = 'stop';
                                                                            $action = trans('general.stop');
                                                                        }
                                                                        elseif($orderOne->proc_status == 11){
                                                                         $status = '';
                                                                        $value = 'add_call';
                                                                        $action = trans('orders.load-callback');
                                                                        }
                                                                        else{
                                                                           $status = '';
                                                                        $value = 'add_call';
                                                                        $action = trans('orders.load-callback');
                                                                        }
                                                                    @endphp
                                                                    {{--@if($orderOne->proc_status == 2 || $orderOne->proc_status == 11)--}}
                                                                    <div class="row data-moderator">
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                            <div id="processing">
                                                                                <div class="table-responsive">
                                                                                    <table class="table "
                                                                                           id="processing">
                                                                                        <thead>
                                                                                        <tr>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="moderator-block">
                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                @lang('orders.load-call')
                                                                            </div>
                                                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                                <div class="form-group upload-cancel">
                                                                                    <div class="checkbox-nice">
                                                                                        <input type="checkbox"
                                                                                               name="addCall"
                                                                                               id="add_call"
                                                                                               value="{{$value}}"
                                                                                               disabled>
                                                                                        <label for="add_call">{{$action}}</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                <input type="hidden" id="proc_status"
                                                                                       value="{{$orderOne->proc_status}}">
                                                                                {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id' => 'addCall'])}}
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    {{--@endif--}}
                                                                    <div class="moderator-block">
                                                                        <div class="row data-moderator">
                                                                            <div class="form-group">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                    <label for="campaign">
                                                                                    @lang('orders.change-queue')</label>
                                                                                </div>
                                                                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                                    <select class="form-control"
                                                                                            name="campaign"
                                                                                            id="campaign">
                                                                                        @if($orderOne->proc_campaign)
                                                                                            @if(!empty($campaigns))
                                                                                                @foreach($campaigns as $key=>$campaign)
                                                                                                    <option
                                                                                                            @if ($orderOne->proc_campaign == $campaign->id)
                                                                                                            selected
                                                                                                            @endif
                                                                                                            value="{{$campaign->id}}">{{$campaign->name }}</option>
                                                                                                @endforeach
                                                                                            @endif
                                                                                        @endif
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                    {{Form::submit('OK', ['class' => 'btn btn-primary pull-right ', 'id' => 'change_campaign'])}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row data-moderator">
                                                                            <div class="form-group">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                    <label for="priority"> @lang('orders.change-priority')
                                                                                        </label></div>
                                                                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                                    <input type="number" name="priority"
                                                                                           class="form-control"
                                                                                           value="{{$orderOne->proc_priority}}"
                                                                                           id="priority">
                                                                                </div>
                                                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                    {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'change_priority'])}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row data-moderator">
                                                                            <div class="form-group">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                    <label for="priority"> @lang('orders.change-logics-callback')</label>
                                                                                </div>
                                                                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">

                                                                                    <div class="slider-minmax noUi-target"></div>
                                                                                    <span class="slider-label"> @lang('orders.stag-callback'): {{$orderOne->proc_stage}}</span>
                                                                                    <span class="proc_stage hidden">{{$orderOne->proc_stage}}</span>
                                                                                </div>
                                                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                    {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'change_stage'])}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="moderator-block">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <div class="main-box clearfix">
                                                                <div class="main-box-body clearfix">
                                                                    <div class="" style="padding: 10px">
                                                                        <header style="font-weight: bold; text-align: center; padding: 5px">
                                                                            @lang('orders.set-time-callback')
                                                                        </header>
                                                                        <div class="row data-moderator">
                                                                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                                                    <label for="callback_date_moderator">
                                                                                    @lang('orders.set-time-callback')
                                                                                  </label></div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                    <div class="checkbox-nice">
                                                                                        <input type="checkbox"
                                                                                               class="add_call_now"
                                                                                               id="add_call_now">
                                                                                        <label for="add_call_now"> @lang('general.now')</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-2 callback_block">
                                                                                    <input name="callback_date_moderator"
                                                                                           type="text"
                                                                                           class="form-control  callback_date_moderator"
                                                                                           id="callback_date_moderator"
                                                                                           placeholder=" @lang('orders.time-callback')">
                                                                                </div>
                                                                                {{--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">--}}
                                                                                {{--{{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'set_call_back'])}}--}}
                                                                                {{--</div>--}}
                                                                            </div>
                                                                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                                <label for="operator">
                                                                                @lang('orders.set-time-callback')
                                                                              </label>
                                                                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                                                    <div class="operators_block">
                                                                                        <select class="form-control"
                                                                                                name="operator"
                                                                                                id="operator">
                                                                                            @if(!empty($operators))
                                                                                                <option value="">
                                                                                                    @lang('orders.select-operator')
                                                                                                </option>
                                                                                                @foreach($operators as $key=>$operator)
                                                                                                    <option
                                                                                                            {{--@if ($orderUser->id == $orderOne->target_user)--}}
                                                                                                            {{--selected--}}
                                                                                                            {{--@endif--}}
                                                                                                            value="{{$operator->login_sip}}">{{$operator->name . ' ' . $operator->surname }}</option>
                                                                                                @endforeach
                                                                                            @endif
                                                                                        </select>
                                                                                    </div>

                                                                                </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                                                    {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'set_call_back_operator'])}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        {{--<div class="row data-moderator">--}}
                                                                        {{--<div class="form-group">--}}
                                                                        {{--<div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">--}}
                                                                        {{--<label for="user_transaction">Изменить--}}
                                                                        {{--транзакцию:</label></div>--}}
                                                                        {{--<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">--}}
                                                                        {{--<select class="form-control" name="user_transaction"--}}
                                                                        {{--id="user_transaction">--}}
                                                                        {{--@if(!empty($orderUsers))--}}
                                                                        {{--@foreach($orderUsers as $key=>$orderUser)--}}
                                                                        {{--<option--}}
                                                                        {{--@if ($orderUser->id == $orderOne->target_user)--}}
                                                                        {{--selected--}}
                                                                        {{--@endif--}}
                                                                        {{--value="{{$orderUser->id}}">{{$orderUser->name . ' ' . $orderUser->surname }}</option>--}}
                                                                        {{--@endforeach--}}
                                                                        {{--@endif--}}
                                                                        {{--</select>--}}
                                                                        {{--</div>--}}
                                                                        {{--<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">--}}
                                                                        {{--{{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'change_transaction'])}}--}}
                                                                        {{--</div>--}}
                                                                        {{--</div>--}}
                                                                        {{--</div>--}}

                                                                        {{Form::hidden('orderId', $orderOne->id)}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12 col-md-12">
                                                        <form id="order_data">
                                                            <div class="main-box clearfix">
                                                                <div class="main-box-body clearfix">
                                                                    <div class="col-md-4 col-sm-6">
                                                                        <div class="form-group ">
                                                                            <label for="surname"> @lang('general.surname')</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="surname" name=" @lang('general.surname')"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="bottom"
                                                                                   title="Фамилия"
                                                                                   value="{{$orderOne->surname}}"
                                                                                   required>
                                                                        </div>
                                                                        <div class="form-group ">
                                                                            <label for="name"> @lang('general.first-name')</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="name" name="name"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="bottom" title=" @lang('general.first-name')"
                                                                                   value="{{$orderOne->name}}"
                                                                                   required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6">
                                                                        <div class="form-group ">
                                                                            <label for="middle"> @lang('general.middle-name')</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="middle" name="middle"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="bottom"
                                                                                   title="Отчество"
                                                                                   value="{{$orderOne->middle}}">
                                                                        </div>
                                                                        <div class="form-group ">
                                                                            <label for="phone"> @lang('general.phone')</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="phone" name=" @lang('general.phone')"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="bottom"
                                                                                   title="Телефон"
                                                                                   value="{{$orderOne->phone}}"
                                                                                   required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-6">
                                                                        <div class="form-group form-group-select2">
                                                                            <label for="country"> @lang('general.country')</label>
                                                                            @if ($country)
                                                                                <select name="country" id=" @lang('general.country')"
                                                                                        style="width: 100%">
                                                                                    @foreach ($country as $oc)
                                                                                        <option data-currency="{{ $oc->currency }}"
                                                                                                value="{{ mb_strtolower($oc->code) }}"
                                                                                                @if ($oc->code == strtoupper($orderOne->geo)) selected @endif>{{ $oc->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </div>
                                                                        <div class="form-group col-md-6">
                                                                            <label for="age"> @lang('general.age')</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="age" name=" @lang('general.age')"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="bottom"
                                                                                   title="Возраст" value="">
                                                                        </div>
                                                                        <div class="form-group col-md-6">
                                                                            <label for="gender"> @lang('general.gender')</label>
                                                                            <select name="gender" id="gender"
                                                                                    class="form-control">
                                                                                <option value="0">Пол</option>
                                                                                <option value="1"> @lang('general.male')</option>
                                                                                <option value="2"> @lang('general.female')</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="main-box clearfix" style="position:relative;">
                                                                <header class="main-box-header clearfix">
                                                                    <h2 class="pull-left"
                                                                        style="color: #929292;">{{$orderOne->offer_name}}</h2>
                                                                    <div class="filter-block pull-right">
                                                                        <div class="form-group pull-left">
                                                                            <input type="text"
                                                                                   class="form-control search"
                                                                                   placeholder=" @lang('general.search')...">
                                                                            <i class="fa fa-search search-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                </header>
                                                                <div class="table-responsive search_block">
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table class="table table_products">
                                                                        <thead>
                                                                        <tr>
                                                                            <th> @lang('general.first-name')</th>
                                                                            <th class="text-center"> @lang('general.warehouse')</th>
                                                                            <th class="text-center"> @lang('general.up-sell')</th>
                                                                            <th class="text-center"> @lang('general.up-sell') 2</th>
                                                                            <th class="text-center"> @lang('general.cross-sell')</th>
                                                                            <th class="text-center"> @lang('general.note')</th>
                                                                            <th class="text-center"> @lang('general.price')</th>
                                                                            <th></th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @if ($offers)
                                                                            @foreach ($offers as $offer)
                                                                                <tr @if ($offer->disabled) class="warning"
                                                                                    @endif data-id="{{ $offer->ooid }}">
                                                                                    <td class="value">
                                                                                        {{ $offer->title }}
                                                                                    </td>
                                                                                    <td class="text-center"><img
                                                                                                src="{{ URL::asset('img/stock_1.png') }}">
                                                                                    </td>
                                                                                    @if ($offer->type == 1 || $offer->type == 2 || $offer->type == 3 || $offer->type == 4)
                                                                                        <td class="text-center">
                                                                                            <div class="checkbox-nice">
                                                                                                <input type="checkbox"
                                                                                                       id="up_sell_{{ $offer->ooid }}"
                                                                                                       class="up_cross_sell"
                                                                                                       value="1"
                                                                                                       name="products[{{$offer->ooid}}][up1]"
                                                                                                       @if ($offer->type == 1)
                                                                                                       checked
                                                                                                        @endif
                                                                                                >
                                                                                                <label for="up_sell_{{ $offer->ooid }}"></label>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <div class="checkbox-nice">
                                                                                                <input type="checkbox"
                                                                                                       id="up_sell_2{{ $offer->ooid }}"
                                                                                                       class="up_cross_sell"
                                                                                                       value="2"
                                                                                                       name="products[{{$offer->ooid}}][up2]"
                                                                                                       @if ($offer->type == 2)
                                                                                                       checked
                                                                                                        @endif
                                                                                                >
                                                                                                <label for="up_sell_2{{ $offer->ooid }}"></label>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <div class="checkbox-nice">
                                                                                                <input type="checkbox"
                                                                                                       name="products[{{$offer->ooid}}][cross]"
                                                                                                       id="cross_sell_{{ $offer->ooid }}"
                                                                                                       class="up_cross_sell"
                                                                                                       value="4"
                                                                                                       @if ($offer->type == 4)
                                                                                                       checked
                                                                                                        @endif
                                                                                                >
                                                                                                <label for="cross_sell_{{ $offer->ooid }}"></label>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="comments">
                                                                                            @if (!$offer->disabled)
                                                                                                <a href="#"
                                                                                                   data-pk="{{$offer->ooid}}"
                                                                                                   data-title=" @lang('general.enter') @lang('general.note')"
                                                                                                   class="editable editable-pre-wrapped editable-click product_comments">{{$offer->comment}}</a>
                                                                                            @endif
                                                                                        </td>
                                                                                    @else
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td class="comments">
                                                                                            @if (!$offer->disabled)
                                                                                                <a href="#"
                                                                                                   data-pk="{{$offer->ooid}}"
                                                                                                   data-title=" @lang('general.enter') @lang('general.note')"
                                                                                                   class="editable editable-pre-wrapped editable-click product_comments">{{$offer->comment}}</a>
                                                                                            @endif
                                                                                        </td>
                                                                                    @endif
                                                                                    <td class="text-center">
                                                                                        <input type="hidden"
                                                                                               name="products[{{$offer->ooid}}][id]"
                                                                                               value="{{$offer->ooid}}">
                                                                                        <input type="hidden"
                                                                                               name="products[{{$offer->ooid}}][disabled]"
                                                                                               value="{{$offer->disabled}}">
                                                                                        @if ($offer->disabled)
                                                                                            {{ $offer->price }}
                                                                                        @else
                                                                                            <input type="text"
                                                                                                   style="width: 70px; display: inline-block;"
                                                                                                   class="form-control price_offer"
                                                                                                   data-value="{{ $offer->price }}"
                                                                                                   value="{{ $offer->price }}"
                                                                                                   placeholder=" @lang('general.price')"
                                                                                                   name="products[{{$offer->ooid}}][price]"
                                                                                            >
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        @if (!$offer->disabled)
                                                                                            <a href="#"
                                                                                               class="table-link danger delete_product">
                                                        <span class="fa-stack " data-id="{{ $offer->ooid }}">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                                                            </a>
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                            <tr>
                                                                                <td class="value text-center"> @lang('general.total')</td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td class="text-center"
                                                                                    id="total_price">{{$orderOne->price_total}}</td>
                                                                                <td class="text-center">
                                                                                    @if (isset($country[$orderOne->geo]))
                                                                                        {{$country[$orderOne->geo]->currency}}
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="result">
                                                                <header class="main-box-header clearfix">
                                                                    <h3 style="border-bottom: none;"><span
                                                                                style="border-bottom: none;"> @lang('orders.result-call')</span>
                                                                    </h3>
                                                                </header>
                                                                <div class="tabs-wrapper targets">
                                                                    <ul class="nav nav-tabs">
                                                                        <li class=" target @if ($orderOne->target_status == 1) active @endif ">
                                                                            <a
                                                                                    href="#approve"
                                                                                    data-toggle="tab"
                                                                                    class="approve "> @lang('general.approved')
                                                                                <input type="radio" name="target_status"
                                                                                       value="1"
                                                                                       @if ($orderOne->target_status == 1) checked @endif>
                                                                            </a>
                                                                            <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                                                                        </li>
                                                                        <li class=" target @if ($orderOne->target_status == 2) active @endif ">
                                                                            <a
                                                                                    href="#failure"
                                                                                    data-toggle="tab"
                                                                                    class="failure "> @lang('general.refusal')
                                                                                <input type="radio" name="target_status"
                                                                                       value="2"
                                                                                       @if ($orderOne->target_status == 2) checked @endif>
                                                                            </a>
                                                                            <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                                                                        </li>
                                                                        <li class=" target @if ($orderOne->target_status == 3) active @endif ">
                                                                            <a
                                                                                    href="#fake"
                                                                                    data-toggle="tab"
                                                                                    class="fake"> @lang('general.annulled')
                                                                                <input type="radio" name="target_status"
                                                                                       value="3"
                                                                                       @if ($orderOne->target_status == 3) checked @endif>
                                                                            </a>
                                                                            <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                                                                        </li>
                                                                    </ul>
                                                                    <input type="radio" name="target_status"
                                                                           id="target_status_def" value="0"
                                                                           @if ($orderOne->target_status == 0) checked @endif>
                                                                    <div class="tab-content">
                                                                        <div class="tab-pane fade @if ($orderOne->target_status == 1) in active @endif"
                                                                             id="approve">
                                                                            <div class="main-box clearfix">
                                                                                <div class="main-box-body clearfix text-center"
                                                                                     style="padding-top: 20px;">
                                                                                    @lang('general.approved')
                                                                                    <select name="target_approve"
                                                                                            class="form-control target">
                                                                                        <option value=""> @lang('general.select')
                                                                                        </option>
                                                                                        @if ($targets_approve)
                                                                                            @foreach($targets_approve as $target)
                                                                                                <option value="{{$target->id}}"
                                                                                                        @if ($target->id == $orderOne->target_approve) selected @endif>{{$target->name}}</option>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </select>
                                                                                </div>
                                                                                <div class="main-box-body clearfix target_block">
                                                                                    <p class="text-center title_tab_content">
                                                                                      @lang('orders.fill-data-order')
                                                                                        </p>
                                                                                    <div class="target_fields form-horizontal">
                                                                                        @if ($target_option['approve'])
                                                                                            @if ($target_value && $orderOne->target_status == 1)
                                                                                                @if($target_value->target_id == $orderOne->target_approve  && $target_option['approve']->id == $target_value->target_id)
                                                                                                    {{renderTarget(json_decode($target_value->values), 'approve[', ']', $target_option['approve']->alias)}}
                                                                                                @else
                                                                                                    {{renderTarget(json_decode($target_value->values), 'approve[', ']')}}
                                                                                                @endif
                                                                                            @else
                                                                                                {{renderTarget(json_decode($target_option['approve']->options), 'approve[', ']', $target_option['approve']->alias)}}
                                                                                            @endif
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade @if ($orderOne->target_status == 2) in active @endif"
                                                                             id="failure">
                                                                            <div class="main-box clearfix">
                                                                                <div class="main-box-body clearfix text-center"
                                                                                     style="padding-top: 20px;">
                                                                                    @lang('orders.change-target')
                                                                                    <select name="target_refuse"
                                                                                            class="form-control target">
                                                                                        <option value=""> @lang('general.select')
                                                                                        </option>
                                                                                        @if ($targets_refuse)
                                                                                            @foreach($targets_refuse as $target)
                                                                                                <option value="{{$target->id}}"
                                                                                                        @if ($target->id == $orderOne->target_refuse) selected @endif>{{$target->name}}</option>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </select>
                                                                                </div>
                                                                                <div class="main-box-body clearfix target_block">
                                                                                    <p class="text-center title_tab_content">
                                                                                        Опишите причину
                                                                                        отказа</p>
                                                                                    <div class="target_fields form-horizontal">
                                                                                        @if ($target_option['refuse'])
                                                                                            @if($target_value && $orderOne->target_status == 2)
                                                                                                @if($target_value->target_id == $orderOne->target_refuse && $target_option['refuse']->id == $target_value->target_id)
                                                                                                    {{renderTarget(json_decode($target_value->values), 'refuse[', ']', $target_option['refuse']->alias)}}
                                                                                                @else
                                                                                                    {{renderTarget(json_decode($target_value->values), 'refuse[', ']')}}
                                                                                                @endif
                                                                                            @else
                                                                                                {{renderTarget(json_decode($target_option['refuse']->options), 'refuse[', ']', $target_option['refuse']->alias)}}
                                                                                            @endif
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade @if ($orderOne->target_status == 3) in active @endif"
                                                                             id="fake">
                                                                            <div class="main-box clearfix">
                                                                                <div class="main-box-body clearfix text-center"
                                                                                     style="padding-top: 20px;">
                                                                                    Сменить цель
                                                                                    <select name="target_cancel"
                                                                                            class="form-control target">
                                                                                        <option value="">Выберите
                                                                                        </option>
                                                                                        @if ($targets_cancel)
                                                                                            @foreach($targets_cancel as $target)
                                                                                                <option value="{{$target->id}}"
                                                                                                        @if ($target->id == $orderOne->target_cancel) selected @endif>{{$target->name}}</option>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </select>
                                                                                </div>
                                                                                <div class="main-box-body clearfix target_block">
                                                                                    <p class="text-center title_tab_content">
                                                                                        Заполните данные для
                                                                                        аннулировки</p>
                                                                                    <div class="target_fields form-horizontal">
                                                                                        @if ($target_option['cancel'])
                                                                                            @if($target_value && $orderOne->target_status == 3)
                                                                                                @if($target_value->target_id == $orderOne->target_cancel && $target_option['cancel']->id == $target_value->target_id)
                                                                                                    {{renderTarget(json_decode($target_value->values), 'cancel[', ']', $target_option['cancel']->alias)}}
                                                                                                @else
                                                                                                    {{renderTarget(json_decode($target_value->values), 'cancel[', ']')}}
                                                                                                @endif
                                                                                            @else
                                                                                                {{renderTarget(json_decode($target_option['cancel']->options), 'cancel[', ']', $target_option['cancel']->alias)}}
                                                                                            @endif
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="tab-pane fade @if ($orderOne->target_status == 0) in active @endif"
                                                                             id="def">
                                                                            <div class="main-box clearfix">
                                                                                <div class="main-box-body clearfix">
                                                                                    @if ($orderOne->proc_callback_time && $orderOne->proc_priority >= $orderOne->proc_stage)
                                                                                        <div style="text-align: center; margin-top: 25px">
                                                                                            Перезвонить
                                                                                            "Автоответчик"/"В ближайшее
                                                                                            время"
                                                                                        </div>
                                                                                    @elseif ($orderOne->proc_callback_time )
                                                                                        <div style="text-align: center; margin-top: 25px">
                                                                                            Перезвонить {{date('H:i d/m/y', $orderOne->proc_callback_time)}}</div>
                                                                                    @endif
                                                                                    <div class="col-sm-offset-3">
                                                                                        <ul>
                                                                                            <li>
                                                                                                <div class="checkbox-nice">
                                                                                                    <input type="checkbox"
                                                                                                           class="call_status"
                                                                                                           name="proc_status"
                                                                                                           id="another_language"
                                                                                                           value="5">
                                                                                                    <label for="another_language"
                                                                                                           class="target_radio">Говорит
                                                                                                        на
                                                                                                        другом
                                                                                                        языке</label>
                                                                                                </div>
                                                                                            </li>
                                                                                            <li>
                                                                                                <div class="checkbox-nice">
                                                                                                    <input type="checkbox"
                                                                                                           class='call_status'
                                                                                                           id="callback_status_1"
                                                                                                           value="1"
                                                                                                           name="proc_status">
                                                                                                    <label for="callback_status_1">Автоответчик</label>
                                                                                                </div>
                                                                                            </li>
                                                                                            <li>
                                                                                                <div class="checkbox-nice">
                                                                                                    <input type="checkbox"
                                                                                                           class='call_status'
                                                                                                           id="callback_status_2"
                                                                                                           value="2"
                                                                                                           name="proc_status">
                                                                                                    <label for="callback_status_2">Плохая
                                                                                                        связь</label>
                                                                                                </div>
                                                                                                <ul style="padding-top: 0;display: none"
                                                                                                    class="call_now">
                                                                                                    <li>
                                                                                                        <div class="checkbox-nice">
                                                                                                            <input type="checkbox"
                                                                                                                   class="callback_status_ext"
                                                                                                                   id="now_1"
                                                                                                                   value="1"
                                                                                                                   name="now">
                                                                                                            <label for="now_1">Сейчас</label>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                    <li>
                                                                                                        <div class="checkbox-nice">
                                                                                                            <input type="checkbox"
                                                                                                                   class="callback_status_ext"
                                                                                                                   id="now_2"
                                                                                                                   value="2"
                                                                                                                   name="now">
                                                                                                            <label for="now_2">Ближайшее
                                                                                                                время</label>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </li>
                                                                                            <li>
                                                                                                <div class="checkbox-nice">
                                                                                                    <input type="checkbox"
                                                                                                           class='call_status'
                                                                                                           id="callback_status_4"
                                                                                                           name="proc_status"
                                                                                                           value="3">
                                                                                                    <label for="callback_status_4">Просит
                                                                                                        перезвонить</label>
                                                                                                </div>
                                                                                                <ul style="padding-top: 0;display: none"
                                                                                                    class="call_now">
                                                                                                    <li>
                                                                                                        <div class="form-group">
                                                                                                            <input type="text"
                                                                                                                   class="form-control  callback_date"
                                                                                                                   id="input_date_4"
                                                                                                                   placeholder="Время перезвона"
                                                                                                                   name="callback_time">
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="suspicious_block">
                                                                    <div class="checkbox-nice">
                                                                        <input type="checkbox" name="suspicious"
                                                                               id="suspicious"
                                                                               @if ($orderOne->proc_status == 10) checked @endif>
                                                                        <label for="suspicious">
                                                                            Подозрительный заказ
                                                                        </label>
                                                                    </div>
                                                                    <div id="suspicious_comment"
                                                                         style="display: @if ($orderOne->proc_status == 10)  block @else none @endif">
                                                                        <div class="pull-left name">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->surname}} {{$suspicious_comment->name}}@endif</div>
                                                                        <div class="pull-right">@if ($orderOne->proc_status == 10 && $suspicious_comment){{date('H:i:s d/m/y', $suspicious_comment->date)}}@endif</div>
                                                                        <div class="form-group">
                                            <textarea name="suspicious_comment"
                                                      class="form-control">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->text}}@endif</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if(isset($permissions['moderator_changes']))
                                                                <div class="row">
                                                                    <div class="moderator-panel" style="color: grey;">
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                            <div class="main-box clearfix profile-box-contact"
                                                                                 style="height: 10em">
                                                                                <div class="main-box-body clearfix">
                                                                                    <div class="profile-box-header blue-bg clearfix"
                                                                                         style="height: 5em">
                                                                                        <h2 style="color: white">Панель
                                                                                            модератора</h2>
                                                                                    </div>
                                                                                    <div class="order-details">
                                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                                            <div class="row">
                                                                                                <div class="col-sm-4">
                                                                                                    Заказ #
                                                                                                </div>
                                                                                                <div class="col-sm-8 data-list">{{$orderOne->id}}</div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-sm-4">
                                                                                                    Дата:
                                                                                                </div>
                                                                                                <div class="col-sm-8 data-list">  {{date('H:i:s d/m/y', $orderOne->time_created)}}</div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-sm-4">
                                                                                                    Статус:
                                                                                                </div>
                                                                                                <div class="col-sm-8 data-list moderator_block_status">  {{$statusInfo}}</div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-sm-4"
                                                                                                     style="padding-top: 5px">
                                                                                                    Страна:
                                                                                                </div>
                                                                                                <div class="col-sm-8 data-list">
                                                                                                    <img class="country-flag"
                                                                                                         src="{{ URL::asset('img/flags/' . mb_strtoupper($orderOne->geo) . '.png') }}" />
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                                            <div class="row">
                                                                                                <div class="col-sm-5">
                                                                                                    Очередь:
                                                                                                </div>
                                                                                                <div class="col-sm-7 data-list">   {{!empty($orderOne->campaign->name) ? $orderOnee->campaign : 'N/A'}}</div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-sm-5">
                                                                                                    Кол-во звонков:
                                                                                                </div>
                                                                                                <div class="col-sm-7 data-list">    {{$orderOne->proc_stage}}</div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-sm-5">
                                                                                                    Следующий звонок:
                                                                                                </div>
                                                                                                <div class="col-sm-7 data-list">
                                                                                                    {{ date('Y/m/d H:i:s', (int)$orderOne->proc_callback_time) }}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                            <div class="main-box clearfix profile-box-menu">
                                                                                <div class="main-box-body clearfix">
                                                                                    <div class="" style="padding: 10px">
                                                                                        <div>
                                                                                            <div class=""
                                                                                                 style="padding: 10px;">
                                                                                                @php
                                                                                                    $status = '';
                                                                                                    $value = NULL;
                                                                                                    $action = NULL;
                                                                                                    if($orderOne->proc_status == 2){
                                                                                                        $status = 'checked';
                                                                                                        $value = 'stop';
                                                                                                        $action = 'Остановить';
                                                                                                    }
                                                                                                    elseif($orderOne->proc_status == 11){
                                                                                                     $status = '';
                                                                                                    $value = 'add_call';
                                                                                                    $action = 'Загрузить на прозвон';
                                                                                                    }
                                                                                                    else{
                                                                                                       $status = '';
                                                                                                    $value = 'add_call';
                                                                                                    $action = 'Загрузить на прозвон';
                                                                                                    }
                                                                                                @endphp
                                                                                                {{--@if($orderOne->proc_status == 2 || $orderOne->proc_status == 11)--}}
                                                                                                <div class="row data-moderator">
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                        <div id="processing">
                                                                                                            <div class="table-responsive">
                                                                                                                <table class="table "
                                                                                                                       id="processing">
                                                                                                                    <thead>
                                                                                                                    <tr>
                                                                                                                    </tr>
                                                                                                                    </thead>
                                                                                                                    <tbody>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="moderator-block">
                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                                            Загрузить
                                                                                                            на
                                                                                                            прозвон
                                                                                                        </div>
                                                                                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                                                            <div class="form-group upload-cancel">
                                                                                                                <div class="checkbox-nice">
                                                                                                                    <input type="checkbox"
                                                                                                                           name="addCall"
                                                                                                                           id="add_call"
                                                                                                                           value="{{$value}}"
                                                                                                                           disabled>
                                                                                                                    <label for="add_call">{{$action}}</label>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                                            <input type="hidden"
                                                                                                                   id="proc_status"
                                                                                                                   value="{{$orderOne->proc_status}}">
                                                                                                            {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id' => 'addCall'])}}
                                                                                                        </div>
                                                                                                    </div>

                                                                                                </div>
                                                                                                {{--@endif--}}
                                                                                                <div class="moderator-block">
                                                                                                    <div class="row data-moderator">
                                                                                                        <div class="form-group">
                                                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                                                <label for="campaign">Сменить
                                                                                                                    очередь</label>
                                                                                                            </div>
                                                                                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                                                                <select class="form-control"
                                                                                                                        name="campaign"
                                                                                                                        id="campaign">
                                                                                                                    @if($orderOne->proc_campaign)
                                                                                                                        @if(!empty($campaigns))
                                                                                                                            @foreach($campaigns as $key=>$campaign)
                                                                                                                                <option
                                                                                                                                        @if ($orderOne->proc_campaign == $campaign->id)
                                                                                                                                        selected
                                                                                                                                        @endif
                                                                                                                                        value="{{$campaign->id}}">{{$campaign->name }}</option>
                                                                                                                            @endforeach
                                                                                                                        @endif
                                                                                                                    @endif
                                                                                                                </select>
                                                                                                            </div>
                                                                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                                                {{Form::submit('OK', ['class' => 'btn btn-primary pull-right ', 'id' => 'change_campaign'])}}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row data-moderator">
                                                                                                        <div class="form-group">
                                                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                                                <label for="priority">
                                                                                                                    Изменить
                                                                                                                    приоритет</label>
                                                                                                            </div>
                                                                                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                                                                <input type="number"
                                                                                                                       name="priority"
                                                                                                                       class="form-control"
                                                                                                                       value="{{$orderOne->proc_priority}}"
                                                                                                                       id="priority">
                                                                                                            </div>
                                                                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                                                {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'change_priority'])}}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row data-moderator">
                                                                                                        <div class="form-group">
                                                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                                                <label for="priority">
                                                                                                                    Изменить
                                                                                                                    логику
                                                                                                                    прозвона</label>
                                                                                                            </div>
                                                                                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">

                                                                                                                <div class="slider-minmax noUi-target"></div>
                                                                                                                <span class="slider-label">Этап прозвона: {{$orderOne->proc_stage}}</span>
                                                                                                                <span class="proc_stage hidden">{{$orderOne->proc_stage}}</span>
                                                                                                            </div>
                                                                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                                                {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'change_stage'])}}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="moderator-block">
                                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                    <div class="main-box clearfix profile-box-menu">
                                                                                        <div class="main-box-body clearfix">
                                                                                            <div class=""
                                                                                                 style="padding: 10px">
                                                                                                <header style="font-weight: bold; text-align: center; padding: 5px">
                                                                                                    Установить время
                                                                                                    прозвона и оператора
                                                                                                </header>
                                                                                                <div class="row data-moderator">
                                                                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                                                                            <label for="callback_date_moderator">Установить
                                                                                                                время
                                                                                                                прозвона</label>
                                                                                                        </div>
                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                                            <div class="checkbox-nice">
                                                                                                                <input type="checkbox"
                                                                                                                       class="add_call_now"
                                                                                                                       id="add_call_now">
                                                                                                                <label for="add_call_now">Сейчас</label>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-2 callback_block">
                                                                                                            <input name="callback_date_moderator"
                                                                                                                   type="text"
                                                                                                                   class="form-control  callback_date_moderator"
                                                                                                                   id="callback_date_moderator"
                                                                                                                   placeholder="Время перезвона">
                                                                                                        </div>
                                                                                                        {{--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">--}}
                                                                                                        {{--{{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'set_call_back'])}}--}}
                                                                                                        {{--</div>--}}
                                                                                                    </div>
                                                                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                                                        <label for="operator">Установить
                                                                                                            оператора на
                                                                                                            прозвон</label>
                                                                                                        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                                                                            <div class="operators_block">
                                                                                                                <select class="form-control"
                                                                                                                        name="operator"
                                                                                                                        id="operator">
                                                                                                                    @if(!empty($operators))
                                                                                                                        <option value="">
                                                                                                                            Выберите
                                                                                                                            оператора
                                                                                                                        </option>
                                                                                                                        @foreach($operators as $key=>$operator)
                                                                                                                            <option
                                                                                                                                    {{--@if ($orderUser->id == $orderOne->target_user)--}}
                                                                                                                                    {{--selected--}}
                                                                                                                                    {{--@endif--}}
                                                                                                                                    value="{{$operator->login_sip}}">{{$operator->name . ' ' . $operator->surname }}</option>
                                                                                                                        @endforeach
                                                                                                                    @endif
                                                                                                                </select>
                                                                                                            </div>

                                                                                                        </div>
                                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                                                                            {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'set_call_back_operator'])}}
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                {{--<div class="row data-moderator">--}}
                                                                                                {{--<div class="form-group">--}}
                                                                                                {{--<div class="col-lg-4 col-md-6 col-sm-6 col-xs-6">--}}
                                                                                                {{--<label for="user_transaction">Изменить--}}
                                                                                                {{--транзакцию:</label></div>--}}
                                                                                                {{--<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">--}}
                                                                                                {{--<select class="form-control" name="user_transaction"--}}
                                                                                                {{--id="user_transaction">--}}
                                                                                                {{--@if(!empty($orderUsers))--}}
                                                                                                {{--@foreach($orderUsers as $key=>$orderUser)--}}
                                                                                                {{--<option--}}
                                                                                                {{--@if ($orderUser->id == $orderOne->target_user)--}}
                                                                                                {{--selected--}}
                                                                                                {{--@endif--}}
                                                                                                {{--value="{{$orderUser->id}}">{{$orderUser->name . ' ' . $orderUser->surname }}</option>--}}
                                                                                                {{--@endforeach--}}
                                                                                                {{--@endif--}}
                                                                                                {{--</select>--}}
                                                                                                {{--</div>--}}
                                                                                                {{--<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">--}}
                                                                                                {{--{{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'change_transaction'])}}--}}
                                                                                                {{--</div>--}}
                                                                                                {{--</div>--}}
                                                                                                {{--</div>--}}

                                                                                                {{Form::hidden('orderId', $orderOne->id)}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="error-messages" style="display: none;">

                                                            </div>
                                                            <div class="main-box-body clearfix text-center">
                                                                <button type="button" class="btn btn-success"
                                                                        id="save_order">
                                                                    <span class="fa fa-save"></span> Сохранить
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <div class="">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="result">
                                                            <header class="main-box-header clearfix">
                                                                <h3 style="border-bottom: none;"><span
                                                                            style="border-bottom: none;">Результат звонка</span>
                                                                </h3>
                                                            </header>
                                                            <div class="tabs-wrapper targets">
                                                                <ul class="nav nav-tabs goals">
                                                                    <li class=" target @if ($orderOne->target_status == 1) active @endif ">
                                                                        <a
                                                                                href="#approve"
                                                                                data-toggle="tab"
                                                                                class="approve ">Подвержден
                                                                            <input type="radio" name="target_status"
                                                                                   value="1"
                                                                                   @if ($orderOne->target_status == 1) checked @endif>
                                                                        </a>
                                                                        <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                                                                    </li>
                                                                    <li class=" target @if ($orderOne->target_status == 2) active @endif ">
                                                                        <a
                                                                                href="#failure"
                                                                                data-toggle="tab"
                                                                                class="failure ">Отказ
                                                                            <input type="radio" name="target_status"
                                                                                   value="2"
                                                                                   @if ($orderOne->target_status == 2) checked @endif>
                                                                        </a>
                                                                        <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                                                                    </li>
                                                                    <li class=" target @if ($orderOne->target_status == 3) active @endif ">
                                                                        <a
                                                                                href="#fake"
                                                                                data-toggle="tab"
                                                                                class="fake">Аннулирован
                                                                            <input type="radio" name="target_status"
                                                                                   value="3"
                                                                                   @if ($orderOne->target_status == 3) checked @endif>
                                                                        </a>
                                                                        <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                                                                    </li>
                                                                </ul>
                                                                <input type="radio" name="target_status"
                                                                       id="target_status_def" value="0"
                                                                       @if ($orderOne->target_status == 0) checked @endif>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane fade @if ($orderOne->target_status == 1) in active @endif"
                                                                         id="approve">
                                                                        <div class="main-box clearfix">
                                                                            <div class="main-box-body clearfix text-center"
                                                                                 style="padding-top: 20px;">
                                                                                Сменить цель
                                                                                <select name="target_approve"
                                                                                        class="form-control target">
                                                                                    <option value="">Выберите</option>
                                                                                    @if ($targets_approve)
                                                                                        @foreach($targets_approve as $target)
                                                                                            <option value="{{$target->id}}"
                                                                                                    @if ($target->id == $orderOne->target_approve) selected @endif>{{$target->name}}</option>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </select>
                                                                            </div>
                                                                            <div class="main-box-body clearfix target_block">
                                                                                <p class="text-center title_tab_content">
                                                                                    Заполните данные по
                                                                                    заказу</p>
                                                                                <div class="target_fields form-horizontal">
                                                                                    @if ($target_option['approve'])
                                                                                        @if ($target_value && $orderOne->target_status == 1)
                                                                                            @if($target_value->target_id == $orderOne->target_approve  && $target_option['approve']->id == $target_value->target_id)
                                                                                                {{renderTarget(json_decode($target_value->values), 'approve[', ']', $target_option['approve']->alias)}}
                                                                                            @else
                                                                                                {{renderTarget(json_decode($target_value->values), 'approve[', ']')}}
                                                                                            @endif
                                                                                        @else
                                                                                            {{renderTarget(json_decode($target_option['approve']->options), 'approve[', ']', $target_option['approve']->alias)}}
                                                                                        @endif
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade @if ($orderOne->target_status == 2) in active @endif"
                                                                         id="failure">
                                                                        <div class="main-box clearfix">
                                                                            <div class="main-box-body clearfix text-center"
                                                                                 style="padding-top: 20px;">
                                                                                Сменить цель
                                                                                <select name="target_refuse"
                                                                                        class="form-control target">
                                                                                    <option value="">Выберите</option>
                                                                                    @if ($targets_refuse)
                                                                                        @foreach($targets_refuse as $target)
                                                                                            <option value="{{$target->id}}"
                                                                                                    @if ($target->id == $orderOne->target_refuse) selected @endif>{{$target->name}}</option>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </select>
                                                                            </div>
                                                                            <div class="main-box-body clearfix target_block">
                                                                                <p class="text-center title_tab_content">
                                                                                    Опишите причину
                                                                                    отказа</p>
                                                                                <div class="target_fields form-horizontal">
                                                                                    @if ($target_option['refuse'])
                                                                                        @if($target_value && $orderOne->target_status == 2)
                                                                                            @if($target_value->target_id == $orderOne->target_refuse && $target_option['refuse']->id == $target_value->target_id)
                                                                                                {{renderTarget(json_decode($target_value->values), 'refuse[', ']', $target_option['refuse']->alias)}}
                                                                                            @else
                                                                                                {{renderTarget(json_decode($target_value->values), 'refuse[', ']')}}
                                                                                            @endif
                                                                                        @else
                                                                                            {{renderTarget(json_decode($target_option['refuse']->options), 'refuse[', ']', $target_option['refuse']->alias)}}
                                                                                        @endif
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade @if ($orderOne->target_status == 3) in active @endif"
                                                                         id="fake">
                                                                        <div class="main-box clearfix">
                                                                            <div class="main-box-body clearfix text-center"
                                                                                 style="padding-top: 20px;">
                                                                                Сменить цель
                                                                                <select name="target_cancel"
                                                                                        class="form-control target">
                                                                                    <option value="">Выберите</option>
                                                                                    @if ($targets_cancel)
                                                                                        @foreach($targets_cancel as $target)
                                                                                            <option value="{{$target->id}}"
                                                                                                    @if ($target->id == $orderOne->target_cancel) selected @endif>{{$target->name}}</option>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </select>
                                                                            </div>
                                                                            <div class="main-box-body clearfix target_block">
                                                                                <p class="text-center title_tab_content">
                                                                                    Заполните данные для
                                                                                    аннулировки</p>
                                                                                <div class="target_fields form-horizontal">
                                                                                    @if ($target_option['cancel'])
                                                                                        @if($target_value && $orderOne->target_status == 3)
                                                                                            @if($target_value->target_id == $orderOne->target_cancel && $target_option['cancel']->id == $target_value->target_id)
                                                                                                {{renderTarget(json_decode($target_value->values), 'cancel[', ']', $target_option['cancel']->alias)}}
                                                                                            @else
                                                                                                {{renderTarget(json_decode($target_value->values), 'cancel[', ']')}}
                                                                                            @endif
                                                                                        @else
                                                                                            {{renderTarget(json_decode($target_option['cancel']->options), 'cancel[', ']', $target_option['cancel']->alias)}}
                                                                                        @endif
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="tab-pane fade @if ($orderOne->target_status == 0) in active @endif"
                                                                         id="def">
                                                                        <div class="main-box clearfix">
                                                                            <div class="main-box-body clearfix">
                                                                                @if ($orderOne->proc_callback_time && $orderOne->proc_priority >= $orderOne->proc_stage)
                                                                                    <div style="text-align: center; margin-top: 25px">
                                                                                        Перезвонить
                                                                                        "Автоответчик"/"В ближайшее
                                                                                        время"
                                                                                    </div>
                                                                                @elseif ($orderOne->proc_callback_time )
                                                                                    <div style="text-align: center; margin-top: 25px">
                                                                                        Перезвонить {{date('H:i d/m/y', $orderOne->proc_callback_time)}}</div>
                                                                                @endif
                                                                                <div class="col-sm-offset-3">
                                                                                    <ul>
                                                                                        <li>
                                                                                            <div class="checkbox-nice">
                                                                                                <input type="checkbox"
                                                                                                       class="call_status"
                                                                                                       name="proc_status"
                                                                                                       id="another_language"
                                                                                                       value="5">
                                                                                                <label for="another_language"
                                                                                                       class="target_radio">Говорит
                                                                                                    на
                                                                                                    другом языке</label>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div class="checkbox-nice">
                                                                                                <input type="checkbox"
                                                                                                       class='call_status'
                                                                                                       id="callback_status_1"
                                                                                                       value="1"
                                                                                                       name="proc_status">
                                                                                                <label for="callback_status_1">Автоответчик</label>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div class="checkbox-nice">
                                                                                                <input type="checkbox"
                                                                                                       class='call_status'
                                                                                                       id="callback_status_2"
                                                                                                       value="2"
                                                                                                       name="proc_status">
                                                                                                <label for="callback_status_2">Плохая
                                                                                                    связь</label>
                                                                                            </div>
                                                                                            <ul style="padding-top: 0;display: none"
                                                                                                class="call_now">
                                                                                                <li>
                                                                                                    <div class="checkbox-nice">
                                                                                                        <input type="checkbox"
                                                                                                               class="callback_status_ext"
                                                                                                               id="now_1"
                                                                                                               value="1"
                                                                                                               name="now">
                                                                                                        <label for="now_1">Сейчас</label>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <li>
                                                                                                    <div class="checkbox-nice">
                                                                                                        <input type="checkbox"
                                                                                                               class="callback_status_ext"
                                                                                                               id="now_2"
                                                                                                               value="2"
                                                                                                               name="now">
                                                                                                        <label for="now_2">Ближайшее
                                                                                                            время</label>
                                                                                                    </div>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div class="checkbox-nice">
                                                                                                <input type="checkbox"
                                                                                                       class='call_status'
                                                                                                       id="callback_status_4"
                                                                                                       name="proc_status"
                                                                                                       value="3">
                                                                                                <label for="callback_status_4">Просит
                                                                                                    перезвонить</label>
                                                                                            </div>
                                                                                            <ul style="padding-top: 0;display: none"
                                                                                                class="call_now">
                                                                                                <li>
                                                                                                    <div class="form-group">
                                                                                                        <input type="text"
                                                                                                               class="form-control  callback_date"
                                                                                                               id="input_date_4"
                                                                                                               placeholder="Время перезвона"
                                                                                                               name="callback_time">
                                                                                                    </div>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="suspicious_block">
                                                                <div class="checkbox-nice">
                                                                    <input type="checkbox" name="suspicious"
                                                                           id="suspicious"
                                                                           @if ($orderOne->proc_status == 10) checked @endif>
                                                                    <label for="suspicious">
                                                                        Подозрительный заказ
                                                                    </label>
                                                                </div>
                                                                <div id="suspicious_comment"
                                                                     style="display: @if ($orderOne->proc_status == 10)  block @else none @endif">
                                                                    <div class="pull-left name">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->surname}} {{$suspicious_comment->name}}@endif</div>
                                                                    <div class="pull-right">@if ($orderOne->proc_status == 10 && $suspicious_comment){{date('H:i:s d/m/y', $suspicious_comment->date)}}@endif</div>
                                                                    <div class="form-group">
                                            <textarea name="suspicious_comment"
                                                      class="form-control">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->text}}@endif</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1"
                                     style="display: flex; flex-wrap: wrap; right: 0.5em; border-radius: 5px">
                                    <div class="row" style="background-color: #e7ebee; border-radius: 5px">
                                        <ul class="nav nav-tabs tabs-right sideways">
                                            <li class="order active"><a href="#order" data-toggle="tab">Заказ</a></li>
                                            <li class="logs"><a href="#logs" data-toggle="tab">Логи</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="tab-pane" id="logs">
                @if ($log && isset($permissions['get_logs_page_order']))
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2>Логи</h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>ФИО</th>
                                            <th>Текст</th>
                                            <th>Дата</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($log as $l)
                                            <tr>
                                                <td>
                                                    {{ $l->user_id }}
                                                </td>
                                                <td>
                                                    @if ($l->company)
                                                        <strong>{{$l->company}}</strong>
                                                        <br>
                                                    @endif
                                                    {{ $l->surname }} {{ $l->name }}
                                                </td>
                                                <td>
                                                    {!! $l->text !!}
                                                </td>
                                                <td>
                                                    {{ date('Y-m-d H:i:s', $l->date) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1"
                             style="display: flex; flex-wrap: wrap; right: 0.5em; border-radius: 5px">
                            <div class="row" style="background-color: #e7ebee; border-radius: 5px">
                                <ul class="nav nav-tabs tabs-right sideways">
                                    <li class="order"><a href="#order" data-toggle="tab">Заказ</a></li>
                                    <li class="logs"><a href="#logs" data-toggle="tab">Логи</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
