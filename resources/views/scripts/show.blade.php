@extends('layouts.app')

@section('title')Скрипт # {{ $script->id }} @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}"/>
    <style>
        blockquote:before {
            display: none !important;
        }

        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
            border-radius: 3px 3px 0 0;
            background-clip: padding-box;
            border-left: 0;
            border-right: 0;
            border-top: 0;
            font-weight: bold;
        }

        li.config-li {
            background: none;
        }

        .block {
            background-color: rgba(238, 246, 243, 0.78);
        }

        .current_block {
            background-color: rgba(194, 199, 226, 0.29);
            border-radius: 7px;
        }

        blockquote {
            border-color: rgba(134, 134, 140, 0.14);
        }

        .comment {
            background: #f5f5f5;
            font-size: 0.875em;
            padding: 8px 10px;
            border-radius: 3px;
            background-clip: padding-box;
        }

        .comment-time {
            text-align: right;
            font-size: 10px;
            color: rgba(142, 145, 147, 0.89);
        }

        #config-tool {
            top: 56px;
        }

        #config-tool #config-tool-cog {
            left: -45px;
            padding: 10px;
            text-align: left;
            width: 50px;
            height: 38px;
        }

        #config-tool.closed #config-tool-cog i {
            animation: none;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js?x=1') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/feedback-add.js') }}"></script>
    <script>
        $(function () {
            $(window).on('scroll', function () {
                var scrollPos = $(document).scrollTop();
                $('#block-content').css({
                    top: scrollPos
                });
                $('#block-objections').css({
                    top: scrollPos
                });
                $('#block-other-questions').css({
                    top: scrollPos
                });
            }).scroll();
        });

        $('a#link-block').click(function (e) {
            var block = $(this).attr('href');
            $('div.initial-block').children('blockquote').removeClass('block');
            // $('div.initial-block').css('opacity', '0.5');
            $('div.initial-block').children('blockquote').css('border-color', 'rgba(194, 199, 226, 0.29)');
            $('div' + block).children('blockquote').addClass('block').css('opacity', '1');
            $('div' + block).children('blockquote').css('border-color', '#1ABC9C');

            $('a#link-block').closest('div').removeClass('current_block');
            $(this).closest('div').addClass('current_block');
        });

        $('#config-tool #config-tool-options ul.nav.nav-tabs li a').click(function (e) {
            $('#config-tool').addClass('closed');
        });
        $('.block-inner').slimScroll({
            height: '150px',
            wheelStep: 35,
        });


        $(document).ready(function () {
            $(document).on("scroll", onScroll);

            //smoothscroll
            $('#block-content a[href^="#"]').on('click', function (e) {
                e.preventDefault();
                $(document).off("scroll");

                var target = this.hash,
                    menu = target;
                $target = $(target);
                $('html, body').stop().animate({
                    'scrollTop': $target.offset().top + 2
                }, 500, 'swing', function () {
                    c
                    $(document).on("scroll", onScroll);
                });
            });
        });

        function onScroll(event) {
            var scrollPos = $(document).scrollTop();

            $('a#link-block').each(function () {
                var currLink = $(this);
                var refElement = $(currLink.attr("href"));
                if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
                    $('a#link-block').closest('div').removeClass('current_block');
                    currLink.closest('div').addClass("current_block");
                }
                else {
                    currLink.closest('div').removeClass("current_block");
                }
            });

            $('div .initial-block').each(function () {
                var currDiv = $(this);

                if (currDiv.position().top <= scrollPos && currDiv.position().top + currDiv.height() > scrollPos) {
                    $('div .initial-block').children('blockquote').removeClass('block');
                    currDiv.children('blockquote').addClass("block").css('border-color', '#1ABC9C');
                    ;
                }
                else {
                    currDiv.children('blockquote').removeClass("block").css('border-color', 'rgba(194, 199, 226, 0.29)');
                }
            });
        }


    </script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/offers">Офферы</a></li>
                <li><a href="/scripts">Скрипты</a></li>
            </ol>
            <h1 style="font-size: 18px; color: #8e9193">Предварительный просмотр скрипта: {{$script->name}}  </h1>

        </div>
    </div>
    <div class="tabs-wrapper">
        <div class="tab-content">
            @if($script->scriptDetails->count())
                <div class="tab-pane fade active in" id="tab-content">
                    <div class="row">
                        <div id="block-content" class="col-lg-3">
                            <div class="block-inner">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-content" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">
                                            Содержание</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                    <div class="main-box-body clearfix menu">
                                        @foreach($script->scriptDetails as $row)
                                            @if($row->category_id == 1)
                                                <div class="col-sm-12 scroll" style="padding: 5px">
                                                    <a id="link-block" style="text-decoration: none"
                                                       href="#tab-{{$row->id}}">
                                                        {{$row->block}}</a>
                                                    @if(!empty($row->geo))
                                                        <img class="country-flag"
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-objections" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">Возражения</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix ">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-other-questions" style="color: #000;" data-toggle="tab"
                                           aria-expanded="false"> Другие вопросы</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                            </div>
                        </div>
                        <div class="slimScrollBar"></div>
                        <div class="col-lg-9">
                            @foreach($script->scriptDetails as $row)
                                @if($row->category_id == 1)
                                    <div class="main-box clearfix">
                                        <header class="main-box-header clearfix">
                                            <div class="icon-box pull-right">
                                            </div>
                                        </header>
                                        <div class="main-box-body clearfix">
                                            <div id="tab-{{$row->id}}" class="initial-block">
                                                <h1 style="font-size: 16px; font-weight: bold">{{$row->block}}</h1>
                                                <blockquote>
                                                    <p>
                                                        {!! $row->text !!}
                                                    </p>
                                                    <div class="pull-right">
                                                        @if(!empty($row->geo))
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                        @endif
                                                    </div>

                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-objections">
                    <div class="row">
                        <div id="block-objections" class="col-lg-3">
                            <div class="block-inner">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-content" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">
                                            Содержание</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-objections" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">Возражения</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        @foreach($script->scriptDetails as $row)
                                            @if($row->category_id == 2)
                                                <div class="col-sm-12 scroll" style="padding: 5px">
                                                    <a id="link-block" style="text-decoration: none"
                                                       href="#tab-{{$row->id}}">
                                                        {{$row->block}}</a>
                                                    @if(!empty($row->geo))
                                                        <img class="country-flag"
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-other-questions" style="color: #000;" data-toggle="tab"
                                           aria-expanded="false"> Другие вопросы</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                            </div>
                        </div>
                        <div class="slimScrollBar"></div>
                        <div class="col-lg-9">
                            @foreach($script->scriptDetails as $row)
                                @if($row->category_id == 2)
                                    <div class="main-box clearfix">
                                        <header class="main-box-header clearfix">
                                            {{--<h2 class="pull-left">Текст скрипта</h2>--}}
                                            <div class="icon-box pull-right">
                                            </div>
                                        </header>
                                        <div class="main-box-body clearfix">
                                            <div id="tab-{{$row->id}}" class="initial-block">
                                                <h1 style="font-size: 16px; font-weight: bold">{{$row->block}}</h1>
                                                <blockquote>
                                                    <p>
                                                        {!! $row->text !!}
                                                    </p>
                                                    <div class="pull-right">
                                                        @if(!empty($row->geo))
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                        @endif
                                                    </div>
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-other-questions">
                    <div class="row ">
                        <div id="block-other-questions" class="col-lg-3">
                            <div class="block-inner">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-content" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">
                                            Содержание</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-objections" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">Возражения</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-other-questions" style="color: #000;" data-toggle="tab"
                                           aria-expanded="false"> Другие вопросы</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        @foreach($script->scriptDetails as $row)
                                            @if($row->category_id == 3)
                                                <div class="col-sm-12 scroll" style="padding: 5px">
                                                    <a id="link-block" style="text-decoration: none"
                                                       href="#tab-{{$row->id}}">
                                                        {{$row->block}}</a>
                                                    @if(!empty($row->geo))
                                                        <img class="country-flag"
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="slimScrollBar"></div>
                        <div class="col-lg-9">
                            @foreach($script->scriptDetails as $row)
                                @if($row->category_id == 3)
                                    <div class="main-box clearfix">
                                        <header class="main-box-header clearfix">
                                            {{--<h2 class="pull-left">Текст скрипта</h2>--}}
                                            <div class="icon-box pull-right">
                                            </div>
                                        </header>
                                        <div class="main-box-body clearfix">
                                            <div id="tab-{{$row->id}}" class="initial-block">
                                                <h1 style="font-size: 16px; font-weight: bold">{{$row->block}}</h1>
                                                <blockquote>
                                                    <p>
                                                        {!! $row->text !!}
                                                    </p>
                                                    <div class="pull-right">
                                                        @if(!empty($row->geo))
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                        @endif
                                                    </div>
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if($script->scriptDetails->count())
            <div id="config-tool" class="closed">
                <a style="text-decoration: none" id="config-tool-cog">
                    <i class="fa fa-file-text-o" style="font-size: 1.7em"></i>
                </a>
                <div id="config-tool-options">
                    <ul class="nav nav-tabs">
                        <li class="config-li">
                            <a href="#tab-content" data-toggle="tab" aria-expanded="false">Содержание</a>
                        </li>
                        <li class="config-li">
                            <a href="#tab-objections" data-toggle="tab" aria-expanded="false">Возражения</a>
                        </li>
                        <li class="config-li">
                            <a href="#tab-other-questions" data-toggle="tab" aria-expanded="false">Другие вопросы</a>
                        </li>

                    </ul>

                </div>
            </div>
        @endif
    </div>
@stop