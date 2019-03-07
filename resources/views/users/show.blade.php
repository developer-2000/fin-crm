@extends('layouts.app')

@section('title')Кабинет@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/morris.css')}}" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/profile.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <style>
        .opacityTr {
            opacity: .5;
        }
    </style>

@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="clearfix">
                            <h1 class="pull-left">Кабинет</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="user-profile">
        <div class="col-lg-3 col-md-4 col-sm-4">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <img src="{{$userProfile->photo}}" alt=""
                         class="profile-img img-responsive center-block"/>
                    <header class="main-box-header clearfix text-center">
                        <h2>{{$userProfile->surname}} {{$userProfile->name}}</h2>
                    </header>

                    <div class="profile-label">
                        <span class="label label-danger">{{$userProfile->role}}</span>
                        {{--<span class="profile-status">--}}
                        {{--<i class="fa fa-circle"></i> Online--}}
                        {{--</span>--}}
                    </div>
                    {{--<div class="profile-stars">--}}
                    {{--<i class="fa fa-star"></i>--}}
                    {{--<i class="fa fa-star"></i>--}}
                    {{--<i class="fa fa-star"></i>--}}
                    {{--<i class="fa fa-star"></i>--}}
                    {{--<i class="fa fa-star-o"></i>--}}
                    {{--<span>Super User</span>--}}
                    {{--</div>--}}
                    {{--<div class="profile-since">--}}
                    {{--Дата создании: Jan 2012--}}
                    {{--</div>--}}
                    <div class="profile-details">
                        <i class="fa fa-money blue-bg"></i>
                        <header class="main-box-header clearfix text-center">
                            <p>Общий баланс</p>
                            <p class="balance">{{$salary['all']}} грн</p>
                        </header>
                    </div>
                    {{--<div class="profile-message-btn center-block text-center">--}}
                    {{--<a href="#" class="btn btn-success">--}}
                    {{--<i class="fa fa-envelope"></i>--}}
                    {{--Send message--}}
                    {{--</a>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        @if ($userProfile->billing_type && $userProfile->roleNumb == 1)
                            <li @if (!$tab) class="active" @endif>
                                <a href="{{route('user', $userProfile->id)}}">Текущий баланс</a>
                            </li>
                            <li @if ($tab == 'transaction') class="active" @endif>
                                <a href="{{route('user', $userProfile->id)}}/transaction">Транзакции</a>
                            </li>
                            <li @if ($tab == 'payout') class="active" @endif><a
                                        href="{{route('user', $userProfile->id)}}/payout">Выплаты</a></li>
                        @endif
                        @if (auth()->user()->id == $userProfile->id)
                                <li @if ($tab == 'settings') class="active" @endif><a href="{{route('user', $userProfile->id)}}/settings">Настройки</a></li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if (!$tab)
                            <div class="tab-pane fade in active" id="balance">
                                <div class="row">
                                    @if ($userProfile->billing_type)
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="main-box small-graph-box emerald-bg">
                                                <div class="box-button">
                                                    <a href="#" class="box-close tooltips" data-toggle="tooltip"
                                                       title="Close Panel"><i class="fa fa-times"></i></a>
                                                </div>
                                                <span class="value">{{$salary['today']}} грн</span>
                                                <span class="headline">За сегодня</span>
                                                {{--<div class="progress">--}}
                                                {{--<div style="width: 84%;" aria-valuemax="100" aria-valuemin="0"--}}
                                                {{--aria-valuenow="84" role="progressbar" class="progress-bar">--}}
                                                {{--<span class="sr-only">84% Complete</span>--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<span class="subinfo">--}}
                                                {{--<i class="fa fa-caret-down"></i> 22% less than last week--}}
                                                {{--</span>--}}
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="main-box small-graph-box blue-bg">
                                                <div class="box-button">
                                                    <a href="#" class="box-close tooltips" data-toggle="tooltip"
                                                       title="Close Panel"><i class="fa fa-times"></i></a>
                                                </div>
                                                <span class="value">{{$salary['yesterday']}} грн</span>
                                                <span class="headline">За вчера</span>
                                                {{--<div class="progress">--}}
                                                {{--<div style="width: 42%;" aria-valuemax="100" aria-valuemin="0"--}}
                                                {{--aria-valuenow="42" role="progressbar" class="progress-bar">--}}
                                                {{--<span class="sr-only">42% Complete</span>--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<span class="subinfo">--}}
                                                {{--<i class="fa fa-caret-up"></i> 15% higher than last week--}}
                                                {{--</span>--}}
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="main-box small-graph-box purple-bg">
                                                <div class="box-button">
                                                    <a href="#" class="box-close tooltips" data-toggle="tooltip"
                                                       title="Close Panel"><i class="fa fa-times"></i></a>
                                                </div>
                                                <span class="value">{{$salary['week']}} грн</span>
                                                <span class="headline">За неделю</span>
                                                {{--<div class="progress">--}}
                                                {{--<div style="width: 77%;" aria-valuemax="100" aria-valuemin="0"--}}
                                                {{--aria-valuenow="77" role="progressbar" class="progress-bar">--}}
                                                {{--<span class="sr-only">77% Complete</span>--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<span class="subinfo">--}}
                                                {{--<i class="fa fa-caret-down"></i> 22% More than last week--}}
                                                {{--</span>--}}
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="main-box small-graph-box red-bg">
                                                <div class="box-button">
                                                    <a href="#" class="box-close tooltips" data-toggle="tooltip"
                                                       title="Close Panel"><i class="fa fa-times"></i></a>
                                                </div>
                                                <span class="value">{{$salary['month']}} грн</span>
                                                <span class="headline">За месяц</span>
                                                {{--<div class="progress">--}}
                                                {{--<div style="width: 60%;" aria-valuemax="100" aria-valuemin="0"--}}
                                                {{--aria-valuenow="60" role="progressbar" class="progress-bar">--}}
                                                {{--<span class="sr-only">60% Complete</span>--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<span class="subinfo">--}}
                                                {{--<i class="fa fa-caret-up"></i> 10% higher than last week--}}
                                                {{--</span>--}}
                                            </div>
                                        </div>
                                    @endif
                                    {{--<div class="col-sm-12">--}}
                                    {{--<table class="table">--}}
                                    {{--<thead>--}}
                                    {{--<tr>--}}
                                    {{--<th class="text-center">Открылось</th>--}}
                                    {{--<th class="text-center">Подтвержденые</th>--}}
                                    {{--<th class="text-center">Отказ</th>--}}
                                    {{--<th class="text-center">Аннулированные</th>--}}
                                    {{--<th class="text-center">Up</th>--}}
                                    {{--<th class="text-center">Up2</th>--}}
                                    {{--<th class="text-center">Cross</th>--}}
                                    {{--<th class="text-center">Время в CRM</th>--}}
                                    {{--<th class="text-center">Время в PBX</th>--}}
                                    {{--<th class="text-center">Время разговора</th>--}}
                                    {{--<th class="text-center">Обед</th>--}}
                                    {{--<th class="text-center">Оформеление</th>--}}
                                    {{--</tr>--}}
                                    {{--</thead>--}}
                                    {{--<tbody>--}}
                                    {{--<tr>--}}
                                    {{--<td class="text-center">{{$statistic['opened']}}</td>--}}
                                    {{--<td class="text-center">{{$statistic['approve']}}</td>--}}
                                    {{--<td class="text-center">{{$statistic['failure']}}</td>--}}
                                    {{--<td class="text-center">{{$statistic['fake']}}</td>--}}
                                    {{--<td class="text-center">{{$statistic['up_sell']}}</td>--}}
                                    {{--<td class="text-center">{{$statistic['up_sell_2']}}</td>--}}
                                    {{--<td class="text-center">{{$statistic['cross_sell']}}</td>--}}
                                    {{--<td class="text-center">{{dateProcessing($statistic['login_time_crm'])}}</td>--}}
                                    {{--<td class="text-center">{{dateProcessing($statistic['login_time_elastix'])}}</td>--}}
                                    {{--<td class="text-center">{{dateProcessing($statistic['talk_time'])}}</td>--}}
                                    {{--<td class="text-center">{{dateProcessing($statistic['pause_time'])}}</td>--}}
                                    {{--<td class="text-center">{{dateProcessing($statistic['order_time'])}}</td>--}}
                                    {{--</tr>--}}
                                    {{--</tbody>--}}
                                    {{--</table>--}}
                                    {{--</div>--}}

                                </div>
                                {{--<div class="row">--}}
                                {{--<div class="col-lg-12">--}}
                                {{--<div class="main-box">--}}
                                {{--<header class="main-box-header clearfix">--}}
                                {{--<h2>Статистика</h2>--}}
                                {{--</header>--}}
                                {{--<div class="main-box-body clearfix">--}}
                                {{--<div id="hero-area"></div>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                            </div> @endif
                        @if ($tab == 'transaction')
                            <div class="tab-pane fade in active " id="statistics">
                                <form class="form-inline" method="get"
                                      action="{{ route("profile", $userProfile->id) }}/transaction">
                                    <div class="form-group">
                                        <label for="date_start">Дата с:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" id="date_start" name="date_start"
                                                   value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : date('d.m.Y', \Carbon\Carbon::now()->startOfMonth()->timestamp) }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_end">Дата до:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" id="date_end" name="date_end"
                                                   value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : date('d.m.Y', time()) }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type='submit' class="btn btn-success" value="Фильтровать">
                                    </div>
                                </form>
                                @if ($orders['orders'])
                                    <div class="main-box-body clearfix" style="margin-top: 20px;">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th class="text-center">Страна</th>
                                                    <th class="text-center">Дата</th>
                                                    <th class="text-center">Тип</th>
                                                    <th class="text-center">Оффер/Товар/Комментарий</th>
                                                    <th class="text-center">Approve</th>
                                                    <th class="text-center">Up</th>
                                                    <th class="text-center">Up2</th>
                                                    <th class="text-center">Cross</th>
                                                    <th class="text-center">Сумма</th>
                                                    <th class="text-center">Инициатор</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($orders['orders'] as $order)
                                                    <?
                                                    switch ($order->type) {
                                                        case 'approve':
                                                            {
                                                                $class = '';
                                                                $text = 'Начисление';
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
                                                    <tr @if ($order->payout_id) class="success opacityTr" @endif>
                                                        <td>{{$order->id}}</td>
                                                        <td class="text-center">
                                                            @if ($order->geo)
                                                                <img class="country-flag"
                                                                     src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}" />
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="time">{{\Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}</div>
                                                            <div class="date">{{\Carbon\Carbon::parse($order->time_created)->format('d/m/y')}}</div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="type {{$class}}">{{$text}}</div>
                                                        </td>
                                                        <td class="text-left">
                                                            @if($order->type == 'approve' && $order->cType == 'lead')
                                                                <b style="font-size: 13px;">{{$order->offer}}</b><br>
                                                                @if (isset($transaction['products'][$order->order_id]))
                                                                    @foreach($transaction['products'][$order->order_id] as $product)
                                                                        <div class="products">
                                                                            {{$product['title']}}
                                                                            - {{$product['price']}} {{$product['currency']}}
                                                                            @if ($product['type'] == 1)
                                                                                <span class="label label-success ">Up Sell {{$order->up1}}</span>
                                                                            @elseif ($product['type'] == 2)
                                                                                <span class="label label-primary ">Up Sell 2 {{$order->up2}}</span>
                                                                            @elseif ($product['type'] == 4)
                                                                                <span class="label label-info ">Cross Sell {{$order->cross}}</span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            @elseif ($order->type == 'approve' && $order->cType == 'hour')
                                                                <div class="comment">
                                                                    Время в CRM : {{dateProcessing($order->time_crm)}}
                                                                    <br>
                                                                    Время в PBX : {{dateProcessing($order->time_pbx)}}
                                                                    <br>
                                                                    Время разговора
                                                                    : {{dateProcessing($order->talk_time)}} <br>
                                                                </div>
                                                            @else
                                                                <div class="comment">
                                                                    {!! $order->comment !!}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            {{$order->approve}} грн
                                                        </td>
                                                        <td class="text-center">
                                                            {{$order->up1}} грн
                                                        </td>
                                                        <td class="text-center">
                                                            {{$order->up2}} грн
                                                        </td>
                                                        <td class="text-center">
                                                            {{$order->cross}} грн
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="type {{$class}}">{{$order->balance}} грн</div>
                                                        </td>
                                                        <td class="text-center">
                                                            <b>{{$order->initiator}}</b>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if ($orders['pagination'] && count($orders['pagination'][0]) > 1)

                                            <ul class="pagination pull-right">
                                                <li>
                                                    <a href="{{ route('users-edit', $userProfile->id) }}/transaction{{ ($orders['pagination'][3]) ? $orders['pagination'][3] : '' }}"><i
                                                                class="fa fa-chevron-left"></i></a></li>
                                                @foreach ($orders['pagination'][0] as $number)
                                                    <? $activaPage = '' ?>
                                                    @if ($orders['pagination'][1] == $number)
                                                        <li class=active><span>{{ $number }}</span></li>
                                                    @else
                                                        @if ($number == 1)
                                                            <li>
                                                                <a href="{{ route('users-edit', $userProfile->id) }}/transaction{{ ($orders['pagination'][3]) ? $orders['pagination'][3] : '' }}">{{ $number }}</a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a href="{{ route('users-edit', $userProfile->id) }}/transaction{{ ($orders['pagination'][3]) ? $orders['pagination'][3] . '&page=' . $number : '?page=' . $number }}">{{ $number }}</a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                                <li>
                                                    <a href="{{ route('users-edit', $userProfile->id) }}/transaction{{ ($orders['pagination'][3]) ? $orders['pagination'][3] . '&page=' . $orders['pagination'][2] : '?page=' . $orders['pagination'][2] }}"><i
                                                                class="fa fa-chevron-right"></i></a></li>
                                            </ul>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if ($tab == 'payout')
                        <div class="tab-pane clearfix fade in active " id="payout">
                            <div class="main-box-body clearfix"></div>
                                <div class="main-box-body clearfix">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th class="text-center">Дата</th>
                                                <th class="text-center">Выплотил</th>
                                                <th class="text-center">Получатель</th>
                                                <th class="text-center">В период</th>
                                                <th class="text-center">Комментарий</th>
                                                <th class="text-center">Сумма</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if ($payouts)
                                                @foreach($payouts as $payout)
                                                    <tr>
                                                        <td class="text-center">
                                                            <div class="time">
                                                                {{\Carbon\Carbon::parse($payout->time_created)->format('H:i:s')}}
                                                            </div>
                                                            <div class="date">
                                                                {{\Carbon\Carbon::parse($payout->time_created)->format('d/m/y')}}
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            {{$payout->initiatorSurname}} {{$payout->initiatorName}}
                                                        </td>
                                                        <td class="text-center">
                                                            {{$payout->operSurname}} {{$payout->operName}}
                                                        </td>
                                                        <td class="text-center">{{\Carbon\Carbon::parse($payout->period_start)->format('d/m/y')}}</td>
                                                        <td class="text-center">{{\Carbon\Carbon::parse($payout->period_end)->format('d/m/y')}}</td>
                                                        <td class="text-left">
                                                            <div class="comment">
                                                                {!! $payout->comment !!}
                                                            </div>
                                                        </td>
                                                        <td class="text-center salary">{{$payout->valuation}} грн</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($pagination && count($pagination[0]) > 1)

                                        <ul class="pagination pull-right">
                                            <li>
                                                <a href="{{ route('users-edit', $userProfile->id) }}/payout{{ ($pagination[3]) ? $pagination[3] : '' }}"><i
                                                            class="fa fa-chevron-left"></i></a></li>
                                            @foreach ($pagination[0] as $number)
                                                <? $activaPage = '' ?>
                                                @if ($orders['pagination'][1] == $number)
                                                    <li class=active><span>{{ $number }}</span></li>
                                                @else
                                                    @if ($number == 1)
                                                        <li>
                                                            <a href="{{ route('users-edit', $userProfile->id) }}/payout{{ ($pagination[3]) ? $pagination[3] : '' }}">{{ $number }}</a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="{{ route('users-edit', $userProfile->id) }}/payout{{ ($pagination[3]) ? $pagination[3] . '&page=' . $number : '?page=' . $number }}">{{ $number }}</a>
                                                        </li>
                                                    @endif
                                                @endif
                                            @endforeach
                                            <li>
                                                <a href="{{ route('users-edit', $userProfile->id) }}/payout{{ ($pagination[3]) ? $pagination[3] . '&page=' . $pagination[2] : '?page=' . $pagination[2] }}"><i
                                                            class="fa fa-chevron-right"></i></a></li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if ($tab == 'settings')
                                <div class="tab-pane clearfix fade in active " id="settings">
                                    <div class="main-box-body clearfix"></div>
                                    <div class="main-box-body clearfix">
                                        <div class="col-md-6">
                                            <form class="form-horizontal" role="form" id="user_settings">
                                                <input type="hidden" name="id" value="{{$userProfile->id}}">
                                                <div class="form-group">
                                                    <label for="time_zone" class="col-lg-4 control-label">Часовой пояс</label>
                                                    <div class="col-lg-8">
                                                        <select id="time_zone" name="time_zone">
                                                            @foreach(timezone_identifiers_list() as $timezone)
                                                                <option value="{{$timezone}}" @if ($userProfile->time_zone == $timezone) selected @endif>{{$timezone}} {{\Carbon\Carbon::now($timezone)->format('P')}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="error_messages"></div>
                                                <div class="text-center">
                                                    <input type="submit" class="btn btn-success" value="Сохранить">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/raphael-min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/morris.js') }}"></script>
    <script src="{{ URL::asset('js/users/profile.js') }}"></script>
@stop