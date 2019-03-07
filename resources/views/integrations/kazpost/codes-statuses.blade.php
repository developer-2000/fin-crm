@extends('layouts.app')
@section('title') @lang('integrations.edit')  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel=" stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        body {
            color: grey;
        }
        .md-show {
            height: 100%; overflow-y:auto;
        }
    </style>
@stop
@section('content')
    <div class="md-modal md-effect-2" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title"> @lang('integrations.add-key')</h4>
            </div>
            <form method="post" class="form-horizontal" id="add_senders">
                <div class="modal-body">
                    @if (!auth()->user()->sub_project_id)
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="project_id"> @lang('general.project')</label>
                            <div class="col-md-9">
                                <input name="project_id" id="project_id" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="sub_project_id"> @lang('general.subproject')</label>
                            <div class="col-md-9">
                                <input name="sub_project_id" id="sub_project_id" >
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="sub_project_id" value="{{auth()->user()->sub_project_id}}">
                    @endif
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="name_last">name_last</label>{{--todo rename--}}
                        <div class="col-md-9">
                            <input required name="name_last" id="name_last" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="name_fm">name_fm</label>
                        <div class="col-md-9">
                            <input required name="name_fm" id="name_fm" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="city"> @lang('general.city')</label>
                        <div class="col-md-9">
                            <input required name="city" id="city" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="address"> @lang('general.address')</label>
                        <div class="col-md-9">
                            <input required name="address" id="address" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="index"> @lang('general.index')</label>
                        <div class="col-md-9">
                            <input required name="index" id="index" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="code"> @lang('general.code')</label>
                        <div class="col-md-9">
                            <input required name="code" id="code" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="doc">doc</label>
                        <div class="col-md-9">
                            <input required name="doc" id="doc" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="doc_num">doc_num</label>
                        <div class="col-md-9">
                            <input required name="doc_num" id="doc_num" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="doc_day">doc_day</label>
                        <div class="col-md-9">
                            <input required name="doc_day" id="doc_day" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="doc_month">doc_month</label>
                        <div class="col-md-9">
                            <input required name="doc_month" id="doc_month" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="doc_year">doc_year</label>
                        <div class="col-md-9">
                            <input required name="doc_year" id="doc_year" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="doc_body">doc_body</label>
                        <div class="col-md-9">
                            <input required name="doc_body" id="doc_body" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label required" for="payment_code">payment_code</label>
                        <div class="col-md-9">
                            <input required name="payment_code" id="payment_code" class="form-control">
                        </div>
                    </div>
                    <div class="error-messages">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"> @lang('general.add')</button>
                </div>
            </form>
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><a href="{{route('integrations')}}"><span> @lang('integrations.all')</span></a></li>
                <li class="active"><span> @lang('integrations.edit')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.edit')</h1>
                @if (isset($permissions['integrations_keys_create']))
                    <div class="pull-right top-page-ui">
                        <button data-modal="form_block"
                                class=" md-trigger btn btn-primary pull-right mrg-b-lg create-key">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('integrations.add-sender')
                        </button>
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
                        <li class="">
                            <a href="{{route('integrations-edit', Request::segment(2))}}"> @lang('integrations.senders')</a>
                        </li>
                        <li class="active">
                            <a href="{{route('integration-codes-statuses',  Request::segment(2))}}"> @lang('integrations.code-status')</a>
                        </li>
                    </ul>
                    @include('integrations.codes-statuses', ['codesStatuses', $codesStatuses])
                </div>
            </div>
        </div>
    </div>

@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/integrations/kazpost.js') }}"></script>
@stop
