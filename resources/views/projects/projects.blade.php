@extends('layouts.app')

@section('title')Проекты@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Проекты</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Проекты</h1>
            </div>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle fa-fw fa-lg"></i>
            {{ session('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        @if ($projects)
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center"><span>Проект name</span></th>
                                    <th class="text-center"><span>Проект alias</span></th>
                                    <th class="text-center"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($projects as $project)
                                    <tr>
                                        <td class="text-center">
                                            {{ $project->name }}
                                        </td>
                                        <td class="text-center">
                                            {{ $project->alias }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('project-show', $project->id) }}"
                                               class="pull-right btn btn-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop