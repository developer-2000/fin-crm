@extends('layouts.app')

@section('title') @lang('general.operators-setting')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/groupByOperators.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class=""><span> @lang('general.all-campaigns')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.all-campaigns')</h1>
                @if (isset($permissions['create_edit_cold_call_list']))
                    <div class="pull-right top-page-ui">
                        <a href="{{route('campaigns-create')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('general.campaign-create')
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="{{ route('cold-calls-campaigns') }}"> @lang('companies.all-campaigns')</a>
                        </li>
                        <li class="active">
                            <a href="{{ route('cold-calls-operators-settings') }}"> @lang('general.operators-setting')</a>
                        </li>
                    </ul>
                    <div class="main-box clearfix">
                        <header class="main-box-header clearfix" style="border-bottom: 1px solid #e4e4e4;">
                            <h2>  @lang('companies.operators-setting')</h2>
                            <div class="filter-block">
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control search"
                                               placeholder=" @lang('general.operators-setting')">
                                        <i class="fa fa-search search-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </header>
                        <div class="wrapper">
                            <div class="col-md-4">
                                <header class="main-box-header clearfix text-center">
                                    <h5> @lang('general.campaigns')</h5>
                                </header>
                                @if ($campaigns)
                                    <ul class="group_operator">
                                        @foreach($campaigns as $campaign)
                                            <li id="group_{{$campaign->id}}">
                                                <div class="name_campaign_wrap">
                                                    <?php
                                                    $color = 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
                                                    ?>
                                                    <div class="count" style="color: {{$color}}">
                                                        @if (isset($operators[$campaign->id]))
                                                            {{count($operators[$campaign->id])}}
                                                        @else
                                                            0
                                                        @endif
                                                    </div>
                                                    <div class="content"
                                                         style="background-color: {{$color}};">{{$campaign->name}}</div>
                                                </div>
                                                <ul class="operators_in_group" style="display: none">
                                                    @if (isset($operators[$campaign->id]))
                                                        @foreach($operators[$campaign->id] as $operator)
                                                            <li id="operator_{{$operator->id}}" class="operator"
                                                                style="border-color: {{$color}};"
                                                                data-id="{{$operator->login_sip}}">
                                                                <div>
                                                                    {{$operator->surname}}  {{$operator->name}}
                                                                </div>
                                                                <span class="pull-right">
                                                <b> @lang('general.id')</b> {{$operator->login_sip}}
                                                </span>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <header class="main-box-header clearfix text-center">
                                    <h5> @lang('general.distribute-to-campaign')</h5>
                                </header>
                                @if ($campaigns)
                                    <ul class="groups">
                                        @foreach($campaigns as $campaign)
                                            <li class="group">
                                                <ul class="target_groups "
                                                    group-id="{{$campaign->id}}">{{$campaign->name}}
                                                </ul>
                                            </li>
                                        @endforeach
                                        <li class="group">
                                            <ul class="target_groups " group-id="0"> @lang('general.not-distributed')
                                            </ul>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <header class="main-box-header clearfix text-center">
                                    <h5> @lang('general.free-operators')</h5>
                                </header>
                                @if (isset($operators[0]))
                                    <ul class="operators">
                                        @foreach($operators[0] as $operator)
                                            <li id="operator_{{$operator->id}}" class="operator"
                                                data-id="{{$operator->login_sip}}">
                                                <div>
                                                    {{$operator->surname}}  {{$operator->name}}
                                                </div>
                                                <span class="pull-right">
                                    <b> @lang('general.id')</b> {{$operator->login_sip}}
                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('jsBottom')
    <script src="{{URL::asset('js/jquery-ui-1.9.2.custom/minified/jquery.ui.core.min.js')}}"></script>
    <script src="{{URL::asset('js/jquery-ui-1.9.2.custom/minified/jquery.ui.widget.min.js')}}"></script>
    <script src="{{URL::asset('js/jquery-ui-1.9.2.custom/minified/jquery.ui.mouse.min.js')}}"></script>
    <script src="{{URL::asset('js/jquery-ui-1.9.2.custom/minified/jquery.ui.sortable.min.js')}}"></script>
    <script src="{{ URL::asset('js/cold-calls/groupByOperatorsColdCalls.js') }}"></script>
@stop