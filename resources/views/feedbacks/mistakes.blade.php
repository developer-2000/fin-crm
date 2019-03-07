@extends('layouts.app')

@section('title') @lang('feedbacks.operator-errors') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        body {
            color: #929292;
        }

        .comment-time {
            text-align: right;
            font-size: 10px;
            color: rgba(142, 145, 147, 0.89);
        }

        .name {
            text-align: left;
        }

        .unread {
            background-color: rgba(38, 157, 73, 0.18) !important;
        }
        .ns-box{
        z-index:  5000
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nanoscroller.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/pace.min.js') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/mistakes.js') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/create-ticket.js') }}"></script>
@stop
@section('content')

    <div class="md-modal md-effect-15" id="modal-15">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title"> @lang('feedbacks.create-ticket')</h4>
            </div>
            <div class="modal-body">
                <div class="tabs-wrapper">
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab-failed-ticket">
                            <form role="form" id="send-info-fault-ticket">
                                @if(auth()->user()->role_id != 1)
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="form-group">
                                            @if(!empty($companies))
                                                <label for="company_id"> @lang('general.select')</label>
                                                <select class="form-control" name="company_id" id="company_id">
                                                    <option value=""> @lang('general.select-company')</option>
                                                    @foreach($companies as $key=>$company)
                                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                                        <span class="company_type"
                                                              style="display:none">{{$company->type}}</span>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        @if(!empty($operators))
                                            <div class="form-group operators">
                                                <label for="operator"> @lang('general.select-operator')</label>
                                                <select required class="form-control" name="operator"
                                                        id="operator">
                                                    @if(!empty($operators))
                                                        <option value=""> @lang('general.select-operator')</option>
                                                        @foreach($operators as $key=>$operator)
                                                            <option
                                                                    value="{{$operator->id}}">{{$operator->name . ' ' . $operator->surname }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <div class="pull-right">
                                            <div class="radio radio-inline" style="top:-0.6em">
                                                {{ Form::radio('ticket-type', 'info', true, ['id' => 'info']) }}
                                                {{ Form::label('info','Информационный') }}
                                            </div>
                                            <div class="radio radio-inline" style="top:-0.3em">
                                                {{ Form::radio('ticket-type', 'fault', false, ['id' => 'fault']) }}
                                                {{ Form::label('fault','Предьявительный') }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{--<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">--}}
                                        {{--<div class="checkbox-nice" style="top:-0.6em">--}}
                                            {{--{{ Form::checkbox('send_all', 'send_all', true, ['id' => 'send_all']) }}--}}
                                            {{--{{ Form::label('send_all','Отправить всем') }}--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{Form::hidden('ticket-type', 'info')}}
                                @endif
                                <div class="form-group">
                                    <label for="title"> @lang('feedbacks.theme-message')</label>
                                    {{ Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) }}
                                    <label for="comment"> @lang('feedbacks.text-message')</label>
                                    {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                                </div>
                                <div class="text-center">
                                    {{Form::submit('Создать', ['class' => 'btn btn-success'])}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('feedbacks.operator-errors')</span></li>
            </ol>
            <h1 class="pull-left"> @lang('feedbacks.operator-errors')</h1>
            <div class="pull-right">
                @if(isset($permissions['ticket_create']))
                    <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-15"> @lang('feedbacks.create-ticket')</button>
                @endif
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{$_SERVER['REQUEST_URI'] }}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="id" class="col-sm-4 control-label"> @lang('general.id')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="id" name="id"
                                                   value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                        </div>

                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="oid" class="col-sm-4 control-label"> @lang('general.oid')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="oid" name="oid"
                                                   value="@if (isset($_GET['oid'])){{ $_GET['oid'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="company" class="col-sm-4 control-label"> @lang('general.company')</label>
                                        <div class="col-sm-8">
                                            <select id="company" name="company[]" style="width: 100%" multiple>
                                                @foreach ($companies as $company)
                                                    <option
                                                            @if (isset($_GET['company']))
                                                            <? $companyGet = explode(',', $_GET['company']); ?>
                                                            @foreach ($companyGet as $cg)
                                                            @if ($company->id == $cg)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="{{ $company->id }}">{{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="user" class="col-sm-4 control-label"> @lang('general.operator')</label>
                                        <div class="col-sm-8">
                                            <select id="user" name="user[]" style="width: 100%" multiple>
                                                @if ($users)
                                                    @foreach ($users as $user)
                                                        <option
                                                                @if (isset($_GET['user']))
                                                                <? $usersGet = explode(',', $_GET['user']); ?>
                                                                @foreach ($usersGet as $usg)
                                                                @if ($user->id == $usg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $user->id }}">{{ $user->name . ' ' . $user->surname }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="user" class="col-sm-4 control-label"> @lang('general.moderator')</label>
                                        <div class="col-sm-8">
                                            <select id="moderator" name="moderator[]" style="width: 100%" multiple>
                                                @if ($moderators)
                                                    @foreach ($moderators as $moderator)
                                                        <option
                                                                @if (isset($_GET['moderator']))
                                                                <? $moderatorsGet = explode(',', $_GET['moderator']); ?>
                                                                @foreach ($moderatorsGet as $usg)
                                                                @if ($moderator->id == $usg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $moderator->id }}">{{ $moderator->name . ' ' . $moderator->surname }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="status" class="col-sm-4 control-label"> @lang('general.status')</label>
                                        <div class="col-sm-8">
                                            <select id="status" name="status[]"
                                                    style="width: 100%">
                                                <option value="opened" selected> @lang('general.opened')</option>
                                                <?
                                                $feedbackStatus = [
                                                    'Все' => registration_trans('general.all'),
                                                    'closed' => registration_trans('general.closed'),
                                                ];
                                                ?>
                                                @if ($feedbackStatus)
                                                    @foreach ($feedbackStatus as $key => $status)

                                                        <option
                                                                @if (isset($_GET['status']))
                                                                <? $statusGet = explode(',', $_GET['status']); ?>
                                                                @foreach ($statusGet as $stg)
                                                                @if ($key == $stg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $key }}">{{ $status }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="status" class="col-sm-4 control-label"> @lang('general.type-error')</label>
                                        <div class="col-sm-8">
                                            <select id="mistake_type" name="mistake_type[]"
                                                    style="width: 100%" multiple>
                                                @if ($operatorMistakes)
                                                    @foreach ($operatorMistakes as $key => $mistake)

                                                        <option
                                                                @if (isset($_GET['mistake_type']))
                                                                <? $mistakeGet = explode(',', $_GET['mistake_type']); ?>
                                                                @foreach ($mistakeGet as $stg)
                                                                @if ($mistake->id == $stg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $mistake->id }}">{{$mistake->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='main-box-body clearfix section_filter'>
                            <div class='main-box-body clearfix'>
                            </div>
                            <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="date_start">С</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="date_start" type="text" data-toggle="tooltip"
                                               name="date_start"
                                               data-placement="bottom"
                                               value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="date_end"> @lang('general.to')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="date_end" type="text" data-toggle="tooltip"
                                               name="date_end"
                                               data-placement="bottom"
                                               value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                    </div>
                                </div>
                            </div>
                            {{--<div class="col-sm-2">--}}
                            {{--<div class="btn-group date_type" data-toggle="buttons">--}}
                            {{--<div>Тип</div>--}}
                            {{--<label class="btn btn-primary @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1) || !isset($_GET['date-type'])) active @endif" id="time_created"  data-toggle="tooltip"--}}
                            {{--data-placement="bottom" title="Дата создания">--}}
                            {{--<input type="radio" name="date-type" value="1"  @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1) || !isset($_GET['date-type'])) checked @endif> <i class="fa fa-calendar"></i>--}}
                            {{--</label>--}}
                            {{--<label class="btn btn-primary @if (isset($_GET['date-type']) && $_GET['date-type'] == 3) active @endif" id="time_modified"  data-toggle="tooltip"--}}
                            {{--data-placement="bottom" title="Дата установки цели">--}}
                            {{--<input type="radio" name="date-type" value="3" @if (isset($_GET['date-type']) && $_GET['date-type'] == 3) checked @endif><i class="fa fa-star-half-empty"></i>--}}
                            {{--</label>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                                <div class="input-group" style="padding-top: 7px">
                                    <div class="btn-group" data-toggle="buttons" id="date_template">
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="1"> @lang('general.today')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="5"> @lang('general.yesterday')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="9"> @lang('general.week')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="10"> @lang('general.month')
                                        </label>
                                        <label class="btn btn-default pattern_date">
                                            <input type="radio" name="date_template" value="2"> @lang('general.last-month')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                        <a href="{{ route('operator-mistakes') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        @if(count($feedbacks))
                            <div class="table-responsive">
                                <table id="feedbacks" class="table table-striped table-hover">
                                    <thead>
                                    <tr style=" font-weight: bold; color: rgba(34,34,34,0.84)">
                                        <th class="text-center"> @lang('general.id')</th>
                                        <th class="text-center"> @lang('general.date-created')</th>
                                        <th class="text-center"> @lang('general.order')</th>
                                        <th class="text-center"> @lang('general.operator')/@lang('general.company')</th>
                                        <th class="text-center"> @lang('general.initiator')</th>
                                        <th class="text-center"> @lang('feedbacks.theme-ticket')/@lang('general.errors')</th>
                                        <th class="text-center"> @lang('general.status')</th>
                                        <th class="text-center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($feedbacks as $feedback)
                                        @if(!empty($feedback->moderator))
                                            @php
                                                $class = '';
                                                    if(auth()->user()->id == $feedback->user_id && $feedback->read == 2 || auth()->user()->id == $feedback->moderator_id && $feedback->read == 1 ){
                                                        $class = 'unread';
                                                    }
                                            @endphp
                                            <tr class="{{$class}}">
                                                <td class="text-center">
                                                    {{$feedback->id}}
                                                </td>
                                                <td class="text-center">
                                                    {{$feedback->created_at}}
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($feedback->order_id))
                                                        <a href="{{ route('order', $feedback->order_id) }}"
                                                           class="crm_id">{{$feedback->order_id}}</a>
                                                    @else
                                                        {{'N\A'}}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($feedback->user->id))
                                                    <div style=" border-bottom: 2px solid #ebebeb; padding-bottom: 5px">
                                                        <a href="{{ route('users-edit', $feedback->user->id) }}"
                                                        >{{$feedback->user->name.'  '.$feedback->user->surname}}</a>
                                                    </div>
                                                        @else
                                                        {{'N/A'}}
                                                    @endif
                                                    @if(!empty($feedback->user->company->name))
                                                        <div style="padding-top: 7px; padding-bottom: 7px; font-weight: bold; color: rgba(41,41,41,0.84)">{{$feedback->user->company->name}}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($feedback->moderator))
                                                        {{$feedback->moderator->name. '  ' .$feedback->moderator->surname}}
                                                    @endif
                                                </td>
                                                {{--<td class="text-center">--}}
                                                {{--{{'Mentor'}}--}}
                                                {{--</td>--}}
                                                <td>
                                                    @if(!empty($feedback->mistakes))
                                                    @foreach($feedback->mistakes as $mistake)
                                                    {{$mistake->name}}<br>
                                                    @endforeach
                                                        @elseif(!empty($feedback->title))
                                                        <span style="color: #383838; font-weight: bold">{{$feedback->title}}</span>
                                                    @else
                                                        {{'N/A'}}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div style="border-bottom: 2px solid #ebebeb; padding-bottom: 15px">
                                                        @if($feedback->status == 'opened')
                                                            <span id="activity"
                                                                  class="label label-success">Opened</span>
                                                        @elseif($feedback->status == 'closed')
                                                            <span id="activity" class="label label-danger">Closed</span>
                                                        @endif
                                                    </div>
                                                    <div style="color: #868b98">
                                                        @if(!empty($feedback->user_id) && !empty($feedback->user->name) && $feedback->moderator_id && $feedback->read == 2 )
                                                            @lang('feedbacks.awaiting-response'):
                                                            <br> {{$feedback->user->name.' '.$feedback->user->surname}}
                                                        @elseif(!empty($feedback->user_id) && $feedback->read == 1)
                                                            @lang('feedbacks.awaiting-response'):
                                                            <br>  {{$feedback->moderator->name.' '.$feedback->moderator->surname}}
                                                        @elseif(!empty($feedback->user_id) && auth()->user()->id == $feedback->user_id && $feedback->read == 2)
                                                            @lang('feedbacks.awaiting-your-response'):
                                                        @elseif(auth()->user()->id == $feedback->moderator_id && $feedback->read == 1 )
                                                            @lang('feedbacks.awaiting-your-response'):
                                                        @elseif($feedback->status == 'closed')
                                                            @lang('feedbacks.discussion-completed'):
                                                        @else
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{route('feedback', $feedback->id)}}"
                                                       class="table-link">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
                {{$feedbacks->links()}}
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
@stop
