@extends('layouts.app')

@section('title') @lang('collectings.collecting') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <style>
        body{
            color: #929292;
        }
    </style>
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/snap.svg-min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/collectings/index.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('collectings.collecting')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('collectings.collecting')(<span class="badge">{{$countOrder}}</span>)</h1>
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url() }}" id="filters"
                      method="post">
                    <input type="hidden" id="route_name" value="{{Request::route()->getName()}}">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_id_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="id" class="col-sm-4 control-label"> @lang('general.id')</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="id" name="id"
                                                       value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                            </div>

                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_surname_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="surname" class="col-sm-4 control-label"> @lang('general.surname')</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="surname" name="surname"
                                                       value="@if (isset($_GET['surname'])){{ $_GET['surname'] }}@endif">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_phone_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="phone" class="col-sm-4 control-label"> @lang('general.phone')</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                    <input type="text" class="form-control" id="phone" name="phone"
                                                           value="@if (isset($_GET['phone'])){{ $_GET['phone'] }}@endif">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_country_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="country" class="col-sm-4 control-label"> @lang('general.country')</label>
                                            <div class="col-sm-8">
                                                <select id="country" name="country[]" style="width: 100%" multiple>
                                                    @foreach ($country as $covalue)
                                                        <option
                                                                @if (isset($_GET['country']))
                                                                <? $countryGet = explode(',', $_GET['country']); ?>
                                                                @foreach ($countryGet as $cg)
                                                                @if ($covalue->code == $cg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{$covalue->code }}">
                                                            @lang('countries.' . $covalue->code)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_proc_status_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="status" class="col-sm-4 control-label"> @lang('general.processing-status')</label>
                                            <div class="col-sm-8">
                                                <select id="status" name="status[]" style="width: 100%" multiple>
                                                    @if ($statuses)
                                                        @foreach ($statuses as $key => $status)
                                                            @if (!$status->parent_id)
                                                                <option
                                                                        @if (isset($_GET['status']))
                                                                        <? $statusGet = explode(',', $_GET['status']); ?>
                                                                        @foreach ($statusGet as $stg)
                                                                        @if ($key == $stg)
                                                                        selected
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                        value="{{ $key }}">{{ !empty($status->key) ? trans('statuses.' . $status->key) : $status->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        @php
                                            $sort = [
                                            'id' => trans('general.id'),
                                            'oid' => trans('general.oid'),
                                            'geo' => trans('general.country'),
                                            'time_created' => trans('general.date-created'),
                                            'time_modified' => trans('general.date-set-target'),
                                            'project_id' => trans('general.project'),
                                            'subproject_id' => trans('general.subproject'),
                                            'price_total' => trans('general.sum'),
                                            ];
                                        @endphp
                                        <div class="form-group col-md-3 form-horizontal">
                                            <label for="order_cell" class="col-sm-4 control-label"> @lang('general.sort')</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="order_cell" id="order_cell">
                                                    <option value=""></option>
                                                    @foreach($sort as $key => $value)
                                                        <option value="{{$key}}"
                                                                @if(isset($_GET['order_cell']) && $_GET['order_cell'] == $key) selected @endif>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 form-horizontal">
                                            <label for="order_sort" class="col-sm-4 control-label"> @lang('general.sort-by')</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="order_sort" id="order_sort">
                                                    <option value=""></option>
                                                    <option value="asc"
                                                            @if(isset($_GET['order_sort']) && $_GET['order_sort'] == 'asc') selected @endif>
                                                        @lang('general.ascending')
                                                    </option>
                                                    <option value="desc"
                                                            @if(isset($_GET['order_sort']) && $_GET['order_sort'] == 'desc') selected @endif>
                                                        @lang('general.descending')
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @if (isset($permissions['filter_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="project" class="col-sm-4 control-label"> @lang('general.project')</label>
                                            <div class="col-sm-8">
                                                <input id="project"
                                                       data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                       class="project " name="project[]"
                                                       value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="sub_project" class="col-sm-4 control-label"> @lang('general.subproject')</label>
                                            <div class="col-sm-8">
                                                <input id="sub_project"
                                                       data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                                       class="sub_project " name="sub_project[]"
                                                       value="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                        @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="processing_count" class="col-sm-4 control-label"> @lang('general.processing-count')</label>
                                            <div class="col-sm-8">
                                                <input id="processing_count" type="text"
                                                       class="processing_count form-control" name="processing_count"
                                                       value="{{Request::get('processing_count') == "0" ? "no_processed" : Request::get('processing_count')}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if (isset($permissions['filter_date_orders']) && Request::route()->getName() != 'collectings-processing')
                            <div class="item_rows">
                                <div class="main-box-body clearfix">
                                    <div class='main-box-body clearfix section_filter'>
                                        <div class='main-box-body clearfix'>
                                        </div>
                                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="date_start">  @lang('general.from')</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                    <input class="form-control" id="date_start" type="text"
                                                           data-toggle="tooltip" name="date_start"
                                                           data-placement="bottom"
                                                           value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="date_end"> @lang('general.to')</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                    <input class="form-control" id="date_end" type="text"
                                                           data-toggle="tooltip" name="date_end"
                                                           data-placement="bottom"
                                                           value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="btn-group date_type" data-toggle="buttons">
                                                <div>  @lang('general.type')</div>
                                                <label class="btn btn-primary @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1) || !isset($_GET['date-type'])) active @endif"
                                                       id="time_created" data-toggle="tooltip"
                                                       data-placement="bottom" title=" @lang('general.date-created')">
                                                    <input type="radio" name="date-type" value="1"
                                                           @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1) || !isset($_GET['date-type'])) checked @endif>
                                                    <i class="fa fa-calendar"></i>
                                                </label>
                                                <label class="btn btn-primary @if (isset($_GET['date-type']) && $_GET['date-type'] == 3) active @endif"
                                                       id="time_modified" data-toggle="tooltip"
                                                       data-placement="bottom" title=" @lang('general.date-set-target')">
                                                    <input type="radio" name="date-type" value="3"
                                                           @if (isset($_GET['date-type']) && $_GET['date-type'] == 3) checked @endif><i
                                                            class="fa fa-star-half-empty"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                                            <div class="input-group">
                                                <div class="btn-group" data-toggle="buttons" id="date_template">
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="1"> @lang('general.today')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="5"> @lang('general.yesterday')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="9"> @lang('general.week')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="10"> @lang('general.month')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="2"> @lang('general.last-month')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                        <a href="{{Request::url() }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-box clearfix">
        <div class="tabs-wrapper">
            <ul class="nav nav-tabs">
                @if (isset($permissions['page_collectings']))
                <li class="{{Route::currentRouteName() == 'collectings' ? 'active' : ''}}">
                    <a href="{{route('collectings')}}"> @lang('general.all-orders')

                        @if (Route::currentRouteName() == 'collectings')
                            (<span class="badge badge-danger">{{$countOrder}}</span>)
                        @elseif (!empty($amount['all']))
                            (<span class="badge badge-danger">{{$amount['all']}}</span>)
                        @endif
                    </a>
                </li>
                @endif
                @if (isset($permissions['page_collectings_hand_processing']))
                <li class="{{Route::currentRouteName() == 'collectings-hand-processing' ? 'active' : ''}}">
                    <a href="{{route('collectings-hand-processing')}}"> @lang('collectings.manual-processing')
                        @if (Route::currentRouteName() == 'collectings-hand-processing')
                            (<span class="badge badge-danger">{{$countOrder}}</span>)
                        @elseif (!empty($amount['hand']))
                            (<span class="badge badge-danger">{{$amount['hand']}}</span>)
                        @endif
                    </a>
                </li>
                @endif
                @if (isset($permissions['page_collectings_auto_processing']))
                <li class="{{Route::currentRouteName() == 'collectings-auto-processing' ? 'active' : ''}}">
                    <a href="{{route('collectings-auto-processing')}}"> @lang('general.auto-call')
                        @if (Route::currentRouteName() == 'collectings-auto-processing')
                            (<span class="badge badge-danger">{{$countOrder}}</span>)
                        @elseif (!empty($amount['auto']))
                            (<span class="badge badge-danger">{{$amount['auto']}}</span>)
                        @endif
                    </a>
                </li>
                @endif
                    @if (isset($permissions['page_collectings_processing']))
                        <li class="{{Route::currentRouteName() == 'collectings-processing' ? 'active' : ''}}">
                            <a href="{{route('collectings-processing')}}"> @lang('general.processed') (@lang('general.today'))

                                @if (Route::currentRouteName() == 'collectings-processing')
                                    (<span class="badge badge-danger">{{$countOrder}}</span>)
                                @elseif (!empty($amount['processed']))
                                    (<span class="badge badge-danger"> {{$amount['processed']}}</span>)
                                @endif
                            </a>
                        </li>
                    @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="orders">
                    @yield('contentdata')
                </div>
            </div>
        </div>
    </div>

@stop
