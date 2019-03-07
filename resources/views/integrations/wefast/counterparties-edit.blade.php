@extends('layouts.app')
@section('title')Редактирование контрагента  @stop
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
    </style>
@stop
@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li><a href="{{route('index')}}">Главная</a></li>
            <li><a href="{{route('integrations')}}">Все интеграции</a></li>
            <li><a href="{{route('integrations-edit', 'wefast')}}">WeFast</a></li>
            <li><a href="{{route('wefast-counterparties')}}">Контрагенты</a></li>
            <li class="active"><span>Редактирование</span></li>
        </ol>
        <div class="clearfix">
            <h1 class="pull-left">{{$counterparty->sender}}</h1>
        </div>
    </div>
</div>
<div class="order_container">
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-body container_desc">
                    <form class="col-xs-12 form-horizontal" method="post" id="edit_counterparty">
                        <input type="hidden" name="id" value="{{$counterparty->id}}">
                        @if (!auth()->user()->sub_project_id)
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="project_id">Проект</label>
                                <div class="col-md-9">
                                    <input name="project_id" id="project_id"
                                           data-content="{{$counterparty->subProject && $counterparty->subProject->parent ? json_encode(['id' => $counterparty->subProject->parent->id, 'text' => $counterparty->subProject->parent->name]) : ''}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label required" for="sub_project_id">Под проект</label>
                                <div class="col-md-9">
                                    <input name="sub_project_id" id="sub_project_id"
                                           data-content="{{$counterparty->subProject ? json_encode(['id' => $counterparty->subProject->id, 'text' => $counterparty->subProject->name]) : ''}}">
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="sub_project_id"
                                   data-content="{{$counterparty->subProject ? json_encode(['id' => $counterparty->subProject->id, 'text' => $counterparty->subProject->name]) : ''}}"
                                   value="{{$counterparty->sub_project_id}}">
                        @endif
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="key">Ключ</label>
                            <div class="col-md-9">
                                <input name="key" id="key"
                                       data-content="{{$counterparty->key ? json_encode(['id' => $counterparty->key->id, 'text' => $counterparty->key->name]) : ''}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="sender">Отправитель</label>
                            <div class="col-md-9">
                                <input required name="sender" id="sender" class="form-control" value="{{$counterparty->sender}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="contact">Контакты</label>
                            <div class="col-md-9">
                                <input required name="contact" id="contact" class="form-control" value="{{$counterparty->contact}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="phone">Телефон</label>
                            <div class="col-md-9">
                                <input required name="phone" id="phone" class="form-control" value="{{$counterparty->phone}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="address">Адрес</label>
                            <div class="col-md-9">
                                <input required name="address" id="address" class="form-control" value="{{$counterparty->address}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="warehouse">Отделение</label>
                            <div class="col-md-9">
                                <input required name="warehouse" id="warehouse" class="form-control" value="{{$counterparty->warehouse}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="province" class="col-lg-3 control-label required">Province</label>
                            <div class="col-lg-8">
                                <input id="province"
                                       name="province"
                                       type="text"
                                       value="{{$counterparty->province_code}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="district" class="col-lg-3 control-label required">District</label>
                            <div class="col-lg-8">
                                <input id="district"
                                       name="district"
                                       type="text"
                                       value="{{$counterparty->district_code}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ward" class="col-lg-3 control-label required">Ward</label>
                            <div class="col-lg-8">
                                <input id="ward"
                                       name="ward"
                                       type="text"
                                       value="{{$counterparty->ward_code}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-nice col-md-offset-3">
                                <input type="checkbox" id="active" name="active" @if ($counterparty->active) checked="checked" @endif>
                                <label for="active">
                                    Вкл/выкл
                                </label>
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
    <script src="{{ URL::asset('js/integrations/wefast.js') }}"></script>
@stop