@extends('layouts.app')

@section('title') @lang('general.feedback') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/feedback.css') }}"/>
    <style>
        .close-feedback {
            color: #ec5353e6;
            font-size: 15px;
        }

        .chat {
            color: #fff;
            display: block;
            font-size: 1.4em;
            font-weight: 300;
            padding: 16px 15px;
            border-radius: 3px 3px 0 0;
            background-clip: padding-box;
            transition: background-color 0.1s ease-in-out 0s;
        }

        .data {
            color: #fff;
            display: block;
            font-size: 1.4em;
            font-weight: 300;
            padding: 16px 15px;
            border-radius: 3px 3px 0 0;
            background-clip: padding-box;
            transition: background-color 0.1s ease-in-out 0s;
        }

        .data-list {
            font-weight: bold;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><a href="\operator-mistakes"><span> @lang('feedbacks.recalls-tickets')</span></a></li>
                <li class="active"><span> @lang('feedbacks.page-recall-ticket')</span></li>
            </ol>
            @if($feedback->type == 'failed_call')
                <h1> @lang('feedbacks.recall-order') <a href="{{ route('order', $feedback->order_id) }}"
                                      class="crm_id">{{$feedback->order_id}} &raquo;</a></h1>
            @elseif($feedback->type == 'fault' || $feedback->type == 'info')
                <h1> @lang('general.ticket') {{$feedback->id}}</h1>
            @endif
        </div>
    </div>
    <div class="clearfix">

    </div>
    <div class="main-container">
        <div class="row" id="user-profile">
            <div class="col-lg-4 col-md-5 col-sm-5">
                <div class="main-box clearfix project-box gray-box">
                    <div class="main-box-body clearfix">
                        <div class="project-box-header gray-bg">
                            <div class="name">
                                    <span class="data">
                                        @lang('general.data')
                                    </span>
                            </div>
                        </div>
                        <div class="project-box-content" style="text-align: left">
                            <div>
                                <div class="row">
                                    @if(!empty($feedback->user->company->name))
                                        <div class="col-sm-3"> @lang('general.company')</div>
                                        <div class="col-sm-9 data-list">{{$feedback->user->company->name}}</div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"> @lang('general.initiator')</div>
                                    <div class="col-sm-9 data-list"><a
                                                href="{{ route('users-edit', $feedback->moderator->id) }}"
                                        >{{$feedback->moderator->name.'  '.$feedback->moderator->surname}}</a>
                                    </div>
                                </div>
                                <div class="row">
                                    @if(!empty($feedback->user))
                                        <div class="col-sm-3"> @lang('general.operator')</div>
                                        <div class="col-sm-9 data-list"><a
                                                    href="{{ route('users-edit', $feedback->user->id) }}"
                                            >{{$feedback->user->name.'  '.$feedback->user->surname}}</a>
                                        </div>
                                    @endif
                                </div>
                                @if(!empty($feedback->order_id))
                                    <div class="row">
                                        <div class="col-sm-3"> @lang('general.order')</div>
                                        <div class="col-sm-9 data-list"><a
                                                    href="{{ route('order', $feedback->order_id) }}"
                                                    class="crm_id">{{$feedback->order_id}} &raquo;</a></div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-3"> @lang('general.date')</div>
                                    <div class="col-sm-9 data-list"><span>{{$feedback->created_at}}</span></div>
                                </div>
                            </div>
                            <hr>
                            @if(!empty($callRecord))
                                <h4> @lang('feedbacks.call-recording')</h4>
                                <div class="btn-group" style="width: 50px">
                                    <?
                                    $url = route('get-call-by-name') . '?fileName=' . $callRecord->file;
                                    $agent = $_SERVER['HTTP_USER_AGENT'];
                                    if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                        $output = '<p><a href="' . $url . '"><span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                            </span></a></p>';
                                    } else {
                                        $output = '
                                            <audio controls>
                                                <source src="' . $url . '" type="audio/mpeg">
                                            </audio>
                                    ';
                                    }
                                    echo $output?>
                                </div>
                                <br>
                            @endif
                            <div>
                                @php
                                    if($feedback->status == 'opened'){
                                    $hidden = '';
                                      $status = false;
                                        $closedFeedback = 'hidden';
                                    }elseif($feedback->status == 'closed'){
                                           $status = true;
                                           $hidden = 'hidden';    $closedFeedback = '';
                                        }
                                @endphp
                                @php
                                    if($feedback->operator_fault == 1){
                                    $operator_fault = true;
                                    }else{
                                        $operator_fault = false;}
                                @endphp
                            </div>
                            {{--<div>--}}
                            {{--<div class="profile-stars">--}}
                            {{--<div class="star-rating">--}}
                            {{--<div class="star-rating__wrap">--}}
                            {{--<form method="post" id="rating">--}}
                            {{--<input class="star-rating__input" id="star-rating-5" type="radio"--}}
                            {{--name="rating" value="5">--}}
                            {{--<label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-5"--}}
                            {{--title="5 out of 5 stars"></label>--}}
                            {{--<input class="star-rating__input" id="star-rating-4" type="radio"--}}
                            {{--name="rating" value="4">--}}
                            {{--<label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-4"--}}
                            {{--title="4 out of 5 stars"></label>--}}
                            {{--<input class="star-rating__input" id="star-rating-3" type="radio"--}}
                            {{--name="rating" value="3">--}}
                            {{--<label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-3"--}}
                            {{--title="3 out of 5 stars"></label>--}}
                            {{--<input class="star-rating__input" id="star-rating-2" type="radio"--}}
                            {{--name="rating" value="2">--}}
                            {{--<label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-2"--}}
                            {{--title="2 out of 5 stars"></label>--}}
                            {{--<input class="star-rating__input" id="star-rating-1" type="radio"--}}
                            {{--name="rating" value="1">--}}
                            {{--<label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-1"--}}
                            {{--title="1 out of 5 stars"></label>--}}
                            {{--</form>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                        </div>

                    </div>
                </div>
                <div class="main-box clearfix project-box gray-box">
                    <div class="main-box-body clearfix">
                        <form id="form1" method="post">
                            @if($feedback->type == 'failed_call' || isset($permissions['close_feedback']))
                                <div class="project-box-header gray-bg">
                                    <div class="name">
                                    <span class="data">
                                       @if($feedback->type == 'failed_call')
                                            @lang('general.errors')
                                        @elseif (isset($permissions['close_feedback']))
                                            @lang('feedbacks.additional-actions')
                                        @endif
                                    </span>
                                    </div>
                                </div>

                                <div class="project-box-content" style="text-align: left">
                                    @if(!empty($feedback->mistakes))
                                        <div class="form-group">
                                            <div>
                                                @foreach($feedback->mistakes as $key=> $mistake)
                                                    <div class="checkbox-nice checkbox">
                                                        {{ Form::checkbox('mistakes[]', $mistake->id, $feedback->mistakes->active[$key], ['id' => $mistake->name]) }}
                                                        {{ Form::label($mistake->name, $mistake->name) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div>
                                        @php
                                            if($feedback->status == 'opened'){
                                            $hidden = '';
                                              $status = false;
                                                $closedFeedback = 'hidden';
                                            }elseif($feedback->status == 'closed'){
                                                   $status = true;
                                                   $hidden = 'hidden';    $closedFeedback = '';
                                                }
                                        @endphp
                                        @php
                                            if($feedback->operator_fault == 1){
                                            $operator_fault = true;
                                            }else{
                                                $operator_fault = false;}
                                        @endphp
                                    </div>
                                </div>
                                <div class="project-box-footer clearfix">
                                </div>
                                @if (isset($permissions['close_feedback']))
                                    <div class="project-box-ultrafooter clearfix">
                                        <div class="form-group">
                                            <div class="checkbox-nice checkbox">
                                                {{ Form::checkbox('close_feedback', 'close_feedback', $status, ['id' => 'close_feedback']) }}
                                                {{ Form::label('close_feedback', 'Обсуждение завершено', ['class' => 'close-feedback']) }}
                                            </div>
                                        </div>
                                        <button type="submit" id="save"
                                                class="btn btn-primary center-block ">
                                            @lang('general.save')
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-7 col-sm-7">
                <div class="main-box clearfix project-box green-box">
                    <div class="main-box-body clearfix">
                        <div class="project-box-header blue-bg">
                            <div class="name">
                                <span class="chat">
                                    @lang('general.chat')
                                </span>
                            </div>
                        </div>
                        <div class="project-box-content" style="text-align: left; padding-top: 10px">
                            {{--<ul class="nav nav-tabs">--}}
                            {{--<li class="active"><a href="#tab-chat" data-toggle="tab">Чат</a></li>--}}
                            {{--</ul>--}}
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab-chat">
                                    <div class="conversation-wrapper">
                                        <div class="conversation-content">
                                            <div class="conversation-inner"
                                                 style="overflow: hidden; width: auto; height: auto;">
                                                <div class="conversation-item item-right clearfix">
                                                    <div class="conversation-user">
                                                        <img src="{{ $feedback->moderator->photo}}"
                                                             alt=""
                                                             class="profile-img img-responsive center-block"/>
                                                    </div>
                                                    <div class="conversation-body moderator-feedback"
                                                         style="background: rgba(240,82,71,0.14);">
                                                        <div class="text-center"> @lang('feedbacks.initiator-message'):</div>
                                                        <div class="name">
                                                            @if(auth()->user()->id == $feedback->moderator->id)
                                                                @lang('general.you')
                                                            @elseif(!empty($feedback->moderator->name))
                                                                {{(!empty($feedback->moderator->name) ? $feedback->moderator->name. ' ' .$feedback->moderator->surname : '') .(!empty($feedback->moderator->company->name) ? ' > '. $feedback->moderator->company->name .' ' : '') }}
                                                            @endif
                                                        </div>
                                                        <div class="time hidden-xs">
                                                            {{$feedback->created_at->format('Y-m-d H:i:s')}}
                                                        </div>
                                                        <div class="text-center"
                                                             style="font-weight: bold; font-size: 14px">
                                                            @if(!empty($comments[0]->text))
                                                                {{ $comments[0]->text }}
                                                            @else
                                                                @lang('feedbacks.comment-not-added')
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(!empty($comments))
                                                    @foreach( $comments as $key=> $comment)
                                                        @if($comment->user->role_id == 1 )
                                                            <div class="conversation-item item-left clearfix">
                                                                <div class="conversation-user">
                                                                    <img src="{{$comment->user->photo}}"
                                                                         alt=""
                                                                         class="profile-img img-responsive center-block"/>
                                                                </div>
                                                                <div class="conversation-body">
                                                                    <div class="name">
                                                                        @if(!empty($comment->user->name))
                                                                            @lang('general.you')
                                                                        @else
                                                                            {{$comment->user->name. ' ' .$comment->user->surname .(!empty($comment->user->company->name) ? ' > '. $comment->user->company->name .' ' : '') }}
                                                                        @endif
                                                                    </div>
                                                                    <div class="time hidden-xs">
                                                                        {{ date('Y/m/d H:i:s', (int)$comment->date) }}
                                                                    </div>
                                                                    <div class="text">
                                                                        {{!empty($comment->text) ? $comment->text : '' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(($comment->user->role_id >1)&& $key != 0 && !empty($comment->text))
                                                            <div class="conversation-item item-right clearfix">
                                                                <div class="conversation-user">
                                                                    <img src="{{$ $comment->user->photo}}"
                                                                         alt=""
                                                                         class="profile-img img-responsive center-block"/>
                                                                </div>
                                                                <div class="conversation-body">
                                                                    <div class="name">
                                                                        @if(!empty($comment->user->name) && auth()->user()->id == $comment->user_id)
                                                                            {{'Вы:'}}
                                                                        @elseif(!empty($comment->user->name))
                                                                            {{$comment->user->name. ' ' .$comment->user->surname .(!empty($comment->user->company->name) ? ' > '. $comment->user->company->name .' ' : '') }}
                                                                        @endif
                                                                    </div>
                                                                    <div class="time hidden-xs">
                                                                        {{ date('Y/m/d H:i:s', (int)$comment->date) }}
                                                                    </div>
                                                                    <div class="text">
                                                                        {{$comment->text}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="conversation-new-message" style="padding-left: 20px; width: 43em">
                                        <form method="post" id="send_message">
                                            <div class="form-group">
                                                <textarea class="form-control {{$hidden}}" rows="2" id="comment"
                                                          name="new_comment"
                                                          placeholder=" @lang('feedbacks.enter-message') ..."></textarea>
                                            </div>
                                            <div class="clearfix send">
                                                <input type="hidden" name="orderId" value="{{$feedback->order_id}}"
                                                       id="orderId">
                                                <input type="hidden" name="feedback_id" value="{{$feedback->id}}"
                                                       id="feedback_id">
                                                <div class="button-block">
                                                    <button type="submit" id="send_message"
                                                            class="btn btn-success center-block pull-rigth {{$hidden}} send_message ">
                                                        @lang('general.send')
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-center {{$closedFeedback}}" id="conversation"> @lang('feedbacks.closed')
                                            </div>
                                        </form>
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
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/mistakes.js') }}"></script>
@stop
