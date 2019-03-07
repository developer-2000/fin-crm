@extends('layouts.app')

@section('title') @lang('orders.suspicious')@stop

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
                                <li class="active"><span> @lang('orders.suspicious')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('orders.suspicious') (<span class="badge">{{$count}}</span>)</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 ">
            <form method="post" action="{{ route('suspicious-orders') }}">
                <div class="main-box clearfix">
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.search')</div>
                        <div class="col-md-11">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="id"> @lang('general.id')</label>
                                    <input id="id" name="id" class="form-control" type="text" @if (isset($_GET['oid'])) value="" @endif>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="countries"> @lang('general.country')</label>
                                    <select name='countries[]' id="countries" style="width: 100%" multiple>
                                        <option value=""> @lang('general.all')</option>
                                        @if ($countries)
                                            @foreach($countries as $country)
                                                <option
                                                    @if (isset($_GET['countries']))
                                                        @php $countriesGet = explode(',', $_GET['countries']); @endphp
                                                        @foreach ($countriesGet as $cg)
                                                            @if (mb_strtolower($country->code) == $cg)
                                                            selected
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    value="{{ mb_strtolower($country->code) }}">
                                                        @lang('countries.' . $country->code)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
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
                            <div class="col-sm-3">
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
                                <div class="col-md-11">
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
                                <div class="col-md-11">
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
                                        <input type="radio" name="date_template" value="2">  @lang('general.last-month')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                    <a href="{{ route('suspicious-orders') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        @lang('general.id')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.country')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.date')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.user')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.comment')
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @if ($orders)
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="text-center">{{$order->id}}</td>
                                        <td class="text-center">
                                            <img class="country-flag"
                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png')  }}"/>
                                        </td>
                                        <td class="text-center">
                                            {{ $order->time_modified}}
                                        </td>
                                        <td class="text-center">
                                            {{$order->surname}} {{$order->name }} @if ($order->login_sip) ({{$order->login_sip}}) @endif<br>
                                            {{$order->company}}
                                        </td>
                                        <td >{{$order->text}}</td>
                                        <td class="text-center">
                                            <a href="{{ route('order', $order->id) }}/" class="table-link">
                                                        <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x "></i>
                                                            <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                        </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">
                                        @lang('orders.suspicious-not-found')
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{$orders->links()}}
            </div>
        </div>
    </div>
@stop
