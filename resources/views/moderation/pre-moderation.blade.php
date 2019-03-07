@extends('layouts.app')

@section('title')@lang('orders.moderation')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/moderation.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/moderation/moderation.js') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/mistakes.js') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/create-ticket.js') }}"></script>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                                <li class="active"><span> @lang('orders.moderation')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('orders.moderation') (<span class="badge">{{$count}}</span>)</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <form method="POST" action="{{ route('pre-moderation') }}">
                <header class="main-box-header clearfix">
                    <h3 style="border-bottom: none;">
                        <span style="border-bottom: none;">
                            @php
                                $text = trans('general.search');
                                if (isset($_GET['grouping'])) {
                                    switch ($_GET['grouping']) {
                                        case 'incorrect_project' : {
                                            $text .= ' (' . trans('statuses.invalid-project') .')';
                                            break;
                                        }
                                        case 'repeat' : {
                                            $text .= ' (' . trans('statuses.repeat') .')';
                                            break;
                                        }
                                        case 'not-call' : {
                                            $text .= ' (' . trans('statuses.no-answer'). ')';
                                            break;
                                        }
                                        case 'not-data' : {
                                            $text .= ' (' . trans('statuses.invalid-phone') .')';
                                            break;
                                        }
                                        case 'other-language' : {
                                            $text .= ' (' . trans('statuses.other-language') .')';
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            {{$text}}
                        </span>
                    </h3>
                    <div class="count-by-status">
                        <div class="col-md-4 col-sm-6"> @lang('statuses.repeat')(<span class="badge btn-default" data-id="4">0</span>)
                        </div>
                        <div class="col-md-4 col-sm-6"> @lang('statuses.no-answer')(<span class="badge label-not-call"
                                                                        data-id="5">0</span>)
                        </div>
                        <div class="col-md-4 col-sm-6"> @lang('statuses.invalid-phone')(<span class="badge label-not-data"
                                                                                  data-id="6">0</span>)
                        </div>
                        <div class="col-md-4 col-sm-6"> @lang('statuses.other-language')(<span class="badge label_other_language"
                                                                        data-id="7">0</span>)
                        </div>
                        <div class="col-md-4 col-sm-6"> @lang('statuses.invalid-project')(<span class="badge badge-danger"
                                                                        data-id="11">0</span>)
                        </div>
                    </div>
                </header>
                <div class="main-box clearfix"></div>
                <div class="main-box clearfix">
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter grouping'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.search')</div>
                        <div class="col-sm-11">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="id"> @lang('general.id')</label>
                                    <input type="text" class="form-control" id="id" name="id"
                                           value="@if (isset($_GET['id'])){{$_GET['id']}}@endif">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="company"> @lang('general.company')</label>
                                    <select name='company[]' id="company" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($companies->count())
                                            @foreach ($companies as $o)
                                                <option @if (isset($_GET['company'])  && $_GET['company'] == $o->id) selected
                                                        @endif value="{{ $o->id }}">{{ $o->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="offer"> @lang('general.offer')</label>
                                    <select name='offer[]' id="offer" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($offers_filter)
                                            @foreach ($offers_filter as $o)
                                                <option @if (isset($_GET['offer'])  && $_GET['offer'] == $o->id) selected
                                                        @endif value="{{ $o->id }}">{{ $o->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="project"> @lang('general.project')</label>
                                    <select name='project' id="project" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($projects)
                                            @foreach ($projects as $s)
                                                <option @if (isset($_GET['project']) && $_GET['project'] == $s->id) selected
                                                        @endif value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="sub_project"> @lang('general.subproject')</label>
                                    <select name='sub_project' id="sub_project" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($subProjects)
                                            @foreach ($subProjects as $s)
                                                <option @if (isset($_GET['sub_project']) && $_GET['sub_project'] == $s->id) selected
                                                        @endif value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="countries"> @lang('general.country')</label>
                                    <select name='country' id="countries" style="width: 100%">
                                        <option value=""> @lang('general.all')</option>
                                        @if ($country)
                                            @foreach ($country as $c)
                                                <option
                                                        @if (isset($_GET['country']) && $_GET['country'] == $c->code)
                                                        selected
                                                        @endif
                                                        value="{{ mb_strtolower($c->code) }}">
                                                    @lang('countries.' . $c->code)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter grouping'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.group-by')</div>
                        <div class="col-sm-12 col-md-11">
                            <div class="btn-group filter" data-toggle="buttons">
                                <label class="btn btn-default @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'repeat') active @endif @endif ">
                                    <input type="radio" name="grouping" value="repeat"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'repeat') checked @endif>
                                    @lang('statuses.repeat')</label>
                                <label class="btn label-not-call @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'not-call') active @endif @endif">
                                    <input type="radio" name="grouping" value="not-call"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'not-call') checked @endif>
                                    @lang('statuses.no-answer')</label>
                                <label class="btn label-not-data @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'not-data') active @endif @endif">
                                    <input type="radio" name="grouping" value="not-data"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'not-data') checked @endif>
                                    @lang('statuses.invalid-phone')</label>
                                <label class="btn label_other_language @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'other-language') active @endif @endif">
                                    <input type="radio" name="grouping" value="other-language"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'other-language') checked @endif>
                                    @lang('statuses.other-language')</label>
                                <label class="btn btn-danger @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'incorrect_project') active @endif @endif">
                                    <input type="radio" name="grouping" value="incorrect_project"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'incorrect_project') checked @endif>
                                    @lang('statuses.invalid-project')</label>
                                @if (isset($_GET['grouping']))
                                    <label class="btn btn-default"><input type="radio" name="grouping"
                                                                          value=""> @lang('general.all')</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="date_start"> @lang('general.date-from')</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" id="date_start_moder" type="text" data-toggle="tooltip"
                                           name="date_start"
                                           data-placement="bottom"
                                           value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : date('d.m.Y', time()) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="date_end"> @lang('general.date-to')</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" id="date_end_moder" type="text" data-toggle="tooltip"
                                           name="date_end"
                                           data-placement="bottom"
                                           value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : date('d.m.Y', time()) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                            <div class="input-group">
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
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                    <a href="{{ route('pre-moderation') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if ($orders)
                @foreach($orders as $order)
                    <div class="md-modal md-effect-15" id="modal-15_{{$order->id}}">
                        <div class="md-content">
                            <div class="modal-header">
                                <button class="md-close close">×</button>
                                <h4 class="modal-title" style="font-weight: bold; color: #414141">
                                    @lang('orders.order-recall') #{{$order->id}}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="tabs-wrapper">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab-failed-ticket_{{$order->id}}" data-toggle="tab"
                                                              aria-expanded="true"
                                                              style="color: #f4786e; border-top: 2px solid #f4786e;"> @lang('general.negative')</a>
                                        </li>
                                        <li class=""><a href="#tab-success-ticket_{{$order->id}}" data-toggle="tab"
                                                        aria-expanded="false"
                                                        style="color: #1ABC9C"> @lang('orders.positive')</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active in" id="tab-failed-ticket_{{$order->id}}">
                                            <form method="post" class="send-failed-ticket"
                                                  id="send-failed-ticket_{{$order->id}}">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <div class="form-group">
                                                            @if(!empty($order->company_name))
                                                                <label for="company_id"> @lang('general.company')</label>
                                                                <select class="form-control" name="company_id"
                                                                        id="company_id" disabled>
                                                                    <option selected
                                                                            value="{{$order->company_id}}"
                                                                    >{{$order->company_name}} </option>
                                                                </select>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        @if(!empty($order->operator_id))
                                                            <div class="form-group operators">
                                                                <label for="operator"> @lang('general.user')</label>
                                                                <select class="form-control" name="operator"
                                                                        id="operator" disabled>

                                                                    <option selected
                                                                            value="{{$order->operator_id}}">{{$order->operName . ' ' . $order->operSurname }}</option>
                                                                </select>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(!empty($operatorMistakes))
                                                    <div class="row">
                                                        <label> @lang('orders.select-type-mistake')</label>
                                                        <br>
                                                        <div class="col-sm-12">
                                                            @foreach($operatorMistakes as $mistake)
                                                                <div class="checkbox-nice">
                                                                    <input type="checkbox" name="mistakes[]" value="{{ $mistake->id }}" class="add_call_now"
                                                                           id={{$mistake->id.$order->id}}>
                                                                    <label for="{{$mistake->id.$order->id}}">{{$mistake->name}}</label>
                                                                    {{--{{ Form::checkbox('mistakes[]', $mistake->id, false, ['id' => $mistake->id]) }}--}}
                                                                    {{--{{ Form::label($mistake->id, $mistake->name) }}--}}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label for="comment"> @lang('orders.text-message')</label>
                                                    {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                                                </div>
                                                <div class="text-center">
                                                    {{Form::hidden('type', 'failed_call')}}
                                                    {{Form::submit(trans('general.create'), ['class' => 'btn btn-success'])}}
                                                </div>
                                                <input type="hidden" name="company_id"
                                                       value="{{!empty($order->company_id) ? $order->company_id : ''}}">
                                                <input type="hidden" name="set-order" value="{{$order->id}}">
                                                <input type="hidden" name="operator"
                                                       value="{{!empty($order->operator_id) ? $order->operator_id : ''}}">
                                            </form>
                                        </div>
                                        <div class="tab-pane fade" id="tab-success-ticket_{{$order->id}}">
                                            <form method="post" class="send-success-ticket"
                                                  id="send-success-ticket_{{$order->id}}">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        @if(!empty($order->company_name))
                                                            <label for="company_id"> @lang('general.company')</label>
                                                            <select class="form-control" name="company_id"
                                                                    id="company_id" disabled>
                                                                <option selected
                                                                        value="{{$order->company_id}}"
                                                                >{{$order->company_name}} </option>
                                                            </select>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    @if(!empty($order->operator_id))
                                                        <div class="form-group operators">
                                                            <label for="operator"> @lang('general.user')</label>
                                                            <select class="form-control" name="operator"
                                                                    id="operator" disabled>

                                                                <option selected
                                                                        value="{{$order->operator_id}}">{{$order->operName . ' ' . $order->operSurname }}</option>
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    <label for="comment"> @lang('orders.text-message')</label>
                                                    {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                                                </div>
                                                <div class="text-center">
                                                    {{Form::hidden('type', 'failed_call')}}
                                                    {{Form::submit(trans('general.create'), ['class' => 'btn btn-success'])}}
                                                </div>
                                                <input type="hidden" name="company_id"
                                                       value="{{!empty($order->company_id) ? $order->company_id : ''}}">
                                                <input type="hidden" name="set-order" value="{{$order->id}}">
                                                <input type="hidden" name="operator"
                                                       value="{{!empty($order->operator_id) ? $order->operator_id : ''}}">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (isset($order->orderParent->children) || isset($order->orderParent->target_status) && $order->orderParent==4 )
                    <!--Повтор-->
                        @if (isset($order->orderParent->children))
                            <div class="table-responsive one_order">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td class="label-default">

                                        </td>
                                        <td class="text-center">
                                            <img class="country_flag"
                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($order->orderParent->geo) . '.png') }}"/>
                                            <div class="crm_id">
                                                {{$order->orderParent->id}}
                                            </div>
                                        </td>
                                        <td class="info-data">
                                            <div class="value">
                                                @lang('general.client') : <span>{{ $order->orderParent->surname }} {{$order->orderParent->name}} {{$order->orderParent->middle}}</span>
                                            </div>
                                            <div class="value">
                                                @lang('general.phone') : <span>{{ $order->orderParent->phone}}</span>
                                            </div>
                                            <div class="value">
                                                @lang('general.date') : <span>{{ \Carbon\Carbon::parse($order->orderParent->time_created)->format('d/m/y H:i:s')}}</span>
                                            </div>
                                            <div class="value">
                                                @lang('general.processing-status') : @if (isset($statuses[$order->orderParent->proc_status])) <label class="label label-default" style="background-color: {{$statuses[$order->orderParent->proc_status]->color}};">{{$statuses[$order->orderParent->proc_status]->name}}</label> @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="ip">
                                                @lang('general.ip-address') : <span>{{ $order->orderParent->host }}</span>
                                                <img class="country_flag" style="margin:0 10px;"
                                                     src="{{ URL::asset('img/flags/' . mb_strtoupper($order->orderParent->geo) . '.png') }}"/>
                                                @if (isset($countries[$order->orderParent->geo]))
                                                    {{$countries[$order->orderParent->geo]->name}}
                                                @endif
                                            </div>
                                        </td>
                                        <td style="width: 22px">
                                            {{--<div class="check_all">Отметить все</div>--}}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('order', $order->orderParent->id) }}" class="btn btn-success">
                                                @lang('general.order')</a>
                                            {{--<a href="{{ route('order', $order->id) }}/" class="custom_btn">--}}
                                            {{--<i class="fa fa-long-arrow-right"></i>--}}
                                            {{--</a>--}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="position: relative;width: 22px">
                                            <div class="check_all">
                                                @lang('general.select-all')
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @foreach($order->orderParent->children as $child)
                                        <tr class="children_{{$child->id}}">
                                            <td>

                                            </td>
                                            <td class="text-center">
                                                <img class="country_flag"
                                                     src="{{ URL::asset('img/flags/' . mb_strtoupper($child->geo) . '.png') }}"/>
                                                <div class="crm_id">
                                                    {{$child->id}}
                                                </div>
                                            </td>
                                            <td class="info-data repeat">
                                                <div class="value">
                                                    @lang('general.client') :
                                                    <span>{{ $child->surname }} {{$child->name}} {{$child->middle}}</span>
                                                </div>
                                                <div class="value">
                                                    @lang('general.phone') : <span>{{ $child->phone}}</span>
                                                </div>
                                                <div class="value">
                                                    @lang('general.date') : <span>{{ \Carbon\Carbon::parse($child->time_created)->format('d/m/y H:i:s')}}</span>
                                                </div>
                                                <div class="value">
                                                    @lang('general.processing-status') : @if (isset($statuses[$child->proc_status])) <label class="label label-default" style="background-color: {{$statuses[$child->proc_status]->color}};">{{$statuses[$child->proc_status]->name}}</label> @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="ip">
                                                    <span class="badge btn-default"> @lang('general.repeat')</span>
                                                    @lang('general.ip-address') : <span>{{ $child->host}}</span>
                                                    <img class="country_flag" style="margin:0 10px;"
                                                         src="{{ URL::asset('img/flags/' . mb_strtoupper($child->geo) . '.png')  }}"/>
                                                    @if (isset($countries[$order->geo]))
                                                        {{$countries[$order->geo]->name}}
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="width: 22px">
                                                <div class="checkbox-nice">
                                                    <input type="checkbox" class="choose_repeat"
                                                           id="choose_repeat_{{$child->id}}" data-id="{{$child->id}}"
                                                           value="">
                                                    <label for="choose_repeat_{{$child->id}}"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('order', $child->id) }}" class="btn btn-success">
                                                    @lang('general.order')
                                                </a>
                                                <br>
                                                <a href="#" class="btn btn-success annul">
                                                    @lang('general.cancel')
                                                </a>
                                                <br>
                                                <a href="#" class="btn btn-success go_to_pbx">
                                                    @lang('orders.pass-to-call')
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($order->orderParent->proc_status == 4)
                            <div class="table-responsive one_order">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td class="label-default">

                                        </td>
                                        <td class="text-center">
                                            <img class="country_flag"
                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($order->orderParent->geo) . '.png') }}"/>
                                            <div class="crm_id">
                                                {{$order->orderParent->id}}
                                            </div>
                                        </td>
                                        <td class="info-data repeat">
                                            <div class="value">
                                                @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                            </div>
                                            <div class="value">
                                                @lang('general.phone') : <span>{{ $order->phone}}</span>
                                            </div>
                                            <div class="value">
                                                @lang('general.date') : <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                            </div>
                                            <div class="value">
                                                @lang('general.processing-status') : @if (isset($statuses[$order->orderParent->proc_status])) <label class="label label-default" style="background-color: {{$statuses[$order->orderParent->proc_status]->color}};">{{$statuses[$order->orderParent->proc_status]->name}}</label> @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="ip">
                                                <span class="badge btn-default">
                                                    @lang('general.repeat')
                                                </span>
                                                @lang('general.ip-address') : <span>{{ $order->host}}</span>
                                                <img class="country_flag"
                                                     src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}"/>
                                            </div>
                                        </td>
                                        <td>

                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('order', $child->id) }}" class="btn btn-success">
                                                @lang('general.order')
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @elseif($order->proc_status == 5)<!--Не дозвон-->
                    <div style="position: relative">
                        <div class="table-responsive one_order">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="label-not-call"></td>
                                    <td class="text-center">
                                        <img class="country_flag"
                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png')  }}"/>
                                        <div class="crm_id">
                                            {{$order->id}}
                                        </div>
                                    </td>
                                    <td class="info-data">
                                        <div class="value">
                                            @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.phone') : <span>{{ $order->phone}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.date') : <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <span class="badge label-not-call">
                                                @lang('statuses.no-answer')
                                            </span>
                                        </div>
                                        @lang('orders.quantity-calls') : <span class="badge badge-danger">{{$order->callCount}}</span>
                                    </td>
                                    <td style="overflow: hidden;">
                                        @lang('general.processing-status') : <br>
                                        @if ($order->calls)
                                            @foreach($order->calls as $type => $calls)
                                                @if ($type == 'Success')
                                                    <div style="position:relative;display: inline-block;margin-bottom: 4px">
                                                        <a href="#" class="pop">
                                                            <span class="label label-primary">{{$type}}
                                                                ({{count($calls)}})</span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title">{{$type}}</h3>
                                                            <div class="content">
                                                                @if ($calls)
                                                                    @foreach($calls as $call)
                                                                        <div>{{$call}}</div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                @elseif($type == 'Failure')
                                                    <div style="position:relative;display: inline-block;margin-bottom: 4px">
                                                        <a href="#" class="pop">
                                                            <span class="label label-danger">{{$type}}({{count($calls)}}
                                                                )</span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title">{{$type}}</h3>
                                                            <div class="content">
                                                                @if ($calls)
                                                                    @foreach($calls as $call)
                                                                        <div>{{$call}}</div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                @elseif($type == 'No Answer')
                                                    <div style="position:relative;display: inline-block;margin-bottom: 4px">
                                                        <a href="#" class="pop">
                                                            <span class="label label-default">{{$type}}
                                                                ({{count($calls)}})</span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title">{{$type}}</h3>
                                                            <div class="content">
                                                                @if ($calls)
                                                                    @foreach($calls as $call)
                                                                        <div>{{$call}}</div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                @elseif($type == 'ShortCall')
                                                    <div style="position:relative;display: inline-block;margin-bottom: 4px">
                                                        <a href="#" class="pop">
                                                            <span class="label label-warning">{{$type}}
                                                                ({{count($calls)}})</span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title">{{$type}}</h3>
                                                            <div class="content">
                                                                @if ($calls)
                                                                    @foreach($calls as $call)
                                                                        <div>{{$call}}</div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                @elseif($type == 'Abandoned')
                                                    <div style="position:relative;display: inline-block;margin-bottom: 4px">
                                                        <a href="#" class="pop">
                                                            <span class="label label-info">{{$type}} ({{count($calls)}}
                                                                )</span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title">{{$type}}</h3>
                                                            <div class="content">
                                                                @if ($calls)
                                                                    @foreach($calls as $call)
                                                                        <div>{{$call}}</div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                @else
                                                    <div style="position:relative;display: inline-block;margin-bottom: 4px">
                                                        <a href="#" class="pop">
                                                            <span class="label label-default">{{$type}}
                                                                ({{count($calls)}})</span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title">{{$type}}</h3>
                                                            <div class="content">
                                                                @if ($calls)
                                                                    @foreach($calls as $call)
                                                                        <div>{{$call}}</div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="md-modal md-effect-2" id="modal-2-{{$order->id}}">
                                            <div class="md-content">
                                                <div class="modal-header">
                                                    <button class="md-close close">×</button>
                                                    <h4 class="modal-title">
                                                        @lang('general.cancel')
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" class="form-horizontal">
                                                        <input type="hidden" value="{{$order->id}}" name="order_id">
                                                        @if ($order->options)
                                                            {{renderTargetForModeration(json_decode($order->options), $order->id)}}
                                                        @endif
                                                    </form>
                                                    <div class="error-messages">

                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary cancel_moderation">
                                                        @lang('general.cancel')
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="md-overlay"></div>
                                        <a href="{{ route('order', $order->id) }}" class="btn btn-success">
                                            @lang('general.order')
                                        </a>
                                        {{--<a href="{{ route('order', $order->id) }}/" class="custom_btn">--}}
                                        {{--<i class="fa fa-long-arrow-right" style="margin-bottom: 5px;"></i>--}}
                                        {{--</a>--}}
                                        <br>
                                        <a href="" class="btn btn-success set_not_calls_callback"
                                           data-id="{{$order->id}}">
                                            @lang('orders.pass-to-call')
                                        </a>
                                        {{--<a href="" class="custom_btn set_not_calls_callback" data-id="{{$order->id}}" style="margin-right: 3px;">--}}
                                        {{--<i class="fa  fa-phone"></i>--}}
                                        {{--</a>--}}

                                        @if ($order->options)
                                            <a class="md-trigger btn btn-warning mrg-b-lg"
                                               data-modal="modal-2-{{$order->id}}">
                                                @lang('general.cancel')
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="popups" style="display: none;"></div>
                    </div>
                    @elseif($order->proc_status == 6)<!--Не корректный номер-->
                    <div class="table-responsive one_order">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="label-not-data"></td>
                                <td class="text-center">
                                    <img class="country_flag"
                                         src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}"/>
                                    <div class="crm_id">
                                        {{$order->id}}
                                    </div>
                                </td>
                                <td class="info-data">
                                    <div class="value">
                                        @lang('general.offer') : <span>{{ $order->offer }} - <span
                                                    class="price">(Цена {{$order->price}})</span> </span>
                                    </div>
                                    <div class="value">
                                        @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('general.phone') : <span>{{ $order->phone}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('general.date') : <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center" style="padding-bottom: 5px">
                                        <span class="badge label-not-data">
                                            @lang('statuses.invalid-phone')
                                        </span>
                                    </div>
                                    <input name="price" class="form-control price_order_incorrect"
                                           value="{{$order->price}}">
                                    <select name="country" class="form-control country">
                                        @foreach ($countries as $oc)
                                            <option data-currency="{{ $oc->currency }}" value="{{ mb_strtolower($oc->code) }}"
                                                    @if ($oc->code == $order->geo) selected @endif>
                                                @lang('countries.' . $oc->code)
                                            </option>
                                        @endforeach
                                    </select>
                                    <input class="form-control order_one_input phone" value="{{$order->phone}}"
                                           placeholder=" @lang('general.phone')">
                                </td>
                                <td class="text-center">
                                    <div class="md-modal md-effect-2" id="modal-2-{{$order->id}}">
                                        <div class="md-content">
                                            <div class="modal-header">
                                                <button class="md-close close">×</button>
                                                <h4 class="modal-title">
                                                    @lang('general.cancel')
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <form role="form" class="form-horizontal">
                                                    <input type="hidden" value="{{$order->id}}" name="order_id">
                                                    @if ($order->options)
                                                        {{renderTargetForModeration(json_decode($order->options), $order->id)}}
                                                    @endif
                                                </form>
                                                <div class="error-messages">

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary cancel_moderation">
                                                    @lang('general.cancel')
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="md-overlay"></div>
                                    <a href="{{ route('order', $order->id) }}" class="btn btn-success">
                                        @lang('general.order')
                                    </a>
                                    <a href="" class="btn btn-success change_phone" data-id="{{$order->id}}">
                                        @lang('general.save')
                                    </a>
                                    @if ($order->options)
                                        <a class="md-trigger btn btn-warning mrg-b-lg"
                                           data-modal="modal-2-{{$order->id}}">
                                            @lang('general.cancel')
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    @elseif($order->proc_status == 7)<!--Иноязычные-->
                    <div class="table-responsive one_order">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="label_other_language"></td>
                                <td class="text-center">
                                    <img class="country_flag"
                                         src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}"/>
                                    <div class="crm_id">
                                        {{$order->id}}
                                    </div>
                                </td>
                                <td class="info-data">
                                    <div class="value">
                                        @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('general.phone') : <span>{{ $order->phone}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('general.date') : <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <div class="text-center" style="padding-bottom: 5px">
                                        <span class="badge label_other_language">
                                            @lang('orders.other-language')
                                        </span>
                                    </div>
                                    @if ($campaigns)
                                        <select name="campaign" class="form-control campaign" style="width: 100%">
                                            @foreach($campaigns as $campaign)
                                                <option value="{{$campaign->id}}"
                                                        @if ($campaign->id == $order->proc_campaign) selected @endif>{{$campaign->name}}</option>
                                            @endforeach
                                        </select>
                                    @endif

                                </td>
                                <td class="text-center">
                                    <a href="{{ route('order', $order->id) }}" class="btn btn-success">
                                        @lang('general.order')
                                    </a>
                                    <br>
                                    <a href="" class="btn btn-success change_campaign" data-id="{{$order->id}}">
                                        @lang('general.save')
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    @elseif($order->proc_status == 11)
                        <div class="table-responsive one_order">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="label-danger"></td>
                                    <td class="text-center">
                                        <img class="country_flag"
                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}"/>
                                        <div class="crm_id">
                                            {{$order->id}}
                                        </div>
                                    </td>
                                    <td class="info-data">
                                        <div class="value">
                                            @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.phone') : <span>{{ $order->phone}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.project') : <span>{{ $order->project_name}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.subproject') : <span>{{ $order->sub_project_name}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.date') : <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        {{renderInputDataForModeration(json_decode($order->input_data))}}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('order', $order->id) }}" class="btn btn-success">
                                            @lang('general.order')
                                        </a>
                                        <br>
                                        @if (isset($permissions['order-change-project']))
                                        <a href="{{ route('orders-change-project', $order->id) }}" class="btn btn-success">
                                            @lang('orders.change-project')
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                @endforeach

            @endif
            @if ($pagination && count($pagination[0]) > 1)
                <ul class="pagination pull-right" style="margin-bottom: 20px;">
                    <li><a href="{{ route('pre-moderation') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}"><i
                                    class="fa fa-chevron-left"></i></a></li>
                    @foreach ($pagination[0] as $number)
                        <? $activaPage = '' ?>
                        @if ($pagination[1] == $number)
                            <li class=active><span>{{ $number }}</span></li>
                        @else
                            @if ($number == 1)
                                <li>
                                    <a href="{{ route('pre-moderation') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}">{{ $number }}</a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('pre-moderation') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $number : '?page=' . $number }}">{{ $number }}</a>
                                </li>
                            @endif
                        @endif
                    @endforeach
                    <li>
                        <a href="{{ route('pre-moderation') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $pagination[2] : '?page=' . $pagination[2] }}"><i
                                    class="fa fa-chevron-right"></i></a></li>
                </ul>
            @endif
            <div class="md-overlay"></div>
        </div>
    </div>

@stop
