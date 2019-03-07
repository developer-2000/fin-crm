@extends('layouts.app')

@section('title')Статусы@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/procStatus/procStatus.js') }}"></script>
@stop

@section('content')
    <div class="md-modal md-effect-2" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Добавление статуса</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal">
                    @if (isset($permissions['add_project_into_proc_status']))
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="project">Проект</label>
                            <div class="col-md-9">
                                <select name="project" id="project" class="form-control">
                                    <option value="0">Для всех</option>
                                    @if ($projects)
                                        @foreach($projects as $project)
                                            <option value="{{$project->id}}">{{$project->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name">Название</label>
                        <div class="col-md-9">
                            <input name="name" id="name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="action">Закрепить действие</label>
                        <div class="col-md-9">
                            <select name="action" id="action" class="form-control">
                                <option value="">Выбрать действие</option>
                                <option value="paid_up">Выкуп</option>
                                <option value="refused">Не выкуп</option>
                                <option value="rejected">Отклонен</option>
                                <option value="sent">Отправлен</option>
                                <option value="at_the_warehouse">На отделении</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="color">Цвет</label>
                        <div class="col-md-9">
                            <input type="color" name="color" id="color" class="form-control">
                        </div>
                    </div>
                    <div id="sub-statuses" style="padding-top: 10px"></div>
                    <div class="form-group text-center">
                        <button class="btn btn-success" id="add_sub_status">Добавить подстатус</button>
                    </div>
                </form>
                <div class="error-messages">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="create_status">Добавить</button>
            </div>
        </div>
    </div>

    @if (isset($permissions['rewrite_statuses_ajax']))
        <div class="md-modal md-effect-2" id="rewrite_status">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">Переопределение статусов</h4>
                </div>
                <div class="modal-body">
                    <form role="form" class="form-horizontal" id="rewrite_form">

                    </form>
                    <div class="error-messages">
                    </div>
                    <div class="text-center">
                        <span class="fa fa-spinner fa-2x alert_spinner" id="spinner"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="rewrite_btn">Переопределить</button>
                </div>
            </div>
        </div>
    @endif
    <div class="md-overlay"></div>

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Статусы</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Статусы</h1>
                @if (isset($permissions['add_new_partner_ajax']))
                    <div class="pull-right top-page-ui">
                        <button data-modal="form_block" class=" md-trigger btn btn-primary pull-right mrg-b-lg">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить
                        </button>
                    </div>
                @endif
                @if (isset($permissions['rewrite_statuses_ajax']))
                    <div class="pull-right top-page-ui" style="margin-right: 10px;">
                        <button data-modal="rewrite_status" class=" md-trigger btn btn-primary pull-right mrg-b-lg">
                            Переопределение статусов
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box no-header clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table table-striped" id="statuses">
                            <thead>
                            <tr>
                                <th class="text-center">Проект</th>
                                <th class="text-center">Статус</th>
                                <th class="text-center">Тип</th>
                                <th class="text-center">Цвет</th>
                                <th class="text-center">Подстатусы</th>
                                <th class="text-center">Действие</th>
                                <th class="text-center"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @include('procStatus.table')
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pull-right">
                    {{$statuses->links()}}
                </div>
            </div>
        </div>
    </div>
    <div class="hidden">
        <div class="form-group sub-status" style="display: none;">
            <label class="col-md-3 control-label">Название подстатуса</label>
            <div class="col-md-8">
                <input name="sub-status[]" class="form-control">
            </div>
            <div class="col-md-1">
                <label class="btn btn-danger delete_sub_status"><i class="fa fa-times"></i></label>
            </div>
        </div>
    </div>
@stop