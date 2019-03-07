@extends('layouts.app')

@section('title') @lang('orders.moderation') @stop

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
            <form method="POST" action="{{ route('moderation') }}">
                <header class="main-box-header clearfix">
                    <h3 style="border-bottom: none;">
                        <span style="border-bottom: none;">
                            @php
                                $text = trans('general.search');
                                if (isset($_GET['grouping'])) {
                                    switch ($_GET['grouping']) {
                                        case 'approve' : {
                                            $text .= ' (' . trans('general.approved') . ')';
                                            break;
                                        }
                                        case 'failure' : {
                                            $text .= ' (' . trans('general.refusal') . ')';
                                            break;
                                        }
                                        case 'cancel' : {
                                            $text .= ' (' . trans('general.cancel') . ')';
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            {{$text}}
                        </span>
                    </h3>
                    <div class="count-by-status">
                        <div class="col-md-4 col-sm-6">
                            @lang('general.approved')(<span class="badge badge-success" data-id="1">0</span>)
                        </div>
                        <div class="col-md-4 col-sm-6">
                            @lang('general.refusal')(<span class="badge badge-danger" data-id="2">0</span>)
                        </div>
                        <div class="col-md-4 col-sm-6"> @lang('general.cancel')(<span class="badge badge-warning"
                                                                            data-id="3">0</span>)
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
                                <label class="btn btn-success @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'approve') active @endif @endif">
                                    <input type="radio" name="grouping" value="approve"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'approve') checked @endif>
                                    @lang('general.approved')</label>
                                <label class="btn btn-danger @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'failure') active @endif @endif">
                                    <input type="radio" name="grouping" value="failure"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'failure') checked @endif>
                                    @lang('general.refusal')</label>
                                <label class="btn btn-warning @if(isset($_GET['grouping']))@if($_GET['grouping'] == 'cancel') active @endif @endif">
                                    <input type="radio" name="grouping" value="cancel"
                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == 'cancel') checked @endif>
                                    @lang('general.cancel')</label>
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
                    <a href="{{ route('moderation') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
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
                                    @lang('orders.recall-order') #{{$order->id}}</h4>
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
                    @if ($order->target_status == 1 & !isset($order->children))
                        @php
                            $currency = isset($countries[$order->geo]) ? $countries[$order->geo]->currency : '';
                        @endphp
                        <div class="table-responsive one_order">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="label-success text-center">
                                    </td>
                                    <td class="text-center">
                                        <img class="country_flag"
                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}"/>
                                        <div class="crm_id">
                                            {{$order->id}}
                                        </div>
                                    </td>
                                    <td class="info-data">
                                        <div class="value">
                                            @lang('general.offer') : <span>{{ $order->offer }}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.ip-address') : <span>{{ $order->host}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.operator') : <span>{{ $order->operSurname }} {{$order->operName}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.date-created') :
                                            <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('orders.date-target') :
                                            <span>{{ \Carbon\Carbon::parse($order->time_modified)->format('d/m/y H:i:s')}}</span>
                                        </div>
                                    </td>
                                    <td style="width: 60%;">
                                        <div style="padding-bottom: 5px;" class="text-center">
                                            <span class="badge badge-success"> @lang('general.approved')</span>
                                        </div>
                                        <table class="info_table">
                                            <tr>
                                                <td></td>
                                                <td class="text-center"> @lang('general.up-sell')</td>
                                                <td class="text-center"> @lang('general.up-sell') 2</td>
                                                <td class="text-center"> @lang('general.cross-sell')</td>
                                                <td class="text-center"> @lang('general.cross-sell') 2</td>
                                                <td class="text-center"> @lang('general.price')</td>
                                            </tr>
                                            @forelse($order->products as $product)
                                                <tr @if ($product->disabled) class="warning" @endif>
                                                    <td >
                                                        {{$product->title}}
                                                    </td>
                                                    @if ($product->disabled || !$product->type)
                                                        <td colspan="4" ></td>
                                                    @else
                                                        <td   class="text-center" >
                                                            <div class="checkbox-nice" style="display: inline-block;">
                                                                <input type="checkbox"
                                                                       name="up_sell"
                                                                       id="type_up1_{{$product->id}}"
                                                                       value="{{$product->id}}"
                                                                       class="product_type"
                                                                       @if($product->type == 1)
                                                                       checked
                                                                        @endif
                                                                >
                                                                <label for="type_up1_{{$product->id}}"></label>
                                                            </div>
                                                        </td>
                                                        <td  class="text-center">
                                                            <div class="checkbox-nice" style="display: inline-block;">
                                                                <input type="checkbox"
                                                                       name="up_sell_2"
                                                                       id="type_up2_{{$product->id}}"
                                                                       value="{{$product->id}}"
                                                                       class="product_type"
                                                                       @if($product->type == 2)
                                                                       checked
                                                                        @endif
                                                                >
                                                                <label for="type_up2_{{$product->id}}"></label>
                                                            </div>
                                                        </td>
                                                        <td  class="text-center">
                                                            <div class="checkbox-nice" style="display: inline-block;">
                                                                <input type="checkbox"
                                                                       name="cross_sell"
                                                                       id="type_cross_{{$product->id}}"
                                                                       value="{{$product->id}}"
                                                                       class="product_type"
                                                                       @if($product->type == 4)
                                                                       checked
                                                                        @endif
                                                                >
                                                                <label for="type_cross_{{$product->id}}"></label>
                                                            </div>
                                                        </td>
                                                        <td  class="text-center">
                                                            <div class="checkbox-nice" style="display: inline-block;">
                                                                <input type="checkbox"
                                                                       name="cross_sell_2"
                                                                       id="type_cross_2_{{$product->id}}"
                                                                       value="{{$product->id}}"
                                                                       class="product_type"
                                                                       @if($product->type == 5)
                                                                       checked
                                                                        @endif
                                                                >
                                                                <label for="type_cross_2_{{$product->id}}"></label>
                                                            </div>
                                                        </td>
                                                    @endif
                                                    <td class="text-center">{{$product->price}} {{$currency}}</td>
                                                </tr>
                                            @empty
                                            @endforelse
                                            <tr>
                                                <td><b> @lang('general.total')</b></td>
                                                <td colspan="3"></td>
                                                <td class="text-center"  ><span class="price"> <b>{{ $order->price}} {{$currency}}</b></span></td>
                                            </tr>
                                        </table>
                                        <table class="info_table">
                                            <tr>
                                                <td><b> @lang('general.delivery')</b></td>
                                                <td >
                                                    @if (isset($targets[$order->id]['name']))
                                                        <b>{{$targets[$order->id]['name']}}</b>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if (isset($targets[$order->id]['name']))
                                                @foreach($targets[$order->id] as $key => $field)
                                                    @if ($key != 'name')
                                                        <tr>
                                                            <td>{{$field['title']}}: </td>
                                                            <td  class="target">
                                                                <div class="cause target_value" style="display: block">
                                                                    @forelse($field['value'] as $k => $value)
                                                                        {{$value}}
                                                                        @if (count($field['value']) != $k + 1)
                                                                            ,
                                                                        @endif
                                                                    @empty
                                                                    @endforelse
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </table>
                                        <table class="info_table">
                                            <tr>
                                                <td> <b> @lang('general.record') </b> :</td>
                                                <td >@if (!$order->records) @lang('general.missing') @endif </td>
                                            </tr>
                                            @if ($order->records)
                                                <tr>
                                                    <td ></td>
                                                    <td >
                                                        @foreach($order->records as $record)
                                                            <div>
                                                                <div>{{$record->surname}} {{$record->name}}</div>
                                                                <?
                                                                $url = route('get-call-by-name') . '?fileName=' . $record->file;
                                                                $agent = $_SERVER['HTTP_USER_AGENT'];
                                                                if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                                    $output = '<a href="' . $url . '"><span class="fa-stack">
                                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                                            </span></a>';
                                                                } else {
                                                                    $output = '<audio controls><source src="' . $url . '" type="audio/mpeg"></audio>';
                                                                }
                                                                echo $output?>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                        <table class="info_table">
                                            <tr>
                                                <td> @lang('orders.select-sender')</td>
                                                <td>
                                                    @if ($order->storages->isNotEmpty())
                                                        <select class="form-control" name="sub_project_id">
                                                            <option></option>
                                                            @foreach($order->storages as $storage)
                                                                <option value="{{$storage->id}}"
                                                                        @if ($storage->id == $order->subproject_id) selected @endif>
                                                                    {{$storage->name}}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="info_table">
                                            <tr>
                                                <td> @lang('orders.select-operator')</td>
                                                <td>
                                                    {{--@if (count($order->ordersOpened))--}}
                                                        {{--<select class="form-control" name="assigned_operator">--}}
                                                            {{--@foreach($order->ordersOpened as $orderOpened)--}}
                                                                {{--<option value="{{$orderOpened->user_id}}"--}}
                                                                        {{--@if ($order->target_user == $orderOpened->user_id) selected @endif>--}}
                                                                    {{--{{$orderOpened->name .' ' . $orderOpened->surname }}--}}
                                                                {{--</option>--}}
                                                            {{--@endforeach--}}
                                                        {{--</select>--}}
                                                    {{--@endif--}}
                                                    <input class="assigned_operator" name="assigned_operator"
                                                           style="width: 100%" data-content="{{json_encode(['id' => $order->target_user, 'text' => $order->operSurname . ' ' . $order->operName])}}">
                                                </td>
                                            </tr>
                                        </table>
                                        <a href="#" data-id="{{$order->id}}" class="btn btn-success moderation"> @lang('general.moderate')</a>
                                        <a href="{{ route('order', $order->id) }}" class="btn btn-default"> @lang('general.order')</a>
                                    </td>
                                    {{--<td class="text-center">--}}
                                    {{--<a href="{{ route('order', $order->id) }}" class="btn btn-success">Перейти к--}}
                                    {{--заказу</a>--}}
                                    {{--<br>--}}
                                    {{--<select id="sender" name="sender" class="form-control" style="margin-bottom: 10px;">--}}
                                    {{--<option value="">Выберите отправтеля</option>--}}
                                    {{--</select>--}}
                                    {{--<a href="#" data-id="{{$order->id}}" class="btn btn-success moderation">Промодерировать</a>--}}
                                    {{--<br>--}}

                                    {{--@if(isset($permissions['feedback_add']) && !empty($order->company_id))--}}
                                    {{--@if(empty($order->feedback_id))--}}
                                    {{--<div class="feedback" id="feedback_order_{{$order->id}}">--}}
                                    {{--<button class="md-trigger btn btn-primary mrg-b-lg leave-feedback"--}}
                                    {{--data-id="{{$order->id}}" data-modal="modal-15_{{$order->id}}">--}}
                                    {{--Оставить отзыв--}}
                                    {{--</button>--}}
                                    {{--</div>--}}
                                    {{--@else--}}
                                    {{--<span style="padding-left: 15px; color: #929292">{{'Отзыв оставлен :'}}--}}
                                    {{--. <br>. {{$order->feedback_created_at}}</span>--}}
                                    {{--<br>--}}
                                    {{--<a href="{{ route('feedback-show', $order->feedback_id) }}">Перейти к отзыву</a>--}}
                                    {{--@endif--}}
                                    {{--@endif--}}
                                    {{--</td>--}}
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @elseif ($order->target_status == 2 && !isset($order->children))<!--Отказ-->
                    <div class="table-responsive one_order">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="label-danger text-center">

                                </td>
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
                                                    class="price">(@lang('general.price') {{$order->price}})</span> </span>
                                    </div>
                                    <div class="value">
                                        @lang('general.ip-address') : <span>{{ $order->host}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('general.operator') : <span>{{ $order->operSurname }} {{$order->operName}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('general.date-created') :
                                        <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                    </div>
                                    <div class="value">
                                        @lang('orders.date-target') :
                                        <span>{{ \Carbon\Carbon::parse($order->time_modified)->format('d/m/y H:i:s')}}</span>
                                    </div>
                                </td>
                                <td style="width: 12%;">
                                    <div>
                                        <span class="badge badge-danger"> @lang('general.refusal')</span>
                                    </div>
                                    @if (isset($targets[$order->id]))
                                        @foreach($targets[$order->id] as $key => $field)
                                            @if ($key != 'name')
                                                <label>{{$field['title']}}: </label>
                                                <div class="cause">
                                                    @forelse($field['value'] as $k => $value)
                                                        {{$value}}
                                                        @if (count($field['value']) != $k + 1)
                                                            ,
                                                        @endif
                                                    @empty
                                                    @endforelse
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div style="display: inline-block">
                                        <div class="text-left"> @lang('general.record') :</div>
                                        @if ($order->records)
                                            @foreach($order->records as $record)
                                                <div>
                                                    <div>{{$record->surname}} {{$record->name}}</div>
                                                    <?
                                                    $url = route('get-call-by-name') . '?fileName=' . $record->file;
                                                    $agent = $_SERVER['HTTP_USER_AGENT'];
                                                    if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                    $output = '<a href="' . $url . '"><span class="fa-stack">
                                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                                            </span></a>';
                                                    } else {
                                                    $output = '<audio controls><source src="' . $url . '" type="audio/mpeg"></audio>';
                                                    }
                                                    echo $output?>
                                                </div>
                                            @endforeach
                                        @else
                                            @lang('general.missing')
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('order', $order->id) }}" class="btn btn-success">
                                        @lang('general.order')
                                    </a>
                                    {{--@if (count($order->ordersOpened))--}}
                                    <input class="assigned_operator" name="assigned_operator"
                                           style="width: 100%" data-content="{{json_encode(['id' => $order->target_user, 'text' => $order->operSurname . ' ' . $order->operName])}}">
                                        {{--<div class="form-group">--}}
                                            {{--<label>@lang('orders.select-operator')</label>--}}
                                            {{--<select class="form-control" name="assigned_operator">--}}
                                                {{--@foreach($order->ordersOpened as $orderOpened)--}}
                                                    {{--<option value="{{$orderOpened->user_id}}"--}}
                                                            {{--@if ($order->target_user == $orderOpened->user_id) selected @endif>--}}
                                                        {{--{{$orderOpened->name .' ' . $orderOpened->surname }}--}}
                                                    {{--</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--@endif--}}
                                    <br>
                                    <a href="#" data-id="{{$order->id}}" class="btn btn-success moderation"> @lang('general.moderate')</a>
                                    <br>

                                    @if(isset($permissions['feedback_add']) && !empty($order->company_id))
                                        @if(empty($order->feedback_id))
                                            <div class="feedback" id="feedback_order_{{$order->id}}">
                                                <button class="md-trigger btn btn-primary mrg-b-lg leave-feedback"
                                                        data-id="{{$order->id}}" data-modal="modal-15_{{$order->id}}">
                                                    @lang('orders.add-feedback')
                                                </button>
                                            </div>
                                        @else
                                            <span style="padding-left: 15px; color: #929292"> @lang('orders.feedback-added')
                                                . <br>. {{$order->feedback_created_at}}</span>
                                            <br>
                                            <a href="{{ route('feedback', $order->feedback_id) }}"> @lang('general.order')</a>
                                        @endif
                                    @endif


                                    {{--<a href="{{ route('order', $order->id) }}/" class="custom_btn">--}}
                                    {{--<i class="fa fa-long-arrow-right" style="margin-bottom: 5px"></i>--}}
                                    {{--</a>--}}
                                    {{--<br>--}}
                                    {{--<a href="#" class="custom_btn moderation" data-id="{{$order->id}}" data-toggle="tooltip"--}}
                                    {{--data-placement="bottom" title="Модерация"--}}
                                    {{--style="margin-right: 3px;">--}}
                                    {{--<i class="fa fa-check"></i>--}}
                                    {{--</a>--}}
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>

                    @elseif($order->target_status == 3 && !isset($order->children))

                    <!--Аннулирован-->
                        <div class="table-responsive one_order">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="label-warning">

                                    </td>
                                    <td class="text-center">
                                        <img class="country_flag"
                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png')  }}"/>
                                        <div class="crm_id">
                                            {{$order->id}}
                                        </div>
                                    </td>
                                    <td class="info-data">
                                        <div class="value">
                                            @lang('general.offer') : <span>{{ $order->offer }} - <span
                                                        class="price">(@lang('general.price') {{$order->price}})</span> </span>
                                        </div>
                                        <div class="value">
                                            @lang('general.ip-address') : <span>{{ $order->host}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.client') : <span>{{ $order->surname }} {{$order->name}} {{$order->middle}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.operator') : <span>{{ $order->operSurname }} {{$order->operName}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.date-created') :
                                            <span>{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/y H:i:s')}}</span>
                                        </div>
                                        <div class="value">
                                            @lang('general.date-target') :
                                            <span>{{ \Carbon\Carbon::parse($order->time_modified)->format('d/m/y H:i:s')}}</span>
                                        </div>
                                    </td>
                                    <td style="width: 12%;">
                                        <div>
                                            <span class="badge badge-warning"> @lang('general.cancel')</span>
                                        </div>
                                        @if (isset($targets[$order->id]))
                                            @foreach($targets[$order->id] as $key => $field)
                                                @if ($key != 'name')
                                                    <label>{{$field['title']}}: </label>
                                                    <div class="cause">
                                                        @forelse($field['value'] as $k => $value)
                                                            {{$value}}
                                                            @if (count($field['value']) != $k + 1)
                                                                ,
                                                            @endif
                                                        @empty
                                                        @endforelse
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div style="display: inline-block">
                                            <div class="text-left"> @lang('orders.record') :</div>
                                            @if ($order->records)
                                                @foreach($order->records as $record)
                                                    <div>
                                                        <div>{{$record->surname}} {{$record->name}}</div>
                                                        <?
                                                        $url = route('get-call-by-name') . '?fileName=' . $record->file;
                                                        $agent = $_SERVER['HTTP_USER_AGENT'];
                                                        if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                        $output = '<a href="' . $url . '"><span class="fa-stack">
                                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                                            </span></a>';
                                                        } else {
                                                        $output = '<audio controls><source src="' . $url . '" type="audio/mpeg"></audio>';
                                                        }
                                                        echo $output?>
                                                    </div>
                                                @endforeach
                                            @else
                                                @lang('general.missing')
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('order', $order->id) }}" class="btn btn-success">
                                            @lang('general.order')
                                        </a>
                                        <div class="form-group">
                                            <label for="">@lang('orders.select-operator')</label>
                                            <input class="assigned_operator" name="assigned_operator"
                                                   style="width: 100%" data-content="{{json_encode(['id' => $order->target_user, 'text' => $order->operSurname . ' ' . $order->operName])}}">
                                        </div>
                                        {{--@if (count($order->ordersOpened))--}}
                                            {{--<div class="form-group">--}}
                                                {{--<label>@lang('orders.select-operator')</label>--}}
                                                {{--<select class="form-control" name="assigned_operator">--}}
                                                    {{--@foreach($order->ordersOpened as $orderOpened)--}}
                                                        {{--<option value="{{$orderOpened->user_id}}"--}}
                                                                {{--@if ($order->target_user == $orderOpened->user_id) selected @endif>--}}
                                                            {{--{{$orderOpened->name .' ' . $orderOpened->surname }}--}}
                                                        {{--</option>--}}
                                                    {{--@endforeach--}}
                                                {{--</select>--}}
                                            {{--</div>--}}
                                        {{--@endif--}}
                                        <br>
                                        <a href="#" data-id="{{$order->id}}" class="btn btn-success moderation"> @lang('general.moderate')</a>
                                        <br>
                                        @if(isset($permissions['feedback_add']) && !empty($order->company_id))
                                            @if(empty($order->feedback_id))
                                                <div class="feedback" id="feedback_order_{{$order->id}}">
                                                    <button class="md-trigger btn btn-primary mrg-b-lg leave-feedback"
                                                            data-id="{{$order->id}}"
                                                            data-modal="modal-15_{{$order->id}}">
                                                        @lang('general.add-feedback')
                                                    </button>
                                                </div>
                                            @else
                                                <span style="padding-left: 15px; color: #929292"> @lang('feedbacks.feedback-added')
                                                    . <br>. {{$order->feedback_created_at}}</span>
                                                <br>
                                                <a href="{{ route('feedback', $order->feedback_id) }}"> @lang('general.order')</a>
                                            @endif
                                        @endif
                                        {{--<a href="{{ route('order', $order->id) }}/" class="custom_btn">--}}
                                        {{--<i class="fa fa-long-arrow-right" style="margin-bottom: 5px"></i>--}}
                                        {{--</a>--}}
                                        {{--<br>--}}
                                        {{--<a href="#" class="custom_btn moderation" data-id="{{$order->id}}" data-toggle="tooltip"--}}
                                        {{--data-placement="bottom" title="Модерация"--}}
                                        {{--style="margin-right: 3px;">--}}
                                        {{--<i class="fa fa-check"></i>--}}
                                        {{--</a>--}}
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
                    <li><a href="{{ route('moderation') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}"><i
                                    class="fa fa-chevron-left"></i></a></li>
                    @foreach ($pagination[0] as $number)
                        <? $activaPage = '' ?>
                        @if ($pagination[1] == $number)
                            <li class=active><span>{{ $number }}</span></li>
                        @else
                            @if ($number == 1)
                                <li>
                                    <a href="{{ route('moderation') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}">{{ $number }}</a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('moderation') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $number : '?page=' . $number }}">{{ $number }}</a>
                                </li>
                            @endif
                        @endif
                    @endforeach
                    <li>
                        <a href="{{ route('moderation') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $pagination[2] : '?page=' . $pagination[2] }}"><i
                                    class="fa fa-chevron-right"></i></a></li>
                </ul>
            @endif
            <div class="md-overlay"></div>
        </div>
    </div>

@stop
