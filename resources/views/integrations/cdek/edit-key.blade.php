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
            height: 100%;
            overflow-y: auto;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span></span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('integrations.edit')</h1>
                {{--@if (isset($permissions['integrations_keys_create']))--}}
                {{--<div class="pull-right top-page-ui">--}}
                {{--<button data-modal="form_block"--}}
                {{--class=" md-trigger btn btn-primary pull-right mrg-b-lg create-key">--}}
                {{--<i class="fa fa-plus-circle fa-lg"></i> Добавить отправителя--}}
                {{--</button>--}}
                {{--</div>--}}
                {{--@endif--}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#block_senders"> @lang('integrations.senders')</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="block_senders">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped integrations_table">
                                                <thead>
                                                <tr>
                                                    <th> @lang('integrations.costumer-id')</th>
                                                    <th> @lang('integrations.warehouse-id')</th>
                                                    <th> @lang('general.name')</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                @if($senders->count())
                                                    <tbody>
                                                    @foreach($senders as $sender)
                                                        <tr>
                                                            <td>
                                                                {{$sender->customer_id ?? ''}}
                                                            </td>
                                                            <td>
                                                                {{$sender->warehouse_id ?? ''}}
                                                            </td>
                                                            <td>
                                                                {{$sender->name ?? ''}}
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
@stop
