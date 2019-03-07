@extends('layouts.app')

@section('title')Права доступа@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>

@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.pwstrength.js') }}"></script>
    <script src="{{ URL::asset('js/roles/index.js') }}"></script>
    <script>
        $('.permissions-list').slimScroll({
            height: '100px',
            wheelStep: 35,
        });

    </script>
@stop
@section('content')
    <div class="md-modal md-effect-2" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Добавление роли</h4>
            </div>
            <form method="post" class="form-horizontal" id="create-role">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="name">Название</label>
                        <div class="col-md-9">
                            <input required name="name" id="name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="project_id" class="col-md-3 control-label">Проект</label>
                        <div class="col-md-9">
                            <select required {{auth()->user()->project_id ? 'disabled' : NULL}} class="form-control"
                                    id="project_id" name="project_id">
                                <option value="">Выберите проект</option>
                                @if($projects->count())
                                    @if(auth()->user()->project_id)
                                        <option selected value="{{auth()->user()->project_id}}"
                                                disabled>{{auth()->user()->project->name}}</option>
                                    @else
                                        @foreach($projects as $project)
                                            <option value="{{$project->id}}">{{$project->name}}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                        </div>
                    </div>
                    @if(isset($permissions['delegate_permissions']))
                        <div class="form-group">
                            <label class="col-md-3 control-label">Делегировать полномочия</label>
                            <div class="col-md-9  permissions-list">
                                @foreach(auth()->user()->role->permissions as $permission)
                                    <div class="checkbox-nice checkbox">
                                        {{ Form::checkbox('delegated_permissions[]', $permission->id, false,
                                         ['id' => $permission->name]) }}
                                        {{ Form::label($permission->name, $permission->alias) }}
                                    </div>
                                @endforeach
                            </div>
                            <div class="slimScrollBar"></div>
                        </div>
                    @endif
                    <div class="error-messages">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary create-role-form">Добавить</button>
                </div>
            </form>
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#">Главная</a></li>
                <li class="active"><span>Роли</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Роли</h1>
                @if (isset($permissions['add_new_role_ajax']))
                    <div class="pull-right top-page-ui">
                        <button data-modal="form_block"
                                class=" md-trigger btn btn-primary pull-right mrg-b-lg create-role">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить роль
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
                        <li class="active">
                            <a href="{{route('roles')}}">Роли</a>
                        </li>
                        @if (isset($permissions['page_roles_and_permissions']))
                            <li>
                                <a href="{{route('role-and-permission')}}">Права доступа</a>
                            </li>
                        @endif
                        @if (isset($permissions['page_ranks']))
                            <li>
                                <a href="{{route('users-ranks')}}">Ранги</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table table-striped table_roles">
                                        <thead>
                                        <tr>
                                            <th>Роль</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @include('roles.roles-table')
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