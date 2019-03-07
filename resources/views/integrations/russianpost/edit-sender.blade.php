@extends('layouts.app')
@section('title')Редактирование отправителя@stop
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
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('integrations')}}">Все интеграции</a></li>
                <li><a href="{{route('integrations-edit', 'russianpost')}}">{{ $sender->target ? $sender->target->name : '' }}</a></li>
                <li class="active"><span>{{implode(' ', [$sender->name_last, $sender->name_first, $sender->name_middle])}}</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">{{implode(' ', [$sender->name_last, $sender->name_first, $sender->name_middle])}}</h1>
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
                                    <label class="col-md-3 control-label required" for="project_id">Проект</label>
                                    <div class="col-md-9">
                                        <input name="project_id" id="project_id"
                                               data-content="{{$sender->subProject && $sender->subProject->parent ? json_encode(['id' => $sender->subProject->parent->id, 'text' => $sender->subProject->parent->name]) : ''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label required" for="sub_project_id">Под проект</label>
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
                                <label class="col-md-3 control-label required" for="name_last">Фамилия</label>
                                <div class="col-md-9">
                                    <input required name="name_last" id="name_last" class="form-control" value="{{ $sender->name_last }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="name_first">Имя</label>
                                <div class="col-md-9">
                                    <input required name="name_first" id="name_first" class="form-control" value="{{ $sender->name_first }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="name_middle">Отчество</label>
                                <div class="col-md-9">
                                    <input required name="name_middle" id="name_middle" class="form-control" value="{{ $sender->name_middle }}">
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
    <script src="{{ URL::asset('js/integrations/russianpost.js') }}"></script>
@stop