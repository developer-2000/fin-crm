@extends('layouts.app')

@section('title')Заказ # {{ $orderOne->id }} @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/style-tabs.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nouislider.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js?x=1') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-order_one.js?x=1') }}"></script>
    {{--<script src="{{ URL::asset('js/cold-calls/catch-time.js') }}"></script>--}}
    <style>
        table .table_products .checkbox-nice{
            padding-left: 0px;
        }
    </style>

@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <?
                            switch ($orderOne->proc_status) {
                                case 1: {
                                    $statusInfo =  trans('general.in-processing');
                                    break;
                                }
                                case 2: {
                                    $statusInfo =  trans('general.dialing');
                                    break;
                                }
                                case 3: {
                                    $statusInfo = trans('general.contact');
                                    break;
                                }
                                case 4: {
                                    $statusInfo = trans('general.repeat');
                                    break;
                                }
                                case 5: {
                                    $statusInfo = trans('general.under-call');
                                    break;
                                }
                                case 6: {
                                    $statusInfo = trans('general.invalid-number');
                                    break;
                                }
                                case 7: {
                                    $statusInfo = trans('general.another-language');
                                    break;
                                }
                                case 8: {
                                    $statusInfo = trans('general.error');
                                    break;
                                }
                                case 9: {
                                    $statusInfo = trans('general.completed');
                                    break;
                                }
                                case 10: {
                                    $statusInfo = trans('general.suspicious-order');
                                    break;
                                }
                                case 13: {
                                    $statusInfo = trans('general.fail');
                                    break;
                                }
                            }
                            ?>
                            <ol class="breadcrumb">
                                <li class="active"><span>Заказ #<span class='order_id'>{{ $orderOne->id }}</span><span class="status_info">({{ $statusInfo }})</span></span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($samePhone)
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box-body clearfix">
                    <div class="panel-group accordion" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle collapsed same_phone" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false">
                                        Похожие заказы <span class="badge badge-danger" style="background-color: #f4786e">{{ $samePhone }}</span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false" style="height: 2px;">
                                <div class="panel-body">
                                    <div class="table-responsive">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-xs-12 col-md-7 col-md-push-5">
            <form id="order_data">
                <div class="main-box clearfix">
                        <div class="main-box-body clearfix">
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ">
                                    <label for="surname">Фамилия</label>
                                    <input type="text" class="form-control" id="surname" name="surname" data-toggle="tooltip"
                                           data-placement="bottom" title="Фамилия" value="{{!empty($orderOne->surname) ? $orderOne->surname : '' }}" required>
                                </div>
                                <div class="form-group ">
                                    <label for="name">Имя</label>
                                    <input type="text" class="form-control" id="name" name="name" data-toggle="tooltip"
                                           data-placement="bottom" title="Имя" value="{{!empty($orderOne->name) ? $orderOne->name : ''}}" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ">
                                    <label for="middle">Отчество</label>
                                    <input type="text" class="form-control" id="middle" name="middle" data-toggle="tooltip"
                                           data-placement="bottom" title="Отчество" value="{{$orderOne->middle}}">
                                </div>
                                <div class="form-group ">
                                    <label for="phone">Телефон</label>
                                    <input type="text" class="form-control" id="phone" name="phone" data-toggle="tooltip"
                                           data-placement="bottom" title="Телефон" value="{{$orderOne->phone}}" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group form-group-select2">
                                    <label for="country">Страна</label>
                                    @if ($country)
                                        <select name="country" id="country" style="width: 100%">
                                            @foreach ($country as $oc)
                                                <option data-currency="{{ $oc->currency }}" value="{{ mb_strtolower($oc->code) }}" @if ($oc->code == strtoupper($orderOne->geo)) selected @endif>{{ $oc->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="age">Возраст</label>
                                    <input type="text" class="form-control" id="age" name="age"
                                           data-toggle="tooltip"
                                           data-placement="bottom" title="Возраст" @if ($orderOne->age) value="{{$orderOne->age}}" @endif>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="gender">Пол</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">Пол</option>
                                        <option value="1" @if ($orderOne->gender == 1) selected @endif>Мучжина</option>
                                        <option value="2" @if ($orderOne->gender == 2) selected @endif>Женщина</option>
                                    </select>
                                </div>
                            </div>
                            {{--<div class="col-sm-12 text-right ">--}}
                                {{--<input class="btn btn-success" type="submit" name="submit" value='Сохранить'/>--}}
                            {{--</div>--}}
                        </div>
                    {{--</form>--}}
                </div>
                <div class="main-box clearfix" style="position:relative;">
                    <header class="main-box-header clearfix">
                    </header>
                    {{--<div class="table-responsive search_block">--}}
                    {{--</div>--}}
                    <div class="table-responsive">
                        <table class="table table_products" >
                            <thead>
                            <tr>
                                <th>Имя</th>
                                <th class="text-center">Склад</th>
                                <th class="text-center">Up</th>
                                <th class="text-center">Up 2</th>
                                <th class="text-center">Cross</th>
                                <th class="text-center">Cross 2</th>
                                <th class="text-center">Примечание</th>
                                <th class="text-center">Price</th>
                                {{--<th class="text-center">Price+</th>--}}
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($offers)
                                @foreach ($offers as $offer)
                                    <tr
                                            @if ($offer->disabled) class="warning" @endif data-id="{{ $offer->ooid }}">
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
                                        @if ($offer->type == 1 || $offer->type == 2 || $offer->type == 3 || $offer->type == 4 || $offer->type == 5)
                                            <td class="text-center">
                                                <div class="checkbox-nice">
                                                    <input type="checkbox" id="up_sell_{{ $offer->ooid }}"
                                                           class="up_cross_sell" value="1"
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
                                                    <input type="checkbox" id="up_sell_2{{ $offer->ooid }}"
                                                           class="up_cross_sell" value="2"
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
                                                           class="up_cross_sell" value="4"
                                                           @if ($offer->type == 4)
                                                           checked
                                                            @endif
                                                    >
                                                    <label for="cross_sell_{{ $offer->ooid }}"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="checkbox-nice">
                                                    <input type="checkbox"
                                                           name="products[{{$offer->ooid}}][cross2]"
                                                           id="cross_sell_2_{{ $offer->ooid }}"
                                                           class="up_cross_sell" value="5"
                                                           @if ($offer->type == 5)
                                                           checked
                                                            @endif
                                                    >
                                                    <label for="cross_sell_2_{{ $offer->ooid }}"></label>
                                                </div>
                                            </td>
                                            <td class="comments">
                                                @if (!$offer->disabled)
                                                    <a href="#" data-pk="{{$offer->ooid}}" data-title="Введите примечание"  class="editable editable-pre-wrapped editable-click product_comments">{{$offer->comment}}</a>
                                                @endif
                                            </td>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="comments">
                                                @if (!$offer->disabled)
                                                    <a href="#" data-pk="{{$offer->ooid}}" data-title="Введите примечание"  class="editable editable-pre-wrapped editable-click product_comments">{{$offer->comment}}</a>
                                                @endif
                                            </td>
                                        @endif
                                        {{--</td>--}}
                                        <td class="text-center">
                                            <input type="hidden" name="products[{{$offer->ooid}}][id]" value="{{$offer->ooid}}">
                                            <input type="hidden" name="products[{{$offer->ooid}}][disabled]" value="{{$offer->disabled}}">
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
                                                <a href="#" class="table-link danger delete_product">
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
                                    <td class="value text-center">Всего</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center" id="total_price">{{$orderOne->price_total}}</td>
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
                        <h3 style="border-bottom: none;"><span style="border-bottom: none;">Результат звонка</span></h3>
                    </header>
                    <div class="tabs-wrapper targets">
                        <ul class="nav nav-tabs">
                            <li class=" target @if ($orderOne->target_status == 1) active @endif "><a
                                        href="#approve"
                                        data-toggle="tab"
                                        class="approve ">Подвержден
                                    <input type="radio" name="target_status" value="1"  @if ($orderOne->target_status == 1) checked @endif>
                                </a>
                                <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                            </li>
                            <li class=" target @if ($orderOne->target_status == 2) active @endif "><a
                                        href="#failure"
                                        data-toggle="tab"
                                        class="failure ">Отказ
                                    <input type="radio" name="target_status" value="2"  @if ($orderOne->target_status == 2) checked @endif>
                                </a>
                                <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                            </li>
                            <li class=" target @if ($orderOne->target_status == 3) active @endif "><a
                                        href="#fake"
                                        data-toggle="tab"
                                        class="fake">Аннулирован
                                    <input type="radio" name="target_status" value="3" @if ($orderOne->target_status == 3) checked @endif>
                                </a>
                                <span class="close_tab">
                                                <i class="fa fa-times"></i>
                                            </span>
                            </li>
                        </ul>
                        <input type="radio" name="target_status" id="target_status_def" value="0" @if ($orderOne->target_status == 0) checked @endif>
                        <div class="tab-content">
                            <div class="tab-pane fade @if ($orderOne->target_status == 1) in active @endif"
                                 id="approve">
                                <div class="main-box clearfix">
                                    <div class="main-box-body clearfix text-center"
                                         style="padding-top: 20px;">
                                        Сменить цель
                                        @if ($targets_approve)
                                            <select name="target_approve" class="form-control target">
                                                <option value="">Выберите</option>
                                                @foreach($targets_approve as $target)
                                                    <option value="{{$target->id}}" @if ($target->id == $orderOne->target_approve) selected @endif>{{$target->name}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="main-box-body clearfix target_block">
                                        <p class="text-center title_tab_content">Заполните данные по
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
                                        @if ($targets_refuse)
                                            <select name="target_refuse" class="form-control target">
                                                <option value="">Выберите</option>
                                                @foreach($targets_refuse as $target)
                                                    <option value="{{$target->id}}" @if ($target->id == $orderOne->target_refuse) selected @endif>{{$target->name}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="main-box-body clearfix target_block">
                                        <p class="text-center title_tab_content">Опишите причину
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
                                        @if ($targets_cancel)
                                            <select name="target_cancel" class="form-control target">
                                                <option value="">Выберите</option>
                                                @foreach($targets_cancel as $target)
                                                    <option value="{{$target->id}}" @if ($target->id == $orderOne->target_cancel) selected @endif>{{$target->name}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="main-box-body clearfix target_block">
                                        <p class="text-center title_tab_content">Заполните данные для аннулировки</p>
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
                                                "Автоответчик"/"В ближайшее время"
                                            </div>
                                        @elseif ($orderOne->proc_callback_time )
                                            <div style="text-align: center; margin-top: 25px">
                                                Перезвонить {{ $orderOne->proc_callback_time}}</div>
                                        @endif
                                        <div class="col-sm-offset-3">
                                            <ul>
                                                <li>
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" class="call_status"
                                                               name="proc_status"
                                                               id="another_language" value="5" >
                                                        <label for="another_language"
                                                               class="target_radio">Говорит
                                                            на
                                                            другом языке</label>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" class='call_status'
                                                               id="callback_status_1" value="1" name="proc_status">
                                                        <label for="callback_status_1">Автоответчик</label>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" class='call_status'
                                                               id="callback_status_2" value="2" name="proc_status">
                                                        <label for="callback_status_2">Плохая
                                                            связь</label>
                                                    </div>
                                                    <ul style="padding-top: 0;display: none"
                                                        class="call_now">
                                                        <li>
                                                            <div class="checkbox-nice">
                                                                <input type="checkbox"
                                                                       class="callback_status_ext"
                                                                       id="now_1" value="1"
                                                                       name="now">
                                                                <label for="now_1">Сейчас</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="checkbox-nice">
                                                                <input type="checkbox"
                                                                       class="callback_status_ext"
                                                                       id="now_2" value="2" name="now">
                                                                <label for="now_2">Ближайшее
                                                                    время</label>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" class='call_status'
                                                               id="callback_status_4" name="proc_status" value="3">
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
                                                                       placeholder="Время перезвона" name="callback_time">
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
                            <input type="checkbox" name="suspicious" id="suspicious"
                                   @if ($orderOne->proc_status == 10) checked @endif>
                            <label for="suspicious">
                                Подозрительный заказ
                            </label>
                        </div>
                        <div id="suspicious_comment"
                             style="display: @if ($orderOne->proc_status == 10)  block @else none @endif">
                            <div class="pull-left name">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->surname}} {{$suspicious_comment->name}}@endif</div>
                            <div class="pull-right">@if ($orderOne->proc_status == 10 && $suspicious_comment){{ $suspicious_comment->date }}@endif</div>
                            <div class="form-group">
                                            <textarea name="suspicious_comment"
                                                      class="form-control">@if ($orderOne->proc_status == 10 && $suspicious_comment){{$suspicious_comment->text}}@endif</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="error-messages" style="display: none;">
                </div>
                <div class="main-box-body clearfix text-center">
                    <button type="button" class="btn btn-success" id="save_order">
                        <span class="fa fa-save"></span> Сохранить
                    </button>
                </div>
            </form>
        </div>

        <div class="col-xs-12 col-md-5 col-md-pull-7">
            <div class="main-box-body clearfix">
                <table class="table first_info">
                    <tr>
                        <td class="text-center key">Оффер</td>
                        <td class=" value">

                            @if(!empty($offersArray[0] ))
                                <select class="form-control" name="offer" id="offer" style="width: 100%">
                                    @foreach ($offersArray as $offer)
                                        <option value="{{$offer->id}}">{{$offer->name}} </option>
                                    @endforeach
                                </select>
                                @else
                                <input type="text" placeholder="Создайте оффер ХП" disabled>
                            @endif
                        </td>
                    </tr>
                    @if (!empty($orderOne->input_data))
                        <? $inputData = json_decode($orderOne->input_data, true); ?>

                        @foreach ($inputData as $inDataKey => $inDataValue)
                            <tr>
                                @if(is_array($inDataValue))
                                    @php
                                           $addInfo = implode(",", $inDataValue);
                                    @endphp
                                    <td class="text-center key">
                                        {{ 'Дополнительная информация' }}
                                    </td>
                                    <td class=" value">
                                        {{ !empty($addInfo) ? $addInfo : '' }}
                                    </td>
                                    @else
                                    <td class="text-center key">
                                        {{ 'Дополнительная информация' }}
                                    </td>
                                    <td class=" value">
                                        {{ !empty($inDataValue) ? $inDataValue : '' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td class="text-center key">Дата создания</td>
                        <td class="value">{{ $orderOne->time_created}}</td>
                    </tr>
                    <tr>
                        <td class="text-center key">Дата установки цели</td>
                        <td class="value">{{ $orderOne->time_modified}}</td>
                    </tr>
                </table>
            </div>
            <div class="recommended-product">
            <div class="main-box-body clearfix">
                <header><h4>Выберите рекомендованные товары из списка 	&#8595;</h4></header>
                @if ($recommended_products)
                    @foreach($recommended_products as $type)
                        <table class="table product_offer">
                            <thead>
                            <tr>
                                <th class="text-left">
                                    @if ($type[0]->type == 1)
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
                                        <input type="text" style="width: 60%; display: inline-block;" class="form-control price_offer_add" data-value="{{$product->price}}" value="{{$product->price}}" placeholder="Цена">
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
            </div>
            <div class="main-box-body clearfix">
                <header class="main-box-header clearfix">
                    <h3 style="border-bottom: none;"><span style="border-bottom: none;">Коментарии к заказу</span>
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
                                    <button type="submit" class="btn btn-success add_comment ">Оставить
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
                                                <img src="{{ $co->photo}}" alt=""/>
                                            </div>
                                            <div class="conversation-body">
                                                <div class="company_user">{{$co->company}}</div>
                                                <div class="name" style="max-width: 50%;">
                                                    {{ $co->name }} ({{ $co->login }})
                                                </div>
                                                <div class="time hidden-xs" style="max-width: 50%;">
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
    </div>
    @if ($userCalls && isset($permissions['get_calls_page_order']))
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2>Звонки</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th class="text-center">Статус</th>
                                <th class="text-center">ФИО(ID)</th>
                                <th class="text-center">Дата</th>
                                <th class="text-center">Время разговора</th>
                                <th class="text-center">Trunk</th>
                                <th class="text-center">Запись разговора</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($userCalls as $ucKey => $ucvalue)
                                <tr>
                                    <td>
                                        {{ $ucKey + 1 }}
                                    </td>
                                    <td class="text-center">{{ $ucvalue->status }}</td>
                                    <td class="text-center">
                                        @if ($ucvalue->status == 'Success' || $ucvalue->status == 'ShortCall')
                                            {{ $ucvalue->name }} {{ $ucvalue->surname }} ({{ $ucvalue->login }})
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $ucvalue->date }}
                                    </td>
                                    <td class="text-center">
                                        {{ dateProcessing($ucvalue->talk_time) }}
                                    </td>
                                    <td class="text-center">
                                        {{ $ucvalue->trunk }}
                                    </td>
                                    <td class="text-center">
                                        @if ($ucvalue->status == 'Success' || $ucvalue->status == 'ShortCall')
                                            <?
                                            $url = route('get-call-by-name') . '?fileName=' . $ucvalue->file;
                                            $agent = $_SERVER['HTTP_USER_AGENT'];
                                            if (preg_match('/(OPR|Firefox)/i', $agent))
                                            {
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
    @endif
    @if ($log && isset($permissions['get_logs_page_order']))
        <div class="row">
            <div class="col-lg-12">
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
                                        {{ $l->surname }} {{ $l->name }}
                                    </td>
                                    <td>
                                        {!! $l->text !!}
                                    </td>
                                    <td>
                                        {{ $l->date }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
