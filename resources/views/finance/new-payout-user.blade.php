@extends('layouts.app')

@section('title') @lang('finance.payment') {{$user->surname}} {{$user->name}}@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/finance/new-payout-user.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('finance.payment') {{$user->surname}} {{$user->name}}</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="col-sm-6">
                    <div class="main-box-body clearfix">
                        <form class=" form-horizontal" method="post">
                            <header class="main-box-header clearfix">
                                <h2 > @lang('general.user') {{$user->surname}} {{$user->name}}</h2>
                            </header>
                            <div class="form-group">
                                <label for="period_start" class="col-sm-4 control-label" style="text-align: left;"> @lang('finance.period')</label>
                                <div class="col-sm-8">
                                    <div class="col-sm-6" style="padding-right: 0;">
                                        <div class="from-group">
                                            <label class="col-sm-4 control-label">  @lang('general.from')</label>
                                            <div class="col-sm-8" style="padding-right: 0;">
                                                <input class="form-control period" type="text" name="period_start" id="period_start" placeholder="с" readonly value="{{\Carbon\Carbon::parse($transaction->min)->format('d/m/y')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="padding-right: 0;">
                                        <div class="from-group">
                                            <label class="col-sm-4 control-label"> @lang('general.to')</label>
                                            <div class="col-sm-8" style="padding-right: 0;">
                                                <input class="form-control period" type="text" name="period_end" id="period_end" placeholder="до" readonly value="{{\Carbon\Carbon::parse($transaction->max)->format('d/m/y')}}" >

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <a class="btn btn-success" id="unlock" style="cursor: pointer; " data-id="{{$user->id}}">
                                            @lang('general.generate')
                                </a>
                            </div>
                            <div class="table-responsive" id="table">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                    <tr>
                                        <td>
                                            @lang('finance.transactions')
                                        </td>
                                        <td>
                                            {{$transaction->count}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @lang('finance.users')
                                        </td>
                                        <td>
                                            {{$transaction->users}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @lang('general.approved')
                                        </td>
                                        <td>
                                            {{$transaction->approve}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @lang('general.up-sell')
                                        </td>
                                        <td>
                                            {{$transaction->count_up}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @lang('general.up-sell') 2
                                        </td>
                                        <td>
                                            {{$transaction->count_up2}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @lang('general.cross-sell')
                                        </td>
                                        <td>
                                            {{$transaction->count_cross}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @lang('general.time-in') CRM
                                        </td>
                                        <td>
                                            {{$transaction->time_crm}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @lang('general.time-in') PBX
                                        </td>
                                        <td>
                                            {{$transaction->time_pbx}}
                                        </td>
                                    </tr>
                                    <tr class="success">
                                        <td>
                                            @lang('general.sum')
                                        </td>
                                        <td id="allPrice">{{$transaction->balance}}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group text-center">
                                <a class="btn btn-success" id="pay">
                                    @lang('general.pay')
                                </a>
                            </div>
                            <div class="block_price" style="display: none">
                                <div class="form-group">
                                    <label for="valuation" class="col-sm-4 control-label" style="text-align: left;" >Сумма</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="text" name="valuation" id="valuation" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="comment" class="col-sm-4 control-label" style="text-align: left;">Коментарий</label>
                                    <div class="col-sm-8">
                                        <textarea name="comment" id="comment" class="form-control" ></textarea>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <input type="submit" value=" @lang('general.confirm')" class="btn btn-success" >
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
