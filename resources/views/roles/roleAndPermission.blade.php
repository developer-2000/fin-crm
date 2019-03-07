@extends('layouts.app')

@section('title')Права доступа@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        .headcol {
            position: absolute;
            width: 18em;
            left: 0;
            border-right: 1px none black;
            word-break: initial;
        }

        .custom {
            overflow-x: scroll;
            margin-left: 15em;
            overflow-y: visible;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/users/role.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#">Главная</a></li>
                <li class="active"><span>Права доступа</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Права доступа</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box no-header clearfix" style="padding-top: 0;padding-bottom: 20px;">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="{{route('users')}}">Все пользователи</a>
                        </li>
                        @if (isset($permissions['page_roles']))
                            <li>
                                <a href="{{route('roles')}}">Роли</a>
                            </li>
                        @endif
                        <li class="active"><a href="{{route('role-and-permission')}}">Права доступа</a>
                        </li>
                        @if (isset($permissions['page_ranks']))
                            <li>
                                <a href="{{route('users-ranks')}}">Ранги</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            <div class="main-box-body clearfix">
                                <div class="table-responsive custom">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th class="text-center headcol" style="height: 4.2em; ">Права доступа</th>
                                            @if ($roles->count())
                                                @foreach($roles as $key=>$role)
                                                    <th class="text-center">{{$role->name}}</th>
                                                @endforeach
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if ($data->count())
                                            @foreach($data as $key => $permissionsGrouped)
                                                <tr class="text-center" style="background-color: #ddf9d9a6">
                                                    <td class="text-center headcol" style="font-weight: bold; background-color: #ddf9d9a6"> {{!empty($key) ? $key : 'Не распределенные'}}</td>
                                                    <td  colspan="{{count($roles)}}" style="height: 3em"></td>
                                                </tr>
                                                @foreach($permissionsGrouped as $permission)
                                                    <tr id="{{$permission['id']}}">
                                                        <td class="headcol">{{$permission['alias']}}</td>
                                                        @if ($roles->count())
                                                            @foreach($roles as $role)
                                                                <td class="text-center">
                                                                    <div class="checkbox-nice"
                                                                         style="display: inline-block;">
                                                                        <input type="checkbox" class="checkbox_role"
                                                                               id="role_{{$role->id}}_perm_{{$permission['id']}}"
                                                                               data-id="{{$role->id}}"
                                                                               @if (in_array($role->id,  $permission->roles->pluck('id')->toArray()))
                                                                               checked="checked"
                                                                                @endif
                                                                        >
                                                                        <label for="role_{{$role->id}}_perm_{{$permission['id']}}">
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            @endforeach
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
@stop