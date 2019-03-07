@extends('layouts.app')
@section('title') @lang('finance.test-plan') @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/"> @lang('general.main')</a></li>
                <li><a href="{{route('plans')}}"><span> @lang('finance.plans')</span></a></li>
            </ol>
            <h1> @lang('finance.test-plan')
                <div class="pull-right top-page-ui">
                    <div class="onoffswitch">
                        @if($plan)
                            @if($plan->status == 'active')
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox"
                                       id="myonoffswitch"
                                       checked>
                            @else
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox"
                                       id="myonoffswitch">
                            @endif
                            <label class="onoffswitch-label" for="myonoffswitch">
                                <div class="onoffswitch-inner"></div>
                                <div class="onoffswitch-switch"></div>
                            </label>
                    </div>
                </div>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    {{Form::open(array('url' => route('plans-test-one', $plan->id), 'method' => 'post'))}}

                    <ul class="nav nav-tabs">
                        <li class="">
                            <a href="{{ route('plan', $plan->id) }}"> @lang('finance.edit-plan')</a>
                        </li>
                        <li class="active">
                            <a href="{{ route('plan-test', $plan->id) }}"> @lang('finance.test-plan')</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            @if($plan->type_method == 'action')
                                {{--<div class="main-box clearfix">--}}
                                {{--<header class="main-box-header clearfix">--}}
                                {{--<h2>Выбрать заказ для тестирования плана:</h2>--}}
                                {{--</header>--}}
                                <div class="main-box-body clearfix">

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th class="text-center"> @lang('general.id')</th>
                                                <th class="text-center"> @lang('general.name')</th>
                                                <th class="text-center"> @lang('general.method')</th>
                                                <th class="text-center"> @lang('general.company')</th>
                                                <th class="text-center"> @lang('finance.order-for-test')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="text-center">
                                                    {{$plan->id}}
                                                </td>
                                                <td class="text-center">
                                                    {{$plan->name}}
                                                </td>
                                                <td class="text-center">
                                                    {{$plan->type_method}}
                                                </td>
                                                <td class="text-center">
                                                    {{$plan['company']->name}}
                                                </td>
                                                <td class="text-center">
                                                    <div class="col-sm-6">
                                                        <div class="checkbox-nice checkbox-inline">
                                                            {{ Form::checkbox('random-order', 'random', true, ['id' => 'random-order']) }}
                                                            {{ Form::label('random-order', 'Случайный заказ') }}
                                                        </div>
                                                    </div>
                                                    <div class="orders-options col-sm-6">
                                                        <div class="random-order">
                                                            <div>


                                                                <select class="form-control" name="order_id"
                                                                        id="order_id"
                                                                        required>
                                                                    <option value="{{$randomOrderIdNotApproved}}">{{$randomOrderIdNotApproved}}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="plan_log_block">

                                    </div>
                                </div>
                            @endif

                            @if($plan->type_method == 'schedule')
                                <div class="main-box-body clearfix">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                              <th class="text-center"> @lang('general.id')</th>
                                              <th class="text-center"> @lang('general.name')</th>
                                              <th class="text-center"> @lang('general.method')</th>
                                              <th class="text-center"> @lang('general.company')</th>
                                              <th class="text-center"> @lang('finance.order-for-test')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="text-center">
                                                    {{$plan->id}}
                                                </td>
                                                <td class="text-center">
                                                    {{$plan->name}}
                                                </td>
                                                <td class="text-center">
                                                    {{$plan->type_method}}
                                                </td>
                                                <td class="text-center">
                                                    {{$plan['company']->name}}
                                                </td>
                                                <td class="text-center">
                                                    <div class="orders-options">
                                                        <div class="random-order">
                                                            <div class="">
                                                                <div class="form-group form-horizontal ">
                                                                    @if($plan->interval == 'week')
                                                                        <input type="week" name="week" id="week">
                                                                    @elseif($plan->interval == 'month')
                                                                        <input type="month" name="month" id="month">
                                                                    @else
                                                                        <input type="date" name="day" id="day">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="plan_log_block">

                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="form-group" style="text-align: center">
                            <input type="hidden" name="id" id="id" value="{{$plan->id}}">
                            {{Form::submit('Run test', [ 'class' => 'btn btn-success'])}}
                        </div>
                        {{ Form::close() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="hidden">
        <div id="orders-all">
            <select class="form-control" name="order_id" id="order_id">
                <option value=""> @lang('general.select-order')</option>
                @if(!empty($ordersNotApproved))
                    @foreach($ordersNotApproved as $orderNotApproved)
                        <option value="{{$orderNotApproved->id}}">{{$orderNotApproved->id}}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div id="random-order-block">
            @if(!empty($randomOrderIdNotApproved))
                <select class="form-control" name="order_id" id="order_id"
                        required>
                    <option value="{{$randomOrderIdNotApproved}}">{{$randomOrderIdNotApproved}}</option>
                </select>
            @endif
        </div>
    </div>


@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plan-test.js') }}"></script>
@stop
