@extends('layouts.app')

@section('title')Пользователь - {{ $userOne->login }}@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        .form-horizontal .control-label {
            text-align: left;
        }

        #pwindicator {
            width: 100%;
        }

        .pwdindicator.pw-very-weak .bar {
            width: 20%;
        }

        .pwdindicator.pw-weak .bar {
            width: 40%;
        }

        .pwdindicator.pw-mediocre .bar {
            width: 60%;
        }

        .pwdindicator.pw-strong .bar {
            width: 80%;
        }

        .pwdindicator.pw-very-strong .bar {
            width: 100%;
        }
    </style>
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.pwstrength.js') }}"></script>
    <script src="{{ URL::asset('js/users/user_one.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('users')}}">Все пользователи</a></li>
                <li class="active"><a href="{{route('user', $userOne->id )}}"><span>Редактирование</span></a></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">{{$userOne->surname}} {{$userOne->name}}</h1>
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="hidden" id="current_user_id" data-id="{{$userOne->id}}"></div>
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box no-header clearfix" style="padding-top: 0;padding-bottom: 20px;">
                    <div class="tabs-wrapper profile-tabs">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a>Настройки пользователя</a>
                            </li>
                            @can('user_permission')
                                <li>
                                    <a href="{{route('user-edit-permissions', $userOne->id)}}">Права доступа</a>
                                </li>
                            @endcan
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade in active">
                                <div class="main-box-body clearfix">
                                    <div class="col-lg-6">
                                                <form class="col-xs-12 form-horizontal" enctype="multipart/form-data" method="post"
                                                      id="data_crm">
                                                    {{--<div class="form-group">--}}
                                                    {{--<label for="role" class="col-sm-4 control-label">Роль</label>--}}
                                                    {{--<div class="col-sm-8">--}}
                                                    {{--<select class="form-control" id="role" name="role">--}}
                                                    {{--@if($roles)--}}
                                                    {{--@foreach($roles as $id => $role)--}}
                                                    {{--@if ($id == 17) @continue @endif--}}
                                                    {{--@if ((auth()->user()->project_id && auth()->user()->project_id == $role->project_id) || !auth()->user()->project_id)--}}
                                                    {{--<option--}}
                                                    {{--@if($userOne->role_id == $id)--}}
                                                    {{--selected--}}
                                                    {{--@endif--}}
                                                    {{--value="{{$id}}" data-id="{{$role->company}}"  data-project="{{$role->project_id}}">{{$role->name}}</option>--}}
                                                    {{--@endif--}}
                                                    {{--@endforeach--}}
                                                    {{--@endif--}}
                                                    {{--</select>--}}
                                                    <div class="form-group">
                                                        <label class="col-lg-4" for="roles"> Роли</label>
                                                        <div class="col-sm-8">
                                                            <input type="hidden" name="roles" id="roles"
                                                                   class="roles" data-roles="{{!empty($userRolesJson) ? $userRolesJson : NULL}}"
                                                                   style="width: 100%"/>
                                                        </div>
                                                    </div>
                                                    {{--</div>--}}
                                                    {{--</div>--}}
                                                    <div class="form-group">
                                                        <label for="project_id" class="col-sm-4 control-label">Проект</label>
                                                        <div class="col-sm-8">
                                                            <input id="project_id"
                                                                   name="project_id" value="{{$userOne->project->id ?? 0}}"
                                                                   data-content='{{$userOne->project ? json_encode(['id' => $userOne->project->id, 'text' => $userOne->project->name]) : ''}}'>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sub_project_id" class="col-sm-4 control-label">Под проект</label>
                                                        <div class="col-sm-8">
                                                            <input id="sub_project_id"
                                                                   name="sub_project_id"
                                                                   data-content='{{$userOne->subProject ? json_encode(['id' => $userOne->subProject->id, 'text' => $userOne->subProject->name]) : ''}}'>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="display: @if($userOne->rank_id) block @else none @endif">
                                                        <label for="rank" class="col-sm-4 control-label">Ранг</label>
                                                        <div class="col-sm-8">
                                                            <select name="rank" id="rank" class="form-control">
                                                                <option value=""></option>
                                                                @if ($ranks)
                                                                    @foreach($ranks as $rank)
                                                                        <option value="{{$rank->id}}"
                                                                                @if ($rank->id == $userOne->rank_id)
                                                                                selected
                                                                                @endif
                                                                                data-id="{{$rank->role_id}}"
                                                                                @if ($rank->role_id != $userOne->role_id)
                                                                                style="display: none"
                                                                                @endif
                                                                        >{{$rank->name}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="company" class="col-sm-4 control-label">Компания</label>
                                                        <div class="col-sm-8">
                                                            <select name="company" id="company" class="form-control">
                                                                <option value=""></option>
                                                                @if ($companies)
                                                                    @foreach($companies as $company)
                                                                        <option value="{{$company->id}}"
                                                                                @if ($company->id == $userOne->company_id) selected @endif>{{$company->name}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="login" class="col-sm-4 control-label">Логин(Email)</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="login" id="login" class="form-control"
                                                                   value="{{$userOne->login}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password" class="col-sm-4 control-label">Пароль
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="password"
                                                                   name="password" data-indicator="pwindicator">
                                                            <div id="pwindicator" class="pwdindicator">
                                                                <div class="bar"></div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password" class="col-sm-4 control-label">
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="button" class="btn btn-success" id="generate_pas"
                                                                   value="Сгенерировать пароль" style="margin-top: 10px">
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <br>
                                                    <div class="form-group">
                                                        <label for="surname" class="col-sm-4 control-label">Фамилия</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="surname" id="surname" class="form-control"
                                                                   value="{{$userOne->surname}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-4 control-label">Имя</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="name" id="name" class="form-control"
                                                                   value="{{$userOne->name}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="middle" class="col-sm-4 control-label">Отчество</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="middle" id="middle" class="form-control"
                                                                   value="{{$userOne->middle}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="phone" class="col-sm-4 control-label">Телефон</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" name="phone" id="phone" class="form-control"
                                                                   value="{{$userOne->phone}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="time_zone" class="col-sm-4 control-label">Часовой пояс</label>
                                                        <div class="col-sm-8">
                                                            <select id="time_zone" name="time_zone">
                                                                @foreach(timezone_identifiers_list() as $timezone)
                                                                    <option value="{{$timezone}}"
                                                                            @if ($userOne->time_zone == $timezone) selected @endif>{{$timezone}} {{\Carbon\Carbon::now($timezone)->format('P')}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="birthday" class="col-sm-4 control-label">Дата рождения</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group">
                                        <span class="input-group-addon"><i
                                                    class="fa fa-calendar"></i></span>
                                                                <input type="text" class="form-control" id="birthday" name="birthday"
                                                                       value="{{$userOne->birthday}}">
                                                            </div>
                                                            <span class="help-block">дд/мм/гггг</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <label class="input-group-btn">
                                    <span class="btn btn-success" style="line-height: 22px;">
                                        <span class="fa fa-photo"></span>
                                        Добавить фото
                                        <input type="file" style="display: none;">
                                    </span>
                                                            </label>
                                                            <input type="text" class="form-control" id="file" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-nice" style="display: inline-block;margin-left: 5px;">
                                                            <input class="include" type="checkbox" @if ($userOne->ban == 1) checked="checked"
                                                                   @endif name="block" id="block"/>
                                                            <label for="block">
                                                                Заблокировать
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group text-center">
                                                        <input type="submit" class="btn btn-success" value="Сохранить">
                                                    </div>
                                                </form>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="main-box clearfix" style="margin-bottom: 0;">
                                            @if (!$userOne->elastix_role && isset($permissions['add_chenge_sip_users']))
                                                <a href="#" class="btn btn-success col-sm-12" id="create_sip">Создать аккаунт SIP </a>
                                            @endif
                                        </div>
                                        <div id="block_sip"
                                             style="padding-top: 20px;display: @if (!$userOne->elastix_role) none @else block @endif">
                                            <form class="form form-horizontal" id="data_sip">
                                                <div class="main-box-body clearfix">
                                                    <div class="form-group">
                                                        <label for="login_sip" class="col-sm-4 control-label">Внутренний номер</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_sip_users']))
                                                                <input type="text" name="login_sip" id="login_sip" class="form-control"
                                                                       value="{{$userOne->login_sip}}">
                                                            @else

                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        @if (isset($permissions['add_chenge_sip_users']))
                                                            <label for="password_sip" class="col-sm-4 control-label"
                                                                   id="label_pas_two">Пароль</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control " id="password_sip"
                                                                       name="password_sip" placeholder="Новый пароль" readonly>
                                                                <input type="button" class="btn btn-success" id="generate_pas_two"
                                                                       value="Сгенерировать пароль" style="margin-top: 10px">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                        <label id="ip_label" class="col-sm-4 control-label">IP пользователя</label>
                                                        <div class="col-sm-8">
                                                            @if (isset($permissions['add_chenge_sip_users']))
                                                                @if ($userOne->ips)
                                                                    @foreach($userOne->ips as $ip)
                                                                        <div>
                                                                            <input type="text" class="form-control ip_user" value="{{$ip->ip}}"
                                                                                   style="margin-bottom: 10px;display: inline-block;width: 80%"
                                                                                   ip_id="{{$ip->id}}" placeholder="Ведите новый IP"><label
                                                                                    class="btn btn-primary delete_ip" style="margin-left: 5px">
                                                                                X </label>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                                <input type="button" class="btn btn-success" id="new_input"
                                                                       value="Добавить IP">
                                                            @else
                                                                @if ($userOne->ips)
                                                                    @foreach($userOne->ips as $ip)
                                                                        <div>{{$ip->ip}}</div>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        </div>

                                                    </div>
                                                    <div class="form-group">
                                                        @if (isset($permissions['add_chenge_sip_users']))
                                                            <div class="checkbox checkbox-nice"
                                                                 style="display: inline-block;margin-left: 5px;">
                                                                <input class="include" type="checkbox"
                                                                       @if ($userOne->nat) checked="checked" @endif name="nat"
                                                                       id="nat"/>
                                                                <label for="nat">
                                                                    NAT
                                                                </label>
                                                            </div>
                                                        @else
                                                            NAT
                                                            @if ($userOne->nat)<i class="fa  fa-check" style="color: #1ABC9C"></i> @endif
                                                        @endif
                                                    </div>
                                                    <div class="form-group text-center">
                                                        @if (isset($permissions['add_chenge_sip_users']))
                                                            <input type="submit" class="btn btn-success" value="Сохранить">
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="row">--}}
        {{--<div class="col-lg-6">--}}
        {{--<div class="panel">--}}
        {{--<div class="panel-body container_desc">--}}
        {{--<div class="col-md-12">--}}
        {{--<div class="form-group">--}}
        {{--<h4>Штраф</h4>--}}
        {{--<label for="fine" id="fine_label">Стоимость</label>--}}
        {{--<input class="form-control " type="text" id="fine" name="fine" placeholder="Стоимость">--}}
        {{--<label for="comment">Причина</label>--}}
        {{--<textarea class="form-control " id="comment" name="comment" style="resize: vertical" placeholder="Причина"></textarea>--}}
        {{--<button class='btn btn-success' id="fine_btn" style="margin-top: 10px">Оштрафовать</button>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
    </div>
@stop