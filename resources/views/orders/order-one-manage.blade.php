@extends('layouts.app')

@section('title')Заказ # {{ $orderOne->id }} @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    {{--<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>--}}
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
            <div class="clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="tab-a-content active">
                            <a class="moderator-a" href="#tab-order" data-toggle="tab"
                               aria-expanded="false"><span>Заказ #<span
                                            class='order_id'>{{ $orderOne->id }}</span><span
                                            class="status_info">({{ $statusInfo }})</span></span></a>
                        </li>
                        <li class="tab-a-content"><a class="logs moderator-a" href="#tab-logs" data-toggle="tab"
                                                     aria-expanded="true">Логи</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab-order">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="" style="padding-top: 2em">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 clearfix">
                                            <div class="">
                                                <table class="table first_info">
                                                    <tr>
                                                        <td class="text-center key">Оффер</td>
                                                        <td class=" value">
                                                            {{$orderOne->offer_name}}
                                                        </td>
                                                    </tr>
                                                    @if ($orderOne->source_url)
                                                        <tr>
                                                            <td class="text-center key">Источник</td>
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
                                                            IP адрес
                                                        </td>
                                                        <td class=" value">
                                                            {{ $orderOne->host }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center key">Страна</td>
                                                        <td class=" value">
                                                            {{$orderOne->country}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center key">Дата создания</td>
                                                        <td class="value">{{$orderOne->time_created}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center key">Дата установки цели</td>
                                                        <td class="value">{{$orderOne->time_modifie)}}</td>
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
                                                                        Товары
                                                                    @elseif ($type[0]->type == 1)
                                                                        Up Sell
                                                                    @elseif($type[0]->type == 2)
                                                                        Up Sell 2
                                                                    @elseif($type[0]->type == 4)
                                                                        Cross Sell
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
                                                                        <input type="text"
                                                                               style="width: 60%; display: inline-block;"
                                                                               class="form-control price_offer_add"
                                                                               data-value="{{$product->price}}"
                                                                               value="{{$product->price}}"
                                                                               placeholder="Цена">
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
                                            <div class="main-box-body clearfix">
                                                @if ($recommended_products)
                                                    @foreach($recommended_products as $type)
                                                        <table class="table product_offer">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-left">
                                                                    @if ($type[0]->type == 0)
                                                                        Товары
                                                                    @elseif ($type[0]->type == 1)
                                                                        Up Sell
                                                                    @elseif($type[0]->type == 2)
                                                                        Up Sell 2
                                                                    @elseif($type[0]->type == 4)
                                                                        Cross Sell
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
                                                                        <input type="text"
                                                                               style="width: 60%; display: inline-block;"
                                                                               class="form-control price_offer_add"
                                                                               data-value="{{$product->price}}"
                                                                               value="{{$product->price}}"
                                                                               placeholder="Цена">
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
                                            <div class="main-box-body clearfix comments_block">
                                                <header class="main-box-header clearfix">
                                                    <h3 style="border-bottom: none;"><span
                                                                style="border-bottom: none;">Коментарии к заказу</span>
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
                                                                    <button type="submit"
                                                                            class="btn btn-success add_comment ">
                                                                        Оставить
                                                                        комментарий
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
                                                                                <img src="{{ $co->photo}}"
                                                                                     alt=""/>
                                                                            </div>
                                                                            <div class="conversation-body">
                                                                                <div class="company_user">{{$co->company}}</div>
                                                                                <div class="name"
                                                                                     style="max-width: 50%;">
                                                                                    {{ $co->name }} ({{ $co->login }})
                                                                                </div>
                                                                                <div class="time hidden-xs"
                                                                                     style="max-width: 50%;">
                                                                                    {{ $co->date }}
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
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                            @if(isset($permissions['moderator_changes']))
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="panel-group accordion" id="accordion">
                                                            <div class="panel panel-default">
                                                                <div class="panel-heading">
                                                                    <h4 class="panel-title">
                                                                        <a class="accordion-toggle collapsed"
                                                                           data-toggle="collapse"
                                                                           data-parent="#accordion"
                                                                           href="#collapseOne">
                                                                            Основная информация
                                                                        </a>
                                                                    </h4>
                                                                </div>
                                                                <div id="collapseOne"
                                                                     class="panel-collapse collapse">
                                                                    <div class="panel-body">
                                                                        <div class="order-details">
                                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                                <div class="row">
                                                                                    <div class="col-sm-4"> Заказ #
                                                                                    </div>
                                                                                    <div class="col-sm-8 data-list">{{$orderOne->id}}</div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-sm-4"> Дата:
                                                                                    </div>
                                                                                    <div class="col-sm-8 data-list">  {{$orderOne->time_created}}</div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-sm-4"> Статус:
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
                                                                                    <div class="col-sm-5"> Очередь:
                                                                                    </div>
                                                                                    <div class="col-sm-7 data-list">   {{!empty($orderOne->campaign->name) ? $orderOne->campaign->name : 'N/A'}}</div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-sm-5"> Кол-во
                                                                                        звонков:
                                                                                    </div>
                                                                                    <div class="col-sm-7 data-list">    {{$orderOne->proc_stage}}</div>
                                                                                </div>
                                                                                @if($orderOne->proc_callback_time > now())
                                                                                    <div class="row">
                                                                                        <div class="col-sm-5">
                                                                                            Следующий
                                                                                            звонок:
                                                                                        </div>
                                                                                        <div class="col-sm-7 data-list">
                                                                                            {{$orderOne->proc_callback_time }}
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="main-box clearfix">
                                                                    <div class="main-box-body clearfix">
                                                                        <div class="col-md-4 col-sm-6">
                                                                            <div class="form-group ">
                                                                                <label for="surname">Фамилия</label>
                                                                                <input type="text"
                                                                                       class="form-control"
                                                                                       id="surname"
                                                                                       name="surname"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="bottom"
                                                                                       title="Фамилия"
                                                                                       value="{{$orderOne->surname}}"
                                                                                       required>
                                                                            </div>
                                                                            <div class="form-group ">
                                                                                <label for="name">Имя</label>
                                                                                <input type="text"
                                                                                       class="form-control"
                                                                                       id="name" name="name"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="bottom"
                                                                                       title="Имя"
                                                                                       value="{{$orderOne->name}}"
                                                                                       required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-6">
                                                                            <div class="form-group ">
                                                                                <label for="middle">Отчество</label>
                                                                                <input type="text"
                                                                                       class="form-control"
                                                                                       id="middle" name="middle"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="bottom"
                                                                                       title="Отчество"
                                                                                       value="{{$orderOne->middle}}">
                                                                            </div>
                                                                            <div class="form-group ">
                                                                                <label for="phone">Телефон</label>
                                                                                <input type="text"
                                                                                       class="form-control"
                                                                                       id="phone" name="phone"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="bottom"
                                                                                       title="Телефон"
                                                                                       value="{{$orderOne->phone}}"
                                                                                       required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-6">
                                                                            <div class="form-group form-group-select2">
                                                                                <label for="country">Страна</label>
                                                                                @if ($country)
                                                                                    <select name="country"
                                                                                            id="country"
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
                                                                                <label for="age">Возраст</label>
                                                                                <input type="text"
                                                                                       class="form-control"
                                                                                       id="age" name="age"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="bottom"
                                                                                       title="Возраст"
                                                                                       @if ($orderOne->age) value="{{$orderOne->age}}" @endif>
                                                                            </div>
                                                                            <div class="form-group col-md-6">
                                                                                <label for="gender">Пол</label>
                                                                                <select name="gender"
                                                                                        id="gender"
                                                                                        class="form-control">
                                                                                    <option value="">Пол
                                                                                    </option>
                                                                                    <option value="1"
                                                                                            @if ($orderOne->gender == 1) selected @endif>
                                                                                        Мучжина
                                                                                    </option>
                                                                                    <option value="2"
                                                                                            @if ($orderOne->gender == 2) selected @endif>
                                                                                        Женщина
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="main-box clearfix"
                                                                     style="position:relative;">
                                                                    <header class="main-box-header clearfix">
                                                                        <h2 class="pull-left"
                                                                            style="color: #929292;">{{$orderOne->offer_name}}</h2>
                                                                        <div class="filter-block pull-right">
                                                                            <div class="form-group pull-left">
                                                                                <input type="text"
                                                                                       class="form-control search"
                                                                                       placeholder="Search...">
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
                                                                                <th>Имя</th>
                                                                                <th class="text-center">Склад
                                                                                </th>
                                                                                <th class="text-center">Up</th>
                                                                                <th class="text-center">Up 2
                                                                                </th>
                                                                                <th class="text-center">Cross
                                                                                </th>
                                                                                <th class="text-center">
                                                                                    Примечание
                                                                                </th>
                                                                                <th class="text-center">Price
                                                                                </th>
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
                                                                                        <td class="text-center">
                                                                                            @if ($offer->storageAmount > 10)
                                                                                                <img src="{{ URL::asset('img/stock_1.png') }}" alt="Есть на складе">
                                                                                            @elseif($offer->storageAmount > 0)
                                                                                                <img src="{{ URL::asset('img/stock_2.png') }}" alt="Заканчивается">
                                                                                            @else
                                                                                                <img src="{{ URL::asset('img/stock_3.png') }}" alt="Нет на складе">
                                                                                            @endif
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
                                                                                                       data-title="Введите примечание"
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
                                                                                                       data-title="Введите примечание"
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
                                                                                                       placeholder="Цена"
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
                                                                                    <td class="value text-center">
                                                                                        Всего
                                                                                    </td>
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
                                                                <div class="error-messages"
                                                                     style="display: none;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="main-box clearfix">
                                                                    <div class="main-box-body clearfix">
                                                                        @if($orderOne->proc_status == 2 || $orderOne->proc_status == 11)
                                                                            <div class=" data-moderator">
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
                                                                        @endif
                                                                            <header style="font-weight: bold; text-align: center; padding: 5px; background-color: ghostwhite">
                                                                               Настройка прозвона
                                                                            </header>
                                                                            <div class="row data-moderator">
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
                                                                            <div class="row data-moderator">
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
                                                                            <div class="row data-moderator">
                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                        <label for="priority">
                                                                                            Изменить логику
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
                                                                        <header style="font-weight: bold; text-align: center; padding: 5px">
                                                                            Установить время прозвона и
                                                                            оператора
                                                                        </header>
                                                                        <div class="row ">
                                                                            <div class="checkbox-nice">
                                                                                <input type="checkbox"
                                                                                       class="add_call_now"
                                                                                       id="add_call_now">
                                                                                <label for="add_call_now">Сейчас</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row ">
                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 callback_block">
                                                                                <input name="callback_date_moderator"
                                                                                       type="text"
                                                                                       class="form-control  callback_date_moderator"
                                                                                       id="callback_date_moderator"
                                                                                       placeholder="Время перезвона">
                                                                            </div>
                                                                            <div class=" col-lg-7 col-md-7 col-sm-7 col-xs-7"
                                                                                 style="  ">
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
                                                                                                        @if ($operator->id == $orderOne->target_user)
                                                                                                        selected
                                                                                                        @endif
                                                                                                        value="{{$operator->login_sip}}">{{$operator->name . ' ' . $operator->surname }}</option>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'set_call_back_operator'])}}
                                                                            </div>
                                                                        </div>
                                                                        {{Form::hidden('orderId', $orderOne->id)}}
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
                                                                        <div class="row">
                                                                            <hr>
                                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                                <label for="user_transaction">Закрепить
                                                                                    за
                                                                                    оператором:</label>
                                                                            </div>
                                                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                                                <select class="form-control"
                                                                                        name="user_transaction"
                                                                                        id="user_transaction">
                                                                                    @if(!empty($orderUsers))
                                                                                        @foreach($orderUsers as $key=>$orderUser)
                                                                                            <option
                                                                                                    @if ($orderUser->id == $orderOne->target_user)
                                                                                                    selected
                                                                                                    @endif
                                                                                                    value="{{$orderUser->id}}">{{$orderUser->name . ' ' . $orderUser->surname }}</option>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                                {{Form::submit('OK', ['class' => 'btn btn-primary pull-right', 'id'=>'change_transaction'])}}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <header style="font-weight: bold; text-align: center; padding: 5px">
                                                                            Установить цель
                                                                        </header>
                                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                                            <label for="target">Результат
                                                                                звонка</label>
                                                                            @if ($orderOne->target_status)
                                                                                @php
                                                                                    $orderStatuses = [
                                                                                    ['target' => 'Подтвержден',
                                                                                    'target_id' =>1],
                                                                                      ['target' => 'Отказ',
                                                                                    'target_id' =>2],
                                                                                      ['target' => 'Аннулирован',
                                                                                    'target_id' =>3],
                                                                                    ]
                                                                                @endphp
                                                                                <select class="form-control"
                                                                                        name="target"
                                                                                        id="target"
                                                                                        style="width: 100%">
                                                                                    @foreach ($orderStatuses as $orderStatus)
                                                                                        <option value="{{$orderStatus['target_id'] }}"
                                                                                                @if ($orderOne->target_status == $orderStatus['target_id']) selected @endif>
                                                                                            {{$orderStatus['target']}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                                                            <div class="result">
                                                                                <div class="tabs-wrapper targets">
                                                                                    <div class="tab-content">
                                                                                        <div class="tab-pane fade @if ($orderOne->target_status == 1) in active @endif"
                                                                                             id="approve">
                                                                                            <div class="main-box clearfix">
                                                                                                <div class="main-box-body clearfix text-center"
                                                                                                     style="padding-top: 20px;">
                                                                                                    Сменить цель
                                                                                                    <select name="target_approve"
                                                                                                            class="form-control target">
                                                                                                        <option value="">
                                                                                                            Выберите
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
                                                                                                        Заполните
                                                                                                        данные
                                                                                                        по
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
                                                                                                        <option value="">
                                                                                                            Выберите
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
                                                                                                        Опишите
                                                                                                        причину
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
                                                                                                        <option value="">
                                                                                                            Выберите
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
                                                                                                        Заполните
                                                                                                        данные
                                                                                                        для
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
                                                                                                            "Автоответчик"/"В
                                                                                                            ближайшее
                                                                                                            время"
                                                                                                        </div>
                                                                                                    @elseif ($orderOne->proc_callback_time )
                                                                                                        <div style="text-align: center; margin-top: 25px">
                                                                                                            Перезвонить {{ $orderOne->proc_callback_time}}</div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="suspicious_block">
                                                                                    <div class="checkbox-nice">
                                                                                        <input type="checkbox"
                                                                                               name="suspicious"
                                                                                               id="suspicious"
                                                                                               @if ($orderOne->proc_status == 10) checked @endif>
                                                                                        <label for="suspicious">
                                                                                            Подозрительный заказ
                                                                                        </label>
                                                                                    </div>
                                                                                    <div id="suspicious_comment"
                                                                                         style="display: @if ($orderOne->proc_status == 10)  block @else none @endif">
                                                                                        <div class="pull-left name">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->surname}} {{$suspicious_comment->name}}@endif</div>
                                                                                        <div class="pull-right">@if ($orderOne->proc_status == 10 && $suspicious_comment){{ $suspicious_comment->date}}@endif</div>
                                                                                        <div class="form-group">
                                            <textarea name="suspicious_comment"
                                                      class="form-control">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->text}}@endif</textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="main-box-body clearfix text-center">
                                                                        <button type="button"
                                                                                class="btn btn-primary"
                                                                                id="save_order">
                                                                            <span class="fa fa-save"></span>
                                                                            Сохранить
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel-group accordion" id="accordion">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a class="accordion-toggle collapsed"
                                                                   data-toggle="collapse"
                                                                   data-parent="#accordion"
                                                                   href="#collapseSix">
                                                                    Транзакции
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseSix"
                                                             class="panel-collapse collapse">
                                                            <div class="panel-body">
                                                                @if ($transactions)
                                                                    <div class="main-box-body clearfix"
                                                                         style="margin-top: 20px;padding: 0 0 20px 0;">
                                                                        <div class="table-responsive">
                                                                            <table class="table">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>Id</th>
                                                                                    <th class="text-center">
                                                                                        Тип/Обьект
                                                                                    </th>
                                                                                    <th class="text-center">
                                                                                        Дата
                                                                                    </th>
                                                                                    <th class="text-center">
                                                                                        Id
                                                                                        заказа
                                                                                    </th>
                                                                                    <th class="text-center">
                                                                                        Страна
                                                                                    </th>
                                                                                    <th class="text-center">
                                                                                        Сумма
                                                                                    </th>
                                                                                    <th class="text-center">
                                                                                        Инициатор
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                @foreach($transactions as $transaction)
                                                                                    <?
                                                                                    switch ($transaction->entity) {
                                                                                        case 'company':
                                                                                            {
                                                                                                $object = 'Компания';
                                                                                                break;
                                                                                            }
                                                                                        case 'user':
                                                                                            {
                                                                                                $object = 'Оператор';
                                                                                                break;
                                                                                            }
                                                                                    }
                                                                                    switch ($transaction->type) {
                                                                                        case 'approve':
                                                                                            {
                                                                                                $class = '';
                                                                                                $text = 'Начисление';
                                                                                                break;
                                                                                            }
                                                                                        case 'bonus':
                                                                                            {
                                                                                                $class = 'bonus';
                                                                                                $text = 'Бонус';
                                                                                                break;
                                                                                            }
                                                                                        case 'retention':
                                                                                            {
                                                                                                $class = 'retention';
                                                                                                $text = 'Удержание';
                                                                                                break;
                                                                                            }
                                                                                        case 'fine':
                                                                                            {
                                                                                                $class = 'danger';
                                                                                                $text = 'Штраф';
                                                                                                break;
                                                                                            }
                                                                                        case 'residue' :
                                                                                            {
                                                                                                $class = 'residue';
                                                                                                $text = 'Остаток';
                                                                                                break;
                                                                                            }
                                                                                        case 'debt' :
                                                                                            {
                                                                                                $class = 'debt';
                                                                                                $text = 'Долг';
                                                                                                break;
                                                                                            }
                                                                                        case 'custom' :
                                                                                            {
                                                                                                $class = 'custom';
                                                                                                $text = 'Кастом';
                                                                                                break;
                                                                                            }
                                                                                        default:
                                                                                            {
                                                                                                $class = 'default';
                                                                                                $text = 'Не определенно';
                                                                                                break;
                                                                                            }
                                                                                    }
                                                                                    ?>
                                                                                    <tr @if ($transaction->payout_id) class="success opacityTr" @endif>
                                                                                        <td>{{$transaction->id}}</td>
                                                                                        <td class="text-center">
                                                                                            <div class="type {{$class}}">{{$text}}
                                                                                                / {{$object}}</div>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <div class="time">{{ $transaction->time_created}}</div>
                                                                                            <div class="date">{{ $transaction->time_created}}</div>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            @if ($transaction->order_id)
                                                                                                <div class="crm_id">
                                                                                                    <a href="{{route('order', $transaction->order_id)}}">{{$transaction->order_id}}</a>
                                                                                                </div>
                                                                                                <div style="font-size: 12px;">
                                                                                                    {{$transaction->operSurname}} {{$transaction->operName }}
                                                                                                    <br>
                                                                                                    {{$transaction->company}}
                                                                                                </div>
                                                                                            @elseif ($transaction->company)
                                                                                                <div style="font-size: 12px;">
                                                                                                    {{$transaction->company}}
                                                                                                </div>
                                                                                            @else
                                                                                                -
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            @if ($transaction->geo)
                                                                                                <img class="country-flag"
                                                                                                     src="{{ URL::asset('img/flags/' . mb_strtoupper($transaction->geo) . '.png')  }}" />
                                                                                            @else
                                                                                                -
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            @if($transaction->type == 'bonus' && $transaction->result !== 0 )
                                                                                                <div class="type {{$class}}">{{$transaction->result}}
                                                                                                    грн
                                                                                                </div>
                                                                                            @elseif($transaction->type == 'retention' && $transaction->result !== 0 )
                                                                                                <div class="type {{$class}}">{{$transaction->result}}
                                                                                                    грн
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="type {{$class}}">{{$transaction->balance}}
                                                                                                    грн
                                                                                                </div>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <b>{{$transaction->initiator}}</b>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if ($dataGrouped && isset($permissions['get_logs_page_order']))
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="main-box clearfix">
                                            <header class="main-box-header clearfix">
                                                <h2>Логи по операторам</h2>
                                            </header>
                                            <div class="main-box-body clearfix">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">Дата открытия/Оператор/Цель/Статус</th>
                                                        {{--<th class="text-center">Оператор</th>--}}
                                                        <th class="text-center">Звонки</th>
                                                        <th class="text-center"> Комментарии</th>
                                                        <th class="text-center">Логи</th>
                                                        <th class="text-center">Оставить фидбек</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($dataGrouped as $row)
                                                        <tr id="{{$row->user->id}}" orders-opened-id="{{$row->id}}">
                                                            <td class="text-center">
                                                                <div>{{  $row->date_opening}}</div>
                                                                <div style=" border-bottom: 2px solid #ebebeb; padding-bottom: 5px">
                                                                    <a href="{{ route('users-edit', $row->user->id) }}"
                                                                    >{{$row->user->name.'  '.$row->user->surname.'  ('.$row->user->login.')'}}</a>
                                                                </div>
                                                                <div style="padding-top: 7px; padding-bottom: 7px">
                                                                    @if ($row->user->company)
                                                                        {{$row->user->company->name}}
                                                                    @endif
                                                                </div>
                                                                @php
                                                                    $target = '';
                                                                    $classLabel = '';
                                                                    $classRow = '';
                                                                    $classBtn = '';
                                                                    switch($row->target) {
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
                                                                        case 4:
                                                                        {
                                                                            $target = 'Call back';
                                                                            $classLabel = 'label-info';
                                                                            $classRow = '';
                                                                            $classBtn = 'custom_info';
                                                                            break;
                                                                        }
                                                                    case 5:
                                                                        {
                                                                            $target = 'Говорит на другом языке';
                                                                            $classLabel = 'label-warning';
                                                                            $classRow = 'warning';
                                                                            $classBtn = 'custom_warning';
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp
                                                                @if($row)
                                                                @endif
                                                                <div class="badge {{$classLabel}}">{{$target}}</div>
                                                                @if (!$row->target)
                                                                    @php
                                                                        $status = '';
                                                                        $class = '';
                                                                               switch ($row->callback_status) {
                                                                        case 1:
                                                                            $status = 'Автоответчик';
                                                                            $class = 'label-default';
                                                                            $classRow = 'default';
                                                                            break;
                                                                        case 2:
                                                                            {
                                                                                $status = 'Плохая связь перезвонить';
                                                                                $class = 'label-success';
                                                                                   $classRow = 'success';
                                                                                break;
                                                                            }
                                                                        case 3:
                                                                            {
                                                                                $status = 'Завершен без цели';
                                                                                $class = 'label-danger';
                                                                                    $classRow = 'danger';
                                                                                break;
                                                                            }
                                                                        case 0:
                                                                            {
                                                                                $status = 'Завершен без цели';
                                                                                $class = 'label-danger';
                                                                                 $classRow = 'danger';
                                                                                break;
                                                                            }
                                                                    }
                                                                    @endphp
                                                                    <span class="label {{$class}}">{{$status}}</span>
                                                                @endif
                                                            </td>
                                                            {{--<td class="text-center"--}}
                                                            {{--style="width: 15%;">--}}
                                                            {{----}}
                                                            {{--</td>--}}
                                                            <td class="text-center">
                                                                @if(count($row->call_progress_log) !== 0)
                                                                    @foreach($row->call_progress_log as $log)
                                                                        <div class="btn-group">
                                                                            <button type="button"
                                                                                    class="btn btn-default dropdown-toggle"
                                                                                    data-toggle="dropdown"
                                                                                    aria-expanded="false">
                                                                                Прослушать <i
                                                                                        class="fa fa-volume-up"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu" role="menu">

                                                                                <li>
                                                                                    <?
                                                                                    $url = route('get-call-by-name') . '?fileName=' . $log->file;
                                                                                    $agent = $_SERVER['HTTP_USER_AGENT'];
                                                                                    if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                                                        $output = '<p><a href="' . $url . '"><span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                            </span></a></p>';
                                                                                    } else {
                                                                                        $output = '
                                            <audio controls>
                                                <source src="' . $url . '" type="audio/mpeg">
                                            </audio>
                                    ';
                                                                                    }
                                                                                    echo $output?>
                                                                                </li>

                                                                            </ul>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    {{'N/A'}}
                                                                @endif
                                                            </td>
                                                            <td style="width: 20%" ;>
                                                                @foreach($row->comments as $comment)
                                                                    <div class="comment">
                                                                        <div class="comment-time">
                                                                            {{ $comment->date }}
                                                                        </div>
                                                                        <br>
                                                                        <div class="text">
                                                                            {{$comment->text}}
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                <div class="log-inner" style="padding: 15px">
                                                                    @foreach($row->logs as $log)
                                                                        <div class="comment-time">{!! $row->date_opening !!}</div>
                                                                        <div>{!!$log->text!!}</div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="slimScrollBar"></div>
                                                            </td>
                                                            <td style="width: 20%;">
                                                                @if(empty($row->feedback))
                                                                    <div class="feedback">
                                                                        <button style="margin-left: 55px "
                                                                                class="btn btn-primary"
                                                                                href="" id="feedback">Оставить
                                                                            отзыв
                                                                        </button>
                                                                    </div>
                                                                @else
                                                                    <dt style="padding-left: 15px; color: #929292">{{'Вы уже оставили отзыв от:'. $row->feedback->created_at}}</dt>
                                                                    <a href="{{ route('operator-mistakes') }}">Перейти к
                                                                        ошибкам
                                                                        операторов</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <div id="feedback-block-failed">
                                        <div class="icon-box pull-right">
                                            <a href="#" id="close-feedback-options" class="pull-left">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                        <form class="failed_call">
                                            @if(!empty($operatorMistakes))
                                                <div class="form-group">
                                                    <label>Выберите тип ошибки</label>
                                                    <br>
                                                    <div class="col-sm-12">
                                                       `
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <label for="comment"> Ваш отзыв</label>
                                                {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                                            </div>
                                            <div class="text-center">
                                                {{Form::hidden('type', 'failed_call')}}
                                                {{Form::submit('Сохранить', ['name' =>'form1-save-plan', 'class' => 'btn btn-success'])}}
                                            </div>
                                        </form>
                                    </div>
                                    <div id="feedback-block-success">
                                        <div class="icon-box pull-right">
                                            <a href="#" id="close-feedback-options" class="pull-left">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                        <form class="success_call">
                                            <div class="form-group">
                                                <label for="comment"> Ваш отзыв</label>
                                                {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                                            </div>
                                            <div class="text-center">
                                                {{Form::hidden('type', 'success_call')}}
                                                {{Form::submit('Сохранить', ['name' =>'form1-save-plan', 'class' => 'btn btn-success'])}}
                                            </div>
                                        </form>
                                    </div>
                                    <div id="feedback-block">
                                        <button style="margin-left: 30px" class="btn btn-success" href=""
                                                id="success_call"><i
                                                    class="fa fa-thumbs-o-up"></i> Положительный
                                        </button>
                                        <br><br>
                                        <button style="margin-left: 30px" class="btn btn-danger" href=""
                                                id="failed_call"><i
                                                    class="fa fa-thumbs-o-down"></i> Отрицательный
                                        </button>
                                        <br>
                                        <div class="icon-box pull-right">
                                            <a href="#" id="close-feedback-options" class="pull-left">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div id="feedback-initial">
                                        <button style="margin-left: 30px " class="btn btn-primary" href=""
                                                id="feedback">Оставить
                                            отзыв
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane fade " id="tab-logs">
                            @if ($log && isset($permissions['get_logs_page_order']))
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="main-box  clearfix">
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
                                                    {{--@foreach ($log as $l)--}}
                                                        {{--<tr>--}}
                                                            {{--<td>--}}
                                                                {{--{{ $l->user_id }}--}}
                                                            {{--</td>--}}
                                                            {{--<td>--}}
                                                                {{--@if ($l->company)--}}
                                                                    {{--<strong>{{$l->company}}</strong>--}}
                                                                    {{--<br>--}}
                                                                {{--@endif--}}
                                                                {{--{{ $l->surname }} {{ $l->name }}--}}
                                                            {{--</td>--}}
                                                            {{--<td>--}}
                                                                {{--{!! $l->text !!}--}}
                                                            {{--</td>--}}
                                                            {{--<td>--}}
                                                                {{--{{ date('Y-m-d H:i:s', $l->date) }}--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                    {{--@endforeach--}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop