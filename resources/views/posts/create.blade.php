@extends('layouts.app')
@section('title') @lang('posts.create') @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/wizard.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/posts.css') }}"/>
    <style>
        body {
            color: grey;
        }
    </style>
@stop
@section('content')
    <div class="row" style="opacity: 1;">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb">
                        <li><a href="/"> @lang('general.main')</a></li>
                        <li><a href="{{route('posts.settings')}}"> @lang('posts.setting')</a></li>
                        <li class="active"><a href=""><span> @lang('posts.create')</span></a></li>
                    </ol>
                    <h1> @lang('posts.setting')</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-box clearfix" style="min-height: 820px;">
                        <div class="main-box-body clearfix">
                            <div id="myWizard" class="wizard">
                                <div class="step-content">
                                    <div class="step-pane active" id="step1">
                                        <div class="col-lg-6">
                                            <div class="main-box-body clearfix">
                                                <div class="form-group">
                                                    <label for="category_id"> @lang('general.category')</label>
                                                    <select class="form-control" name="category_id" id="category_id"
                                                    >
                                                        <option value=""> @lang('general.select-category')</option>
                                                        @foreach($categories as $key=>$category)
                                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title"> @lang('general.title')</label>
                                                    <input class="form-control" type="text" name="title" id="title"
                                                           placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="main-box-body clearfix">
                                                <br>
                                                <div class="form-group">
                                                    <label for="priority" class="col-lg-5"> @lang('posts.select-priority'):</label>
                                                    <div class="btn-group" data-toggle="buttons">
                                                        <div class="btn-group" data-toggle="buttons">
                                                            <label class="btn btn-primary priority-label active">
                                                                <input type="radio" onclick="setPriority(this.id);" name="priority" value="low" id="low"
                                                                       checked> @lang('posts.priority-low')
                                                            </label>
                                                            <label class="btn btn-primary priority-label">
                                                                <input type="radio" onclick="setPriority(this.id);" name="priority" value="medium"
                                                                       id="medium"> @lang('posts.priority-medium')
                                                            </label>
                                                            <label class="btn btn-primary priority-label">
                                                                <input type="radio" onclick="setPriority(this.id);" name="priority" value="high"
                                                                       id="high"> @lang('posts.priority-high')
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-5" for="date"> @lang('posts.time-publish')
                                                    </label>
                                                    <div class="input-group col-lg-5">
                                                        <span class="input-group-addon"><i
                                                                    class="fa fa-calendar"></i></span>
                                                        <input class="form-control date" id="date" type="text"
                                                               data-toggle="tooltip"
                                                               name="publish_at"
                                                               data-placement="bottom"
                                                        >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-nice">
                                                        <input type="checkbox"
                                                               id="familiar">
                                                        <label for="familiar"> @lang('posts.required-familiar')
                                                          {{-- Требует обязательного ознакомления --}}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="form-group pull-right">
                                                        <button class="btn btn-primary" id="button_save" onclick="save();"> @lang('general.save') </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="main-box clearfix">
                                                    <header class="main-box-header clearfix">
                                                        <h2> @lang('posts.description')</h2>
                                                    </header>
                                                    <div class="main-box-body clearfix">
                        <textarea class="form-control ckeditor" id="ckeditor" name="ckeditor" rows="3"
                                  style="visibility: hidden; display: none;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/multiselect.min.js') }}"></script>
    <script src="{{ URL::asset('js/posts/create.js') }}"></script>
    <script>
        var editor = CKEDITOR.replace('ckeditor', {
            filebrowserBrowseUrl: '/elfinder/ckeditor'
        });
    </script>
@stop
