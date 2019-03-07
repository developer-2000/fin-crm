@extends('layouts.app')

@section('title') @lang('companies.create') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/companies/registration-company.js') }}"></script>
@stop

@section('content')
    @php(
        $types = [
          'lead'  => registration_trans('companies.by-lead'),
          'hour'  => registration_trans('companies.by-hour'),
          'month' => registration_trans('general.month'),
          'week'  => registration_trans('general.week')
        ]
    )


    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('companies.create')</span></li>
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
                        <div class="row">
                            <div class="col-sm-6">
                                <header class="main-box-header clearfix">
                                    <h2 > @lang('companies.create')</h2>
                                </header>
                                <div class="form-group">
                                    <div class="">
                                        <input class="form-control" type="text" name="name" id="name" value="" placeholder="Наименование">
                                    </div>
                                </div>
                                <div class="checkbox checkbox-nice">
                                    <input type="checkbox" name="billing" id="billing" />
                                    <label for="billing">
                                        @lang('general.billing')
                                    </label>
                                </div>
                                <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                                    <div class="wrapper">
                                        <div class="form-group">
                                            <label for="type" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.type-pay')</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="type" id="type">
                                                    @foreach($types as $value => $type)
                                                        <option value="{{$value}}">{{$type}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="type">
                                            <div class="form-group">
                                                <label for="global[approve]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.approved')</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="global[approve]" id="global[approve]">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="global[up-sell]" class="col-sm-4 control-label required" style="text-align: left;">Up
                                                    sell</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="global[up-sell]" id="global[up-sell]">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="global[up-sell-2]" class="col-sm-4 control-label required" style="text-align: left;">Up
                                                    sell 2</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="global[up-sell-2]" id="global[up-sell-2]">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="global[cross-sell]" class="col-sm-4 control-label required"
                                                       style="text-align: left;"> @lang('general.cross-sell')</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="global[cross-sell]" id="global[cross-sell]">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="global[cross-sell-2]" class="col-sm-4 control-label required"
                                                       style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" name="global[cross-sell-2]" id="global[cross-sell-2]">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ranks_block">
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-success add_new_rank"> @lang('companies.add-rank')</button>
                                    </div>
                                </div>
                                <input type="submit" value="Сохранить" class="btn btn-success">
                            </div>
                            <div class="col-sm-6 billing_block">
                            </div>
                        </div>
                        <div class="error-messages"></div>
                    </form>
                    <div class="hidden">
                        <div id="hour">
                            <div class="form-group">
                                <label for="in-system" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.hour-system')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="in-system" id="in-system">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="in-talk" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.hour-talk')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="in-talk" id="in-talk">
                                </div>
                            </div>
                        </div>
                        <div id="lead">
                            <div class="form-group">
                                <label for="approve" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.approved')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="approve" id="approve">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="up-sell" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="up-sell" id="up-sell">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="up-sell-2" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell') 2</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="up-sell-2" id="up-sell-2">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cross-sell" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="cross-sell" id="cross-sell">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cross-sell-2" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="cross-sell-2" id="cross-sell-2">
                                </div>
                            </div>
                        </div>
                        <div id="billing_hour">
                            <div class="form-group">
                                <label for="billing-in-system" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.hour-system')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-in-system" id="billing-in-system">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing-in-talk" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.hour-talk')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-in-talk" id="billing-in-talk">
                                </div>
                            </div>
                        </div>
                        <div id="billing_lead">
                            <div class="form-group">
                                <label for="billing-approve" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.approved')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-approve" id="billing-approve">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing-up-sell" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-up-sell" id="billing-up-sell">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing-up-sell-2" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell') 2</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-up-sell-2" id="billing-up-sell-2">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing-cross-sell" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-cross-sell" id="billing-cross-sell">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing-cross-sell-2" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-cross-sell-2" id="billing-cross-sell-2">
                                </div>
                            </div>
                        </div>
                        <div id="month">
                            <div class="form-group">
                                <label for="rate" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="rate" id="rate">
                                </div>
                            </div>
                        </div>
                        <div id="week">
                            <div class="form-group">
                                <label for="rate" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="rate" id="rate">
                                </div>
                            </div>
                        </div>
                        <div id="billing_month">
                            <div class="form-group">
                                <label for="billing-rate" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-rate" id="billing-rate">
                                </div>
                            </div>
                        </div>
                        <div id="billing_week">
                            <div class="form-group">
                                <label for="billing-rate" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="billing-rate" id="billing-rate">
                                </div>
                            </div>
                        </div>
                        <div id="billing_block">
                            @if (isset($permissions['add_chenge_sip_users']))
                            <header class="main-box-header clearfix">
                                <h2 > @lang('general.billing')</h2>
                            </header>
                            <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                                <div class="wrapper">
                                    <div class="form-group">
                                        <label for="global[type-billing]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.type-pay')</label>
                                        <div class="col-sm-8">
                                            <select class="form-control select_billing" name="global[type-billing]" id="global[type-billing]">
                                                @foreach($types as $value => $type)
                                                    <option value="billing_{{$value}}">{{$type}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="type_billing">
                                        <div class="form-group">
                                            <label for="global[billing-approve]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.approved')</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="global[billing-approve]" id="global[billing-approve]">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="global[billing-up-sell]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell')</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="global[billing-up-sell]" id="global[billing-up-sell]">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="global[billing-up-sell-2]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell') 2</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="global[billing-up-sell-2]" id="global[billing-up-sell-2]">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="global[billing-cross-sell]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell')</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="global[billing-cross-sell]" id="global[billing-cross-sell]">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="global[billing-cross-sell-2]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="global[billing-cross-sell-2]" id="global[billing-cross-sell-2]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ranks_block">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-success add_new_rank"> @lang('companies.add-rank')</button>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="wrapper">
                            <div class="form-group"></div>
                            <div class="form-group">
                                <label for="rank" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rank')</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="rank" id="rank">
                                        <option value=""> @lang('general.select-rank')</option>
                                        @forelse($ranks as $rank)
                                            <option value="{{$rank->id}}">{{$rank->name}}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rank-type" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.type')</label>
                                <div class="col-sm-8">
                                    <select class="form-control rank-type" name="rank-type" id="rank-type">
                                        <option value=""> @lang('general.select')</option>
                                        @foreach($types as $value => $type)
                                            <option value="{{$value}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="type"></div>
                            <div class="form-group">
                                <button class="btn btn-danger delete_new_rank"> @lang('general.delete')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
