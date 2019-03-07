@extends('layouts.app')

@section('title') @lang('finance.plan-add') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('finance.plan-add')</span></li>
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
                <div class="main-box-body clearfix">
                    <form class="col-sm-12 " method="post">
                        <div class="col-sm-6">
                            <header class="main-box-header clearfix">
                                <h2> @lang('finance.plan-add')</h2>
                            </header>
                            <div class="form-group">
                                <div class="">
                                    <input class="form-control" type="text" name="name" id="name" value=""
                                           placeholder="Наименование" required>
                                </div>
                            </div>
                            <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                                <div class="form-group">
                                    <label for="calculation-type" class="col-sm-4 control-label"
                                           style="text-align: left;"> @lang('finance.plan-recalculation')</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="calculation-type" id="calculation-type">
                                            <option value="company" selected> @lang('finance.by-companies')</option>
                                            <option value="operator"> @lang('finance.by-operators')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="interval" class="col-sm-4 control-label"
                                           style="text-align: left;"> @lang('general.interval')</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="interval" id="interval">
                                            <option value="month" selected> @lang('general.monthly')</option>
                                            <option value="week"> @lang('general.weekly')</option>
                                            <option value="day"> @lang('general.daily')Ежедневно</option>
                                            <option value="hour"> @lang('general.hourly')</option>
                                            <option value="minute"> @lang('general.per-minute')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="type-payment" class="col-sm-4 control-label" style="text-align: left;">Тип
                                        оплаты</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="type-payment" id="type-payment">
                                            <option value="lead" selected> @lang('general.by-lead')</option>
                                            <option value="hour"> @lang('general.by-hour')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="company" class="col-sm-4 control-label" style="text-align: left;">
                                        Компания</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="company_id" id="company">
                                            <option value="default"> @lang('general.select-company')</option>
                                            @foreach($companies as $key=>$company)
                                                <option value="{{$key}}">{{$company}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="type-action" class="col-sm-4 control-label" style="text-align: left;">Тип
                                        действия</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="type-action" id="type-action">
                                            <option value="schedule" selected> @lang('general.by-schedule')</option>
                                            <option value="action"> @lang('general.by-action')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="radio">
                                    <fieldset>
                                        <legend> @lang('finance.select-base-recalculation')</legend>
                                        <input type="radio" class="status" name="basis-for-calculation" id="percent" value="percent" required>
                                        <label for="percent" class="target_radio"> @lang('general.percent')</label>
                                        <input type="radio" class="status" name="basis-for-calculation" id="quantity" value="quantity" required>
                                        <label for="quantity" class="target_radio"> @lang('general.count')</label>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                            <div class="form-group">
                                <div class="col-sm-8">
                                    <div class="col-sm-4">
                                <label for="success-plan" class="target_radio"> @lang('finance.plan-completed'), @lang('general.count')/%</label>
                                <input class="form-control" type="number" name="success-plan" id="success-plan" required>
                                </div>
                                    <div class="col-sm-6">
                                   <label for="success-plan" class="target_radio"> @lang('finance.plan-not-completed'), @lang('general.count')/%</label>
                                    <input class="form-control" type="number" name="failed-plan" id="failed-plan" required>
                                    </div>
                                </div>
                            </div>
                            </div>
                            {{ Form::submit('Сохранить', array('class' => 'btn btn-success')) }}
                        </div>
                </div>
                </form>
                <div class="hidden">
                    <div id="hour">
                        <div class="form-group">
                            <label for="in_system" class="col-sm-4 control-label" style="text-align: left;">Час в
                                системе</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="in_system" id="in_system">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="in_talk" class="col-sm-4 control-label" style="text-align: left;">Час
                                разговора</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="in_talk" id="in_talk">
                            </div>
                        </div>
                    </div>
                    <div id="lead">
                        <div class="form-group">
                            <label for="approve" class="col-sm-4 control-label"
                                   style="text-align: left;"> @lang('general.approved')</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="approve" id="approve">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="up_sell" class="col-sm-4 control-label" style="text-align: left;">Up
                                sell</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="up_sell" id="up_sell">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="up_sell_2" class="col-sm-4 control-label" style="text-align: left;">Up sell
                                2</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="up_sell_2" id="up_sell_2">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cross_sell" class="col-sm-4 control-label" style="text-align: left;">Cross
                                sell</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="cross_sell" id="cross_sell">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
