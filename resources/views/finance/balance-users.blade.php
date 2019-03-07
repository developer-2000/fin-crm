@extends('layouts.app')

@section('title') @lang('general.balance') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}" />
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
                <li class="active"><span> @lang('finance.operator-balance')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('finance.operator-balance')</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{route('balance-users')}}"> @lang('general.balance')</a>
                        </li>
                        @if (isset($permissions['page_transactions_operstors']))
                            <li>
                                <a href="{{route('transaction-users')}}"> @lang('finance.transactions')</a>
                            </li>
                        @endif
                        @if (isset($permissions['page_payouts_operators']))
                            <li>
                                <a href="{{ route('payouts-users') }}"> @lang('finance.payments')</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            <form class="form" method="post" action="{{ route("balance-users") }}" style="padding: 0;">
                                <div class="main-box">
                                    <div class="item_rows ">
                                        <div class="main-box-body clearfix">
                                            <div class="row">
                                                @if (isset($permissions['filter_companies_page_balance_users']))
                                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                        <label for="company"
                                                               class="col-sm-4 control-label"> @lang('general.companies')</label>
                                                        <div class="col-sm-8">
                                                            <select id="company" name="company[]" style="width: 100%"
                                                                    multiple>
                                                                @if($companies->count())
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
                                                    </div>
                                                @endif
                                                <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                    <label for="operator"
                                                           class="col-sm-4 control-label"> @lang('general.operator')</label>
                                                    <div class="col-sm-8">
                                                        <select id="operator" name="operator[]" style="width: 100%"
                                                                multiple>
                                                            @foreach ($allOperators as $operator)
                                                                <option
                                                                        @if (isset($_GET['operator']))
                                                                        <? $opGet = explode(',', $_GET['operator']); ?>
                                                                        @foreach ($opGet as $cg)
                                                                        @if ($operator->id == $cg)
                                                                        selected
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                        value="{{ $operator->id }}">{{$operator->surname}} {{ $operator->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btns_filter">
                                    <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                                    <a href="{{ route('balance-users') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                                </div>

                            </form>
                            <div class="row">
                                <div class="table-responsive">
                                    @if ($operators)
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th> @lang('general.fio')</th>
                                                <th class="text-center"> @lang('general.approved')</th>
                                                <th class="text-center"> @lang('general.up-sell') 1 </th>
                                                <th class="text-center"> @lang('general.up-sell') 2</th>
                                                <th class="text-center"> @lang('general.cross-sell')</th>
                                                <th class="text-center"> @lang('general.time') CRM</th>
                                                <th class="text-center"> @lang('finance.time-line')</th>
                                                <th class="text-center"> @lang('finance.time-talk')</th>
                                                <th class="text-center"> @lang('general.period')</th>
                                                <th class="text-center"> @lang('general.balance')</th>
                                                @if (isset($permissions['do_payout_operators']))
                                                    <th></th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($operators as $operator)
                                                <tr>
                                                    <td class="text-center">{{$operator->surname}} {{$operator->name}}</td>
                                                    <td class="text-center">{{$operator->approve ? $operator->approve : '-' }}</td>
                                                    <td class="text-center">{{$operator->count_up ? $operator->count_up : '-' }}</td>
                                                    <td class="text-center">{{$operator->count_up2 ? $operator->count_up2 : '-' }}</td>
                                                    <td class="text-center">{{$operator->count_cross ? $operator->count_cross : '-' }}</td>
                                                    <td class="text-center">{{$operator->time_crm ? dateProcessing($operator->time_crm) : '-' }}</td>
                                                    <td class="text-center">{{$operator->time_pbx ? dateProcessing($operator->time_pbx) : '-' }}</td>
                                                    <td class="text-center">{{$operator->time_talk ? dateProcessing($operator->time_talk) : '-' }}</td>
                                                    <td class="text-center">
                                                        {{\Carbon\Carbon::parse($operator->min)->format('d/m/y')}}
                                                        -
                                                        {{\Carbon\Carbon::parse($operator->max)->format('d/m/y')}}
                                                    </td>
                                                    <td><b>{{$operator->balance}}</b> грн</td>
                                                    @if (isset($permissions['do_payout_operators']))
                                                        <td class="text-center">
                                                            <a href="{{route('new-payout-user', $operator->user_id)}}"
                                                               class="table-link ">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x "></i>
                                                                <i class="fa fa-file-text-o fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                            </a>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{$operators->links()}}
            </div>
        </div>
    </div>
@stop
