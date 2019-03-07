@extends('layouts.app')
@section('title')Добавить скрипт @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/offers">Офферы</a></li>
                <li><a href="/scripts">Скрипты</a></li>
                <li class="active"><a href=""><span>Добавить скрипт</span></a></li>
            </ol>
            <h1>Добавить скрипт</h1>

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
                    <h2>Добавление скрипта </h2>
                </header>
                <div class="main-box-body clearfix">
                    <div class="form-group">
                        <label for="name">Название скрипта</label>
                        <input class="form-control" type="text" name="name" id="name"
                               placeholder="Название скрипта" required>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label for="category">Категория</label>--}}
                        {{--<select class="form-control" name="category" id="category" required>--}}
                            {{--<option value="">Выберите категорию</option>--}}
                            {{--@foreach($categories as $key=>$category)--}}
                                {{--<option value="{{$category->id}}">{{$category->name}}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<div class="">--}}
                            {{--<label for="name">Название блока</label>--}}
                            {{--<input class="form-control" type="text" name="block" id="block"--}}
                                   {{--placeholder="Название блока" required>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label for="offers">Добавить оффер</label>
                        <input type="hidden" name="offers" id="offers"
                               class="offers"
                               style="width: 100%"/>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<div class="">--}}
                            {{--<label for="position">Позиция в блоке</label>--}}
                            {{--<input class="form-control" type="number" style="width: 100px" min="1" required--}}
                                   {{--name="position" id="position">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label for="comment"> Комментарий</label>
                        {{ Form::textarea('comment', NULL, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                    </div>
                    {{--<input type="hidden" id="offerId" value="{{$offerId}}">--}}

                    {{Form::submit('Сохранить', ['class' => 'btn btn-success'])}}
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
    <script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ URL::asset('js/scripts/script-add.js') }}"></script>
@stop


