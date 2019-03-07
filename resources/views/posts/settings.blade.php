@extends('layouts.app')
@section('title') Настройка новостей @stop
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
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('posts.post-setting-title')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('posts.post-setting-title')</h1>
                @if(isset($permissions['documentations']))
                    <div class="pull-right top-page-ui">
                        <a href="{{route('posts.create')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('posts.add-post')</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="main-box">
          <div class="item_rows ">
            <div class="main-box-body clearfix">
              <br>
              <div class="row">

                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <input class="form-control" id="title" placeholder="Title" value="">
                  </div>
                </div>

                <div class="col-md-2 col-sm-6">
                  <div class="form-group">
                    <select id="category" class="form-control selectpicker">
                      <option selected value=""> @lang('general.categories')</option>
                      @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="table">
      {!! view('posts.table', ['posts' => $posts]) !!}
    </div>

@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/posts/settings.js') }}"></script>
@stop
