@extends('layouts.app')

@section('title') @lang('orders.bad-connection') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/moderation.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('tablesorter_master/jquery.tablesorter.js') }}"></script>
    <script src="{{ URL::asset('js/moderation/moderation.js') }}"></script>
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
                                <li class="active"><span> @lang('orders.bad-connection')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('orders.bad-connection') (<span class="badge">{{$count}}</span>)</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 ">
            <form method="post" action="{{ route('bad-connection') }}">
                <div class="main-box clearfix">
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.search')</div>
                        <div class="col-md-11">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="id"> @lang('general.id')</label>
                                    <input id="id" name="id" class="form-control" type="text" @if (isset($_GET['id'])) value="{{$_GET['id']}}" @endif>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="oid"> @lang('general.oid')</label>
                                    <input id="oid" name="oid" class="form-control" type="text" @if (isset($_GET['oid'])) value="{{$_GET['oid']}}" @endif>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="cause"> @lang('general.cause')</label>
                                    <select name='cause' class="form-control" id="cause" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        <option value="1" @if (isset($_GET['cause']))@if ($_GET['cause'] == 1)selected @endif @endif>
                                            @lang('orders.answerphone')
                                        </option>
                                        <option value="2" @if (isset($_GET['cause']))@if ($_GET['cause'] == 2)selected @endif @endif>
                                            @lang('orders.bad-connection')
                                        </option>
                                        <option value="4" @if (isset($_GET['cause']))@if ($_GET['cause'] == 4)selected @endif @endif>
                                            @lang('orders.liars')
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="status"> @lang('general.status')</label>
                                    <select name='status' class="form-control" id="status" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        <option value="1" @if (isset($_GET['status']))@if ($_GET['status'] == 1)selected @endif @endif>
                                            @lang('general.verified')
                                        </option>
                                        <option value="2" @if (isset($_GET['status']))@if ($_GET['status'] == 2)selected @endif @endif>
                                            @lang('general.unverified')
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="user"> @lang('general.user')</label>
                                    <select name='user[]' id="user" style="width: 100%" multiple>
                                        <option value=""> @lang('general.all')</option>
                                        @if ($users)
                                            @foreach($users as $user)
                                                <option
                                                        @if (isset($_GET['user']))
                                                        <? $usersGet = explode(',', $_GET['user']); ?>
                                                        @foreach ($usersGet as $cg)
                                                        @if ($user->id == $cg)
                                                        selected
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                        value="{{ $user->id }}">{{ $user->surname }} {{ $user->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="company"> @lang('general.company')</label>
                                    <select name='company[]' id="company" style="width: 100%" multiple>
                                        <option value=""> @lang('general.all')</option>
                                        @if ($companies)
                                            @foreach($companies as $company)
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
                            </div>
                        </div>

                        <div class="col-md-1 hidden-sm hidden-xs"></div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                        <div class="col-sm-3">
                            <div class="form-group form-horizontal">
                                <label for="date_start_moder"  class="col-md-2 control-label"> @lang('general.date-from')</label>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="date_start_moder" type="text" data-toggle="tooltip" name="date_start"
                                               data-placement="bottom" value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : "" }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-horizontal">
                                <label for="date_end_moder" class="col-md-2 control-label"> @lang('general.date-to')</label>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="date_end_moder" type="text" data-toggle="tooltip" name="date_end"
                                               data-placement="bottom" value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : "" }}">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-5" style="padding-bottom: 10px;">
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
                    <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                    <a href="{{ route('bad-connection') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="table-responsive">
                        @if ($orders)
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center">
                                        @lang('general.id')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.oid')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.user')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cause')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.record')
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr id="order_{{$order->id}}" @if ($order->callback_status == 4) class="danger" @endif>
                                        <td class="text-center">
                                            {{$order->id}}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('order', $order->order_id)}}">{{$order->order_id}}</a>
                                        </td>
                                        <td>
                                            @if (isset($companies[$order->company_id]))
                                                {{$companies[$order->company_id]->name}} <br>
                                            @endif
                                            {{$order->surname}} {{$order->name }}
                                        </td>
                                        <td class="callback_status">
                                            @if ($order->callback_status == 1)
                                                @lang('orders.answerphone')
                                            @elseif ($order->callback_status == 2)
                                                @lang('orders.bad-connection')
                                            @elseif ($order->callback_status == 4)
                                                @lang('orders.marked-as') @lang('orders.not-answerphone') / @lang('orders.bad-connection'))
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <?
                                            $url = route('get-call-by-name') . '?fileName=' . $order->file;
                                            $agent = $_SERVER['HTTP_USER_AGENT'];
                                            if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                $output = '<a href="' . $url . '"><span class="fa-stack">
                                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                                            </span></a>';
                                            } else {
                                                $output = '<audio controls><source src="' . $url . '" type="audio/mpeg"></audio>';
                                            }
                                            echo $output?>
                                            <br>
                                            {{ $order->date}}
                                        </td>
                                        <td class="actions">
                                            @if ($order->verified_uid)
                                                @if (isset($users[$order->verified_uid]))
                                                    @lang('general.verified') : <br>
                                                    {{$users[$order->verified_uid]->surname}} {{$users[$order->verified_uid]->name}}
                                                @endif
                                            @else
                                                @if (isset($permissions['buttons_confirm_cancel_bad_connection']))
                                                    <a href="#" class="custom_btn bad_connection" style="margin-right: 3px;"
                                                       data-toggle="tooltip" data-placement="bottom" title="" data-original-title=" @lang('general.confirm')" data-id="{{$order->id}}">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                    <a href="#" class="custom_btn cancel" data-toggle="tooltip" data-placement="bottom" title="" data-original-title=" @lang('general.reject')" data-id="{{$order->id}}">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{$orders->links()}}
            </div>
        </div>
    </div>
@stop
