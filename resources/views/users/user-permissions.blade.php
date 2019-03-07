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
    <script src="{{ URL::asset('js/users/user-permissions.js') }}"></script>
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
                            <li>
                                <a href="{{route('users-edit', $userOne->id)}}">Настройки пользователя</a>
                            </li>
                            <li class="active"><a href="{{route('user-edit-permissions', $userOne->id)}}">Права
                                    доступа</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade in active">
                                <div class="main-box-body clearfix">
                                    <div class="table-responsive custom">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th class="headcol">Права доступа</th>
                                                <th class="text-center">Доступно</th>
                                                <th class="text-center">Роли</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if ($data->count())
                                                @foreach($data as $key => $permissionsGrouped)
                                                    <tr class="text-center" style="background-color: #ddf9d9a6">
                                                        <td class="text-center" style="font-weight: bold"> {{$key}}</td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    @foreach($permissionsGrouped as $permission)
                                                        <tr id="{{$permission['id']}}">
                                                            <td class="headcol">{{$permission['alias']}}</td>
                                                            @if ($userPermissions->count())
                                                                <td class="text-center">
                                                                    <div class="checkbox-nice"
                                                                         style="display: inline-block;">
                                                                        <input type="checkbox"
                                                                               class="checkbox_permission"
                                                                               id="permission_{{$permission->id}}"
                                                                               data-id="{{$permission->id}}"
                                                                               @if (in_array($permission->name, $userPermissions->pluck('name')->toArray()))
                                                                               checked="checked"
                                                                               @endif
                                                                               @php
                                                                                   $permRoles = $permission->roles->pluck('name')->toArray();
                                                                               @endphp

                                                                               @foreach($userOne->roles as $userRole)
                                                                               @if(in_array($userRole->name, $permRoles) && in_array($permission->name, $userPermissions->pluck('name')->toArray()))disabled
                                                                                @endif
                                                                                @endforeach
                                                                        >
                                                                        <label for="permission_{{$permission->id}}"></label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @foreach($userOne->roles as $userRole)
                                                                        @if(in_array($userRole->name, $permRoles))
                                                                            <span class="label label-info">{{$userRole->name}}</span>
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                            @else
                                                                <td></td>
                                                                <td></td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endif
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
@stop