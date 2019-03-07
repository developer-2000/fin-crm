@extends('layouts.app')

@section('title') @lang('general.monitoring')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/monitoring_company.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/monitoring/monitoring-ws-client.js') }}"></script>
    <script src="{{ URL::asset('js/monitoring/monitoring_company.js') }}"></script>
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
                                <li class="active"><span>  @lang('general.monitoring')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('general.companies')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="target_user_company" class="hidden">{{auth()->user()->company_id}}</div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 processing">
                    <header class="main-box-header clearfix">
                        <h3>  @lang('general.processes')</h3>
                    </header>
                    <div class="table-responsive">
                        <table class="table " id="processing">
                            <thead>
                            <tr>
                                <th class="text-left"> @lang('general.operator')</th>
                                <th class="text-center"> @lang('general.order')</th>
                                <th class="text-center"> @lang('general.country')</th>
                                <th class="text-center"> @lang('general.phone')</th>
                                <th class="text-center"> @lang('general.status')</th>
                                <th class="text-center"> @lang('general.time')</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        @if ($operators)
                            @foreach ($operators as $operator)
                                <div id="oper_{{ $operator->login_sip }}" data-id="{{$operator->campaign_id}}"
                                     company_id="{{$operator->company_id}}" style="display: none">
                                    {{ $operator->surname }} {{ $operator->name }}
                                </div>
                            @endforeach
                        @endif
                        @if ($campaigns)
                            @foreach($campaigns as $campaign)
                                <div id="campaign_{{ $campaign->id }}" style="display: none">
                                    {{ $campaign->name}}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    {{--<div class="main-box clearfix">--}}
                    {{--<header class="main-box-header clearfix">--}}
                    {{--<h3>Лист ожиданий</h3>--}}
                    {{--</header>--}}
                    {{--<div class="table-responsive">--}}
                    {{--<table class="table" id="list_orders">--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                    {{--<th class="text-center">Id</th>--}}
                    {{--<th class="text-center">Оператор</th>--}}
                    {{--<th class="text-center">Статус</th>--}}
                    {{--<th class="text-center">Время</th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}
                    {{--<tbody>--}}
                    {{--</tbody>--}}
                    {{--</table>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    <div class="main-box clearfix">
                        <header class="main-box-header clearfix">
                            <h3> @lang('general.calling-list')</h3>
                        </header>
                        <div class="table-responsive">
                            <table class="table" id="call_list">
                                <thead>
                                <tr>
                                    <th class="text-center"> @lang('general.id')</th>
                                    <th class="text-center"> @lang('general.status')</th>
                                    <th class="text-center"> @lang('general.phone')</th>
                                    <th class="text-center">  @lang('general.trunk')</th>
                                    <th class="text-center"> @lang('general.time')</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop