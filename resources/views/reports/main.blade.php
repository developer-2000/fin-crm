@extends('layouts.app')

@section('title')@lang('reports.title')@stop

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
                                <li class="active"><span> @lang('reports.title')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('reports.title')</h1>
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
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.search')</div>
                        <div class="col-md-11">
                            <div class="row">
                                @if (isset($permissions['filter_companies_page_account']))
                                    <div class="col-sm-4">
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
                                @endif
                                @if (isset($permissions['filter_operators_page_account']))
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="user">
                                                @lang('general.user')
                                            </label>
                                            <select name='user' id="user" style="width: 100%">
                                                <option value=""> @lang('general.all')</option>
                                                @if ($users)
                                                    @foreach ($users as $u)
                                                        <option @if (isset($_GET['user']) && $_GET['user'] == $u->id) selected
                                                                @endif value="{{ $u->id }}">{{ $u->name . ' ' . $u->surname }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($permissions['filter_offer_page_account']))
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="offer"> @lang('general.offer')</label>
                                            <select name='offer' id="offer" style="width: 100%">
                                                <option value=""> @lang('general.all')</option>
                                                @if ($offers_filter)
                                                    @foreach ($offers_filter as $o)
                                                        <option @if (isset($_GET['offer'])  && $_GET['offer'] == $o->id) selected
                                                                @endif value="{{ $o->id }}">{{ $o->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($permissions['filter_projects_page_account']))
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="project_select"> @lang('general.project')</label>
                                            <select name='project' id="project_select" style="width: 100%">
                                                <option value=""> @lang('general.all')</option>
                                                @if ($projects)
                                                    @foreach ($projects as $s)
                                                        <option @if (isset($_GET['project']) && $_GET['project'] == $s->id) selected
                                                                @endif value="{{ $s->id }}">{{ $s->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="country"> @lang('general.country')</label>
                                        <select name='country' id="country" style="width: 100%">
                                            <option value=""> @lang('general.all')</option>
                                            @if ($country)
                                                @foreach ($country as $c)
                                                    <option @if (isset($_GET['country']) && mb_strtolower($_GET['country']) == $c->code) selected
                                                            @endif value="{{ mb_strtolower($c->code) }}">
                                                        @lang('countries.' . $c->code)
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="learning">
                                            @lang('reports.queue-type')
                                        </label>
                                        <select name='learning' id="learning" style="width: 100%" class="form-control">
                                            <option value="" @if (!isset($_GET['learning'])) selected @endif>
                                                @lang('general.all')
                                            </option>
                                            <option value="1"
                                                    @if (isset($_GET['learning']) && $_GET['learning'] == 1) selected @endif>
                                                @lang('reports.learning')
                                            </option>
                                            <option value="2"
                                                    @if (isset($_GET['learning']) && $_GET['learning'] == 2) selected @endif>
                                                @lang('reports.not-learning')
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"></div>
                        <div class="col-md-11">
                            <div class="row">
                                @if (isset($permissions['filter_by_hp']))
                                    <div class="col-sm-2">
                                        <div class="checkbox-nice">
                                            <input type="checkbox" id="display_hp" name="display_hp"
                                                   @if (isset($_GET['display_hp']) && $_GET['display_hp'] == 'on') checked @endif>
                                            <label for="display_hp">
                                                @lang('general.cold-calls')
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter grouping'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;">
                            @lang('general.group-by')
                        </div>
                        <div class="col-sm-12 col-md-11">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-primary @if ((isset($_GET['group']) && $_GET['group'] == 'date') || !isset($_GET['group']))) active @endif">
                                    <input type="radio"
                                           @if (isset($_GET['group']) && $_GET['group'] == 'date')
                                           checked
                                           @endif
                                           name="group" value="date">
                                    @lang('general.date')
                                </label>
                                @if (isset($permissions['grouping_operators_page_account']))
                                    <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'user')) active @endif">
                                        <input type="radio"
                                               @if (isset($_GET['group']) && $_GET['group'] == 'user')
                                               checked
                                               @endif
                                               name="group" value="user">
                                        @lang('reports.by-operators')
                                    </label>
                                @endif
                                @if (isset($permissions['grouping_offers_page_account']))
                                    <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'offer')) active @endif">
                                        <input type="radio"
                                               @if (isset($_GET['group']) && $_GET['group'] == 'offer')
                                               checked
                                               @endif
                                               name="group" value="offer">
                                        @lang('general.offer')
                                    </label>
                                @endif
                                @if (isset($permissions['grouping_projects_page_account']))
                                    <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'source')) active @endif">
                                        <input type="radio"
                                               @if (isset($_GET['group']) && $_GET['group'] == 'source')
                                               checked
                                               @endif
                                               name="group" value="source">
                                        @lang('general.project')
                                    </label>
                                @endif
                                @if (isset($permissions['reports_main_grouping_countries_page_account']))
                                <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'country')) active @endif">
                                    <input type="radio"
                                           @if (isset($_GET['group']) && $_GET['group'] == 'country')
                                           checked
                                           @endif
                                           name="group" value="country">
                                    @lang('general.country')
                                </label>
                                @endif
                                @if (isset($permissions['reports_main_grouping_approve_offers_page_account']))
                                <label class="btn btn-primary @if (isset($_GET['group']) && $_GET['group'] == 'approveOffer')) active @endif">
                                    <input type="radio"
                                           @if (isset($_GET['group']) && $_GET['group'] == 'approveOffer')
                                           checked
                                           @endif
                                           name="group" value="approveOffer">
                                    @lang('reports.offer-approve')
                                </label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;">
                            @lang('general.date')
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="date_start"> @lang('general.date-from')</label>
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
                                <label for="date_end"> @lang('general.date-to')</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" id="date_end" type="text" data-toggle="tooltip"
                                           name="date_end"
                                           data-placement="bottom"
                                           value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : date('d.m.Y', time()) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="btn-group date_type" data-toggle="buttons">
                                <div>Тип</div>
                                <label class="btn btn-primary @if (!isset($_GET['date_type'])) active @endif"
                                       id="time_created" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-created')">
                                    <input type="radio"
                                           @if (!isset($_GET['date_type']))
                                           checked
                                           @endif
                                           name="date_type" value=""> <i class="fa fa-calendar"></i>
                                </label>
                                <label class="btn btn-primary @if (isset($_GET['date_type'])) active @endif"
                                       id="time_modified" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-target')">
                                    <input type="radio"
                                           @if (isset($_GET['date_type']))
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
                    <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                    <a href="{{ route('reports-main') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="table-responsive">
                        @if ((isset($_GET['group']) && $_GET['group'] == 'date') || !isset($_GET['group']))
                            <table class="table tablesorter" id="order_table">
                                <thead>
                                <tr>
                                    <th class="header">
                                        @lang('general.date')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.total')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.approved')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.processing')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.refusal')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.annulled')
                                    </th>
                                    <th class="text-center header">
                                        Up
                                    </th>
                                    <th class="text-center header">
                                        Up 2
                                    </th>
                                    <th class="text-center header">
                                        Cross
                                    </th>
                                    <th class="text-center header">
                                        Cross2
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.crm-time')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.time-on-line')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.talk-time')
                                    </th>
                                    <th class="text-center header">
                                        @lang('reports.dinner-break')
                                    </th>
                                    <th class="text-center header">
                                        @lang('general.registration-time')
                                    </th>
                                </tr>
                                </thead>
                                @if ($orders)
                                    <tbody>
                                    <?
                                    $approve = 0;
                                    $failure = 0;
                                    $fake = 0;
                                    $processing = 0;
                                    $up_sell = 0;
                                    $up_sell_2 = 0;
                                    $cross_sell = 0;
                                    $cross_sell_2 = 0;
                                    $login_time_crm = 0;
                                    $login_time_elastix = 0;
                                    $talk_time = 0;
                                    $pause_time = 0;
                                    $order_time = 0;
                                    ?>

                                    @foreach ($orders as $order)
                                        @if (isset($_GET['date_type']) && $_GET['date_type'] == 1)
                                            <? $order['processing'] = 0; ?>
                                        @endif
                                        <tr>
                                            <?
                                            $approve += $order['approve'];
                                            $failure += $order['failure'];
                                            $fake += $order['fake'];
                                            $processing += $order['processing'];
                                            $up_sell += $order['up_sell'];
                                            $up_sell_2 += $order['up_sell_2'];
                                            $cross_sell += $order['cross_sell'];
                                            $cross_sell_2 += $order['cross_sell_2'];
                                            $login_time_crm += $order['login_time_crm'];
                                            $login_time_elastix += $order['login_time_elastix'];
                                            $talk_time += $order['talk_time'];
                                            $pause_time += $order['pause_time'];
                                            $order_time += $order['order_time'];
                                            ?>
                                            <td>{{ $order['date'] }}</td>
                                            @if (!isset($_GET['date_type']))
                                                <td class="text-center">{{ $order['approve'] + $order['fake'] + $order['failure'] + $order['processing'] }}</td>
                                            @else
                                                <td class="text-center">{{ $order['approve'] + $order['fake'] + $order['failure'] }}</td>
                                            @endif
                                            <td class="text-center">{{ $order['approve'] }}</td>
                                            <td class="text-center">{{ $order['processing'] }}</td>
                                            <td class="text-center">{{ $order['failure'] }}</td>
                                            <td class="text-center">{{ $order['fake'] }}</td>
                                            <td class="text-center">{{ $order['up_sell'] }}</td>
                                            <td class="text-center">{{ $order['up_sell_2'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell_2'] }}</td>
                                            <td class="text-center">{{ dateProcessing($order['login_time_crm']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['login_time_elastix']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['talk_time']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['pause_time']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['order_time']) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th> @lang('general.total')</th>
                                        <th class="text-center">{{ $approve + $failure + $fake + $processing }}</th>
                                        <th class="text-center">{{ $approve }}</th>
                                        <th class="text-center">{{ $processing }}</th>
                                        <th class="text-center">{{ $failure }}</th>
                                        <th class="text-center">{{ $fake }}</th>
                                        <th class="text-center">{{ $up_sell }}</th>
                                        <th class="text-center">{{ $up_sell_2 }}</th>
                                        <th class="text-center">{{ $cross_sell }}</th>
                                        <th class="text-center">{{ $cross_sell_2 }}</th>

                                        <th class="text-center">{{ dateProcessing($login_time_crm) }}</th>
                                        <th class="text-center">{{ dateProcessing($login_time_elastix) }}</th>
                                        <th class="text-center">{{ dateProcessing($talk_time) }}</th>
                                        <th class="text-center">{{ dateProcessing($pause_time) }}</th>
                                        <th class="text-center">{{ dateProcessing($order_time) }}</th>
                                    </tr>
                                    </tfoot>
                                @endif

                            </table>
                        @elseif (isset($_GET['group']) && $_GET['group'] == 'user')
                            <table class="table tablesorter" id="order_table">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('general.user')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.approved') %
                                    </th>
                                    <th class="text-center">
                                        @lang('orders.order-opened')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.answerphone')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.bad-connection')
                                    </th>
                                    <th class="text-center">
                                        @lang('reports.liar')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.approved')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.refusal')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cancel')
                                    </th>
                                    <th class="text-center">Up</th>
                                    <th class="text-center">Up2</th>
                                    <th class="text-center">Cross</th>
                                    <th class="text-center">Cross2</th>
                                    <th class="text-center">
                                        @lang('general.crm-time')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.time-on-line')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.talk-time')
                                    </th>
                                    <th class="text-center">
                                        @lang('reports.dinner-break')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.registration-time')
                                    </th>
                                </tr>
                                </thead>
                                @if ($orders)
                                    <?
                                    $approve = 0;
                                    $failure = 0;
                                    $fake = 0;
                                    $opened = 0;
                                    $avto = 0;
                                    $bad_con = 0;
                                    $false = 0;
                                    $up_sell = 0;
                                    $up_sell_2 = 0;
                                    $cross_sell = 0;
                                    $cross_sell_2 = 0;
                                    $login_time_crm = 0;
                                    $login_time_elastix = 0;
                                    $talk_time = 0;
                                    $pause_time = 0;
                                    $order_time = 0;

                                    $newCrm = 0;
                                    $newPbx = 0;
                                    ?>
                                    <tbody>
                                    @foreach ($orders as $order)
                                        <?
                                        $approve += $order['approve'];
                                        $failure += $order['failure'];
                                        $fake += $order['fake'];
                                        $opened += $order['opened'];
                                        $avto += $order['avto'];
                                        $bad_con += $order['bad_con'];
                                        $false += $order['false'];
                                        $up_sell += $order['up_sell'];
                                        $up_sell_2 += $order['up_sell_2'];
                                        $cross_sell += $order['cross_sell'];
                                        $cross_sell_2 += $order['cross_sell_2'];
                                        $login_time_crm += $order['login_time_crm'];
                                        $login_time_elastix += $order['login_time_elastix'];
                                        $talk_time += $order['talk_time'];
                                        $pause_time += $order['pause_time'];
                                        $order_time += $order['order_time'];

                                        $newCrm += $order['new_crm'];
                                        $newPbx += $order['new_pbx'];

                                        $url = route('bad-connection') . '?user=' . $order['user_id'];

                                        if (isset($_GET['date_start'])) {
                                            $url .= "&date_start=" . strtotime($_GET['date_start'] . ' 00:00:00');
                                        }
                                        if (isset($_GET['date_end'])) {
                                            $url .= "&date_end=" . strtotime($_GET['date_end'] . ' 23:59:59');
                                        }
                                        if (isset($_GET['company'])) {
                                            $url .= "&company=" . $_GET['company'];
                                        }

                                        ?>
                                        <tr>
                                            <td>
                                                <a href="{{route('users-edit', $order['user_id'])}}">{{ (isset($users[$order['user_id']])) ? $users[$order['user_id']]->name . ' ' . $users[$order['user_id']]->surname : $order['user_id'] }}</a>
                                            </td>
                                            <td class="text-center">{{ (($order['opened'] - $order['fake'] - $order['avto'] - $order['bad_con']) > 0) ? round($order['approve'] / ($order['opened'] - $order['fake'] - $order['avto'] - $order['bad_con']) * 100) : 0 }}
                                                %
                                            </td>
                                            <td class="text-center">{{ $order['opened'] }}</td>
                                            <td class="text-center">
                                                @if (isset($permissions['page_bad_connection']))
                                                    <a href="{{$url}}&cause=1">
                                                        {{ $order['avto'] }}
                                                    </a>
                                                @else
                                                    {{ $order['avto'] }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (isset($permissions['page_bad_connection']))
                                                    <a href="{{$url}}&cause=2">
                                                        {{ $order['bad_con'] }}
                                                    </a>
                                                @else
                                                    {{ $order['bad_con'] }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (isset($permissions['page_bad_connection']))
                                                    <a href="{{$url}}&cause=4">
                                                        {{ $order['false'] }}
                                                    </a>
                                                @else
                                                    {{ $order['false'] }}
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $order['approve'] }}</td>
                                            <td class="text-center">{{ $order['failure'] }}</td>
                                            <td class="text-center">{{ $order['fake'] }}</td>
                                            <td class="text-center">{{ $order['up_sell'] }}</td>
                                            <td class="text-center">{{ $order['up_sell_2'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell_2'] }}</td>
                                            <td class="text-center">{{ dateProcessing($order['new_crm']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['new_pbx']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['talk_time']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['pause_time']) }}</td>
                                            <td class="text-center">{{ dateProcessing($order['order_time']) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th> @lang('general.total')</th>
                                        <th class="text-center">{{ (($opened - $fake - $avto - $bad_con) > 0) ? round($approve / ($opened - $fake - $avto - $bad_con) * 100) : 0}}
                                            %
                                        </th>
                                        <th class="text-center">{{ $opened }}</th>
                                        <th class="text-center">{{ $avto }}</th>
                                        <th class="text-center">{{ $bad_con }}</th>
                                        <th class="text-center">{{ $false }}</th>
                                        <th class="text-center">{{ $approve }}</th>
                                        <th class="text-center">{{ $failure }}</th>
                                        <th class="text-center">{{ $fake }}</th>
                                        <th class="text-center">{{ $up_sell }}</th>
                                        <th class="text-center">{{ $up_sell_2 }}</th>
                                        <th class="text-center">{{ $cross_sell }}</th>
                                        <th class="text-center">{{ $cross_sell_2 }}</th>
                                        <th class="text-center">{{ dateProcessing($newCrm) }}</th>
                                        <th class="text-center">{{ dateProcessing($newPbx) }}</th>
                                        <th class="text-center">{{ dateProcessing($talk_time) }}</th>
                                        <th class="text-center">{{ dateProcessing($pause_time) }}</th>
                                        <th class="text-center">{{ dateProcessing($order_time) }}</th>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        @elseif (isset($_GET['group']) && $_GET['group'] == 'offer')
                            <table class="table tablesorter" id="order_table">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('general.offer')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.approved') %
                                    </th>
                                    <th class="text-center">
                                        @lang('general.total')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.approved')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.processing')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.refusal')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.cancel')
                                    </th>
                                    <th class="text-center">
                                        Up
                                    </th>
                                    <th class="text-center">
                                        Up 2
                                    </th>
                                    <th class="text-center">
                                        Cross
                                    </th>
                                    <th class="text-center">
                                        Cross2
                                    </th>
                                </tr>
                                </thead>
                                @if ($orders)
                                    <tbody>
                                    <?
                                    $allOrders = 0;
                                    $approve = 0;
                                    $processing = 0;
                                    $failure = 0;
                                    $fake = 0;
                                    $up_sell = 0;
                                    $up_sell_2 = 0;
                                    $cross_sell = 0;
                                    $cross_sell_2 = 0;
                                    ?>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <? $all = $order['approve'] + $order['failure'] + $order['fake'] + $order['processing'] ?>
                                            <td>{{ isset($offers_filter[$order['offer_id']]) ? $offers_filter[$order['offer_id']]->name : $order['offer_id'] }}</td>
                                            <td class="text-center">{{ (($all - $order['fake']) > 0) ? round($order['approve'] / ($all - $order['fake']) * 100) : 0 }}
                                                %
                                            </td>
                                            <td class="text-center">{{ $all }}</td>
                                            <td class="text-center">{{ $order['approve'] }}</td>
                                            <td class="text-center">{{ $order['processing'] }}</td>
                                            <td class="text-center">{{ $order['failure'] }}</td>
                                            <td class="text-center">{{ $order['fake'] }}</td>
                                            <td class="text-center">{{ $order['up_sell'] }}</td>
                                            <td class="text-center">{{ $order['up_sell_2'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell_2'] }}</td>
                                        </tr>
                                        <?
                                        $allOrders += $all;
                                        $approve += $order['approve'];
                                        $processing += $order['processing'];
                                        $failure += $order['failure'];
                                        $fake += $order['fake'];
                                        $up_sell += $order['up_sell'];
                                        $up_sell_2 += $order['up_sell_2'];
                                        $cross_sell += $order['cross_sell'];
                                        $cross_sell_2 += $order['cross_sell_2'];
                                        ?>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th> @lang('general.total')</th>
                                        <th class="text-center"></th>
                                        <th class="text-center">{{$allOrders}}</th>
                                        <th class="text-center">{{$approve}}</th>
                                        <th class="text-center">{{$processing}}</th>
                                        <th class="text-center">{{$failure}}</th>
                                        <th class="text-center">{{$fake}}</th>
                                        <th class="text-center">{{$up_sell}}</th>
                                        <th class="text-center">{{$up_sell_2}}</th>
                                        <th class="text-center">{{$cross_sell}}</th>
                                        <th class="text-center">{{$cross_sell_2}}</th>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        @elseif (isset($_GET['group']) && $_GET['group'] == 'source')
                            <table class="table tablesorter" id="order_table">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('general.project')
                                    </th>
                                    <th class="text-center"> @lang('general.total')</th>
                                    <th class="text-center"> @lang('general.approved') %</th>
                                    <th class="text-center"> @lang('general.processing')</th>
                                    <th class="text-center"> @lang('general.refusal')</th>
                                    <th class="text-center"> @lang('general.cancel')</th>
                                    <th class="text-center"> @lang('general.talk-time')</th>
                                    <th class="text-center">Up</th>
                                    <th class="text-center">Up 2</th>
                                    <th class="text-center">Cross</th>
                                    <th class="text-center">Cross2</th>
                                </tr>
                                </thead>
                                @if ($orders)
                                    <tbody>
                                    <?
                                    $allOrders = 0;
                                    $approve = 0;
                                    $processing = 0;
                                    $failure = 0;
                                    $fake = 0;
                                    $time = 0;
                                    $up_sell = 0;
                                    $up_sell_2 = 0;
                                    $cross_sell = 0;
                                    $cross_sell_2 = 0;
                                    ?>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ isset($projects[$order['project_id']]) ? $projects[$order['project_id']]->name : $order['project_id'] }}</td>
                                            <td class="text-center">{{ $order['approve'] + $order['failure'] + $order['fake'] + $order['processing'] }}</td>
                                            <td class="text-center">{{ $order['approve'] }}</td>
                                            <td class="text-center">{{ $order['processing'] }}</td>
                                            <td class="text-center">{{ $order['failure'] }}</td>
                                            <td class="text-center">{{ $order['fake'] }}</td>
                                            <td class="text-center">{{ dateProcessing($order['time']) }}</td>
                                            <td class="text-center">{{ $order['up_sell'] }}</td>
                                            <td class="text-center">{{ $order['up_sell_2'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell_2'] }}</td>
                                        </tr>
                                        <?php
                                        $allOrders += ($order['approve'] + $order['failure'] + $order['fake'] + $order['processing']);
                                        $approve += $order['approve'];
                                        $processing += $order['processing'];
                                        $failure += $order['failure'];
                                        $fake += $order['fake'];
                                        $time += $order['time'];
                                        $up_sell += $order['up_sell'];
                                        $up_sell_2 += $order['up_sell_2'];
                                        $cross_sell += $order['cross_sell'];
                                        $cross_sell_2 += $order['cross_sell_2'];
                                        ?>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th> @lang('general.total')</th>
                                        <th class="text-center">{{$allOrders}}</th>
                                        <th class="text-center">{{$approve}}</th>
                                        <th class="text-center">{{$processing}}</th>
                                        <th class="text-center">{{$failure}}</th>
                                        <th class="text-center">{{$fake}}</th>
                                        <th class="text-center">{{dateProcessing($time)}}</th>
                                        <th class="text-center">{{$up_sell}} </th>
                                        <th class="text-center">{{$up_sell_2}}</th>
                                        <th class="text-center">{{$cross_sell}} </th>
                                        <th class="text-center">{{$cross_sell_2}} </th>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        @elseif (isset($_GET['group']) && $_GET['group'] == 'country')
                            <table class="table tablesorter" id="order_table">
                                <thead>
                                <tr>
                                    <th> @lang('general.country')</th>
                                    <th class="text-center"> @lang('general.total')</th>
                                    <th class="text-center"> @lang('general.approved')</th>
                                    <th class="text-center"> @lang('general.processing')</th>
                                    <th class="text-center"> @lang('general.refusal')</th>
                                    <th class="text-center"> @lang('general.cancel')</th>
                                    <th class="text-center">Up</th>
                                    <th class="text-center">Up 2</th>
                                    <th class="text-center">Cross</th>
                                    <th class="text-center">Cross2</th>
                                </tr>
                                </thead>
                                @if ($orders)
                                    <tbody>
                                    <?
                                    $allOrders = 0;
                                    $approve = 0;
                                    $processing = 0;
                                    $failure = 0;
                                    $fake = 0;
                                    $up_sell = 0;
                                    $up_sell_2 = 0;
                                    $cross_sell = 0;
                                    $cross_sell_2 = 0;
                                    ?>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>@if (isset($country[$order['country']])) @lang('countries.' . $country[$order['country']]->code) @else {{$order['country'] }} @endif</td>
                                            <td class="text-center">{{ $order['approve'] + $order['failure'] + $order['fake'] + $order['processing'] }}</td>
                                            <td class="text-center">{{ $order['approve'] }}</td>
                                            <td class="text-center">{{ $order['processing'] }}</td>
                                            <td class="text-center">{{ $order['failure'] }}</td>
                                            <td class="text-center">{{ $order['fake'] }}</td>
                                            <td class="text-center">{{ $order['up_sell'] }}</td>
                                            <td class="text-center">{{ $order['up_sell_2'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell'] }}</td>
                                            <td class="text-center">{{ $order['cross_sell_2'] }}</td>
                                        </tr>
                                        <?php
                                        $allOrders += ($order['approve'] + $order['failure'] + $order['fake'] + $order['processing']);
                                        $approve += $order['approve'];
                                        $processing += $order['processing'];
                                        $failure += $order['failure'];
                                        $fake += $order['fake'];
                                        $up_sell += $order['up_sell'];
                                        $up_sell_2 += $order['up_sell_2'];
                                        $cross_sell += $order['cross_sell'];
                                        $cross_sell_2 += $order['cross_sell_2'];
                                        ?>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th> @lang('general.total')</th>
                                        <th class="text-center">{{$allOrders}}</th>
                                        <th class="text-center">{{$approve}}</th>
                                        <th class="text-center">{{$processing}}</th>
                                        <th class="text-center">{{$failure}}</th>
                                        <th class="text-center">{{$fake}}</th>
                                        <th class="text-center">{{$up_sell}} </th>
                                        <th class="text-center">{{$up_sell_2}}</th>
                                        <th class="text-center">{{$cross_sell}} </th>
                                        <th class="text-center">{{$cross_sell_2}} </th>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        @elseif (isset($_GET['group']) && $_GET['group'] == 'approveOffer')
                            <table class="table tablesorter" id="order_table">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('general.offer')
                                    </th>
                                    <th class="text-center"> @lang('general.total')</th>
                                    <th class="text-center"> @lang('general.approved')</th>
                                    <th class="text-center"> @lang('general.approved'), %</th>
                                    <th class="text-center">Up-Cross, %</th>
                                    @forelse($orders['countries'] as $c)
                                        <th class="text-center {{$c}}">{{mb_strtoupper($c)}}, %</th>
                                    @empty
                                    @endforelse
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders['orders'] as $offer)
                                    <tr>
                                        <td>
                                            {{$offer['name']}}
                                        </td>
                                        <td class="text-center">{{$offer['all']}}</td>
                                        <td class="text-center">{{$offer['all_approve']}}</td>
                                        <td class="text-center">
                                            @if ($offer['all'])
                                                {{round($offer['all_approve'] / $offer['all'] * 100, 2)}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($offer['all_products'])
                                                {{round($offer['all_up_cross'] / $offer['all_products'] * 100, 2)}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                        @forelse($orders['countries'] as $c)
                                            @php
                                                $class = '';
                                                $geoApprove = 0;
                                                $geoUpApprove = 0;
                                                if (isset($offer[$c]) && $offer['all']) {
                                                    $geoApprove = round($offer[mb_strtolower($c)] / $offer['all'] * 100, 2);
                                                }
                                                //if (isset($offer['up_' . mb_strtolower($c)]) && $offer['all']) {
                                                  //  $geoUpApprove = round($offer['up_' . mb_strtolower($c)] / $offer['all_products'] * 100, 2);
                                                //}

                                                if (isset($offer[mb_strtolower($c) . '_rate'])) {
                                                    if ($geoApprove >= $offer[mb_strtolower($c) . '_rate']->rate) {
                                                        $class = 'success';
                                                    } else {
                                                        $class = 'danger';
                                                    }
                                                }
                                            @endphp
                                            <td class="text-center text-center {{$c}} {{$class}}">
                                                {{$geoApprove}}
                                            </td>
                                        @empty
                                        @endforelse
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
