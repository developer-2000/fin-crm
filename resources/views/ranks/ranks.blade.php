@extends('layouts.app')

@section('title')Ранги@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/ranks/ranks.js') }}"></script>
@stop

@section('content')
    <div class="md-modal md-effect-2" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Добавление ранга</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name">Название</label>
                        <div class="col-md-9">
                            <input name="name" id="name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="role">Роль</label>
                        <div class="col-md-9">
                            <select id="role" class="form-control" name="role">
                                <option value="">Выберите роль</option>
                                @if ($roles)
                                    @foreach($roles AS $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </form>
                <div class="error-messages">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary create_rank">Добавить</button>
            </div>
        </div>
    </div>

    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#">Главная</a></li>
                <li class="active"><span>Ранги</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Ранги</h1>
                @if (isset($permissions['add_new_rank_ajax']))
                    <div class="pull-right top-page-ui">
                        <button data-modal="form_block" class=" md-trigger btn btn-primary pull-right mrg-b-lg">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить ранг
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box no-header clearfix" style="padding-top: 0;padding-bottom: 20px;">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        @if (isset($permissions['page_users']))
                            <li>
                                <a href="{{route('users')}}">Все пользователи</a>
                            </li>
                        @endif
                        @if (isset($permissions['page_roles']))
                            <li>
                                <a href="{{route('roles')}}">Роли</a>
                            </li>
                        @endif
                        @if (isset($permissions['page_roles_and_permissions']))
                            <li>
                                <a href="{{route('role-and-permission')}}">Права доступа</a>
                            </li>
                        @endif
                        <li class="active">
                            <a href="{{route('users-ranks')}}">Ранги</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table table-striped table_ranks">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Название</th>
                                            <th>Роль</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @include('ranks.ranks-table')
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
@stop