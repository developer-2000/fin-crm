@extends('layouts.app')

@section('title') @lang('reports.by-operators')@stop

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
            background-color: #b8e9c8 !important;
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
                                <li class="active"><span> @lang('reports.by-operators') @lang('general.detailed')</span>
                                </li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('reports.by-operators') @lang('general.detailed')</h1>
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
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                            <div class="col-md-11">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="company"> @lang('general.company')</label>
                                        <select name='company' id="company" style="width: 100%">
                                            <option value=""> @lang('general.all')</option>
                                            @if ($companies->count())
                                                @foreach ($companies as $company)
                                                    <option @if (isset($_GET['company'])  && $_GET['company'] == $company->id) selected
                                                            @endif value="{{ $company->id }}">{{ $company->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="moderator" class="col-sm-4 control-label"> @lang('general.operator')</label>
                                        <select id="operator" name="operator[]" style="width: 100%" multiple>
                                            @if ($operators)
                                                @foreach ($operators as $key => $operator)
                                                    <option
                                                            @if (isset($_GET['operator']))
                                                            <? $operatorsGet = explode(',', $_GET['operator']); ?>
                                                            @foreach ($operatorsGet as $operatorGet)
                                                            @if ($key == $operatorGet)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="{{ $key }}">{{ $operator->surname .' '. $operator->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
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
                                           value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : '' }}">
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
                                           value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="btn-group date_type" data-toggle="buttons">
                                <div> @lang('general.type')</div>
                                {{--<label class="btn btn-primary @if (!isset($_GET['date_type'])) active @endif"--}}
                                {{--id="time_created" data-toggle="tooltip"--}}
                                {{--data-placement="bottom" title="Дата создания">--}}
                                {{--<input type="radio"--}}
                                {{--@if (!isset($_GET['date_type']))--}}
                                {{--checked--}}
                                {{--@endif--}}
                                {{--name="date_type" value=""> <i class="fa fa-calendar"></i>--}}
                                {{--</label>--}}
                                <label class="btn btn-primary @if (isset($_GET['date_type'])  && $_GET['date_type'] == 1 ) active @endif"
                                       id="time_modified" data-toggle="tooltip"
                                       data-placement="bottom" title="Дата установки цели">
                                    <input type="radio"
                                           @if (isset($_GET['date_type'])  && $_GET['date_type'] == 1 )
                                           checked
                                           @endif
                                           name="date_type" value="1"><i class="fa fa-star-half-empty"></i>
                                </label>
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
                    <a href="{{ route('report-operators') }}" class="btn btn-warning" type="submit">
                        @lang('general.reset')
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    @if (!empty($operatorsData))
                        <div class="table-responsive">
                            <table class="table tablesorter table-hover ">
                                <thead>
                                <tr>
                                    <th class="text-center">N</th>
                                    <th class="text-center"> @lang('general.id')</th>
                                    <th class="text-center"> @lang('general.opened')</th>
                                    <th class="text-center"> @lang('statuses.approved')</th>
                                    <th class="text-center"> @lang('general.verified')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total = 0;
                                    $totalApprove = 0;
                                    $total = [];
                                @endphp
                                @foreach($operatorsData as $key => $operatorData)
                                    <tr>
                                        <td class="text-center"
                                            style="font-size: 16px; font-weight: bold; color: grey; text-decoration: underline"
                                            colspan="5">{{$key}}  {{isset($operators[$key]) ? $operators[$key]->name . ' '. $operators[$key]->surname : ''}}</td>
                                    </tr>
                                    @php
                                        $number = 0;
                                        $numberTotal = 0;

                                    @endphp
                                    @foreach($operatorData as $order)
                                        @php
                                            $number += 1;
                                            $numberTotal += 1;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{$number }}</td>
                                            <td class="text-center">
                                                @if(isset($permissions['order_page_in_operators_report']))
                                                    <a href="{{ route('order', $order->id) }}" class="table-link" style="font-weight: bold">
                                                        {{$order->id}}
                                                    </a>
                                                @else
                                                    {{$order->id}}
                                                @endif
                                            </td>
                                            <td class="text-center">{{isset($order->orderOpened->last()->date_opening) ? $order->orderOpened->last()->date_opening : ''}}</td>
                                            <td class="text-center">{{$order->time_modified}}</td>
                                            <td class="text-center">
                                                @if($order->targetUser->id != $key)
                                                    {{$order->targetUser->name .' '. $order->targetUser->surname }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="text-center" style="font-weight: bold;   background-color: #eefdef"
                                            colspan="5">Всего {{$numberTotal}}</td>
                                    </tr>
                                    @php
                                        $total[] = $numberTotal;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td class="text-center" style="font-weight: bold;   background-color: #b8e9c8"
                                        colspan="5">Итого {{array_sum($total)}}</td>
                                </tr>
                                {{--<tr>--}}
                                {{--<td style="font-weight: bold" class="total"></td>--}}
                                {{--<td style="font-weight: bold" class="total">Всего</td>--}}
                                {{--<td style="font-weight: bold"--}}
                                {{--class="text-center total" >{{!empty($total) ? $total : 0}}</td>--}}
                                {{--<td style="font-weight: bold"--}}
                                {{--class="text-center total">{{!empty($totalApprove) ? $totalApprove : 0}}</td>--}}
                                {{--</tr>--}}
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop