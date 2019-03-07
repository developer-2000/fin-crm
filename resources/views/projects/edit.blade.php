@extends('layouts.app')

@section('title')Редактирование @if($project->parent_id) субпроекта @else проекта @endif @stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('projects')}}">Проекты</a></li>
                @if($project->parent_id)
                <li><a href="{{route('project-show', $project->parent_id)}}">{{ $project->parent->name }}</a></li>
                @endif
                <li class="active">Редактирование @if($project->parent_id) субпроекта @else проекта @endif</li>
            </ol>
            <h1>Редактирование @if($project->parent_id) субпроекта @else проекта @endif {{ $project->name }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="main-box">
                <header class="main-box-header clearfix"></header>
                <div class="main-box-body">
                    <form action="{{ route('project-update', $project->id) }}" class="form-horizontal" method="POST">
                        {{ csrf_field() }}

                        <div class="form-group @if($errors->has('name')) has-error @endif">
                            <label for="name" class="col-md-4 control-label">
                                Имя @if($project->parent_id) субпроекта @else проекта @endif
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" value="{{ old('name') ?: $project->name }}" class="form-control">
                                @if($errors->has('name'))
                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group @if($errors->has('alias')) has-error @endif">
                            <label for="alias" class="col-md-4 control-label">
                                Псевдоним
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="alias" id="alias" value="{{ old('alias') ?: $project->alias }}" class="form-control">
                                @if ($errors->has('alias'))
                                    <span class="help-block">{{ $errors->first('alias') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Сохранить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop