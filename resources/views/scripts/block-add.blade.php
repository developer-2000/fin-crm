@extends('layouts.app')
@section('title')Добавить блок @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="{{route('scripts-edit', $scriptId)}}">Вернуться к скрипту</a></li>
                <li class="active"><a href=""><span>Добавить блок</span></a></li>
            </ol>
            <h1>Добавить блок</h1>
        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{ Form::open(['method'=>'POST'])}}
    <div class="main-box clearfix">
        <div class="row">
            <div class="col-lg-6">
                <header class="main-box-header clearfix">
                    <h2>Добавление блока </h2>
                </header>
                <div class="main-box-body clearfix">
                    <div class="form-group">
                        <div class="">
                            <label for="category">Категория</label>
                            <div class="row">
                                <select class="form-control" name="category" id="category" required>
                                    <option value="">Выберите категорию</option>
                                    @foreach($categories as $key=>$category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                            <label for="name">Название блока</label>
                            <input class="form-control" type="text" name="block" id="block"
                                   placeholder="Название блока" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                            <label for="geo">Выбрать страну</label>
                            <select class="form-control" name="geo" id="geo">
                                <option value="" selected >Выберите страну</option>
                                @foreach($countries as $country)
                                    <option value="{{strtolower($country->code)}}"> @lang('countries.' . $country->code)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <label for="position">Позиция в блоке</label>
                            <input class="form-control" type="number" style="width: 100px" min="-50" max="50" name="position" id="position" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="checkbox-nice">
                                {{ Form::checkbox('key', 1, NULL, ['id' => 'key']) }}
                                {{ Form::label('key', 'Отметить блок как важный') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2>Добавьте текст блока</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <textarea class="form-control ckeditor" id="ckeditor" name="ckeditor" rows="3" style="visibility: hidden; display: none;" required></textarea>
                        <input type="hidden" id="scriptId" value="{{$scriptId}}">
                        <br>
                        {{Form::submit('Сохранить', ['class' => 'btn btn-success'])}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close()}}
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/scripts/script-block-add.js') }}"></script>
    <script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
    <script>
        var editor = CKEDITOR.replace( 'ckeditor',{
            filebrowserBrowseUrl : '/elfinder/ckeditor'
        } );
    </script>
@stop


