@extends('layouts.app')

@section('title') @lang('finance.transactions') @stop

@section('css')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/profile.css') }}"/>
    <style>
        .opacityTr {
            opacity: .5;
        }
    </style>
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/finance/transaction-company.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('finance.operator-transactions')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('finance.operator-transactions')</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        @if (isset($permissions['page_finance_operators']))
                            <li>
                                <a href="{{route('balance-users')}}"> @lang('general.balance')</a>
                            </li>
                        @endif
                        <li class="active">
                            <a href="{{route('transaction-users')}}"> @lang('finance.transactions')</a>
                        </li>
                        @if (isset($permissions['page_payouts_operators']))
                            <li>
                                <a href="{{ route('payouts-users') }}"> @lang('finance.payment')</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <form class="form" method="post" action="{{ route("transaction-users") }}"
                                  style="padding: 0;">
                                <div class="main-box">
                                    <div class="item_rows ">
                                        <div class="main-box-body clearfix">
                                            <div class="row">
                                                <div class="col-sm-1">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="trans_payed" name="trans_payed"
                                                               @if (isset($_GET['trans_payed'])) checked @endif>
                                                        <label for="trans_payed">
                                                            @lang('finance.paid')
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2 col-sm-6 form-horizontal">
                                                    <label for="id" class="col-sm-4 control-label"> @lang('general.id')</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="id" name="id"
                                                               data-toggle="tooltip"
                                                               data-placement="bottom" title="ID"
                                                               value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                    <label for="oid" class="col-sm-4 control-label"> @lang('finance.paid')</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="oid" name="oid"
                                                               data-toggle="tooltip"
                                                               data-placement="bottom" title=" @lang('general.oid')"
                                                               value="@if (isset($_GET['oid'])){{ $_GET['oid'] }}@endif">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                    <label for="country" class="col-sm-4 control-label">Страна</label>
                                                    <div class="col-sm-8">
                                                        <select id="country" name="country[]" style="width: 100%"
                                                                multiple>
                                                            @foreach ($country as $covalue)
                                                                <option
                                                                        @if (isset($_GET['country']))
                                                                        <? $countryGet = explode(',', $_GET['country']); ?>
                                                                        @foreach ($countryGet as $cg)
                                                                        @if (mb_strtolower($covalue->code) == $cg)
                                                                        selected
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                        value="{{mb_strtolower($covalue->code) }}">
                                                                    @lang('countries.' . $covalue->code)
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                @if (isset($permissions['filter_companies_page_transaction_users']))
                                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                        <label for="company"
                                                               class="col-sm-4 control-label"> @lang('general.company')</label>
                                                        <div class="col-sm-8">
                                                            <select id="company" name="company[]" style="width: 100%"
                                                                    multiple>
                                                                @if($companies)
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
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>@endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item_rows">
                                        <div class="main-box-body clearfix">
                                            <div class="row">
                                                <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i
                                                                        class="fa fa-calendar"></i></span>
                                                            <input class="form-control" id="date_start" type="text"
                                                                   placeholder=" @lang('general.from')" data-toggle="tooltip"
                                                                   name="date_start"
                                                                   data-placement="bottom" title=" @lang('general.from')"
                                                                   value="@if (isset($_GET['date_start'])){{ $_GET['date_start'] }}@endif">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i
                                                                        class="fa fa-calendar"></i></span>
                                                            <input class="form-control" id="date_end" type="text"
                                                                   data-toggle="tooltip" placeholder=" @lang('general.to')"
                                                                   name="date_end"
                                                                   data-placement="bottom" title=" @lang('general.to')"
                                                                   value="@if (isset($_GET['date_end'])){{ $_GET['date_end']}}@endif">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 col-sm-6 tags ">
                                                    <div class="input-group ">
                                                        <div class="btn-group" data-toggle="buttons" id="date_template">
                                                            <label class="btn btn-primary">
                                                                <input type="radio" name="date_template" value="1">
                                                                @lang('general.today')
                                                            </label>
                                                            <label class="btn btn-primary">
                                                                <input type="radio" name="date_template" value="5">
                                                                @lang('general.yesterday')
                                                            </label>
                                                            <label class="btn btn-primary">
                                                                <input type="radio" name="date_template" value="9">
                                                                @lang('general.week')
                                                            </label>
                                                            <label class="btn btn-primary">
                                                                <input type="radio" name="date_template" value="10">
                                                                @lang('general.month')
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btns_filter">
                                    <input class="btn btn-success" type="submit" name="button_filter"
                                           value='@lang('general.search')'/>
                                    <a href="{{ route('transaction-users') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                                </div>

                            </form>
                            @if ($orders)
                                <div class="main-box-body clearfix" style="margin-top: 20px;padding: 0 0 20px 0;">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th> @lang('general.id')</th>
                                                <th class="text-center"> @lang('general.type')</th>
                                                <th class="text-center"> @lang('general.date')</th>
                                                <th class="text-center"> @lang('general.order') @lang('general.id')</th>
                                                <th class="text-center"> @lang('general.country')</th>
                                                <th class="text-center"> @lang('general.offer')/@lang('general.product')/@lang('general.comment')</th>
                                                <th class="text-center"> @lang('general.sum')</th>
                                                <th class="text-center"> @lang('general.initiator')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($orders as $order)
                                                <?
                                                switch ($order->type) {
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
                                                    case 'week' :
                                                        {
                                                            $class = 'default';
                                                            $text = 'Недельная';
                                                            break;
                                                        }
                                                    case 'month' :
                                                        {
                                                            $class = 'default';
                                                            $text = 'Месячная';
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
                                                        <div class="type {{$class}}">{{$text}}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="time">{{\Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}</div>
                                                        <div class="date">{{\Carbon\Carbon::parse($order->time_created)->format('d/m/y')}}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($order->order_id)
                                                            <div class="crm_id">
                                                                <a href="{{route('order', $order->order_id)}}">{{$order->order_id}}</a>
                                                            </div>
                                                            <div style="font-size: 12px;">
                                                                {{$order->operSurname}} {{$order->operName }}
                                                                <br>
                                                                {{$order->company}}
                                                            </div>
                                                        @elseif ($order->company)
                                                            <div style="font-size: 12px;">
                                                                {{$order->company}}
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($order->geo)
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png')  }}" />
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-left">
                                                        @if($order->type == 'approve' && $order->cType == 'lead')
                                                            <b style="font-size: 13px;">{{$order->offer}}</b><br>
                                                            @if (isset($products[$order->order_id]))
                                                                @foreach($products[$order->order_id] as $product)
                                                                    <div class="products">
                                                                        {{$product['title']}}
                                                                        - {{$product['price']}} {{$product['currency']}}
                                                                        @if ($product['type'] == 1)
                                                                            <span class="label label-success "> @lang('general.up-sell') {{$order->up1}}</span>
                                                                        @elseif ($product['type'] == 2)
                                                                            <span class="label label-primary "> @lang('general.up-sell') 2 {{$order->up2}}</span>
                                                                        @elseif ($product['type'] == 4)
                                                                            <span class="label label-info "> @lang('general.cross-sell') {{$order->cross}}</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        @elseif ($order->type == 'approve' && $order->cType == 'hour')
                                                            <div class="comment">
                                                                Время в CRM : {{dateProcessing($order->time_crm)}} <br>
                                                                Время в PBX : {{dateProcessing($order->time_pbx)}} <br>
                                                                Время разговора : {{dateProcessing($order->talk_time)}}
                                                                <br>
                                                            </div>
                                                        @elseif($order->type == 'bonus' && $order->result !== 0 ||
                                                      $order->type == 'retention' && $order->result !== 0)
                                                            <div class="comment" style="font-size: 13px;">
                                                                @lang('finance.according-plan') <a
                                                                        href='/plan/{{$order->plan_id}}'> {{$order->plan->name}}</a>
                                                            </div>
                                                        @else
                                                            <div class="comment">
                                                                {!! $order->comment !!}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{--{{dump($order)}}--}}
                                                        @if($order->type == 'bonus' && $order->result !== 0 )
                                                            <div class="type {{$class}}">{{$order->result}} грн</div>
                                                        @elseif($order->type == 'retention' && $order->result !== 0 )
                                                            <div class="type {{$class}}">{{$order->result}} грн</div>
                                                        @else
                                                            <div class="type {{$class}}">{{$order->balance}} грн</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <b>{{$order->initiator}}</b>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="pull-right">
                                        {{$orders->links()}}
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
