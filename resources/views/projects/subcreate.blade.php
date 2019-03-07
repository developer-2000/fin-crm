@extends('layouts.app')

@section('title')Создание склада@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('index') }}">Главная</a></li>
                <li><a href="{{ route('projects') }}">Проекты</a></li>
                <li><a href="{{ route('project-show', $parent_project->id) }}">{{ $parent_project->name }}</a></li>
                <li class="active">Создание субпроекта</li>
            </ol>
            <h1>Создание субпроекта (для проекта "{{ $parent_project->name }}")</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="main-box">
                <header class="main-box-header clearfix"></header>
                <div class="main-box-body">
                    <form class="form-horizontal" action="{{ route('subproject-store', $parent_project->id) }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="parent_id" value="{{ $parent_project->id }}" />

                        <div class="form-group @if($errors->has('name')) has-error @endif">
                            <label for="name" class="col-md-4 control-label">
                                Имя склада
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" />
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
                                <input type="text" name="alias" id="alias" value="{{ old('alias') }}" class="form-control" />
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