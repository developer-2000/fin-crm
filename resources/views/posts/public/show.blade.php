@extends('layouts.app')
@section('title')Редактирование новости  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-attached.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-other.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/posts.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/"> @lang('general.main')</a></li>
                <li><a href="/posts"> @lang('posts.all-posts')</a></li>
                <li class="active"><a href=""><span> @lang('posts.view-post')</span></a></li>
            </ol>
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
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix ">
                <div class="main-box-header">
                    <div class="pull-right">
                        Рубрика: {{ $post->category->name }}
                    </div>
                </div>
                <div class="main-box-body clearfix">
                    <h3 class="text-center">  {{$post->title}}</h3>
                    <br>
                    <div class="">
                        {!! $post->body !!}
                    </div>
                    <br>
                    <div class="post-time pull-left">
                        Опубликовано: {{ $post->created_at->format("d/m/Y H:i") }}
                    </div>
                    @if($post->required)
                    @if(! $familiar)
                        <div class="post-time pull-right">
                            <button id="notification-trigger-bouncyflip"
                                    class="btn btn-info progress-button mrg-b-lg">
                                <span class="content"> @lang('posts.familiar-post')</span>
                                {{-- С даной информацией ознакомлен --}}
                            </button>
                            <input type="hidden" id="currentUserFamiliar" >
                        </div>
                    @endif
                  @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nanoscroller.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/snap.svg-min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script>
        var bttnBouncyflip = document.getElementById('notification-trigger-bouncyflip');

        bttnBouncyflip.disabled = false;
        bttnBouncyflip.addEventListener('click', function () {
            this.disabled = true;
            $.ajax({
                type: 'post',
                url: '/posts/familiar/set',
                data: ({
                  id: '{{ $post->id }}'
                }),
                success: function (response) {
                    if (response) {
                        $('.pricing-star').removeClass('hidden');
                        $('.shape-progress').addClass('hidden');

                        var notification = new NotificationFx({
                            message: '@lang('posts.alert-familiar-success')',
                            // <p>Спасибо! Администрация уведомлена о Вашем ознакомленни!</p>
                            layout: 'attached',
                            effect: 'bouncyflip',
                            type: 'success',
                            ttl: 3000
                        });
                       notification.show();
                    }
                    else {
                      showMessage('error', "Request server error.")
                    }
                }
            });

        });
    </script>
@stop
