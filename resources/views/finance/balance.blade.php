@extends('layouts.app')

@section('title') @lang('general.balance') @stop

@section('css')
@stop

@section('jsBottom')
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('finance.balance-companies')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('finance.balance-companies')</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{route('finance-balance-companies')}}"> @lang('general.balance')</a>
                        </li>
                        @if (isset($permissions['page_transactions_companies']))
                        <li>
                            <a href="{{route('finance-transactions-companies')}}"> @lang('finance.transactions')</a>
                        </li>
                        @endif
                        @if (isset($permissions['page_payouts_companies']))
                        <li>
                            <a href="{{ route('finance-payouts-companies') }}"> @lang('finance.payments')</a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            <div class="row">
                                <div class="table-responsive">
                                    @if ($companies->count())
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th> @lang('general.company')</th>
                                                <th class="text-center"> @lang('general.approved')</th>
                                                <th class="text-center"> @lang('general.up-sell') 1 </th>
                                                <th class="text-center"> @lang('general.up-sell') 2</th>
                                                <th class="text-center"> @lang('general.cross-sell')</th>
                                                <th class="text-center"> @lang('general.time') CRM</th>
                                                <th class="text-center"> @lang('general.time') PBX</th>
                                                <th class="text-center"> @lang('finance.time-talk')</th>
                                                <th class="text-center"> @lang('finance.period')</th>
                                                <th class="text-center"> @lang('finance.balance')</th>
                                                @if (isset($permissions['do_payout_companies']))
                                                <th></th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($companies as $c)
                                                <tr>
                                                    <td>{{ $c->name }}</td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{$balance[$c->id]->approve}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{$balance[$c->id]->up_sell}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{$balance[$c->id]->up_sell_2}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{$balance[$c->id]->cross_sell}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{dateProcessing($balance[$c->id]->time_crm)}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{dateProcessing($balance[$c->id]->time_pbx)}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{dateProcessing($balance[$c->id]->time_talk)}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            {{\Carbon\Carbon::parse($balance[$c->id]->min)->format('d/m/y')}}
                                                            -
                                                            {{\Carbon\Carbon::parse($balance[$c->id]->max)->format('d/m/y')}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (isset($balance[$c->id]))
                                                            <b>{{$balance[$c->id]->sum}}</b> грн
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                    @if (isset($permissions['do_payout_companies']))
                                                    <td class="text-center">
                                                        <a href="{{route('new-payout-company', $c->id)}}"
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
        </div>
    </div>
@stop
