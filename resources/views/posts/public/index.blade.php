@extends('layouts.app')
@section('title') @lang('posts.public-title') @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
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

            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li><a href="#"> @lang('general.main')</a></li>
                    <li class="active"><span> @lang('posts.all-posts')</span></li>
                </ol>
                <h1> @lang('posts.feed-posts')</h1>
                    <div id="search-form">
                            <div class="input-group">
                                <input type="text" id="search" value="" class="form-control input-lg">
                                <div class="input-group-btn">
                                    <button class="btn btn-lg btn-primary" type="submit" onclick="search(1);">
                                        <i class="fa fa-search"></i> @lang('general.search')
                                    </button>
                                </div>
                            </div>
                    </div>
            </div>

            <div class="col-lg-9 mr-2" style="margin-top:50px">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <ul id="search-results">
                            <div class="tab-pane fade in active" id="tab-newsfeed">
                                <div id="newsfeed">

                                        {!! view('posts.public.block', ['posts' => $posts]) !!}

                                </div>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <h3> @lang('posts.rubrics-posts')</h3>
                {{-- Рубрики новостей --}}
                <div class="list-group">
                    <a href="#" id="all" onclick="setCategory('0');" class="list-group-item category active">
                        @lang('general.all')
                    </a>
                    @foreach($categories as $category)
                        <a href="#" id="{{$category->id}}" onclick="setCategory(this.id);" class="list-group-item category">
                            {{$category->name}}
                            @if(($category->posts_count))
                                <span class="badge">{{$category->posts_count}}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/posts/public.js') }}"></script>
@stop
