@extends('layouts.app')

@section('title')Все цели@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active">Цели</li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Цели </h1>
                @if (isset($permissions['page_create_target']))
                    <div class="pull-right top-page-ui">
                        <a href="{{ route("targets-create") }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить цель
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Имя</th>
                                    <th>Псевдоним</th>
                                    <th class="text-center">Сущность</th>
                                    <th class="text-center">Тип</th>
                                    <th>Фильтр</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($targets as $target)
                                        <tr>
                                            <td>{{$target->id}}</td>
                                            <td>{{$target->name}}</td>
                                            <td>{{$target->alias}}</td>
                                            <td class="text-center">{{$target->entity}}</td>
                                            <td class="text-center">{{$target->target_type}}</td>
                                            <td style="font-size: 11px">
                                                @if ($target->filter_geo)
                                                    <div>Страна : <b>{{$target->filter_geo}}</b></div>
                                                @endif
                                                @if ($target->offer_name)
                                                    <div>Offer : <b>{{$target->offer_name}}</b></div>
                                                @endif
                                                @if ($target->project)
                                                    <div>Проект : <b>{{$target->project}}</b></div>
                                                @endif
                                                @if ($target->tag_campaign)
                                                    <div>tag_campaign : <b>{{$target->tag_campaign}}</b></div>
                                                @endif
                                                @if ($target->tag_content)
                                                    <div>tag_content : <b>{{$target->tag_content}}</b></div>
                                                @endif
                                                @if ($target->tag_medium)
                                                    <div>tag_medium : <b>{{$target->tag_medium}}</b></div>
                                                @endif
                                                @if ($target->tag_source)
                                                    <div>tag_source : <b>{{$target->tag_source}}</b></div>
                                                @endif
                                                @if ($target->tag_term)
                                                    <div>tag_term : <b>{{$target->tag_term}}</b></div>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($permissions['page_one_target']))
                                                <a href="{{route('target', $target->id)}}" class="table-link ">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x "></i>
                                                        <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Нет целей</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop