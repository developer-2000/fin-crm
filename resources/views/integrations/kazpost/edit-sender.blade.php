@extends('layouts.app')
@section('title') @lang('integrations.sender-edit') @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel=" stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        body {
            color: grey;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><a href="{{route('integrations')}}"> @lang('integrations.all')</a></li>
                <li><a href="{{route('integrations-edit', 'kazpost')}}">{{ $sender->target ? $sender->target->name : '' }}</a></li>
                <li class="active"><span>{{implode(' ', [$sender->name_last, $sender->name_fm])}}</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">{{implode(' ', [$sender->name_last, $sender->name_fm])}}</h1>
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-6">
                <div class="panel">
                    <div class="panel-body container_desc">
                        <form class="col-xs-12 form-horizontal" method="post" id="edit_counterparty">
                            <input type="hidden" name="id" id="sender_id" value="{{$sender->id}}">
                            @if (!auth()->user()->sub_project_id)
                                <div class="form-group">
                                    <label class="col-md-3 control-label required" for="project_id"> @lang('general.project')</label>
                                    <div class="col-md-9">
                                        <input name="project_id" id="project_id"
                                               data-content="{{$sender->subProject && $sender->subProject->parent ? json_encode(['id' => $sender->subProject->parent->id, 'text' => $sender->subProject->parent->name]) : ''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label required" for="sub_project_id"> @lang('general.subproject')</label>
                                    <div class="col-md-9">
                                        <input name="sub_project_id" id="sub_project_id"
                                               data-content="{{$sender->subProject ? json_encode(['id' => $sender->subProject->id, 'text' => $sender->subProject->name]) : ''}}">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="sub_project_id"
                                       data-content="{{$sender->subProject ? json_encode(['id' => $sender->subProject->id, 'text' => $sender->subProject->name]) : ''}}"
                                       value="{{$sender->sub_project_id}}">
                            @endif
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="name_last"> @lang('general.surname')</label>{{--todo rename--}}
                                <div class="col-md-9">
                                    <input required name="name_last" id="name_last" class="form-control" value="{{ $sender->name_last }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="name_fm">name_fm</label>
                                <div class="col-md-9">
                                    <input required name="name_fm" id="name_fm" class="form-control" value="{{ $sender->name_fm }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="city">Город</label>
                                <div class="col-md-9">
                                    <input required name="city" id="city" class="form-control" value="{{ $sender->city }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="address">Адрес</label>
                                <div class="col-md-9">
                                    <input required name="address" id="address" class="form-control" value="{{ $sender->address }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="index">Индекс</label>
                                <div class="col-md-9">
                                    <input required name="index" id="index" class="form-control" value="{{ $sender->index }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="code">Код</label>
                                <div class="col-md-9">
                                    <input name="code" id="code" class="form-control" value="{{ $sender->code }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="doc">doc</label>
                                <div class="col-md-9">
                                    <input name="doc" id="doc" class="form-control" value="{{ $sender->doc }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="doc_num">doc_num</label>
                                <div class="col-md-9">
                                    <input name="doc_num" id="doc_num" class="form-control" value="{{ $sender->doc_num }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="doc_day">doc_day</label>
                                <div class="col-md-9">
                                    <input name="doc_day" id="doc_day" class="form-control" value="{{ $sender->doc_day }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="doc_month">doc_month</label>
                                <div class="col-md-9">
                                    <input name="doc_month" id="doc_month" class="form-control" value="{{ $sender->doc_month }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="doc_year">doc_year</label>
                                <div class="col-md-9">
                                    <input name="doc_year" id="doc_year" class="form-control" value="{{ $sender->doc_year }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="doc_body">doc_body</label>
                                <div class="col-md-9">
                                    <input name="doc_body" id="doc_body" class="form-control" value="{{ $sender->doc_body }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="payment_code">payment_code</label>
                                <div class="col-md-9">
                                    <input name="payment_code" id="payment_code" class="form-control" value="{{ $sender->payment_code }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="document">document</label>
                                <div class="col-md-9">
                                    <input name="document" id="document" class="form-control" value="{{ $sender->document }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="support_phone">support phone</label>
                                <div class="col-md-9">
                                    <input name="support_phone" id="support_phone" class="form-control" value="{{ $sender->support_phone }}">
                                </div>
                            </div>
                            <div class="error-messages">
                            </div>
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success" value="Сохранить">
                            </div>
                        </form>
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
    <script src="{{ URL::asset('js/integrations/kazpost.js') }}"></script>
@stop
