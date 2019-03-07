@extends('layouts.app')

@section('title')Открытые заказы@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/reports/orders-opened.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Открытые заказы</span></li>
            </ol>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url()}}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <div class="clearfix" style="font-size: 12px">
                                    @php
                                        if(isset($_GET['status'])){
                                          $array =  explode(',', $_GET['status']);
                                        }
                                    @endphp
                                    @if(isset($_GET['status'])&&$_GET['status'] !='Все'&& in_array(3,$array ))
                                        <div class="col-sm-4">
                                            <h4>Завершен без цели
                                                <span class="badge badge-info">
                                                    {{isset($ordersOpenedQuantity->ordersWithoutTargetQuantity) ?
                                                    $ordersOpenedQuantity->ordersWithoutTargetQuantity : 0}}
                                                </span>
                                            </h4>
                                        </div>
                                    @elseif(isset($_GET['status'])&&$_GET['status'] !='Все'&& in_array(4,$array ))
                                        <div class="col-sm-4">
                                            <h4> @lang('general.call-back')
                                                <span class="badge badge-success">
                                                    {{isset($ordersOpenedQuantity->ordersCallBackTargetQuantity) ?
                                                    $ordersOpenedQuantity->ordersCallBackTargetQuantity : 0}}
                                                </span>
                                            </h4>
                                        </div>
                                    @elseif(isset($_GET['status'])&&$_GET['status'] !='Все'&& in_array(5,$array ))
                                        <div class="col-sm-4">
                                            <h4>Говорит на другом языке
                                                <span class="badge badge-warning">
                                                    {{isset($ordersOpenedQuantity->ordersOtherLanguageTargetQuantity) ?
                                                    $ordersOpenedQuantity->ordersOtherLanguageTargetQuantity : 0 }}
                                                </span>
                                            </h4>
                                        </div>
                                    @else
                                        <div class="row">
                                            <h1 class="pull-left">Открытые заказы
                                                <span class="badge">
                                                    {{$ordersOpenedQuantity->ordersOpenedQuantity}}
                                                </span>
                                            </h1>
                                        </div>
                                        <div class=" row">
                                            <div class="col-sm-4">
                                                <h4>Подтвержденные
                                                    <span class="badge badge-primary">
                                                        {{isset($ordersOpenedQuantity->ordersOpenedApprovedQuantityAssigned) ?
                                                        $ordersOpenedQuantity->ordersOpenedApprovedQuantityAssigned :
                                                        $ordersOpenedQuantity->ordersOpenedApprovedQuantity}}
                                                    </span>
                                                </h4>
                                                <h4>Отказ
                                                    <span class="badge badge-danger">
                                                        {{$ordersOpenedQuantity->ordersOpenedRejectedQuantity}}
                                                    </span>
                                                </h4>
                                            </div>
                                            <div class="col-sm-4">
                                                <h4>Аннулированные
                                                    <span class="badge badge-warning">
                                                        {{$ordersOpenedQuantity->ordersOpenedCanceledQuantity}}
                                                    </span>
                                                </h4>
                                                <h4>Завершен без цели
                                                    <span class="badge badge-info">
                                                        {{isset($ordersOpenedQuantity->ordersWithoutTargetQuantity) ?
                                                         $ordersOpenedQuantity->ordersWithoutTargetQuantity : 0}}
                                                    </span>
                                                </h4>
                                            </div>
                                            <div class="col-sm-4">
                                                <h4>@lang('general.call-back')
                                                    <span class="badge badge-success">
                                                        {{isset($ordersOpenedQuantity->ordersCallBackTargetQuantity) ?
                                                         $ordersOpenedQuantity->ordersCallBackTargetQuantity : 0}}
                                                    </span>
                                                </h4>
                                                <h4>Говорит на другом языке
                                                    <span class="badge badge-warning">
                                                        {{isset($ordersOpenedQuantity->ordersOtherLanguageTargetQuantity) ?
                                                         $ordersOpenedQuantity->ordersOtherLanguageTargetQuantity : 0 }}
                                                    </span>
                                                </h4>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <br>
                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="id" class="col-sm-4 control-label">ID</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="id" name="id"
                                                   value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="oid" class="col-sm-4 control-label">OID</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="oid" name="oid"
                                                   value="@if (isset($_GET['oid'])){{ $_GET['oid'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="country" class="col-sm-4 control-label">Страна</label>
                                        <div class="col-sm-8">
                                            <select id="country" name="country[]" style="width: 100%" multiple>
                                                @foreach ($countries as $covalue)
                                                    <option
                                                            @if (isset($_GET['country']))
                                                            <? $countryGet = explode(',', $_GET['country']); ?>
                                                            @foreach ($countryGet as $cg)
                                                            @if (mb_strtolower($covalue->code) == $cg)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="{{ mb_strtolower($covalue->code) }}">
                                                        @lang('countries.' . $covalue->code)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if (isset($permissions['filter_companies_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="company" class="col-sm-4 control-label">Компания</label>
                                            <div class="col-sm-8">
                                                <select id="company" name="company[]" style="width: 100%" multiple>
                                                    @foreach ($companies as $company)
                                                        <option
                                                                @if (isset($_GET['company']))
                                                                <? $companyGet = explode(',', $_GET['company']); ?>
                                                                @foreach ($companyGet as $cg)
                                                                @if ($company->id == $cg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $company->id }}">{{ $company->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_target_user_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="user" class="col-sm-4 control-label">Оператор</label>
                                            <div class="col-sm-8">
                                                <select id="user" name="user[]" style="width: 100%" multiple>
                                                    @if ($users)
                                                        @foreach ($users as $user)
                                                            <option
                                                                    @if (isset($_GET['user']))
                                                                    <? $usersGet = explode(',', $_GET['user']); ?>
                                                                    @foreach ($usersGet as $usg)
                                                                    @if ($user->id == $usg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $user->id }}">{{ $user->name . ' ' . $user->surname }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_proc_status_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="status" class="col-sm-4 control-label">Статус</label>
                                            <div class="col-sm-8">
                                                <select id="status" name="status[]"
                                                        style="width: 100%" multiple>
                                                    {{--<option value="" selected>Все</option>--}}
                                                    <?
                                                    $dataProcStatus = [
                                                        1 => 'Автоответчик',
                                                        2 => 'Плохая связь, перезвонить',
                                                        3 => 'Завершен без цели',
                                                        4 => 'Call back',
                                                        5 => 'Говорит на другом языке'
                                                    ];
                                                    ?>
                                                    @if ($dataProcStatus)
                                                        @foreach ($dataProcStatus as $key => $status)
                                                            <option
                                                                    @if (isset($_GET['status']))
                                                                    <? $statusGet = explode(',', $_GET['status']); ?>
                                                                    @foreach ($statusGet as $stg)
                                                                    @if ($key == $stg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $key }}">{{ $status }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_target_status_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="target"
                                                   class="col-sm-4 control-label"> @lang('general.target-status')</label>
                                            <div class="col-sm-8">
                                                <select id="target" name="target[]" style="width: 100%" multiple>
                                                    <?
                                                    $dataTargets = [
                                                        1 => trans('general.approved'),
                                                        2 => trans('general.refusal'),
                                                        3 => trans('general.annulled'),
                                                        10 => trans('general.without-target'),
                                                    ];
                                                    ?>
                                                    @if ($dataTargets)
                                                        @foreach ($dataTargets as $key => $status)
                                                            <option
                                                                    @if (isset($_GET['target']))
                                                                    <? $statusGet = explode(',', $_GET['target']); ?>
                                                                    @foreach ($statusGet as $stg)
                                                                    @if ($key == $stg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $key }}">{{ $status }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm-4">
                                        <div class="checkbox-nice">
                                            <input type="checkbox" id="operator_assigned" name="operator_assigned"
                                                   @if (isset($_GET['operator_assigned'])
                                                   && $_GET['operator_assigned'] == 'on') checked
                                                    @endif>
                                            <label for="operator_assigned">
                                                Закреплены за операторами
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class='main-box-body clearfix section_filter'>
                                    <div class='main-box-body clearfix'>
                                    </div>
                                    <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;">Дата</div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="date_start">С</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control" id="date_start" type="text"
                                                       data-toggle="tooltip" name="date_start"
                                                       data-placement="bottom"
                                                       value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="date_end">До</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control" id="date_end" type="text"
                                                       data-toggle="tooltip" name="date_end"
                                                       data-placement="bottom"
                                                       value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                                        <div class="input-group" style="padding-top: 7px">
                                            <div class="btn-group" data-toggle="buttons" id="date_template">
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template" value="1"> Сегодня
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template" value="5"> Вчера
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template" value="9"> Неделя
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template" value="10"> Месяц
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template" value="2"> Прошлый месяц
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='Фильтровать'/>
                        <a href="{{ route('reports-orders-opened') }}" class="btn btn-warning" type="submit">
                            Сбросить фильтр
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        @if ($ordersOpened)
                            <div class="table-responsive">
                                <table id="orders" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Заказ</th>
                                        <th class="text-center">Страна</th>
                                        <th class="text-center">Компания</th>
                                        <th class="text-center">Дата <br> открытия <br> заказа</th>
                                        <th class="text-center">Дата <br> установки <br> цели</th>
                                        <th class="text-center">Оператор</th>
                                        <th class="text-center">Цель/CallBack</th>
                                        <th class="text-center">Закреплен</th>
                                        <th class="text-center">Причина</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($ordersOpened as $orderOpened)
                                        @php
                                            $status = '';
                                                 switch ($orderOpened->callback) {
                                                        case 5: {
                                                            $status = 'Другой язык';
                                                            $class = 'label-default';
                                                            $classRow = 'default';
                                                            break;
                                                            }
                                                        case 2:
                                                            {
                                                                $status = 'Плохая связь';
                                                                $class = 'label-success';
                                                                   $classRow = 'success';
                                                                break;
                                                            }
                                                        case 1:{
                                                            $status = 'Автоответчик';
                                                            $class = 'label-default';
                                                            $classRow = 'default';
                                                            break;
                                                            }
                                                    }
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                {{$orderOpened->id}}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('order', $orderOpened->order_id) }}" class="crm_id">
                                                    {{$orderOpened->order_id}}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if (!empty($orderOpened->order->geo))
                                                    <img class="country-flag"
                                                         src="{{ URL::asset('img/flags/' . mb_strtoupper($orderOpened->order->geo) . '.png') }}"/>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{isset($orderOpened->user->company->name) ? $orderOpened->user->company->name : '--' }}
                                            </td>
                                            <td class="text-center ">
                                                <div class="order_phone_block">
                                                    {{$orderOpened->date_opening }}
                                                </div>
                                            </td>
                                            <td class="text-center ">
                                                <div class="order_phone_block">
                                                    {{$orderOpened->target_status_time ?? '--' }}
                                                </div>
                                            </td>
                                            <td class="text-center ">
                                                <a href="/profile/{{$orderOpened->user_id}}">
                                                    {{(isset($orderOpened->user->name)  ? $orderOpened->user->name : '').
                                                    '  ' .(isset($orderOpened->user->surname) ? $orderOpened->user->surname :'')}}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $target = '';
                                                    $classLabel = '';
                                                    $classRow = '';
                                                    $classBtn = '';
                                                    $status = '';
                                                @endphp
                                                @if(!empty($orderOpened->target_status))
                                                    @php
                                                        $target = '';
                                                        $classLabel = '';
                                                        $classRow = '';
                                                        $classBtn = '';
                                                        switch($orderOpened->target_status) {
                                                        case 1:{
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
                                                    @endphp
                                                    <span class="badge {{$classLabel}}">{{$target}}</span>
                                                @elseif(!empty($orderOpened->callback) && empty($orderOpened->target_status))
                                                    @php
                                                        switch ($orderOpened->callback) {
                                                        case 5: {
                                                        $status = 'Другой язык';
                                                        $class = 'label-default';
                                                        $classRow = 'default';
                                                        break;
                                                        }
                                                        case 1:{
                                                        $status = 'Автоответчик';
                                                        $class = 'label-default';
                                                        $classRow = 'default';
                                                        break;
                                                        }
                                                        case 2:
                                                        {
                                                        $status = 'Плохая связь';
                                                        $class = 'label-success';
                                                        $classRow = 'success';
                                                        break;
                                                        }
                                                        case 3:
                                                        {
                                                        $status = 'Просит перезвонить';
                                                        $class = 'label-default';
                                                        $classRow = 'default';
                                                        break;
                                                        }
                                                        }
                                                    @endphp
                                                    <span class="badge {{$class}}">{{$status}}</span>
                                                @else
                                                    <span class="badge badge-danger">Завершен без цели</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(isset($orderOpened->order->target_user) && $orderOpened->order->target_user == $orderOpened->user_id &&
                                                $orderOpened->target_status == 1 )
                                                    <i class="fa fa-check-square-o"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $cause = '';
                                                    $causeClassLabel = '';
                                                    $causeClassRow = '';
                                                @endphp
                                                @if(!empty($orderOpened->target_status))
                                                    @php
                                                        switch($orderOpened->target_status_type) {
                                                        case 5:{
                                                        $cause = 'Повтор';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }
                                                        case 6:{
                                                        $cause = 'Не корректный телефон';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }
                                                        case 7:{
                                                        $cause = 'Не корректные данные';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }   case 8:{
                                                        $cause = 'Сервис';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }   case 9:{
                                                        $cause = 'Нет в наличие';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }   case 10:{
                                                        $cause = 'Доставка не возможна';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }  case 11:{
                                                        $cause = 'Сопровождение';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        } case 12:{
                                                        $cause = 'Недозвон';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }case 13:{
                                                        $cause = 'Не заказывал';
                                                        $causeClassLabel = 'label-warning';
                                                        break;
                                                        }
                                                        }
                                                    @endphp
                                                    <span class="label {{$causeClassLabel}}">{{$cause}}</span>
                                                @elseif(empty($orderOpened->callback) && empty($orderOpened->target_status))
                                                    <i class="fa fa-warning"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
                {{$ordersOpened->links()}}
            </div>
        </div>
    </div>
@stop
