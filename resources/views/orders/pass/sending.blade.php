@extends('layouts.app')

@section('title')@lang('general.passes') (@lang('general.sending'))@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        #search_block.md-modal {
            width: 80%;
            max-width: 1366px;
        }
        .md-show {
            max-height: 100%;
            overflow-y:auto;
        }
        .md-effect-2{
            font-size: 0.875em;
        }
        .number-total-row {
            color: #676767;
            font-weight: bold
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/orders/pass.js?') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><a href="{{route('pass')}}"> @lang('orders.passes')</a></li>
                <li class="active"><span> @lang('general.sending')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.sending')</h1>
                @if ($pass->active)
                <div class="pull-right">
                    <div class="row">
                        <form method="post" class="form-inline" id="form_track" style="display: inline;">
                            <div class="form-group">
                                <label for="track"> @lang('general.track')</label>
                                <input type="text" class="form-control" id="track" name="track">
                            </div>
                            <div class="form-group">
                                <label for="id"> @lang('general.id')</label>
                                <input type="text" class="form-control" id="id" name="id">
                            </div>
                            <input type="hidden" name="pass_id" id="pass_id" value="{{$pass->id}}">
                            <input type="hidden" name="type_send"  value="1">
                            <input type="submit" class="btn btn-success" value=" @lang('general.add')">
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="md-modal md-effect-2" id="search_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title"> @lang('general.search') <span class="fa fa-spinner fa-2x alert_spinner" id="spinner"></span></h4>
            </div>
            <div class="modal-body">
                <div id="searchResult">
                </div>
            </div>
        </div>
    </div>

    <div class="md-overlay"></div>
    @if ($pass->active)
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url()}}"
                      method="post" id="filter">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="track" class="col-sm-4 control-label"> @lang('general.track')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="track" name="track"
                                                   value="@if (isset($_GET['track'])){{ $_GET['track'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="id" class="col-sm-4 control-label"> @lang('general.id')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="id" name="id"
                                                   value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                        </div>

                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="surname" class="col-sm-4 control-label"> @lang('general.surname')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="surname" name="surname"
                                                   value="@if (isset($_GET['surname'])){{ $_GET['surname'] }}@endif">
                                        </div>
                                    </div>
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
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="index" class="col-sm-4 control-label"> @lang('general.index')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="index" name="index"
                                                   value="@if (isset($_GET['index'])){{ $_GET['index'] }}@endif">
                                        </div>
                                    </div>
                                    <input type="hidden" name="type" value="{{\App\Models\Pass::TYPE_SENDING }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success md-trigger" data-modal="search_block" type="submit" name="button_filter" value='@lang('general.search')'/>
                        <a href="#" class="btn btn-warning" id="reset_form"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <form id="save_pass_sending" method="post" action="{{route('pass-save', $pass->id)}}">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover"  id="pass_table">
                                    <thead>
                                        <tr>
                                            <th class="text-center number-total-row">№</th>
                                            <th> @lang('general.id')</th>
                                            <th></th>
                                            <th class="text-center"> @lang('general.status')</th>
                                            <th class="text-center"> @lang('general.subproject')</th>
                                            <th class="text-center"> @lang('general.phone')</th>
                                            <th class="text-center"> @lang('general.total')</th>
                                            <th class="text-center"> @lang('general.products')</th>
                                            <th class="text-center"> @lang('general.cost')</th>
                                            <th class="text-center"> @lang('general.income')</th>
                                            <th class="text-center"> @lang('general.cost-actual')</th>
                                            <th class="text-center"> @lang('general.track')</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @include('orders.pass.sending-table')
                                    </tbody>
                                </table>
                            </div>
                        @if ($pass->active)
                            <div class="form-group">
                                <label for="comment"> @lang('general.comment')</label>
                                <textarea name="comment" class="form-control" id="comment">{{$pass->comment ?? ''}}</textarea>
                            </div>
                            <input type="hidden" name="pass_id" value="{{$pass->id}}">
                            <div class="text-center">
                                <input type="submit" value=" @lang('general.save')" class="btn btn-success">
                            </div>
                        </form>
                        @else
                            {{$pass->comment ?? ''}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
