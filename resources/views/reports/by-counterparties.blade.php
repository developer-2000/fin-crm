@extends('layouts.app')

@section('title') @lang('reports.by-counterparties')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/daterangepicker/daterangepicker.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <style>
        .click td {
            background-color: #ebebeb !important;
            font-weight: bold;
        }
    </style>
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/flot/jquery.flot.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.pie.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.stack.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.resize.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.time.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.orderBars.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('tablesorter_master/jquery.tablesorter.js') }}"></script>
    <script src="{{ URL::asset('js/users/account.js') }}"></script>
    <script src="{{ URL::asset('js/reports/by-counterparties.js') }}"></script>
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
                                <li class="active"><span> @lang('reports.by-counterparties')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('reports.by-counterparties')</h1>
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
                        <div class="col-md-1 hidden-sm hidden-xs"
                             style="padding-left: 0;"> @lang('general.search')</div>
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="proc_status">  @lang('general.status')</label>
                                        <select name='proc_status[]' id="proc_status" style="width: 100%" multiple>
                                            <option value=""> @lang('general.all')</option>
                                            @if ($senderProcStatuses)
                                                @foreach ($senderProcStatuses as $c)
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
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;">  @lang('general.date')</div>
                        <input class="form-control" id="date_start" type="hidden" data-toggle="tooltip"
                               name="date_start"
                               data-placement="bottom"
                               value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : '01-01-2018' }}">
                        <input class="form-control" id="date_end" type="hidden" data-toggle="tooltip"
                               name="date_end"
                               data-placement="bottom"
                               value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : date('d-m-Y', time())}}">
                        <div class="col-sm-3">
                            <div id="form-group">
                                @php
                                    $startDate = isset($_GET['date_start']) ? $_GET['date_start'] : '01-01-2018';
                                    $endDate = isset($_GET['date_end']) ? $_GET['date_end'] : date('d-m-Y', time());
                                @endphp
                                <input type="text" class="form-control" id="daterange" name="daterange"
                                       value="{{$startDate .' - '. $endDate}}"/>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="btn-group date_type" data-toggle="buttons">
                                <div>  @lang('general.type')</div>
                                <label class="btn btn-primary @if (isset($_GET['date_type']) && $_GET['date_type'] == 2 ) active @endif"
                                       id="time_result" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-set-result')">
                                    <input type="radio"
                                           @if (isset($_GET['date_type'])  && $_GET['date_type'] == 2 )
                                           checked
                                           @endif
                                           name="date_type" value="2"><i class="fa fa-empire"></i>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                            <div class="input-group">
                                <div class="btn-group" data-toggle="buttons" id="date_template_statuses">
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
                                    <label class="btn btn-default pattern_date">
                                        <input type="radio" name="date_template" value="3"> 3 @lang('general.months')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value=' @lang('general.search')'/>
                    <a href="{{ route('report-by-counterparties') }}" class="btn btn-warning"
                       type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="col-md-4">
                        <h2></h2>
                        @if(!empty($novaposhtaData))
                            @foreach($novaposhtaData as $key => $row)
                                <h2>{{$novaposhtaSenders[$key]['id'] ?? '' }} - {{$novaposhtaSenders[$key]['name'] ?? ''}}
                                /  {{isset($novaposhtaSenders[$key]['active']) && $novaposhtaSenders[$key]['active'] == 1 ? 'Active' : 'Inactive'}}</h2>
                                <div class="table-responsive">
                                    @php
                                    $totalStatuses = 0;
                                    @endphp
                                    <table class="table tablesorter table_country">
                                        <thead>
                                        <tr>
                                            <th class="header"> Текущий статус в системе</th>
                                            <th class="text-center header">Кол-во</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($row as $item)
                                        <tr>
                                            <td>{{$procStatuses[$item->proc_status]['name'] ?? ''}}</td>
                                            <td class="count">{{$item->total}}</td>
                                        </tr>
                                            @php
                                                $totalStatuses += $item->total;
                                            @endphp
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>
                                                @lang('general.total')
                                            </th>
                                            <th>
                                                {{$totalStatuses}}
                                            </th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop