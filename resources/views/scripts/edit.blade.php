@extends('layouts.app')
@section('title')Редактировать скрипт @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        .dd-handle {
            font-size: 15px;
            color: grey;
            border: 1px solid #e1e1e1;
            font-weight: 500;
            background-color: white;
        }

        li .dd-handle:hover {
            background-color: rgba(220, 232, 248, 0.49);
        }

        .header-list:hover {
            background-color: white;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="clearfix">
                <ol class="breadcrumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="{{route('scripts')}}">Все скрипты</a></li>
                    <li class="active"><a href=""><span>Страница скрипта</span></a></li>
                </ol>
                <div class="pull-right top-page-ui">
                    <a href="{{route('scripts-blocks-create', Request::segment(2))}}"
                       class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Добавить блок</a>
                </div>
                <h1>{{$script->name}}</h1>
            </div>
        </div>
    </div>
    @if(!empty($scriptDetailsCollections))
        <div class="main-box clearfix">
            <div class="row">
                <div class="col-lg-6">
                    <header class="main-box-header clearfix">
                        {{Form::open(['id' => 'form1', 'method' => 'post'])}}
                        <div class="form-group">
                            <div>
                                <label for="name">Название скрипта</label>
                                <input class="form-control" type="text" value="{{$script->name}}" name="name" id="name"
                                       placeholder="Название скрипта" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="offers">Добавить оффер</label>
                            <input type="hidden" name="offers" id="offers"
                                   class="offers"
                                   style="width: 100%"/>
                            <input type="hidden" name="offersJson" id="offersJson"
                                   value="{{!empty($offersJson) ? $offersJson : NULL}}">
                        </div>
                        <input type="hidden" name="script_id" value="{{$script->id}}">
                        {{Form::submit('Сохранить', ['class' => 'btn btn-success'])}}
                        {{ Form::close() }}
                    </header>
                </div>
            </div>
            <div class=" row">
                <div class="col-lg-12">
                    <div class="main-box clearfix">
                        @if ($scriptDetailsCollections)
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <ol class="" style=" list-style:none">
                                        <li class="header-list"
                                            style="font-size: 14px; height: 5em; color: grey; font-weight: bold; border-radius: 5px; border: 1px solid #e1e1e1; margin-left: -40px;">
                                            <div class=""
                                                 style="border-radius: 5px; height: 100%;">
                                                <div style="width: 3%; height: 100%; top: 30%"
                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1">ID
                                                </div>
                                                <div style="width: 13%;  height: 100%; top: 30%"
                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1"> Категория
                                                </div>
                                                <div style="width: 21%;  height: 100%;top: 30%; text-align: left"
                                                     class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Наименование блока
                                                </div>
                                                <div style="width: 11%;  height: 100%; top: 30%"
                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1">Страна
                                                </div>
                                                <div style="width: 12%;  height: 100%; top: 30%"
                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1 position">Позиция
                                                </div>
                                                <div style="width: 12.5%;  height: 100%; top: 30%"
                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                    Активность
                                                </div>
                                                <div style="width: 10%;  height: 100%; top: 30%"
                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1 dd-nodrag">
                                                </div>
                                                <div style="width: 10%;  height: 100%; top: 30%"
                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1 dd-nodrag">
                                                </div>
                                            </div>
                                        </li>
                                    </ol>
                                    @foreach($scriptDetailsCollections as $key=>$scriptDetails)
                                        <div class="row cf nestable-lists">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dd"
                                                 id="nestable{{$key}}"
                                                 style="width: 100%">
                                                <div class="row text-center"
                                                     style="font-size: 14px; font-weight: bold; color: grey">Блок:
                                                    <span style="font-size: 18px; font-weight: bold; color: #1ABC9C; text-decoration: underline">
                                                        {{$scriptDetails[0]->category ? $scriptDetails[0]->category->name : ''}}</span></div>
                                                <ol class="dd-list">
                                                    @foreach($scriptDetails as $scriptDetail)
                                                        <li class="dd-item" data-id="{{$scriptDetail->id}}"
                                                            style="font-size: 14px; height: 5em">
                                                            <div class="dd-handle"
                                                                 style="border-radius: 5px; height: 100%;">
                                                                <div style="width: 3%; height: 100%; top: 30%"
                                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1">{{$scriptDetail->id}}</div>
                                                                <div style="width: 13%;  height: 100%; top: 30%"
                                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1"> {{$scriptDetail->category ? $scriptDetail->category->name : ''}}</div>
                                                                <div style="width: 22.5%;  height: 100%;top: 30%; text-align: left"
                                                                     class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> {{$scriptDetail->block}}</div>
                                                                <div style="width: 12.5%;  height: 100%; top: 30%"
                                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1">   @if(!empty($scriptDetail->geo))
                                                                        <img class="country-flag"
                                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($scriptDetail->geo) . '.png') }}" />
                                                                    @else
                                                                        {{'N\A'}}
                                                                    @endif</div>
                                                                <div style="width: 12.5%;  height: 100%; top: 30%"
                                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1 position"> {{$scriptDetail->position}}</div>
                                                                <div style="width: 12.5%;  height: 100%; top: 10%"
                                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1 dd-nodrag">
                                                                    <div class="checkbox-nice checkbox">
                                                                        @php
                                                                            if($scriptDetail->status == 'active'){
                                                                            $status = true;
                                                                            }else{
                                                                                $status = false;}
                                                                        @endphp
                                                                        {{ Form::checkbox('activity', $scriptDetail->id, $status, ['id' => 'activate_'.$scriptDetail->id, 'class' => 'activate_script_row']) }}
                                                                        {{ Form::label('activate_'.$scriptDetail->id, ' ') }}
                                                                    </div>
                                                                </div>
                                                                <div style="width: 10%;  height: 100%; top: 30%"
                                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1 dd-nodrag">
                                                                    <a
                                                                            href="/script/{{$scriptDetail->script->id}}/edit/{{$scriptDetail->id}}"
                                                                            class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                                    </a>
                                                                </div>
                                                                <div style="width: 10%;  height: 100%; top: 30%"
                                                                     class="col-lg-1 col-md-1 col-sm-1 col-xs-1 dd-nodrag">
                                                                    <a href="#"
                                                                       data-type="text"
                                                                       data-pk="1"
                                                                       data-title="Вы действительно хотите удалить блок?"
                                                                       data-id="{{ $scriptDetail->id }}"
                                                                       data-url="/ajax/scripts/blocks/{{ $scriptDetail->id }}/delete"
                                                                       class="editable editable-click table-link danger  delete-block">Удалить</a>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nestable.js') }}"></script>
    <script src="{{ URL::asset('js/scripts/script-edit.js') }}"></script>
    <script src="{{ URL::asset('js/scripts/script-block-delete.js') }}"></script>
@stop

