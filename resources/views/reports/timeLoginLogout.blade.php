@extends('layouts.app')

@section('title') @lang('reports.login-logout')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/control_user.css') }}" />
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
                                <li class="active"><span> @lang('reports.login-logout')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('reports.login-logout')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <form method="post" action="{{ route('reports-time-login-logout')}}">
                <div class="main-box">
                    <div class="item_rows ">
                        <div class="main-box-body clearfix">
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                    <label for="id" class="col-sm-4 control-label"> @lang('general.id')</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="id" name="id"  value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                    <label for="surname" class="col-sm-4 control-label"> @lang('general.surname')</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="surname" name="surname"  value="@if (isset($_GET['surname'])){{ $_GET['surname'] }}@endif">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                    <label for="name" class="col-sm-4 control-label"> @lang('general.phone')</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="name" name="name"  value="@if (isset($_GET['name'])){{ $_GET['name'] }}@endif">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                    <label for="date_start" class="col-sm-4 control-label"> @lang('general.date-from')</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control" id="date_start" type="text" placeholder=" @lang('general.date-from')" data-toggle="tooltip" name="date_start"
                                                   value="@if (isset($_GET['date_start'])){{  $_GET['date_start'] }}@endif">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                    <label for="date_end" class="col-sm-4 control-label"> @lang('general.date-to')</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control" id="date_end" type="text" data-toggle="tooltip" placeholder=" @lang('general.date-to')" name="date_end"
                                                   value="@if (isset($_GET['date_end'])){{ $_GET['date_end'] }}@endif">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                    <div class="col-sm-8">
                                        <div class="checkbox-nice">
                                            <input type="checkbox" id="detail" name="detail" @if (isset($_GET['detail'])) checked @endif>
                                            <label for="detail">
                                                @lang('general.detail')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                    <a href="{{ route('reports-time-login-logout') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>
                                    @lang('general.id')
                                </th>
                                <th>
                                    @lang('general.user')
                                </th>
                                <th class="text-center">
                                    @lang('general.date-start')
                                </th>
                                <th class="text-center">
                                    @lang('general.date-end')
                                </th>
                                @if (isset($_GET['detail']))
                                    <th class="text-center">
                                        @lang('general.time')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.type')
                                    </th>
                                @else
                                    <th class="text-center">
                                        @lang('general.crm-time')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.time-on-line')
                                    </th>
                                @endif
                                <th class="text-center">
                                    @lang('general.talk-time')
                                </th>
                                <th class="text-center">
                                    @lang('general.status')
                                </th>
                            </tr>
                            </thead>
                            @if ($times)
                                <tbody>
                                @foreach($times as $userTime)
                                    <tr>
                                        <td >{{$userTime->user_id}}</td>
                                        <td >{{$userTime->surname}} {{$userTime->name}}</td>
                                        <td class="text-center">
                                            @if ($userTime->min)
                                                {{$userTime->min}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($userTime->max)
                                                {{$userTime->max}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if (isset($_GET['detail']))
                                            @if ($userTime->time_crm)
                                                <td class="text-center">
                                                    {{dateProcessing($userTime->time_crm)}}
                                                </td>
                                                <td class="text-center">
                                                    CRM
                                                </td>
                                            @elseif ($userTime->time_pbx)
                                                <td class="text-center">
                                                    {{dateProcessing($userTime->time_pbx)}}
                                                </td>
                                                <td class="text-center">
                                                    @lang('reports.on-line')
                                                </td>
                                            @else
                                                <td class="text-center">-</td>
                                                <td class="text-center">-</td>
                                            @endif
                                        @else
                                            <td class="text-center">
                                                @if ($userTime->time_crm)
                                                    {{dateProcessing($userTime->time_crm)}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($userTime->time_pbx)
                                                    {{dateProcessing($userTime->time_pbx)}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif

                                        <td class="text-center">
                                            @if ($userTime->talkTime)
                                                {{dateProcessing($userTime->talkTime)}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($userTime->online)
                                                <span class="label label-success">
                                                    @lang('general.online')
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
