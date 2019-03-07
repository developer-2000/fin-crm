@extends('layouts.app')
@section('title')Создать план @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/plans"><span>Планы</span></a></li>
                <li class="active"><a href="/plan/create"><span>Добавить план</span></a></li>
            </ol>
            <h1>Создать план</h1>

        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{ Form::open(array('route' => 'plans-create','method'=>'POST')) }}

    <div class="main-box clearfix">
        <div class="col-lg-6">
            <header class="main-box-header clearfix">
                <h2>Основные настройки</h2>

            </header>
            <div class="main-box-body clearfix">
                <div class="form-group">
                    <div class="">
                        <label for="name">Название</label>
                        <input class="form-control" type="text" name="name" id="name"
                               placeholder="Название плана" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="company_id">Компания</label>
                    <select class="form-control" name="company_id" id="company_id" required>
                        <option value="">Выберите компанию</option>
                        @foreach($companies as $key=>$company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                            <span class="company_type"
                                  style="display:none">{{$company->type}}</span>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="type-object">Тип транзакции</label>
                    <select class="form-control" name="type-object" id="type-object" disabled>
                        <option value="" selected>Выберите тип транзакции</option>
                        <option value="company">По компании</option>
                        <option value="operator">По операторам</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type-method" style="text-align: left;">Метод расчета</label>
                    <select class="form-control" name="type-method" id="type-method" disabled>
                        <option value="" selected>Выберите метод расчета</option>
                        <option value="action">По подтверждению заказа</option>
                        <option value="schedule">По расписанию</option>
                    </select>
                </div>
                <div class="type-method ">
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="col-sm-6">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment"> Комментарий</label>
                        {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <header class="main-box-header clearfix">
                <h2>Выбрать критерии</h2>
            </header>
            <div class="main-box-body clearfix">
                <div class="col-sm-6">
                    <h3 class="status green">Основные</h3>
                    <div class="form-group">
                        <label for="user-group-select2">Группа операторов</label>
                        <input type="hidden" name="user-group-select2" id="user-group-select2"
                               class="user-group-select2"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="user-select2">Оператор</label>
                        <input type="hidden" name="user-select2" id="user-select2" class="user-select2"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="county-select2">Страна</label>
                        <input type="hidden" name="country-select2" id="county-select2" class="country-select2 required"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="offer-select2">Оффер</label>
                        <input type="hidden" name="offers-select2" id="offer-select2" class="offers-select2"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="products-select2">Товар</label>
                        <input type="hidden" name="products-select2" class="products-select2"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label>Тип товара</label>
                        <br>
                        <div class="checkbox-nice checkbox-inline">
                            {{ Form::checkbox('product-type[]', '1', false, ['disabled'=> true, 'id' => 'up_sell_1']) }}
                            {{ Form::label('up_sell_1', 'Up sell 1') }}
                        </div>
                        <div class="checkbox-nice checkbox-inline">
                            {{ Form::checkbox('product-type[]', '2', false, ['disabled'=> true,'id' => 'up_sell_2']) }}
                            {{ Form::label('up_sell_2', 'Up sell 2') }}
                        </div>
                        <div class="checkbox-nice checkbox">
                            {{ Form::checkbox('product-type[]', '4', false, ['disabled'=> true,'id' => 'cross_sell']) }}
                            {{ Form::label('cross_sell', 'Cross sell') }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <h3 class="status red">Исключающие</h3>
                    <div class="form-group">
                        <label for="user-group-select2-except">Группа операторов</label>
                        <input type="hidden" name="user-group-select2-except" id="user-group-select2-except"
                               class="user-group-select2-except"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="user-select2-except">Оператор</label>
                        <input type="hidden" name="user-select2-except" id="user-select2-except"
                               class="user-select2-except"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="county-select2-except">Страна</label>
                        <input type="hidden" name="country-select2-except" id="county-select2-except"
                               class="country-select2-except required"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="offer-select2-except">Оффер</label>
                        <input type="hidden" name="offers-select2-except" id="offer-select2-except"
                               class="offers-select2-except"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="products-select2-except">Товар</label>
                        <input type="hidden" name="products-select2-except" class="products-select2-except"
                               style="width: 100%" disabled/>
                    </div>
                    <div class="form-group">
                        <label>Тип товара</label>
                        <br>
                        <div class="checkbox-nice checkbox-inline">
                            {{ Form::checkbox('product-type-except[]', '1', false, ['disabled'=> true, 'id' => 'up_sell_1_except']) }}
                            {{ Form::label('up_sell_1_except', 'Up sell 1') }}
                        </div>
                        <div class="checkbox-nice checkbox-inline">
                            {{ Form::checkbox('product-type-except[]', '2', false, ['disabled'=> true,'id' => 'up_sell_2_except']) }}
                            {{ Form::label('up_sell_2_except', 'Up sell 2') }}
                        </div>
                        <div class="checkbox-nice checkbox">
                            {{ Form::checkbox('product-type-except[]', '4', false, ['disabled'=> true,'id' => 'cross_sell_except']) }}
                            {{ Form::label('cross_sell_except', 'Cross sell') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="rate_block">
            </div>
            <div class="fixed-rate_block">
            </div>
        </div>
    </div>
    <div class="text-center">
        {{Form::submit('Сохранить', ['name' =>'form1-save-plan', 'class' => 'btn btn-success'])}}
    </div>
    {{ Form::close() }}
    <div class="hidden">
        <div id="rate_block_lead">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                            <div class="col-sm-6">
                                <div class="main-box-body clearfix">
                                    <header class="main-box-header clearfix text-center">
                                        <h3 class="status green">План выполнен:</h3>
                                    </header>
                                    <div class="form-group">
                                        <div class="checkbox checkbox-nice">
                                            {{ Form::checkbox('rate-success', NULL, false, ['id' => 'rate-success']) }}
                                            {{ Form::label('rate-success', 'Установить новые цены') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="approve-bonus-def" class="col-sm-7 control-label"
                                                   style="text-align: left; ">Подтвержденные</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="approve-bonus-def"
                                                       id="approve-bonus-def" min="value" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="up_sell-bonus-def" class="col-sm-7 control-label"
                                                   style="text-align: left;">Up
                                                sell</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell-bonus-def"
                                                       id="up_sell-bonus-def" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="up_sell_2-bonus-def" class="col-sm-7 control-label"
                                                   style="text-align: left;">Up
                                                sell 2</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell_2-bonus-def"
                                                       id="up_sell_2-bonus-def" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="cross_sell-bonus-def" class="col-sm-7 control-label"
                                                   style="text-align: left;">Cross sell</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="cross_sell-bonus-def"
                                                       id="cross_sell-bonus-def" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="approve-bonus"
                                                       id="approve-bonus" min="value" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell-bonus"
                                                       id="up_sell-bonus" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell_2-bonus"
                                                       id="up_sell_2-bonus" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="cross_sell-bonus"
                                                       id="cross_sell-bonus" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-nice">
                                                    {{ Form::checkbox('fixed-rate-success', NULL, false, ['id' => 'fixed-rate-success']) }}
                                                    {{ Form::label('fixed-rate-success', 'Фиксировання ставка') }}
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="success-plan" style="text-align: left;">Бонус к
                                                    начислению</label>
                                                <input id="success-plan" class="form-control"
                                                       style="text-align: left;" type="type" name="fixed-bonus"
                                                       min="1" disabled>
                                                {{--<span class="validity"></span>--}}

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="main-box-body clearfix">
                                    <header class="main-box-header clearfix text-center">
                                        <h3 class="status red">План не выполнен:</h3>
                                    </header>
                                    <div class="form-group">
                                        <div class="checkbox checkbox-nice">
                                            {{ Form::checkbox('rate-failed', NULL, false, ['id' => 'rate-failed']) }}
                                            {{ Form::label('rate-failed', 'Установить новые цены') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="approve-retention-def" class="col-sm-7 control-label"
                                                   style="text-align: left; ">Подтвержденные</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="approve-retention-def"
                                                       id="approve-retention-def" min="value" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="up_sell-retention-def" class="col-sm-7 control-label"
                                                   style="text-align: left;">Up
                                                sell</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell-retention-def"
                                                       id="up_sell-retention-def" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="up_sell_2-retention-def" class="col-sm-7 control-label"
                                                   style="text-align: left;">Up
                                                sell 2</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell_2-retention-def"
                                                       id="up_sell_2-retention-def" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="cross_sell-retention-def" class="col-sm-7 control-label"
                                                   style="text-align: left;">Cross sell</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="cross_sell-retention-def"
                                                       id="cross_sell-retention-def" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="approve-retention"
                                                       id="approve-retention" min="value" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell-retention"
                                                       id="up_sell-retention" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="up_sell_2-retention"
                                                       id="up_sell_2-retention" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <input class="form-control" type="text" name="cross_sell-retention"
                                                       id="cross_sell-retention" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-nice">
                                                    {{ Form::checkbox('fixed-rate-failed', NULL, false, ['id' => 'fixed-rate-failed']) }}
                                                    {{ Form::label('fixed-rate-failed', 'Фиксировання ставка') }}
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="failed-plan" style="text-align: left;">Бонус к
                                                    удержанию</label>
                                                <input id="failed-plan" class="form-control"
                                                       style="text-align: left;" type="text" name="fixed-retention"
                                                       min="1" disabled>
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
        <div id="rate_block_hour">
            <div class="col-sm-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                            <div class="col-sm-6">
                                <header class="main-box-header clearfix text-center">
                                    <h2>План выполнен</h2>
                                </header>
                                <div class="form-group">
                                    <label for="in_system-success-def" class="col-sm-4 control-label"
                                           style="text-align: left;">Час
                                        в
                                        системе</label>
                                    <div class="col-sm-4">
                                        <input class="form-control" type="number" name="in_system-success-def"
                                               id="in_system-success-def" disabled>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control" type="number" name="in_system-bonus"
                                               id="in_system-bonus">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="in_talk-success-def" class="col-sm-4 control-label"
                                           style="text-align: left;">Час
                                        разговора</label>
                                    <div class="col-sm-4">
                                        <input class="form-control" type="number" name="in_talk-success-def"
                                               id="in_talk-success-def" disabled>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control" type="number" name="in_talk-bonus"
                                               id="in_talk-bonus">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                            <div class="col-sm-6">
                                <div class="failed-hour">
                                    <header class="main-box-header clearfix text-center">
                                        <h2>План не выполнен</h2>
                                    </header>
                                    <div class="form-group">
                                        <label for="in_system-failed-def" class="col-sm-4 control-label"
                                               style="text-align: left;">Час
                                            в
                                            системе</label>
                                        <div class="col-sm-4">
                                            <input class="form-control" type="number"
                                                   name="in_system-failed-def"
                                                   id="in_system-failed-def" disabled>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-control" type="number"
                                                   name="in_system-retention"
                                                   id="in_system-retention">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="in_talk-failed-def" class="col-sm-4 control-label"
                                               style="text-align: left;">Час
                                            разговора</label>
                                        <div class="col-sm-4">
                                            <input class="form-control" type="number"
                                                   name="in_talk-failed-def"
                                                   id="in_talk-failed-def" disabled>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-control" type="number"
                                                   name="in_talk-retention"
                                                   id="in_talk-retention">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="basis-for-schedule">
            <div class="row">
                <div class="col-sm-12">
                    {{ Form::label('basis-for-schedule','Параметры определения плана') }}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3">
                    <div class="radio ">
                        {{ Form::radio('basis-for-schedule', 'quantity', true, ['id' => 'quantity']) }}
                        {{ Form::label('quantity','Количество') }}
                    </div>
                    <div class="radio ">
                        {{ Form::radio('basis-for-schedule', 'percent', false, ['id' => 'percent']) }}
                        {{ Form::label('percent','Процент') }}
                    </div>
                </div>

                <div class="col-xs-2">
                    <label for="action-quantity-operator" style="text-align: left;"></label>
                    <select class="form-control" id="action-quantity-operator" name="action-quantity-operator">
                        <option value=">" selected>&#62;</option>
                        <option value=">=">&#8805;</option>
                    </select>
                </div>
                <div class="col-xs-2">
                    <label for="action-quantity-value" style="text-align: left;"></label>
                    <input id="action-quantity-value" class="form-control" type="text" name="action-quantity-value">
                </div>
                <div class="col-xs-5" style="top: -5px">
                    <label for="product-type-action" style="text-align: left;">Тип товара</label>
                    <div class="checkbox-nice">
                        {{ Form::checkbox('product-type-action[]', 'approve', false, ['id' => 'approve']) }}
                        {{ Form::label('approve', 'Approve') }}
                    </div>
                    <div class="checkbox-nice">
                        {{ Form::checkbox('product-type-action[]', '1', false, ['id' => 'base_up_sell_1']) }}
                        {{ Form::label('base_up_sell_1', 'Up sell 1') }}
                    </div>
                    <div class="checkbox-nice">
                        {{ Form::checkbox('product-type-action[]', '2', false, ['id' => 'base_up_sell_2']) }}
                        {{ Form::label('base_up_sell_2', 'Up sell 2') }}
                    </div>
                    <div class="checkbox-nice">
                        {{ Form::checkbox('product-type-action[]', '4', false, ['id' => 'base_cross_sell']) }}
                        {{ Form::label('base_cross_sell', 'Cross sell') }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row">
                    <div class="col-sm-12">
                        {{ Form::label('basis-for-schedule','Расчет производится для каждой транзакции') }}
                    </div>
                </div>
                <div class="col-xs-3">
                    <div class="radio">
                        {{ Form::radio('basis-for-schedule', 'sum-each', false, ['id' => 'sum-each']) }}
                        {{ Form::label('sum-each','Сумма') }}
                    </div>
                    <div class="radio">
                        {{ Form::radio('basis-for-schedule', 'percent-each', false, ['id' => 'percent-each']) }}
                        {{ Form::label('percent-each','Процент') }}
                    </div>
                </div>
                <div class="col-xs-2" style="top: 25px">
                    <select class="form-control" id="action-sum-operator" name="action-sum-operator" disabled>
                        <option value=">" selected>&#62;</option>
                        <option value=">=">&#8805;</option>
                    </select>
                </div>
                <div class="col-xs-2" style="top: 25px">
                    {{Form::text('action-sum-value', '', [ 'class' =>'form-control', 'disabled' => true, 'id' => 'action-sum-value'])}}
                </div>
                <div class="col-xs-5">
                    <label for="sum-percent-product-type-action" style="text-align: left;">Тип товара</label>
                    <select class="form-control" id="sum-percent-product-type-action"
                            name="sum-percent-product-type-action[]"
                            disabled>
                        <option value="" selected> Выберите тип товара</option>
                        <option value="total"> Total</option>
                        <option value="1"> Up sell 1</option>
                        <option value="2"> Up sell 2</option>
                        <option value="4"> Cross sell</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <header><label for="interval">Интервал</label></header>
                <div class="col-xs-8">
                    <div class="radio">
                        {{ Form::radio('interval', 'month', true, ['id' => 'month' ]) }}
                        {{ Form::label('month','Месяц') }}
                    </div>
                    <div class="radio">
                        {{ Form::radio('interval', 'week', false, ['id' => 'week' ]) }}
                        {{ Form::label('week','Неделя') }}
                    </div>
                    <div class="radio">
                        {{ Form::radio('interval', 'day', false, ['id' => 'day' ]) }}
                        {{ Form::label('day','День') }}
                    </div>
                </div>
            </div>
        </div>
        <div id="basis-action">
            <div class="row">
                <div class="col-sm-12">
                    {{ Form::label('basis-for-action','Параметры определения плана') }}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3">
                    <div class="radio ">
                        {{ Form::radio('basis-for-action', 'quantity', true, ['id' => 'quantity']) }}
                        {{ Form::label('quantity','Количество') }}
                    </div>
                </div>
                <div class="col-xs-2">
                    <label for="action-quantity-value" style="text-align: left;"></label>
                    <input id="action-quantity-value" class="form-control" type="text" name="action-quantity-value"
                           required>
                </div>
                <div class="col-xs-2">
                    <label for="action-quantity-operator" style="text-align: left;"></label>
                    <select class="form-control" id="action-quantity-operator" name="action-quantity-operator">
                        <option value=">" selected>&#62;</option>
                        <option value=">=">&#8805;</option>
                    </select>
                </div>
                <div class="col-xs-5" style="top: -5px">
                    <label for="product-type-action" style="text-align: left;">Тип товара</label>
                    <div class="checkbox-nice">
                        {{ Form::checkbox('product-type-action[]', '1', false, ['id' => 'base_up_sell_1']) }}
                        {{ Form::label('base_up_sell_1', 'Up sell 1') }}
                    </div>
                    <div class="checkbox-nice">
                        {{ Form::checkbox('product-type-action[]', '2', false, ['id' => 'base_up_sell_2']) }}
                        {{ Form::label('base_up_sell_2', 'Up sell 2') }}
                    </div>
                    <div class="checkbox-nice">
                        {{ Form::checkbox('product-type-action[]', '4', false, ['id' => 'base_cross_sell']) }}
                        {{ Form::label('base_cross_sell', 'Cross sell') }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row">
                    <div class="col-sm-12">
                        {{ Form::label('basis-for-action','Расчет производится для каждой транзакции') }}
                    </div>
                </div>
                <div class="col-xs-3">
                    <div class="radio">
                        {{ Form::radio('basis-for-action', 'sum-each', false, ['id' => 'sum-each']) }}
                        {{ Form::label('sum-each','Сумма') }}
                    </div>
                    <div class="radio">
                        {{ Form::radio('basis-for-action', 'percent-each', false, ['id' => 'percent-each']) }}
                        {{ Form::label('percent-each','Процент') }}
                    </div>
                </div>
                <div class="col-xs-2" style="top: 25px">
                    {{Form::text('action-sum-value', '', [ 'class' =>'form-control',  'id' => 'action-sum-value',
                     'required'=>'required','disabled' => true])}}
                </div>
                <div class="col-xs-2" style="top: 25px">
                    <select class="form-control" id="action-sum-operator" name="action-sum-operator" disabled>
                        <option value=">" selected>&#62;</option>
                        <option value=">=">&#8805;</option>
                    </select>
                </div>
                <div class="col-xs-5">
                    <label for="sum-percent-product-type-action" style="text-align: left;">Тип товара</label>
                    <select class="form-control" id="sum-percent-product-type-action"
                            name="sum-percent-product-type-action"
                            disabled>
                        <option value="" selected> Выберите тип товара</option>
                        <option value="total"> Total</option>
                        <option value="1"> Up sell 1</option>
                        <option value="2"> Up sell 2</option>
                        <option value="4"> Cross sell</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plan-create.js') }}"></script>

@stop


