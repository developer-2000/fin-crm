@extends('layouts.app')

@section('title')  @lang('general.report') @lang('reports.by-collectings')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <style>
        .click td {
            background-color: #ebebeb !important;
            font-weight: bold;
        }

        .total {
            font-weight: bold;
            color: grey;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('tablesorter_master/jquery.tablesorter.js') }}"></script>
    <script src="{{ URL::asset('js/users/account.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                                <li class="active"><span>  @lang('general.report') @lang('reports.by-collectings')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left">  @lang('general.report') @lang('reports.by-collectings')</h1>
                            </div>
                        </div>
                        <div class="pull-right">
                            <div class="clearfix">
                                <a href="{{route('collectings')}}" class="pull-right"> @lang('reports.go-to-collectings') &raquo;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <form method="POST" action="{{Request::url()}}">
                <div class="main-box clearfix">
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.filter')</div>
                        <div class="col-md-11">
                            <div class="row">
                                @if (isset($permissions['filter_projects_page_account']))
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="project" class="col-sm-4 control-label"> @lang('general.project')</label>
                                            <input id="project"
                                                   data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                   class="project " name="project"
                                                   value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                   style="width: 100%">
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sub_project" class="col-sm-4 control-label"> @lang('general.subproject')</label>
                                        <input id="sub_project"
                                               data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                               class="sub_project " name="sub_project"
                                               value="{{$dataSubProject ?? NULL}}"
                                               style="width: 100%">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="country"> @lang('general.country') </label>
                                        <select name='country' id="country" style="width: 100%">
                                            <option value=""> @lang('general.all')</option>
                                            @if ($countries)
                                                @foreach ($countries as $c)
                                                    <option @if (isset($_GET['country']) && mb_strtolower($_GET['country']) == $c->code) selected
                                                            @endif value="{{ mb_strtolower($c->code) }}">
                                                        @lang('countries.' . $c->code)
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="proc_status"> @lang('general.status')</label>
                                        <select name='proc_status[]' id="proc_status" style="width: 100%" multiple>
                                            <option value=""> @lang('general.all')</option>
                                            @if ($procStatuses)
                                                @foreach ($procStatuses as $c)
                                                    <option @if (isset($_GET['proc_status']))
                                                            <? $statusGet = explode(',', $_GET['proc_status']); ?>
                                                            @foreach ($statusGet as $stg)
                                                            @if ($c->id == $stg)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif value="{{ $c->id }}">
                                                        {{$c->project ? $c->project->name . '::' : ''}}{{ !empty($c->key) ? trans('statuses.' . $c->key) : $c->name}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="date_start"> @lang('general.from')</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" id="date_start" type="text" data-toggle="tooltip"
                                           name="date_start"
                                           data-placement="bottom"
                                           value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : date('d.m.Y', time()) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="date_end"> @lang('general.to')</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" id="date_end" type="text" data-toggle="tooltip"
                                           name="date_end"
                                           data-placement="bottom"
                                           value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : date('d.m.Y', time()) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
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
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value=' @lang('general.search')'/>
                    <a href="{{ route('report-collectings') }}" class="btn btn-warning" type="submit">
                        @lang('general.reset')
                    </a>
                </div>
            </form>
        </div>
    </div>
    <!-- Start Collectings by Statuses Report -->
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix" style="color: #1ABC9C; font-weight: bold">
                    {{--Дожим по Статусам и Обработке--}}
                    @lang('reports.collectings-by-statuses-processing')
                </header>
                <div class='main-box-body clearfix'>
                    @if (isset($collectings['resultDataByStatuses']) && count($collectings['resultDataByStatuses']))
                        <div class="table-responsive">
                            <table class="table tablesorter table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center"> @lang('collectings.collectings-all')</th>
                                    <th class="text-center"> @lang('general.in-processing')</th>
                                    <th class="text-center"> @lang('general.total-orders')<br> @lang('statuses.sent')<br> @lang('statuses.at-department')</th>
                                    <th class="text-center"> @lang('statuses.received')<br> @lang('statuses.paid-up')</th>
                                    <th class="text-center"> @lang('statuses.returned')<br> @lang('statuses.refused')</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{--@php--}}
                                {{--$orders =0;--}}
                                {{--$ordersColLogs =0;--}}
                                {{--$handAll =0;--}}
                                {{--$autoAll =0;--}}
                                {{--@endphp--}}
                                <tr>
                                    <td class="text-center"
                                        rowspan="2">{{ isset($collectings['resultDataByStatuses']['allCounts']) && count($collectings['resultDataByStatuses']['allCounts']) ? array_sum($collectings['resultDataByStatuses']['allCounts']) : 0}}</td>
                                    <td class="text-center"
                                        rowspan="2">{{isset($collectings['resultDataByStatuses']['processing']) ? array_sum($collectings['resultDataByStatuses']['processing']) : 0}}</td>
                                    <td class="text-center"
                                        rowspan="2">{{isset($collectings['resultDataByStatuses']['sent']) ? array_sum($collectings['resultDataByStatuses']['sent']): 0}}</td>
                                    <td class="text-center"
                                        rowspan="2">{{isset($collectings['resultDataByStatuses']['received']) ? array_sum($collectings['resultDataByStatuses']['received']) : 0}}</td>
                                    <td class="text-center"
                                        rowspan="2">{{isset($collectings['resultDataByStatuses']['returned']) ? array_sum($collectings['resultDataByStatuses']['returned']) : 0}}</td>
                                </tr>
                                {{--@php--}}
                                {{--$orders += $collecting->countOrders;--}}
                                {{--$ordersColLogs += $collecting->countColLogs;--}}
                                {{--$handAll += $collecting->hand;--}}
                                {{--$autoAll += $collecting->auto;--}}
                                {{--@endphp--}}
                                {{--<tr>--}}
                                {{--<td class="text-center" style="color: grey; font-weight: bold">{{'ВСЕГО'}}</td>--}}
                                {{--<td class="text-center total">{{$orders}}</td>--}}
                                {{--<td class="text-center total">{{$ordersColLogs}}</td>--}}
                                {{--<td class="text-center total">{{$handAll}}</td>--}}
                                {{--<td class="text-center total">X</td>--}}
                                {{--<td class="text-center total">{{$autoAll}}</td>--}}
                                {{--<td class="text-center total"></td>--}}
                                {{--</tr>--}}
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End Collectings by Statuses Report -->

    <!-- Start Collectings by SubProjects Report -->
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix" style="color: #1ABC9C; font-weight: bold">
                    Дожим по подпроектам
                </header>
                <div class='main-box-body clearfix'>
                    @if (isset($collectings['resultData']) && count($collectings['resultData']))
                        <div class="table-responsive">
                            <table class="table tablesorter table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center " rowspan="2"> @lang('general.subproject')</th>
                                    <th class="text-center " rowspan="2"> @lang('collectings.collectings-all')</th>
                                    <th class="text-center " rowspan="2"> @lang('collectings.processing-quantity')</th>
                                    <th class="text-center doj"> @lang('general.processes')</th>
                                    <th class="text-center " rowspan="2"> @lang('collectings.manual-processing')</th>
                                    <th class="text-center " rowspan="2"> @lang('general.auto-call')</th>
                                    <th class="text-center " rowspan="2"> @lang('collectings.processing-quantity-statistic')</th>
                                </tr>
                                <tr>
                                    <th class="text-center doj"> @lang('general.not-processed')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $orders =0;
                                    $ordersColLogs =0;
                                    $handAll =0;
                                    $autoAll =0;
                                @endphp
                                @foreach($collectings['resultData'] as $key => $collecting)
                                    <tr>
                                        <td class="text-center" rowspan="2"
                                            style="font-weight: bold; color: grey">{{$collecting->subproject}}</td>
                                        <td class="text-center" rowspan="2">{{$collecting->countOrders}}</td>
                                        <td class="text-center" rowspan="2">{{$collecting->countColLogs}}</td>
                                        <td class="text-center" style="color: #14930a">{{$collecting->processed}}</td>
                                        <td class="text-center" rowspan="2">{{$collecting->hand}}</td>
                                        <td class="text-center" rowspan="2">{{$collecting->auto}}</td>
                                        <td class="text-center" rowspan="2">
                                            <div style="position: absolute;     padding-left: 10%">
                                                <a href="#" class="pop">
                                                    <div class="offer_name">......</div>
                                                </a>
                                                <div class="data_popup">
                                                    <div class="arrow"></div>
                                                    <h3 class="title">
                                                        @lang('collectings.processing-quantity')/ @lang('general.orders-quantity')
                                                    </h3>
                                                    <p class="content">
                                                        @foreach($collecting->dataCollected as $key => $value)
                                                            <span style="font-weight: bold">{{$key}}</span>  {{ ' => '. $value}}
                                                            <br>
                                                        @endforeach</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" style="color:red;">{{$collecting->noProcessed}}</td>
                                    </tr>
                                    @php
                                        $orders += $collecting->countOrders;
                                        $ordersColLogs += $collecting->countColLogs;
                                        $handAll += $collecting->hand;
                                        $autoAll += $collecting->auto;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td class="text-center" style="color: grey; font-weight: bold"> @lang('general.total') </td>
                                    <td class="text-center total">{{$orders}}</td>
                                    <td class="text-center total">{{$ordersColLogs}}</td>
                                    <td class="text-center total">{{$handAll}}</td>
                                    <td class="text-center total">X</td>
                                    <td class="text-center total">{{$autoAll}}</td>
                                    <td class="text-center total"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End Collectings by SubProjects Report -->

    <!-- Start Collectings by Collectors Report -->
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix" style="color: #1ABC9C; font-weight: bold">
                   @lang('collectings.collectings-by-collectors')
                </header>
                <div class='main-box-body clearfix'>
                    @if (isset($collectings['resultByUsers']) && $collectings['resultByUsers']->count())
                        <div class="table-responsive">
                            <table class="table tablesorter table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center" rowspan="2"> @lang('collectings.collector')</th>
                                    <th class="text-center" rowspan="2"> @lang('collectings.collectings-all')</th>
                                    <th class="text-center" rowspan="2"> @lang('collectings.processing-quantity')</th>
                                    <th class="text-center"> @lang('general.processes')</th>
                                    <th class="text-center" rowspan="2"> @lang('collectings.manual-processing')</th>
                                    <th class="text-center" rowspan="2"> @lang('general.auto-call')</th>
                                </tr>
                                <tr>
                                    <th class="text-center"> @lang('general.not-processed')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $ordersByUser =0;
                                    $ordersColLogsByUser =0;
                                    $handAllByUser =0;
                                    $autoAllByUser =0;
                                @endphp
                                @foreach($collectings['resultByUsers'] as $key => $collectingByUser)
                                    <tr>
                                        <td class="text-center" rowspan="2" style="font-weight: bold; color: grey">{{isset($collectingByUser->name ) ?
                                        $collectingByUser->name .' '. $collectingByUser->surname : 'Не распределены'}}</td>
                                        <td class="text-center" rowspan="2">{{$collectingByUser->countUsersOrders}}</td>
                                        <td class="text-center"
                                            rowspan="2">{{$collectingByUser->countColLogsByUser}}</td>
                                        <td class="text-center"
                                            style="color: #13a70a">{{$collectingByUser->processedUsers}}</td>
                                        <td class="text-center" rowspan="2">{{$collectingByUser->handUsers}}</td>
                                        <td class="text-center" rowspan="2">{{$collectingByUser->autoUsers}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" style="color: red;">
                                            {{$collectingByUser->noProcessedUsers}}
                                        </td>
                                    </tr>
                                    @php
                                        $ordersColLogsByUser += $collectingByUser->countUsersOrders;
                                        $ordersByUser += $collectingByUser->countColLogsByUser;
                                        $handAllByUser += $collectingByUser->handUsers;
                                        $autoAllByUser += $collectingByUser->autoUsers;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td class="text-center" style="color: grey; font-weight: bold"> @lang('general.total')</td>
                                    <td class="text-center total">{{$ordersColLogsByUser}}</td>
                                    <td class="text-center total">{{$ordersByUser}}</td>
                                    <td class="text-center total">X</td>
                                    <td class="text-center total">{{$handAllByUser}}</td>
                                    <td class="text-center total">{{$autoAllByUser}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End Collectings by Collectors Report -->
@stop