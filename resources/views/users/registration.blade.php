@extends('layouts.app')

@section('title')Регистрация пользователя@stop

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
    <script src="{{ URL::asset('js/users/registration.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('users')}}">Все пользователи</a></li>
                <li class="active"><a href="#"><span>Регистрация</span></a></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Регистрация</h1>
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-6">
                <div class="panel">
                    <div class="panel-body container_desc">
                        <form class="col-xs-12 form-horizontal" enctype="multipart/form-data" method="post"
                              id="data_crm">
                            <div class="form-group">
                                <label for="role" class="col-sm-4 control-label">Роль</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="role" name="role">
                                        <option value="">Выберите роль</option>
                                        @if($roles)
                                            @foreach($roles as $id => $role)
                                                @if ($id == 17|| $id == 20 || $id == 2|| $id == 9  ) @continue @endif
                                                @if ((auth()->user()->project_id && auth()->user()->project_id == $role->project_id) || !auth()->user()->project_id)
                                                <option value="{{$id}}"
                                                        data-id="{{$role->company}}" data-project="{{$role->project_id}}">{{$role->name}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="hidden" id="current_project_id" data-id="{{(int)auth()->user()->project_id}}"></div>
                            </div>
                            <div class="form-group" @if (!auth()->user()->project_id) style="display: none;" @endif>
                                <label for="sub_project_id" class="col-sm-4 control-label">Под проект</label>
                                <div class="col-sm-8">
                                    <input id="sub_project_id" name="sub_project_id">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rank" class="col-sm-4 control-label">Ранг</label>
                                <div class="col-sm-8">
                                    <select name="rank" id="rank" class="form-control">
                                        <option value=""></option>
                                        @if ($ranks)
                                            @foreach($ranks as $rank)
                                                <option value="{{$rank->id}}"
                                                        data-id="{{$rank->role_id}}"
                                                        style="display: none"
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
                                        @if ($companies->count())
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{$company->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="login" class="col-sm-4 control-label">Логин(Email)</label>
                                <div class="col-sm-8">
                                    <input type="text" name="login" id="login" class="form-control">
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
                                    <input type="text" name="surname" id="surname" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-4 control-label">Имя</label>
                                <div class="col-sm-8">
                                    <input type="text" name="name" id="name" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="middle" class="col-sm-4 control-label">Отчество</label>
                                <div class="col-sm-8">
                                    <input type="text" name="middle" id="middle" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-4 control-label">Телефон</label>
                                <div class="col-sm-8">
                                    <input type="text" name="phone" id="phone" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="time_zone" class="col-sm-4 control-label">Часовой пояс</label>
                                <div class="col-sm-8">
                                    <select id="time_zone" name="time_zone">
                                        @foreach(timezone_identifiers_list() as $timezone)
                                            <option value="{{$timezone}}" @if ('UTC' == $timezone) selected @endif>{{$timezone}} {{\Carbon\Carbon::now($timezone)->format('P')}}</option>
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
                                        <input type="text" class="form-control" id="birthday" name="birthday">
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
                                    <input class="include" type="checkbox" name="block" id="block"/>
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
                </div>
            </div>
            <div class="col-lg-6" style="display: none">
                <div class="main-box clearfix" style="margin-bottom: 0;">
                    <a href="#" class="btn btn-success col-sm-12" id="create_sip">Создать аккаунт SIP </a>
                </div>
                <div class="main-box clearfix" id="block_sip" style="padding-top: 20px;display: none">
                    <form class="form form-horizontal" id="data_sip">
                        <div class="main-box-body clearfix">
                            <div class="form-group">
                                <label for="login_sip" class="col-sm-4 control-label">Внутренний номер</label>
                                <div class="col-sm-8">
                                    <input type="text" name="login_sip" id="login_sip" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_sip" class="col-sm-4 control-label"
                                       id="label_pas_two">Пароль</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control " id="password_sip"
                                           name="password_sip" placeholder="Новый пароль" readonly>
                                    <input type="button" class="btn btn-success" id="generate_pas_two"
                                           value="Сгенерировать пароль" style="margin-top: 10px">
                                </div>
                            </div>
                            <div class="form-group">
                                <label id="ip_label" class="col-sm-4 control-label">IP пользователя</label>
                                <div class="col-sm-8">
                                    <input type="button" class="btn btn-success" id="new_input"
                                           value="Добавить IP">
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="checkbox checkbox-nice"
                                     style="display: inline-block;margin-left: 5px;">
                                    <input class="include" type="checkbox" name="nat"
                                           id="nat"/>
                                    <label for="nat">
                                        NAT
                                    </label>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-success" value="Сохранить">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop