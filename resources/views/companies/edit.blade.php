@extends('layouts.app')

@section('title'){{$company->name}}@stop

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
    @php
        $types = [
            'lead'  => registration_trans('companies.by-lead'),
            'hour'  => registration_trans('companies.by-hour'),
            'month' => registration_trans('general.month'),
            'week'  => registration_trans('general.week')
        ];
        $prices = json_decode($company->prices);
        $billingPrices = json_decode($company->billing);
    @endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('general.company') {{$company->name}}</span></li>
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
                                    <h2 > @lang('general.company') {{$company->name}}</h2>
                                </header>
                                @if (!auth()->user()->company_id)
                                    <div class="form-group">
                                        <div class="">
                                            <input class="form-control" type="text" name="name" id="name" value="{{$company->name}}" placeholder=" @lang('general.name')">
                                        </div>
                                    </div>
                                @endif
                                @if (isset($permissions['add_chenge_companies_billing']))
                                    <div class="checkbox checkbox-nice">
                                        <input type="checkbox" name="billing" id="billing" @if($company->billing) checked @endif/>
                                        <label for="billing">
                                            @lang('general.billing')
                                        </label>
                                    </div>
                                @else
                                    @lang('general.billing')
                                    @if($company->billing)
                                        <i class="fa  fa-check" style="color: #1ABC9C"></i>
                                    @endif
                                @endif
                                <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                                    <div class="wrapper">
                                        <div class="form-group">
                                            <label for="type" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.type-pay')</label>
                                            <div class="col-sm-8">
                                                @if (!auth()->user()->company_id)
                                                <select class="form-control" name="type" id="type">
                                                        @foreach($types as $value => $type)
                                                            <option value="{{$value}}" @if ($company->type == $value) selected @endif>{{$type}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    @foreach($types as $value => $type)
                                                        @if ($company->type == $value)
                                                            {{$type}}
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="type">
                                            @if($company->type == 'hour')
                                                <div class="form-group">
                                                    <label for="global[in-system]" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.hour-system')</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                            <input class="form-control" type="text" name="global[in-system]" id="global[in-system]" value="{{$prices->global->in_system}}">
                                                        @else
                                                            {{$prices->global->in_system}}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="global[in-talk]" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.hour-talk')</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                            <input class="form-control" type="text" name="global[in-talk]" id="global[in-talk]" value="{{$prices->global->in_talk}}">
                                                        @else
                                                            {{$prices->global->in_talk}}
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif ($company->type == 'lead')
                                                <div class="form-group">
                                                    <label for="global[approve]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.approved')</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                        <input class="form-control" type="text" name="global[approve]" id="global[approve]" value="{{$prices->global->approve}}">
                                                        @else
                                                            {{$prices->global->approve}}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="global[up-sell]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.up-sell')</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                        <input class="form-control" type="text" name="global[up-sell]" id="global[up-sell]" value="{{$prices->global->up_sell}}">
                                                        @else
                                                            {{$prices->global->up_sell}}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="global[up-sell-2]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.up-sell') 2</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                        <input class="form-control" type="text" name="global[up-sell-2]" id="global[up-sell-2]" value="{{$prices->global->up_sell_2}}">
                                                        @else
                                                            {{$prices->global->up_sell_2}}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="global[cross-sell]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.cross-sell')</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                        <input class="form-control" type="text" name="global[cross-sell]" id="global[cross-sell]" value="{{$prices->global->cross_sell}}">
                                                        @else
                                                            {{$prices->global->cross_sell}}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="global[cross-sell02]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                            <input class="form-control" type="text" name="global[cross-sell-2]" id="global[cross-sell-2]" value="{{$prices->global->cross_sell_2}}">
                                                        @else
                                                            {{$prices->global->cross_sell_2}}
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif ($company->type == 'month' || $company->type == 'week')
                                                <div class="form-group">
                                                    <label for="global[rate]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                                    <div class="col-sm-8">
                                                        @if (!auth()->user()->company_id)
                                                        <input class="form-control" type="text" name="global[rate]" id="global[rate]" value="{{$prices->global->rate}}">
                                                        @else
                                                            {{$prices->global->rate}}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ranks_block">
                                        @if (isset($prices->ranks))
                                            @foreach($prices->ranks as $rankId => $rank)
                                                <div class="wrapper">
                                                    <div class="form-group"></div>
                                                    <div class="form-group">
                                                        <label for="ranks[{{$rankId}}][rank]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rank')</label>
                                                        <div class="col-sm-8">
                                                            @if (!auth()->user()->company_id)
                                                            <select class="form-control" name="ranks[{{$rankId}}][rank]" id="ranks[{{$rankId}}][rank]">
                                                                @forelse($ranks as $r)
                                                                    <option value="{{$r->id}}" @if($r->id == $rankId) selected @endif>{{$r->name}}</option>
                                                                @empty
                                                                @endforelse
                                                            @else
                                                                @forelse($ranks as $r)
                                                                     @if($r->id == $rankId) {{$r->name}} @endif
                                                                @empty
                                                                @endforelse
                                                            @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="ranks[{{$rankId}}][rank-type]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.type')</label>
                                                        <div class="col-sm-8">
                                                            @if (!auth()->user()->company_id)
                                                            <select class="form-control rank-type" name="ranks[{{$rankId}}][rank-type]" id="ranks[{{$rankId}}][rank-type]">
                                                                @foreach($types as $value => $type)
                                                                    <option value="{{$value}}" @if($value == $rank->type) selected @endif >{{$type}}</option>
                                                                @endforeach
                                                            </select>
                                                            @else
                                                                @foreach($types as $value => $type)
                                                                   @if($value == $rank->type) {{$type}} @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="type">
                                                        @if ($rank->type == 'lead')
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][approve]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.approved')</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][approve]" id="ranks[{{$rankId}}][approve]" value="{{$rank->approve}}">
                                                                    @else
                                                                        {{$rank->approve}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][up-sell]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.up-sell')</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][up-sell]" id="ranks[{{$rankId}}][up-sell]" value="{{$rank->up_sell}}">
                                                                    @else
                                                                        {{$rank->up_sell}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][up-sell-2]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.up-sell') 2</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][up-sell-2]" id="ranks[{{$rankId}}][up-sell-2]" value="{{$rank->up_sell_2}}">
                                                                    @else
                                                                        {{$rank->up_sell_2}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][cross-sell]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.cross-sell')</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][cross-sell]" id="ranks[{{$rankId}}][cross-sell]" value="{{$rank->cross_sell}}">
                                                                    @else
                                                                        {{$rank->cross_sell}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][cross-sell-2]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][cross-sell-2]" id="ranks[{{$rankId}}][cross-sell-2]" value="{{$rank->cross_sell_2}}">
                                                                    @else
                                                                        {{$rank->cross_sell_2}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @elseif($rank->type == 'hour')
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][in-system]" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.hour-system')</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][in-system]" id="ranks[{{$rankId}}][in-system]" value="{{$rank->in_system}}">
                                                                    @else
                                                                        {{$rank->in_system}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][in-talk]" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.hour-talk')</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][in-talk]" id="ranks[{{$rankId}}][in-talk]" value="{{$rank->in_talk}}">
                                                                    @else
                                                                        {{$rank->in_talk}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @elseif($rank->type == 'week' || $rank->type == 'month')
                                                            <div class="form-group">
                                                                <label for="ranks[{{$rankId}}][rate]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                                                <div class="col-sm-8">
                                                                    @if (!auth()->user()->company_id)
                                                                        <input class="form-control" type="text" name="ranks[{{$rankId}}][rate]" id="ranks[{{$rankId}}][rate]" value="{{$rank->rate}}">
                                                                    @else
                                                                        {{$rank->rate}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                        @if (!auth()->user()->company_id)
                                                            <button class="btn btn-danger delete_new_rank"> @lang('general.delete')</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        @if(!auth()->user()->company_id)
                                            <button class="btn btn-success add_new_rank"> @lang('companies.add-rank')</button>
                                        @endif
                                    </div>
                                </div>
                                <input type="submit" value=" @lang('general.save')" class="btn btn-success">
                            </div>
                            <div class="col-sm-6 billing_block">
                                @if ($company->billing)
                                    <header class="main-box-header clearfix">
                                        <h2 > @lang('general.billing')</h2>
                                    </header>
                                    <div class="form-horizontal" style="padding-left: 8px; padding-right: 8px;">
                                        <div class="wrapper">
                                            <div class="form-group">
                                                <label for="global[type-billing]" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.type-pay')</label>
                                                <div class="col-sm-8">
                                                    @if (isset($permissions['add_chenge_companies_billing']))
                                                        <select class="form-control select_billing" name="global[type-billing]" id="global[type-billing]">
                                                            @foreach($types as $value => $type)
                                                                <option value="billing_{{$value}}" @if ($value == $company->billing_type) selected @endif>{{$type}}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        @foreach($types as $value => $type)
                                                            @if ($value == $company->billing_type) {{$type}} @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="type_billing">
                                                @if($company->billing_type == 'hour')
                                                    <div class="form-group">
                                                        <label for="global[billing-in-system]" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.hour-system')</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-in-system]" id="global[billing-in-system]" value="{{$billingPrices->global->in_system}}">
                                                            @else
                                                                {{$billingPrices->global->in_system}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="global[billing-in-talk]" class="col-sm-4 control-label" style="text-align: left;"> @lang('companies.hour-talk')</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-in-talk]" id="global[billing-in-talk]" value="{{$billingPrices->global->in_talk}}">
                                                            @else
                                                                {{$billingPrices->global->in_talk}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif ($company->billing_type == 'lead')
                                                    <div class="form-group">
                                                        <label for="global[billing-approve]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.approved')</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-approve]" id="global[billing-approve]" value="{{$billingPrices->global->approve}}">
                                                            @else
                                                                {{$billingPrices->global->approve}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="global[billing-up-sell]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.up-sell')</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-up-sell]" id="global[billing-up-sell]" value="{{$billingPrices->global->up_sell}}">
                                                            @else
                                                                {{$billingPrices->global->up_sell}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="global[billing-up-sell-2]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.up-sell') 2</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-up-sell-2]" id="global[billing-up-sell-2]" value="{{$billingPrices->global->up_sell_2}}">
                                                            @else
                                                                {{$billingPrices->global->up_sell_2}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="global[billing-cross-sell]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.cross-sell')</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-cross-sell]" id="global[billing-cross-sell]" value="{{$billingPrices->global->cross_sell}}">
                                                            @else
                                                                {{$billingPrices->global->cross_sell}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="global[billing-cross-sell-2]" class="col-sm-4 control-label" style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-cross-sell-2]" id="global[billing-cross-sell-2]" value="{{$billingPrices->global->cross_sell_2}}">
                                                            @else
                                                                {{$billingPrices->global->cross_sell_2}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif ($company->billing_type == 'month' || $company->billing_type == 'week')
                                                    <div class="form-group">
                                                        <label for="global[billing-rate]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_companies_billing']))
                                                                <input class="form-control" type="text" name="global[billing-rate]" id="global[billing-rate]" value="{{$billingPrices->global->rate}}">
                                                            @else
                                                                {{$billingPrices->global->rate}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ranks_block">
                                            @if (isset($billingPrices->ranks))
                                                @foreach($billingPrices->ranks as $rankId => $rank)
                                                    <div class="wrapper">
                                                        <div class="form-group"></div>
                                                        <div class="form-group">
                                                            <label for="ranks_billing[{{$rankId}}][rank]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rank')</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="ranks_billing[{{$rankId}}][rank]" id="ranks_billing[{{$rankId}}][rank]">
                                                                    @forelse($ranks as $r)
                                                                        <option value="{{$r->id}}" @if($r->id == $rankId) selected @endif>{{$r->name}}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="ranks_billing[{{$rankId}}][rank-type]" class="col-sm-4 control-label required" style="text-align: left;">Тип</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control rank-type" name="ranks_billing[{{$rankId}}][rank-type]" id="ranks_billing[{{$rankId}}][rank-type]">
                                                                    @foreach($types as $value => $type)
                                                                        <option value="{{$value}}" @if($value == $rank->type) selected @endif >{{$type}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="type">
                                                            @if ($rank->type == 'lead')
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][approve]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.approved')</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][approve]" id="ranks_billing[{{$rankId}}][approve]" value="{{$rank->approve}}">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][up-sell]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell')</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][up-sell]" id="ranks_billing[{{$rankId}}][up-sell]" value="{{$rank->up_sell}}">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][up-sell-2]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.up-sell') 2</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][up-sell-2]" id="ranks_billing[{{$rankId}}][up-sell-2]" value="{{$rank->up_sell_2}}">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][cross-sell]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell')</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][cross-sell]" id="ranks_billing[{{$rankId}}][cross-sell]" value="{{$rank->cross_sell}}">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][cross-sell-2]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.cross-sell') 2</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][cross-sell-2]" id="ranks_billing[{{$rankId}}][cross-sell-2]" value="{{$rank->cross_sell_2}}">
                                                                    </div>
                                                                </div>
                                                            @elseif($rank->type == 'hour')
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][in-system]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.hour-system')</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][in-system]" id="ranks_billing[{{$rankId}}][in-system]" value="{{$rank->in_system}}">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][in-talk]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('companies.hour-talk')</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][in-talk]" id="ranks_billing[{{$rankId}}][in-talk]" value="{{$rank->in_talk}}">
                                                                    </div>
                                                                </div>
                                                            @elseif($rank->type == 'week' || $rank->type == 'month')
                                                                <div class="form-group">
                                                                    <label for="ranks_billing[{{$rankId}}][rate]" class="col-sm-4 control-label required" style="text-align: left;"> @lang('general.rate')</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" type="text" name="ranks_billing[{{$rankId}}][rate]" id="ranks_billing[{{$rankId}}][rate]" value="{{$rank->rate}}">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                            <button class="btn btn-danger delete_new_rank"> @lang('general.delete')</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-success add_new_rank"> @lang('companies.add-rank')</button>
                                        </div>
                                    </div>
                                @endif
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
                                <label for="rank-type" class="col-sm-4 control-label required" style="text-align: left;">Тип</label>
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
