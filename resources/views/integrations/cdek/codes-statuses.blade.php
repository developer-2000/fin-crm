@extends('layouts.app')
@section('title') @lang('integrations.edit')  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel=" stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <style>
        body {
            color: grey;
        }

        .ns-box {
            z-index: 5000
        }
    </style>
@stop
@section('content')
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
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
                        {{--<button data-modal="form_block"--}}
                                {{--class=" md-trigger btn btn-primary pull-right mrg-b-lg account_create">--}}
                            {{--<i class="fa fa-plus-circle fa-lg"></i>Создать учетку--}}
                        {{--</button>--}}
                        <br>
                        <button data-modal="sender_add"
                                class=" md-trigger btn btn-primary pull-right mrg-b-lg sender_add">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('integrations.add-sender')
                        </button>
                        <br>
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
                            <a href="{{route('integrations-edit' , Request::segment(2))}}"> @lang('integrations.all-keys')</a>
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
    <div class="md-overlay"></div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.datetimepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/integrations/viettel/index.js') }}"></script>

@stop
