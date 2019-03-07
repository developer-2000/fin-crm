@extends('layouts.app')
@section('title')Редактирование интеграции  @stop
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
                <li><a href="{{route('integrations')}}"><span>Все интеграции</span></a></li>
                <li class="active"><span>Редактировать интеграцию</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Редактирование</h1>
                @if (isset($permissions['integrations_keys_create']))
                    <div class="pull-right top-page-ui">
                        <button data-modal="form_block"
                                class=" md-trigger btn btn-primary pull-right mrg-b-lg create-key">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить отправителя
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="md-modal md-effect-2" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Добавление ключа</h4>
            </div>
            <form method="post" class="form-horizontal" id="add_senders">
                <div class="modal-body">
                    @if (!auth()->user()->sub_project_id)
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="project_id">Проект</label>
                            <div class="col-md-9">
                                <input name="project_id" id="project_id">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required" for="sub_project_id">Под проект</label>
                            <div class="col-md-9">
                                <input name="sub_project_id" id="sub_project_id">
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="sub_project_id" value="{{auth()->user()->sub_project_id}}">
                    @endif
                    <div class="form-group">
                        <label class="col-md-3 control-label required"
                               for="name">Название</label>
                        <div class="col-md-9">
                            <input required name="name" id="name" class="form-control">
                        </div>
                    </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required"
                                   for="extra">Extra</label>
                            <div class="col-md-9">
                                <input required name="extra" id="extra" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required"
                                   for="login">Login</label>
                            <div class="col-md-9">
                                <input required name="login" id="login" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label required"
                                   for="password">Password</label>
                            <div class="col-md-9">
                                <input required name="password" id="password" class="form-control">
                            </div>
                        </div>

                    <div class="error-messages">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{route('integrations-edit',  $integration->alias)}}">Отправители</a>
                        </li>
                        <li class="">
                            <a href="{{route('integration-codes-statuses',  $integration->alias)}}">Коды/Статусы</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        <div class="table-responsive">
                                                <table class="table table-hover table-striped integrations_table" id="block_senders">
                                                    <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Под проект</th>
                                                        <th>Название</th>
                                                        <th>Extra</th>
                                                        <th>Login</th>
                                                        <th>Password</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @include('integrations.measoft.edit-table')
                                                    </tbody>
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
    <script src="{{ URL::asset('js/integrations/measoft.js') }}"></script>
@stop


