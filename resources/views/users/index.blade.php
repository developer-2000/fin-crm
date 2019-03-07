@extends('layouts.app')

@section('title')Все пользователи@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/control_user.css') }}"/>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script>
        $(function () {
            $('#company, #role').select2({
                placeholder: 'Все',
                allowClear: true,
            });
        })
    </script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#">Главная</a></li>
                <li class="active"><span>Все пользователи</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Все пользователи(<span class="badge">{{$count}}</span>)</h1>
                @if (isset($permissions['add_chenge_users']))
                    <div class="pull-right top-page-ui">
                        <a href="{{ route("users-registration") }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить пользователя
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{  Request::path() }}" method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                        <label for="id" class="col-sm-4 control-label">ID</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="id" name="id"
                                                   value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                        <label for="login" class="col-sm-4 control-label">Login</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="login" name="login"
                                                   value="@if (isset($_GET['login'])){{ $_GET['login'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                        <label for="surname" class="col-sm-4 control-label">Фамилия</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="surname" name="surname"
                                                   value="@if (isset($_GET['surname'])){{ $_GET['surname'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                        <label for="name" class="col-sm-4 control-label">Имя</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="name" name="name"
                                                   value="@if (isset($_GET['name'])){{ $_GET['name'] }}@endif">
                                        </div>
                                    </div>
                                    @if (isset($permissions['filter_companies_page_users']))
                                        <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                            <label for="company" class="col-sm-4 control-label">Компания</label>
                                            <div class="col-sm-8">
                                                <select id="company" name="company[]" style="width: 100%" multiple>
                                                    @if($companies->count())
                                                        @foreach ($companies as $company)
                                                            <option
                                                                    @if (isset($_GET['company']))
                                                                    <? $companyGet = explode(',', $_GET['company']); ?>
                                                                    @foreach ($companyGet as $cg)
                                                                    @if ($company->id == $cg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $company->id }}">{{ $company->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group col-md-4 col-sm-6 form-horizontal">
                                        <label for="role" class="col-sm-4 control-label">Роль</label>
                                        <div class="col-sm-8">
                                            <select id="role" name="role[]" style="width: 100%" multiple>
                                                @foreach ($roles as $role)
                                                    <option
                                                            @if (isset($_GET['role']))
                                                            <? $roleGet = explode(',', $_GET['role']); ?>
                                                            @foreach ($roleGet as $cg)
                                                            @if ($role->id == $cg)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="{{ $role->id }}">{{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-6 ">
                                        <div class="form-group form-horizontal">
                                            <label for="status" class="col-sm-4 control-label">Статус</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="status" name="status">
                                                    <option value="0">Активный</option>
                                                    <option @if (isset($_GET['status']) && $_GET['status'] == 1) selected
                                                            @endif value="1">Заблокирован
                                                    </option>
                                                    <option @if (isset($_GET['status']) && $_GET['status'] == 2) selected
                                                            @endif value="2">Все
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='Фильтровать'/>
                        <a href="{{ route('users') }}" class="btn btn-warning" type="submit">Сбросить фильтр</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box no-header clearfix" style="padding-top: 0;padding-bottom: 20px;">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{route('users')}}">Все пользователи</a>
                        </li>
                        @if (isset($permissions['page_roles']))
                            <li>
                                <a href="{{route('roles')}}">Роли</a>
                            </li>
                        @endif
                        @if (isset($permissions['page_roles_and_permissions']))
                            <li>
                                <a href="{{route('role-and-permission')}}">Права</a>
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
                                    <table class="table user-list table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th><span>ID</span></th>
                                            <th><span>Пользователь</span></th>
                                            <th><span>Логин</span></th>
                                            <th><span>Компания</span></th>
                                            <th><span>Проект<br>Под проект</span></th>
                                            <th class="text-center"><span>SIP</span></th>
                                            <th>Дата регистрации</th>
                                            <th class="text-center"><span>Статус</span></th>
                                            @if (isset($permissions['add_chenge_users']))
                                                <th>&nbsp;</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if ($allUsers)
                                            @foreach($allUsers as $user)
                                                <tr>
                                                    <td>
                                                        {{$user->id}}
                                                    </td>
                                                    <td>
                                                        <img src="{{ $user->photo}}" alt=""/>
                                                        <a href="{{route('user', $user->id)}}"
                                                           class="user-link">{{$user->name}} {{$user->surname}}</a>
                                                        <span class="user-subhead">
                                                            @if(isset($user->roles))
                                                                @foreach($user->roles as $userRole)
                                                                    {{$userRole->name}} |
                                                                @endforeach
                                                            @endif
                                                            @if($user->rank)
                                                                ({{$user->rank->name}})
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{$user->login}}
                                                    </td>
                                                    <td>
                                                        {{$user->company->name ?? ''}}
                                                    </td>
                                                    <td>
                                                        {{isset($user->project->name) ? $user->project->name .'::' : ''}}
                                                        {{$user->subproject->name ?? ''}}
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($user->elastix_role)
                                                            <i class="fa  fa-check" style="color: #1ABC9C"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="label
                                                            @if($user->ban) label-danger
                                                            @else label-success
                                                            @endif">
                                                            @if($user->ban)
                                                                Заблокирован
                                                            @else
                                                                Активный
                                                            @endif
                                                        </span>
                                                    </td>
                                                    @if (isset($permissions['add_chenge_users']))
                                                        <td>
                                                            <a href="{{ route("users-edit", $user->id) }}" class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                            </a>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pull-right">
                    {{$allUsers->links()}}
                </div>
            </div>
        </div>
    </div>
@stop