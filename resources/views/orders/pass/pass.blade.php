@extends('layouts.app')

@section('title') @lang('orders.passes')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jBox.all.min.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order.js?a=1') }}"></script>
    <script src="{{ URL::asset('js/vendor/jBox.all.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jBox.all.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $.fn.editable.defaults.mode = 'popup';

            $('.pass-reversal').editable({
                type: 'none',
                escape: true,
                pk: 1,
                title: "Вы действительно хотите сторнировать (аннулировать) проводку?",
                tpl: '',
                success: function (response) {
                    if (response.success) {
                        window.location = '/pass';
                    }
                }
            });
        });
        var options = {
            attach: '.pass-tooltip',
            getTitle: 'data-title',
            getContent: 'data-content',
            theme: 'TooltipBorderThick',
            maxWidth : 300,
        };
        new jBox(options);
        new jBox('Tooltip', options);
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('orders.passes')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('orders.passes')(<span class="badge">{{$passes->total()}}</span>) <i
                            class="fa fa-info-circle pass-tooltip"
                            id="Tooltip-2"
                            data-title=" @lang('orders.passes')"
                            data-content=" @lang('orders.passes-determination')"
                    ></i>
                    {{--definition -> Passes are a record of order results depending on their type --}}
                </h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @php
                $count = 0;
                if (isset($permissions['page_pass_redemption'])) $count ++;
                if (isset($permissions['page_pass_no_redemption'])) $count ++;
                if (isset($permissions['page_pass_sending'])) $count ++;
                $cell = $count ? round(12/$count) : 12;
                if ($cell < 2) $cell = 2;
            @endphp
            @if (isset($permissions['page_pass_redemption']))
                <div class="col-sm-{{$cell}}">
                    <a href="{{route('pass-redemption')}}" class="btn btn-success col-sm-12">
                        @lang('general.good-client')
                    </a>
                </div>
            @endif
            @if (isset($permissions['page_pass_no_redemption']))
                <div class="col-sm-{{$cell}}">
                    <a href="{{route('pass-no-redemption')}}" class="btn btn-danger col-sm-12">
                        @lang('general.bad-client')
                    </a>
                </div>
            @endif
            @if (isset($permissions['page_pass_sending']))
                <div class="col-sm-{{$cell}}">
                    <a href="{{route('pass-sending')}}" class="btn btn-info col-sm-12">
                        @lang('general.sending')
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url()}}"
                      method="post" id="filter" style="padding-top: 10px;">
                    <div class="main-box">
                        <div class="item_rows">
                            <div class='main-box-body clearfix '>
                                <div class="col-sm-3">
                                    <div class="form-group col-sm-12 form-horizontal">
                                        <label for="pass_id" class="col-sm-4 control-label">
                                            @lang('orders.pass-id')
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="pass_id" name="pass_id"
                                                   value="@if (isset($_GET['pass_id'])){{ $_GET['pass_id'] }}@endif">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group col-sm-12 form-horizontal">
                                        <label for="project" class="col-sm-3 control-label">
                                            @lang('general.project')
                                        </label>
                                        <div class="col-sm-9">
                                            @if(!auth()->user()->project_id)
                                                <input id="project"
                                                       data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                       class="project " name="project[]"
                                                       value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                       style="width: 100%">
                                            @else
                                                <input type="hidden" id="project"
                                                       class="project " name="project[]"
                                                       value="{{auth()->user()->project_id}}">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group col-sm-12 form-horizontal">
                                        <label for="sub_project" class="col-sm-4 control-label">
                                            @lang('general.subproject')
                                        </label>
                                        <div class="col-sm-8">
                                        <input id="sub_project"
                                               data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                               class="sub_project " name="sub_project[]"
                                               value="{{$dataSubProject ?? NULL}}"
                                               style="width: 100%">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group col-sm-12 form-horizontal">
                                        <label for="initiator" class="col-sm-4 control-label">
                                            @lang('general.initiator')
                                        </label>
                                        <div class="col-sm-8">
                                            <input id="initiator"
                                                   data-initiators="{{!empty($dataInitiators) ? $dataInitiators : ''}}"
                                                   class="initiator " name="initiator[]"
                                                   value="{{$dataInitiatorsIds ?? NULL}}" style="width: 100%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item_rows">
                        <div class='main-box-body clearfix '>
                            <div class='main-box-body clearfix'>
                            </div>
                            <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;">  @lang('general.date')</div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="date_start"> @lang('general.date-from')</label>
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
                                    <label for="date_end"> @lang('general.date-to')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="date_end" type="text"
                                               data-toggle="tooltip" name="date_end"
                                               data-placement="bottom"
                                               value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6" style="padding-top: 20px;padding-bottom: 10px;">
                                <div class="input-group">
                                    <div class="btn-group" data-toggle="buttons" id="date_template">
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="1"> @lang('general.today')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="5"> @lang('general.yesterday')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="9"> @lang('general.week')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="10"> @lang('general.month')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="2"> @lang('general.last-month')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success md-trigger" data-modal="search_block" type="submit"
                               name="button_filter" value='@lang('general.search')'/>
                        <a href="{{route('pass')}}" class="btn btn-warning"> @lang('general.reset')</a>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('orders.pass-id')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.type')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.user')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.date')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.orders')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.total')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cost')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cost-actual')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.income')
                                    </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ($passes->isNotEmpty())
                                    @foreach($passes as $pass)
                                        @php
                                            $class = '';
                                            $title = '';
                                            $type = '';
                                            switch ($pass->type) {
                                                case \App\Models\Pass::TYPE_REDEMPTION : {
                                                    $class = 'success';
                                                    $title = trans('general.good-client');
                                                    break;
                                                }
                                                case \App\Models\Pass::TYPE_NO_REDEMPTION : {
                                                    $class = 'danger';
                                                    $title = trans('general.bad-client');
                                                    break;
                                                }
                                                case \App\Models\Pass::TYPE_SENDING : {
                                                    $class = 'info';
                                                    $title = trans('general.sending');
                                                    break;
                                                }
                                                  case \App\Models\Pass::TYPE_REVERSAL : {
                                                    $class = 'warning';
                                                    $title = trans('statuses.reversal');
                                                    $type = 'reversal';
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{$pass->id}} </td>
                                            <td class="text-center {{$class}}">
                                                {{$title}}
                                                @if($type == 'reversal')
                                                    <a href="{{route('pass-one', $pass->origin_id)}}">
                                                        @lang('general.pass') {{$pass->origin_id}}</a>
                                                @endif
                                            </td>
                                            <td class="text-center">{{$pass->user ? implode(' ', [$pass->user->surname, $pass->user->name, $pass->user->middle]) : ''}}</td>
                                            <td class="text-center">
                                                <div>
                                                    <div class="col-sm-5"> @lang('general.date-created') :</div>
                                                    <div class="col-sm-7">
                                                        {{$pass->created_at}}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="col-sm-5">
                                                        @lang('orders.date-modified') :</div>
                                                    <div class="col-sm-7">
                                                        {{$pass->updated_at}}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{$pass->orders_pass_count}}
                                            </td>
                                            <td class="text-center">
                                                @if ($pass->type == \App\Models\Pass::TYPE_SENDING)
                                                    {{$pass->price_total_send}}
                                                @else
                                                    {{$pass->price_total}}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $cost = 0;
                                                    if ($pass->orders->isNotEmpty() && $pass->type != \App\Models\Pass::TYPE_SENDING) {
                                                        foreach ($pass->orders as $order) {
                                                            $json = json_decode($order->getTargetValue->values ?? '', true);
                                                            $cost += (float)($json['cost']['field_value'] ?? 0);
                                                        }
                                                    } elseif ($pass->ordersToSend->isNotEmpty() && $pass->type == \App\Models\Pass::TYPE_SENDING) {
                                                        foreach ($pass->ordersToSend as $order) {
                                                            $json = json_decode($order->getTargetValue->values ?? '', true);
                                                            $cost += (float)($json['cost']['field_value'] ?? 0);
                                                        }

                                                    }
                                                @endphp
                                                {{$cost}}
                                            </td>
                                            <td class="text-center">
                                                {{$pass->cost_actual}}
                                            </td>
                                            <td class="text-center">
                                                @if ($pass->type == \App\Models\Pass::TYPE_SENDING)
                                                    <span class="label label-info"
                                                          style="background: #0298d1;">{{(float)$pass->cost_actual ? '-' . (float)$pass->cost_actual : ''}}</span>
                                                @elseif ($pass->type == \App\Models\Pass::TYPE_REDEMPTION)
                                                    <span class="label label-success">{{$cost + $pass->price_total ? (float)($cost + $pass->price_total) : ''}}</span>
                                                @elseif ($pass->type == \App\Models\Pass::TYPE_NO_REDEMPTION)
                                                    <span class="label label-danger">{{(float)$pass->cost_return ? '-' . (float)$pass->cost_return : ''}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($permissions['page_pass_one']))
                                                    <a href="{{route('pass-one', $pass->id)}}" class="table-link ">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x "></i>
                                                        <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($permissions['reversal_one_pass']))
                                                    @if(!$pass->reversalPass->count() && ($pass->type == 'redemption'|| $pass->type == 'no-redemption'))
                                                        <div class="pull-right">
                                                            <div class="btn-group button-open">
                                                                <a aria-expanded="false"
                                                                   class="btn btn-default btn-xs dropdown-toggle"
                                                                   data-toggle="dropdown">
                                                                    @lang('general.actions') <i class="fa fa-angle-down"></i>
                                                                </a>
                                                                <ul class="dropdown-menu pull-right" role="menu">
                                                                    <li>
                                                                        <a href="#" data-type="text"
                                                                           data-pk="{{ $pass->id }}"
                                                                           data-name="name"
                                                                           data-url="/pass/reversal"
                                                                           data-id="{{ $pass->id }}"
                                                                           data-title=" @lang('alerts.sure-reverse-pass')"
                                                                           class="editable editable-click  pass-reversal"
                                                                           title="">
                                                                            @lang('general.reverse')
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            @lang('general.no-results')
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-center">
                        {{$passes->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--<div class="main-container">--}}
    {{--<div class="row">--}}
    {{--<div class="col-lg-12">--}}
    {{--<div class="main-box clearfix">--}}
    {{--<div class="main-box-body clearfix">--}}
    {{--<div class="table-responsive">--}}
    {{--<table class="table table-striped table-hover">--}}
    {{--<thead>--}}
    {{--<tr>--}}
    {{--<th class="text-center">Sub Project</th>--}}
    {{--<th class="text-center">Orders</th>--}}
    {{--<th class="text-center">Total</th>--}}
    {{--<th class="text-center">Product total</th>--}}
    {{--<th class="text-center">Cost</th>--}}
    {{--<th class="text-center">Cost Actual</th>--}}
    {{--<th class="text-center">Cost Return</th>--}}
    {{--<th class="text-center">Income</th>--}}
    {{--</tr>--}}
    {{--</thead>--}}
    {{--<tbody>--}}
    {{--@if ($statistics)--}}
    {{--@foreach($statistics as $subProjectId => $statistic)--}}
    {{--<tr>--}}
    {{--<td>{{$projects[$subProjectId]->name ?? ''}}</td>--}}
    {{--<td class="text-center">{{$statistic['orders']}}</td>--}}
    {{--<td class="text-center">{{$statistic['price_total']}}</td>--}}
    {{--<td class="text-center">{{$statistic['price_products']}}</td>--}}
    {{--<td class="text-center">{{$statistic['cost']}}</td>--}}
    {{--<td class="text-center">{{$statistic['cost_actual']}}</td>--}}
    {{--<td class="text-center">{{$statistic['cost_return']}}</td>--}}
    {{--<td class="text-center">{{$statistic['income']}}</td>--}}
    {{--</tr>--}}
    {{--@endforeach--}}
    {{--@else--}}
    {{--<tr>--}}
    {{--<td colspan="8" class="text-center">Нет данных</td>--}}
    {{--</tr>--}}
    {{--@endif--}}
    {{--</tbody>--}}
    {{--</table>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">
                                        @lang('general.subproject')</th>
                                    <th class="text-center">
                                        @lang('general.type')</th>
                                    <th class="text-center">
                                        @lang('general.orders')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.total')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.total')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cost')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cost-actual')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cost-return')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.income')
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ($statistics)
                                    @foreach($statistics as $subProjectId => $statistic)
                                        @php
                                            $flag = false;
                                        @endphp
                                        @if(isset($statistic['passes']))
                                            @foreach($statistic['passes'] as $type => $pass)
                                                <tr>
                                                    @if (!$flag)
                                                        @php
                                                            $flag = true;
                                                        @endphp
                                                        <td rowspan="{{count($statistic['passes']) + 1}}"
                                                            style="border-bottom: 2px solid #e7ebee;">{{$projects[$subProjectId]->name ?? ''}}</td>
                                                    @endif
                                                    <td class="text-center"> @lang('pass.' . $type)</td>
                                                    <td class="text-center">{{$pass['orders']}}</td>
                                                    <td class="text-center">{{$pass['price_total']}}</td>
                                                    <td class="text-center">{{$pass['price_products']}}</td>
                                                    <td class="text-center">{{$pass['cost']}}</td>
                                                    <td class="text-center">{{$pass['cost_actual']}}</td>
                                                    <td class="text-center">{{$pass['cost_return']}}</td>
                                                    <td class="text-center">{{$pass['income']}}</td>
                                                </tr>
                                            @endforeach
                                            <tr style="border-top: 2px solid #ccc;border-bottom: 2px solid #e7ebee;">
                                                <td class="text-center"> @lang('general.total')</td>
                                                <td class="text-center">{{$statistic['orders']}}</td>
                                                <td class="text-center">{{$statistic['price_total']}}</td>
                                                <td class="text-center">{{$statistic['price_products']}}</td>
                                                <td class="text-center">{{$statistic['cost']}}</td>
                                                <td class="text-center">{{$statistic['cost_actual']}}</td>
                                                <td class="text-center">{{$statistic['cost_return']}}</td>
                                                <td class="text-center">{{$statistic['income']}}</td>
                                            </tr>
                                        @else
                                            <tr style="border-bottom: 2px solid #e7ebee;">
                                                <td class="text-center">{{$projects[$subProjectId]->name ?? ''}}</td>
                                                <td class="text-center"> @lang('general.total')</td>
                                                <td class="text-center">{{$statistic['orders']}}</td>
                                                <td class="text-center">{{$statistic['price_total']}}</td>
                                                <td class="text-center">{{$statistic['price_products']}}</td>
                                                <td class="text-center">{{$statistic['cost']}}</td>
                                                <td class="text-center">{{$statistic['cost_actual']}}</td>
                                                <td class="text-center">{{$statistic['cost_return']}}</td>
                                                <td class="text-center">{{$statistic['income']}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            @lang('orders.data-not-found')
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
