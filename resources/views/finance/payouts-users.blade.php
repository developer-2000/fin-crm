@extends('layouts.app')

@section('title')Выплаты@stop

@section('css')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/profile.css') }}" />
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/transactions/transaction-company.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('finance.payment-operators')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('finance.payment-operators')</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        @if (isset($permissions['page_finance_operators']))
                        <li>
                            <a href="{{route('balance-users')}}"> @lang('general.balance')</a>
                        </li>
                        @endif
                        @if (isset($permissions['page_transactions_operstors']))
                            <li>
                                <a href="{{route('transaction-users')}}"> @lang('finance.transactions')</a>
                            </li>
                        @endif
                        <li class="active">
                            <a href="{{ route('payouts-users') }}"> @lang('finance.payment')</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            @if ($payouts)
                            <div class="main-box-body clearfix" style="margin-top: 20px;padding: 0 0 20px 0;">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th> @lang('general.id')</th>
                                            <th class="text-center"> @lang('general.date')</th>
                                            <th class="text-center"> @lang('finance.paid-off')</th>
                                            <th class="text-center"> @lang('finance.recipient')</th>
                                            <th class="text-center"> @lang('finance.since')</th>
                                            <th class="text-center"> @lang('general.to')</th>
                                            <th class="text-center"> @lang('general.comment')</th>
                                            <th class="text-center"> @lang('general.sum')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($payouts as $payout)
                                        <tr>
                                            <td>{{$payout->id}}</td>
                                            <td class="text-center">
                                                <div class="time">{{\Carbon\Carbon::parse($payout->time_created)->format('H:i:s')}}</div>
                                                <div class="date">{{\Carbon\Carbon::parse($payout->time_created)->format('d/m/y')}}</div>
                                            </td>
                                            <td class="text-center">{{$payout->initiatorSurname}} {{$payout->initiatorName}}</td>
                                            <td class="text-center">{{$payout->operSurname}} {{$payout->operName}}</td>
                                            <td class="text-center">{{\Carbon\Carbon::parse($payout->period_start)->format('H:i:s')}}</td>
                                            <td class="text-center">{{\Carbon\Carbon::parse($payout->period_start)->format('d/m/y')}}</td>
                                            <td class="text-left">
                                                <div class="comment">{!! $payout->comment !!}
                                                </div>
                                            </td>
                                            <td class="text-center salary">{{$payout->valuation}} грн</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pull-right">
                                    {{$payouts->links()}}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
