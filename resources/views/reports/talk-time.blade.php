@extends('layouts.app')

@section('title')
  @lang('reports.talk-time')
@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
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
                                <li class="active"><span> @lang('reports.talk-time')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('reports.talk-time')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <form method="post" action="{{ route('reports-talk-time')}}">
                <div class="main-box clearfix">
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.search')</div>
                        <div class="col-md-11">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="company"> @lang('general.company')</label>
                                    <select name='company' id="company" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($companies->count())
                                            @foreach ($companies as $company)
                                                <option @if (isset($_GET['company'])  && $_GET['company'] == $o->id) selected
                                                        @endif value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="user"> @lang('general.user')</label>
                                    <select name='user' id="user" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($users)
                                            @foreach ($users as $user)
                                                <option @if (isset($_GET['user']) && $_GET['user'] == $user->id) selected
                                                        @endif value="{{ $user->id }}">{{ $user->name . ' ' . $user->surname }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="trunk">
                                        @lang('general.trunk')
                                    </label>
                                    <select name='trunk' id="trunk" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($trunks)
                                            @foreach($trunks as $trunk)
                                                <option @if (isset($_GET['trunk']) && $_GET['trunk'] == $trunk) selected @endif>{{$trunk}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="country">
                                        @lang('general.country')
                                    </label>
                                    <select name='country' id="country" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($countries)
                                            @foreach ($countries as $c)
                                                <option @if (isset($_GET['country']) && $_GET['country'] == mb_strtolower($c->code)) selected
                                                        @endif value="{{ mb_strtolower($c->code) }}"> @lang('countries.' . $c->code)</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1 hidden-sm hidden-xs"></div>
                    </div>
                    <div class='main-box-body clearfix section_filter grouping'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.group-by')</div>
                        <div class="col-sm-12 col-md-11">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-primary @if ((isset($_GET['group']) && $_GET['group'] == 'company') || !isset($_GET['group'])) active @endif">
                                    <input type="radio" name="group" value="company"
                                           @if ((isset($_GET['group']) && $_GET['group'] == 'company') || !isset($_GET['group'])) checked @endif>
                                    @lang('general.company')
                                </label>
                                <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'trunk') active @endif">
                                    <input type="radio" name="group" value="trunk"
                                           @if (isset($_GET['group']) && $_GET['group'] == 'trunk') checked @endif>
                                    @lang('general.trunk')
                                </label>
                                <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'user') active @endif">
                                    <input type="radio" name="group" value="user"
                                           @if (isset($_GET['group']) && $_GET['group'] == 'user') checked @endif>
                                    @lang('general.user')
                                </label>
                                <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'country') active @endif">
                                    <input type="radio" name="group" value="country"
                                           @if (isset($_GET['group']) && $_GET['group'] == 'country') checked @endif>
                                    @lang('general.country')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                        <div class="col-sm-3">
                            <div class="form-group form-horizontal">
                                <label for="date_start" class="col-md-1 control-label"> @lang('general.date-from')</label>
                                <div class="col-md-11">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="date_start" type="text" data-toggle="tooltip"
                                               name="date_start"
                                               data-placement="bottom"
                                               value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : "" }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-horizontal">
                                <label for="date_end" class="col-md-1 control-label"> @lang('general.date-to')</label>
                                <div class="col-md-11">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="date_end" type="text" data-toggle="tooltip"
                                               name="date_end"
                                               data-placement="bottom"
                                               value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : "" }}">
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
                    <a href="{{ route('reports-talk-time') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="alert alert-info">
                <div><i class="fa fa-info-circle fa-fw fa-lg"></i>
                    {{__('orders.calls-determination', ['Success' => 'Success', 'ShortCall' => 'ShortCall', 'Calls' => trans('general.calls')])}}
                </div>
                {{-- Calls is the number of calls with the status ":Success" and ":ShortCall --}}
                <div><i class="fa fa-info-circle fa-fw fa-lg"></i>
                    {{__('orders.orders-determination', ['Orders' => trans('general.orders')])}}
                </div>
                {{-- Orders - is the number of orders that are called during this period --}}
            </div>
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="table-responsive">
                            <table class="table tablesorter" id="order_table">
                                <thead>
                                <tr>
                                    <th class="header">
                                        {{$group}}
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.talk-time')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.calls')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.orders')
                                    </th>
                                </tr>
                                </thead>
                                @if ($data->isNotEmpty())
                                <?
                                $talk_time = 0;
                                $count_talk = 0;
                                $count_order = 0;
                                ?>

                                    <tbody>
                                @foreach($data as $datum)
                                    <tr>
                                        <td>
                                            @if (isset($_GET['group']) && $_GET['group'] == 'user')
                                                @if (isset($users[$datum->name]))
                                                    {{$users[$datum->name]->surname}} {{$users[$datum->name]->name}}
                                                @else
                                                    {{$datum->name}}
                                                @endif
                                            @else
                                                {{$datum->name}}
                                            @endif
                                        </td>
                                        <td class="text-center ">{{dateProcessing($datum->talk_time)}}</td>
                                        <td class="text-center ">{{$datum->count}}</td>
                                        <td class="text-center ">{{$datum->count_order}}</td>
                                    </tr>
                                    <?
                                    $talk_time += $datum->talk_time;
                                    $count_talk += $datum->count;
                                    $count_order += $datum->count_order;
                                    ?>
                                @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th> @lang('general.total')</th>
                                        <th class="text-center">{{ dateProcessing($talk_time) }}</th>
                                        <th class="text-center">{{ $count_talk }}</th>
                                        <th class="text-center">{{ $count_order}}</th>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
