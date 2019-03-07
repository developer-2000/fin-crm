@extends('layouts.app')

@section('title') @lang('general.order') # {{ $orderOne->id }} @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order-sending.css') }}"/>
    <style>
        .linkIsDisabled {
            cursor: not-allowed;
            opacity: 0.5;
            display: inline-block; /* For IE11/ MS Edge bug */
            pointer-events: none;
            text-decoration: none;
        }

        .md-effect-8 {
            font-size: 18px;
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
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js?x=1') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/feedback-add.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nouislider.js') }}"></script>
    <script src="{{ URL::asset('js/orders/moderator-panel.js') }}"></script>
    <script src="{{ URL::asset('js/orders/sms.js') }}"></script>
    <script src="{{ URL::asset('js/orders/sending.js') }}"></script>
@stop
@section('content')
    @if(isset($permissions['sms_send']))
        <div class="md-modal md-effect-8" id="sms-send">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title"> @lang('sms.send')</h4>
                </div>
                <div class="modal-body">
                    <div class="tabs-wrapper">
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="tab-failed-ticket">
                                {{ Form::open(['method'=>'POST', 'id' => 'sms-send'])}}
                                <div class="form-group">
                                    @if(!empty($templates))
                                        <label for="template"> @lang('sms.templates')</label>
                                        <select class="form-control" name="template" id="template">
                                            <option value=""> @lang('sms.select-template')</option>
                                            @foreach($templates as $key=>$template)
                                                <option value="{{$template->id}}">{{$template->name}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="message"> @lang('sms.message-text')</label>
                                    {{ Form::textarea('message', null, ['class' => 'form-control', 'id' => 'message', 'rows' => 3]) }}
                                </div>
                                <div class="text-center">
                                    {{Form::submit(trans('general.Send'), ['class' => 'btn btn-success'])}}
                                </div>
                                <input type="hidden" name="phone_number" value="{{$orderOne->phone}}">
                                {{ Form::close()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="md-modal md-effect-2" id="sender_add">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Please, enter your user credentials</h4>
            </div>
            <div class="modal-body">
                <form role="form" method="post" class="form-horizontal" id="sign_in">
                    <div class="form-group col-lg-6">
                        <label class="col-lg-3 control-label text-center" for="account_email">Email</label>
                        <div class="col-lg-9">
                            <input required placeholder="Email" name="account_email" id="account_email"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="input-group col-md-6">
                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                        <input id="account_password" type="password"
                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               name="account_password" required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary sign_in">Sign-in</button>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="tab-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div id="content-header" class="clearfix">
                    <div class="pull-left col-xs-12">
                        <ol class="breadcrumb">
                            <li class="active">
                                <span> @lang('general.order') #<span class='order_id'>{{ $orderOne->id }}</span></span>
                            </li>
                        </ol>
                    </div>
                    <div class="pull-left" id="tabs_menu">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#order" data-toggle="tab"> @lang('general.order')</a>
                            </li>
                            @if (isset($permissions['get_logs_page_order']))
                                <li>
                                    <a href="#logs" data-toggle="tab"> @lang('general.logs') </a>
                                </li>
                            @endif
                            @if (isset($permissions['get_statuses_page_order']))
                                <li>
                                    <a href="#statuses_logs" data-toggle="tab"> @lang('general.statuses-log') </a>
                                </li>
                            @endif
                            @if (isset($permissions['get_calls_page_order']))
                                <li>
                                    <a href="#recordings" data-toggle="tab"> @lang('general.calls')</a>
                                </li>
                            @endif
                            <li>
                                <a href="#tracking" data-toggle="tab"> @lang('general.tracking') </a>
                            </li>
                        </ul>
                    </div>
                    @if(isset($permissions['page_order_create_clone']))
                        <div class="pull-right top-page-ui clone_order">
                            <a href="{{ route("order-create-clone", $orderOne->id) }}"
                               class="btn btn-primary pull-right">
                                <i class="fa fa-plus-circle fa-lg"></i>
                                @lang('general.order-clone')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade in active"
                 id="order">
                <div class="row">
                    <div class="col-xs-12 col-md-8 col-md-push-4">
                        <form id="order_data" method="post">
                            {{--<input type="hidden" id="track2" name="track2"--}}
                            {{--value="{{!empty($target_value->track2) ? $target_value->track2 : NULL}}">--}}
                            {{--<input type="hidden" id="track" name="track"--}}
                            {{--value="{{!empty($target_value->track) ? $target_value->track : NULL}}">--}}
                            <div class="main-box clearfix">
                                <div class="main-box-body clearfix">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group ">
                                            <label for="surname"> @lang('general.surname')</label>
                                            <input type="text" class="form-control" id="surname" name="surname"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.surname')"
                                                   value="{{$orderOne->name_last}}" required>
                                        </div>
                                        <div class="form-group ">
                                            <label for="name"> @lang('general.first-name')</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.first-name')"
                                                   value="{{$orderOne->name_first}}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group ">
                                            <label for="middle"> @lang('general.middle-name')</label>
                                            <input type="text" class="form-control" id="middle" name="middle"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.middle-name')"
                                                   value="{{$orderOne->name_middle}}">
                                        </div>
                                        <div class="form-group ">
                                            <label for="phone"> @lang('general.phone')</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.phone')"
                                                   value="{{$orderOne->phone}}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group form-group-select2">
                                            <label for="country"> @lang('general.country')</label>
                                            @if ($country)
                                                <select name="country" id="country" style="width: 100%">
                                                    @foreach ($country as $oc)
                                                        <option data-currency="{{ $oc->currency }}"
                                                                value="{{ mb_strtolower($oc->code) }}"
                                                                @if ($oc->code == $orderOne->geo) selected @endif>
                                                            {{ $oc->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="age"> @lang('general.age')</label>
                                            <input type="text" class="form-control" id="age" name="age"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.age')"
                                                   @if ($orderOne->age) value="{{$orderOne->age}}" @endif>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="gender"> @lang('general.gender')</label>
                                            <select name="gender" id="gender" class="form-control">
                                                <option value=""> @lang('general.gender')</option>
                                                <option value="1" @if ($orderOne->gender == 1) selected @endif>
                                                    @lang('general.male')
                                                </option>
                                                <option value="2" @if ($orderOne->gender == 2) selected @endif>
                                                    @lang('general.female')
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="main-box clearfix" style="position:relative;">
                                <header class="main-box-header clearfix">
                                    <h2 class="pull-left" style="color: #929292;">
                                        @if ($orderOne->offer)
                                            {{$orderOne->offer->name}}
                                        @else
                                        @endif
                                    </h2>
                                    <div class="filter-block pull-right">
                                        <div class="form-group pull-left">
                                            <input type="text" class="form-control search"
                                                   placeholder=" @lang('general.search')...">
                                            <i class="fa fa-search search-icon"></i>
                                        </div>
                                    </div>
                                </header>
                                <div class="table-responsive search_block"></div>
                                <div class="table-responsive">
                                    <table class="table table_products">
                                        <thead>
                                        <tr>
                                            <th> @lang('products.name')</th>
                                            <th class="text-center">@lang('general.storage')</th>
                                            <th class="text-center">@lang('general.note')</th>
                                            <th class="text-center">@lang('general.price')</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if ($offers)
                                            @php
                                                $productTotal = 0;
                                            @endphp
                                            @foreach ($offers as $offer)
                                                <tr @if ($offer->disabled) class="warning"
                                                    @endif data-id="{{ $offer->ooid }}">
                                                    <td class="value"> {{ $offer->title }} </td>
                                                    <td class="text-center">
                                                        @if ($offer->storageAmount > 10)
                                                            <img src="{{ URL::asset('img/stock_1.png') }}"
                                                                 alt=" @lang('products.in-stock')">
                                                        @elseif($offer->storageAmount > 0)
                                                            <img src="{{ URL::asset('img/stock_2.png') }}"
                                                                 alt=" @lang('products.ends')">
                                                        @else
                                                            <img src="{{ URL::asset('img/stock_3.png') }}"
                                                                 alt=" @lang('products.not-in-stock')">
                                                        @endif
                                                    </td>
                                                    <td class="comments">
                                                        @if (!empty($offer->comment))
                                                            {{$offer->comment}}
                                                        @else
                                                            -
                                                        @endif
                                                        @if (!empty(($offer->option)))
                                                            <br>
                                                            {{$offer->option}}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="hidden" name="products[{{$offer->ooid}}][id]"
                                                               value="{{$offer->ooid}}">
                                                        <input type="hidden" name="products[{{$offer->ooid}}][disabled]"
                                                               value="{{$offer->disabled}}">
                                                        @if ($offer->disabled)
                                                            {{ $offer->price }}
                                                        @else
                                                            <input type="text"
                                                                   style="width: 90px; display: inline-block;"
                                                                   class="form-control price_offer"
                                                                   data-value="{{ $offer->price }}"
                                                                   value="{{ $offer->price }}"
                                                                   placeholder=" @lang('general.price')"
                                                                   name="products[{{$offer->ooid}}][price]">
                                                            @php
                                                                $productTotal += $offer->price;
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @if (!$offer->disabled)
                                                            <a href="#" class="table-link danger delete_product">
                                                        <span class="fa-stack " data-id="{{ $offer->ooid }}">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="value text-center"> @lang('general.total')</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center" id="total_price">{{$productTotal}}</td>
                                                <td class="text-center">
                                                    @if (isset($country[$orderOne->geo]))
                                                        {{$country[$orderOne->geo]->currency}}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{--delivery data--}}
                            <div class="main-box clearfix project-box gray-box" id="delivery_block">
                                <div class="main-box-body clearfix">
                                    <div class="project-box-header gray-bg">
                                        <div class="name">
                                            <span class="data text-center">
                                                @lang('general.sending-order')
                                            </span>
                                        </div>
                                    </div>
                                    <div class="project-box-content">
                                        @if ($targets_approve)
                                            <label class="col-lg-3 control-label">
                                                @lang('orders.target-change')
                                            </label>
                                            <div class="form-group result-sending">
                                                <div class="col-lg-8">
                                                    <select required name="target_approve"
                                                            class="form-control target" id="target_approve">
                                                        <option value=""> @lang('general.select')</option>
                                                        @foreach($targets_approve as $target)
                                                            <option
                                                                    data-alias="{{$target->alias}}"
                                                                    value="{{$target->id}}"
                                                                    @if ($target->id == $orderOne->target_approve)
                                                                    selected
                                                                    @endif >
                                                                {{$target->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="main-box-body clearfix target_block">
                                            <p class="text-center title_tab_content">
                                                @lang('general.fill-in') @lang('orders.order-data')
                                            </p>
                                            <div class="fields  form-horizontal">
                                                <div class="poshta-data">
                                                    @if ($target_option['approve'])
                                                        @if ($target_value && $orderOne->target_status == 1)
                                                            @if($target_value->target_id == $orderOne->target_approve  && $target_option['approve']->id == $target_value->target_id)
                                                                {{renderTarget(json_decode($target_value->values), 'approve[', ']', $target_option['approve']->alias)}}
                                                            @else
                                                                {{renderTarget(json_decode($target_value->values), 'approve[', ']')}}
                                                            @endif
                                                        @else
                                                            {{renderTarget(json_decode($target_option['approve']->options), 'approve[', ']', $target_option['approve']->alias)}}
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div id="other_target_fields" class="form-horizontal">
                                            {!! integrationOtherFields($target_option['approve']->alias ?? '', $otherParams) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="error-messages" style="display: none;">
                            </div>
                            <div class="row">
                                <div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label" for="procStatus">
                                            @lang('general.status-change')
                                        </label>
                                        <div class="col-lg-8">
                                            <select required name="proc_status" id="procStatus" class="form-control">
                                                <option value=""> @lang('general.select')</option>
                                                <option value="3"> @lang('statuses.new')</option>
                                                @if ($statuses->count())
                                                    @foreach($statuses as $status)
                                                        <option
                                                                data-action-alias="{{!empty($status->action_alias) ?
                                                                 $status->action_alias : ''}}"
                                                                value="{{$status->id}}"
                                                                @if ($status->id == $orderOne->proc_status)
                                                                selected
                                                                @endif>
                                                            {{!empty($status->key) ? trans('statuses.' . $status->key) : $status->name}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    @if ($procStatuses2->count())
                                        <div class="proc-statuses2">
                                            <br>
                                            <br>
                                            <div class="form-group">
                                                <div class="col-lg-3">
                                                    <label class="control-label" for="procStatus2">
                                                        @lang('general.substatus-change')
                                                    </label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <select required name="proc_status2" id="procStatus2"
                                                            class="form-control">
                                                        <option value=""> @lang('general.select')</option>
                                                        @if ($procStatuses2->count())
                                                            @foreach($procStatuses2 as $procStatus2)
                                                                <option value="{{$procStatus2->id}}"
                                                                        @if ($procStatus2->id == $orderOne->proc_status_2)
                                                                        selected
                                                                        @endif>
                                                                    {{checkTranslate('statuses',$procStatus2->name)}}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    @endif
                                    <br>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label" for="order-price">
                                            @lang('general.order-price')
                                        </label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="order-price" name="order-price"
                                                   value="{{$orderOne->price_total}}">
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                        @if (isset($permissions['cancel_sending_button'])) {{--todo костыль--}}
                                        <div class="text-center">
                                            @if ($orderOne->procStatus && $orderOne->procStatus->action == 'received'
                                             && $orderOne->geo == 'vn' ||
                                             $orderOne->procStatus
                                             && $orderOne->procStatus->action == 'paid_up'
                                             && $orderOne->geo == 'vn' )
                                                <button class="btn btn-warning" id="cancel_send"
                                                        data-id="{{$orderOne->id}}">
                                                    @lang('general.sending-cancel')
                                                </button>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    @if (!empty($divisions) && count($divisions))
                                        <div class="proc-statuses2">
                                            <div class="form-group">
                                                <div class="col-lg-3">
                                                    <label class="control-label" for="division_id">
                                                        @lang('general.division')
                                                    </label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <select name="division_id" id="division_id"
                                                            class="form-control">
                                                        <option value=""> @lang('general.select')</option>
                                                        @foreach($divisions as $division)
                                                            <option value="{{$division->id}}"
                                                                    @if ($division->id == $orderOne->division_id)
                                                                    selected
                                                                    @endif>
                                                                {{ $division->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <br>
                            @if (!$orderOne->final_target)
                                <div class="" style="padding-left: 15em">
                                    <button type="button" class="btn btn-lg btn-success col-lg-8"
                                            id="save_order_sending" style="display: block; margin: 0 auto;">
                                        @lang('general.save')
                                    </button>
                                    <br>
                                    <br>
                                </div>
                            @endif
                            <input type="hidden" name="target_status" value="1">
                            <input type="hidden" name="order_id" value="{{$orderOne->id}}">
                        </form>
                        <br>
                        <br>
                        @if (isset($permissions['collectors_buttons']) && $orderOne->collectorLogs->isNotEmpty())
                            <div class="text-center">
                                <a href="{{route('collector-processed', $orderOne->id)}}"
                                   class="btn btn-success col-md-12">
                                    <span class="fa  fa-check-square"></span>
                                    @lang('general.process')
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="col-xs-12 col-md-4 col-md-pull-8">
                        <div class="main-box-body clearfix">
                            <table class="table first_info">
                                <tr>
                                    <td class="text-center key">
                                        @lang('general.status')
                                    </td>
                                    <td class=" value">
                                        <span class="label label-default"
                                              style="font-size: 13px; background-color: {{$orderOne->procStatus->color ?? ''}};">{{ !empty($orderOne->procStatus->key) ? trans('statuses.' . $orderOne->procStatus->key) : $orderOne->procStatus->name}}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center key">
                                        @lang('general.country')
                                    </td>
                                    <td class=" value">
                                        <img class="country-flag"
                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($orderOne->geo) . '.png') }}"/>
                                        @if (isset($country[strtoupper($orderOne->geo)]))
                                            {{$country[strtoupper($orderOne->geo)]->name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center key">
                                        @lang('general.project')
                                    </td>
                                    <td class="value">{{ $orderOne->project ? $orderOne->project->name : '' }}</td>
                                    <input type="hidden" name="project_id" id="project_id"
                                           value="{{$orderOne->project_id}}">
                                </tr>
                                <tr>
                                    <td class="text-center key">
                                        @lang('general.subproject')
                                    </td>
                                    <td class="value">{{ $orderOne->subProject ? $orderOne->subProject->name : '' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center key"> @lang('general.date-created')</td>
                                    <td class="value">{{ $orderOne->time_created }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center key"> @lang('general.date-target')</td>
                                    <td class="value">{{ $orderOne->time_modified }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center key"> @lang('general.date-changed-status')</td>
                                    <td class=" order_status_date">{{isset($orderOne->time_status_updated) ?
                                    \Carbon\Carbon::parse($orderOne->time_status_updated)->format(' H:i:s d/m/y') : ''}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        @if(isset($permissions['sms_send']))
                            <div class="main-box-body clearfix comments_block">
                                <div class="main-box-body clearfix" style="padding: 0">
                                    <div class="conversation-wrapper">
                                        <div class="conversation-new-message">
                                            <form onsubmit="return false;">
                                                <div class="clearfix text-center">
                                                    <button class="md-trigger btn btn-success mrg-b-lg"
                                                            data-modal="sms-send">
                                                        @lang('sms.send')
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="main-box-body clearfix comments_block">
                            <header class="main-box-header clearfix">
                                <h3 style="border-bottom: none;">
                                    <span style="border-bottom: none;">
                                        @lang('general.order-comments')
                                    </span>
                                </h3>
                            </header>
                            <div class="main-box-body clearfix" style="padding: 0">
                                <div class="conversation-wrapper">
                                    <div class="conversation-new-message">
                                        <form onsubmit="return false;">
                                            <div class="form-group">
                                        <textarea class="form-control field_comment" rows="2"
                                                  placeholder=" @lang('general.comment')..."
                                                  style="resize:vertical;"></textarea>
                                            </div>
                                            <div class="clearfix text-center">
                                                <button type="submit" class="btn btn-success add_comment ">
                                                    @lang('general.comment-add')
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="conversation-content ">
                                        <div class="conversation-inner" id="comment_block">
                                            @if ($comments)
                                                @foreach ($comments as $co)
                                                    <div class="conversation-item item-left clearfix">
                                                        <div class="conversation-user">
                                                            <img src="{{$co->photo}}" alt=""/>
                                                        </div>
                                                        <div class="conversation-body">
                                                            <div class="company_user">{{$co->company}}</div>
                                                            <div class="name" style="max-width: 50%;">
                                                                {{ $co->name }} ({{ $co->login }})
                                                            </div>
                                                            <div class="time hidden-xs">
                                                                {{ $co->date }}
                                                            </div>
                                                            <div class="text">
                                                                {{ $co->text }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main-box-body clearfix sms_comments_block">
                            <header class="main-box-header clearfix">
                                <h3 style="border-bottom: none;">
                                    <span style="border-bottom: none;">
                                        @lang('orders.sms-history')
                                    </span>
                                </h3>
                            </header>
                            <div class="main-box-body clearfix" style="padding: 0">
                                <div class="conversation-wrapper">
                                    <div class="conversation-content ">
                                        <div class="conversation-inner" id="sms_comment_block">
                                            @if (isset($smsComments))
                                                @foreach ($smsComments as $sms)
                                                    <div class="conversation-item item-left clearfix">
                                                        <div class="conversation-user">
                                                            <img src="{{$sms->photo}}" alt=""/>
                                                        </div>
                                                        <div class="conversation-body">
                                                            <div class="company_user">{{$sms->company}}</div>
                                                            <div class="name" style="max-width: 50%;">
                                                                {{ $sms->name }} ({{ $sms->login }})
                                                            </div>
                                                            <div class="time hidden-xs" style="max-width: 50%;">
                                                                {{ $sms->date }}
                                                            </div>
                                                            <div class="text">
                                                                {{ $sms->text }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (($log && isset($permissions['get_logs_page_order'])) || ($dataGrouped && isset($permissions['get_logs_page_order'])))
                <div class="tab-pane fade" id="logs">
                    @if ($log && isset($permissions['get_logs_page_order']))
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <h2>
                                            @lang('general.logs')
                                        </h2>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>
                                                    @lang('general.id')
                                                </th>
                                                <th>
                                                    @lang('general.user')
                                                </th>
                                                <th>
                                                    @lang('general.text')
                                                </th>
                                                <th>
                                                    @lang('general.date')
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($log as $l)
                                                <tr>
                                                    <td>
                                                        {{ $l->user_id }}
                                                    </td>
                                                    <td>
                                                        @if ($l->company)
                                                            <strong>{{$l->company}}</strong>
                                                            <br>
                                                        @endif
                                                        {{ $l->surname }} {{ $l->name }}
                                                    </td>
                                                    <td>
                                                        {!! $l->text !!}
                                                    </td>
                                                    <td>
                                                        {{ $l->date }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                <!-- Логи -->
                    @if ($dataGrouped && isset($permissions['get_logs_page_order']))
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <h2> @lang('general.logs-by-operators')</h2>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th class="text-center">
                                                    @lang('general.time-opened')/ @lang('general.user')
                                                    /@lang('general.target')/@lang('general.status')
                                                </th>
                                                {{--<th class="text-center">Оператор</th>--}}
                                                <th class="text-center">
                                                    @lang('general.calls')
                                                </th>
                                                <th class="text-center">
                                                    @lang('general.comment')
                                                </th>
                                                <th class="text-center">
                                                    @lang('general.logs')
                                                </th>
                                                <th class="text-center">
                                                    @lang('general.feedback-add')
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($dataGrouped as $row)
                                                <tr id="{{$row->user->id}}" orders-opened-id="{{$row->id}}">
                                                    <td class="text-center">
                                                        <div>{{ $row->date_opening }}</div>
                                                        <div style=" border-bottom: 2px solid #ebebeb; padding-bottom: 5px">
                                                            <a href="{{ route('users-edit', $row->user->id) }}">
                                                                {{$row->user->name.'  '.$row->user->surname
                                                                .'  ('.$row->user->login.')'}}
                                                            </a>
                                                        </div>
                                                        <div style="padding-top: 7px; padding-bottom: 7px">
                                                            @if ($row->user->company)
                                                                {{$row->user->company->name}}
                                                            @endif
                                                        </div>
                                                        @php
                                                            $target = '';
                                                            $classLabel = '';
                                                            $classRow = '';
                                                            $classBtn = '';
                                                            switch($row->target) {
                                                                case 1: {
                                                                    $target = trans('general.approved');
                                                                    $classLabel = 'label-primary';
                                                                    $classRow = 'success';
                                                                    break;
                                                                }
                                                                case 2: {
                                                                    $target = trans('general.refusal');
                                                                    $classLabel = 'label-danger';
                                                                    $classRow = 'danger';
                                                                    $classBtn = 'custom_danger';
                                                                    break;
                                                                }
                                                                case 3: {
                                                                    $target = trans('general.cancel');
                                                                    $classLabel = 'label-warning';
                                                                    $classRow = 'warning';
                                                                    $classBtn = 'custom_warning';
                                                                    break;
                                                                }
                                                                case 4:
                                                                {
                                                                    $target = trans('general.call-back');
                                                                    $classLabel = 'label-info';
                                                                    $classRow = '';
                                                                    $classBtn = 'custom_info';
                                                                    break;
                                                                }
                                                            case 5:
                                                                {
                                                                    $target = trans('general.speaks-another-language');
                                                                    $classLabel = 'label-warning';
                                                                    $classRow = 'warning';
                                                                    $classBtn = 'custom_warning';
                                                                    break;
                                                                }
                                                            }
                                                        @endphp
                                                        @if($row)
                                                        @endif
                                                        <div class="badge {{$classLabel}}">{{$target}}</div>
                                                        @if (!$row->target)
                                                            @php
                                                                $status = '';
                                                                $class = '';
                                                                       switch ($row->callback_status) {
                                                                case 1:
                                                                    $status = trans('general.answerphone');
                                                                    $class = 'label-default';
                                                                    $classRow = 'default';
                                                                    break;
                                                                case 2:
                                                                    {
                                                                        $status = trans('general.bad-connection');
                                                                        $class = 'label-success';
                                                                           $classRow = 'success';
                                                                        break;
                                                                    }
                                                                case 3:
                                                                    {
                                                                        $status = trans('general.completed-without-target');
                                                                        $class = 'label-danger';
                                                                            $classRow = 'danger';
                                                                        break;
                                                                    }
                                                                case 0:
                                                                    {
                                                                        $status = trans('general.completed-without-target');
                                                                        $class = 'label-danger';
                                                                         $classRow = 'danger';
                                                                        break;
                                                                    }
                                                            }
                                                            @endphp
                                                            <span class="label {{$class}}">{{$status}}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(count($row->call_progress_log) !== 0)
                                                            @foreach($row->call_progress_log as $callLog)
                                                                <div class="btn-group">
                                                                    <button type="button"
                                                                            class="btn btn-default dropdown-toggle"
                                                                            data-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                        @lang('general.listen')
                                                                        <i class="fa fa-volume-up"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                        <li>
                                                                            <?
                                                                            $url = route('get-call-by-name')
                                                                                . '?fileName=' . $callLog->file;
                                                                            $agent = $_SERVER['HTTP_USER_AGENT'];
                                                                            if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                                                $output = '
                                                                            <p>
                                                                            <a href="' . $url . '">
                                                                            <span class="fa-stack">
                                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                                            </span>
                                                                            </a>
                                                                            </p>';
                                                                            } else {
                                                                                $output = '
                                                                                    <audio controls>
                                                                                    <source src="' . $url . '" type="audio/mpeg">
                                                                                    </audio>
                                                                                 ';
                                                                            }
                                                                            echo $output?>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            {{'N/A'}}
                                                        @endif
                                                    </td>
                                                    <td style="width: 20%" ;>
                                                        @foreach($row->comments as $comment)
                                                            <div class="comment">
                                                                <div class="comment-time">
                                                                    {{ $comment->date }}
                                                                </div>
                                                                <br>
                                                                <div class="text">
                                                                    {{$comment->text}}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <div class="log-inner" style="padding: 15px">
                                                            @foreach($row->logs as $rLog)
                                                                <div class="comment-time">{!! $row->date_opening !!}</div>
                                                                <div>{!!$rLog->text!!}</div>
                                                            @endforeach
                                                        </div>
                                                        <div class="slimScrollBar"></div>
                                                    </td>
                                                    <td style="width: 20%;">
                                                        @if(empty($row->feedback))
                                                            <div class="feedback">
                                                                <button style="margin-left: 55px "
                                                                        class="btn btn-primary"
                                                                        href="" id="feedback">
                                                                    @lang('orders.add-feedback')
                                                                </button>
                                                            </div>
                                                        @else
                                                            <dt style="padding-left: 15px; color: #929292">
                                                                {{--('orders.You have already added a feedback from')--}}
                                                                @lang('orders.feedback-added-from')
                                                                {{':'. $row->feedback->created_at}}
                                                            </dt>
                                                            <a href="{{ route('operator-mistakes') }}">
                                                                @lang('feedbacks.operator-errors')
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            @if (($log && isset($permissions['get_statuses_page_order'])) ||
             ($dataGrouped && isset($permissions['get_statuses_page_order'])))
                <div class="tab-pane fade" id="statuses_logs">
                    @if ($log && isset($permissions['get_statuses_page_order']))
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <h2>
                                            @lang('general.statuses-log')
                                        </h2>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th class="text-center">
                                                    @lang('general.id')
                                                </th>
                                                <th class="text-center">
                                                    @lang('general.user')
                                                </th>
                                                <th class="text-center">
                                                    @lang('general.status')
                                                </th>
                                                <th class="text-center">
                                                    @lang('general.text')
                                                </th>
                                                <th class="text-center">
                                                    @lang('general.date')
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($log as $l)
                                                @if(isset($l->status_id) && $l->status_id != 0)
                                                    <tr>
                                                        @if(empty($l->user_id))
                                                            <td class="text-center" colspan="2">
                                                                {{ !empty($l->user_id) ?
                                                                 $l->user_id : '-- ' . trans('general.system') . ' --' }}
                                                            </td>
                                                        @else
                                                            <td>
                                                                {{$l->user_id}}
                                                            </td>
                                                            <td>
                                                                @if ($l->company)
                                                                    <strong>{{$l->company}}</strong>
                                                                    <br>
                                                                @endif
                                                                {{ $l->surname }} {{ $l->name }}
                                                            </td>
                                                        @endif
                                                        <td class="text-center">
                                                            {{ checkTranslate('statuses', $l->status_name) }}
                                                        </td>
                                                        <td>
                                                            {!! $l->text !!}
                                                        </td>
                                                        <td>
                                                            {{ $l->date }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        <!-- Записи разговоров -->
            @if ($userCalls && isset($permissions['get_calls_page_order']))
                <div class="tab-pane fade" id="recordings">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2>
                                        @lang('general.calls')
                                    </h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>№</th>
                                            <th class="text-center">
                                                @lang('general.status')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.user')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.date')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.talk-time')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.trunk')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.record')
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($userCalls as $ucKey => $ucvalue)
                                            <tr>
                                                <td>
                                                    {{ $ucKey + 1 }}
                                                </td>
                                                <td class="text-center">{{ $ucvalue->status }}</td>
                                                <td class="text-center">
                                                    @if ($ucvalue->status == 'Success' || $ucvalue->status == 'ShortCall')
                                                        @if ($ucvalue->company)
                                                            <strong>{{$ucvalue->company}}</strong>
                                                            <br>
                                                        @endif
                                                        {{ $ucvalue->name }} {{ $ucvalue->surname }}
                                                        ({{ $ucvalue->login }})
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{  $ucvalue->date }}
                                                </td>
                                                <td class="text-center">
                                                    {{ dateProcessing($ucvalue->talk_time) }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $ucvalue->trunk }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($ucvalue->status == 'Success' || $ucvalue->status == 'ShortCall')
                                                        <?
                                                        $url = route('get-call-by-name') . '?fileName=' . $ucvalue->file;
                                                        $agent = $_SERVER['HTTP_USER_AGENT'];
                                                        if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                            $output = '
                                                            <p>
                                                            <a href="' . $url . '">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                            </a>
                                                            </p>';
                                                        } else {
                                                            $output = '
                                                                <audio controls>
                                                                    <source src="' . $url . '" type="audio/mpeg">
                                                                </audio>
                                                                ';
                                                        }
                                                        echo $output?>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        <!-- tracking documents-->
            @if ($documentTracks && isset($permissions['get_document_status']))
                <div class="tab-pane fade" id="tracking">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2>
                                        @lang('general.tracking')
                                    </h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>№</th>
                                            <th class="text-center">
                                                @lang('general.track')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.status-code')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.status')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.comment')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.created')
                                            </th>
                                            <th class="text-center">
                                                @lang('general.updated')
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($documentTracks as $key=>$documentTrack)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td class="text-center">{{ $documentTrack->track }}</td>
                                                <td class="text-center">{{ $documentTrack->status_code }}</td>
                                                <td>{!! $documentTrack->status !!}</td>
                                                <td>{!! $documentTrack->comment !!}</td>
                                                <td class="text-center">{{ $documentTrack->created_at->format('Y-m-d H:i:s') }}</td>
                                                <td class="text-center">{{ $documentTrack->updated_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
