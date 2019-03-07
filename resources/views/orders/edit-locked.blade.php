@extends('layouts.app')

@section('title') @lang('general.order') # {{ $orderOne->id }} @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/order.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/style-tabs.css') }}"/>
    <style>
        .table_products .checkbox-nice {
            padding-left: 0px;
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
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>

    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js?x=1') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/feedback-add.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nouislider.js') }}"></script>
    <script src="{{ URL::asset('js/orders/moderator-panel.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order-one-monitoring-call.js') }}"></script>
    <script>
        $(function () {
            $('.result').find('input, select, textarea').each(function (id,obj) {
                $(obj).attr('disabled', true);
            })
        })
    </script>
@stop

@section('content')
    <div class="tabs-wrapper">
        <div class="tab-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="content-header" class="clearfix">
                                <div class="pull-left">
                                    <ol class="breadcrumb">
                                        <li class="active">
                                            <span> @lang('general.order') #<span class='order_id'>{{ $orderOne->id }}</span>
                                                <span class="status_info">
                                                    @if ($orderStatus->name)
                                                        {{!empty($orderStatus->key) ? trans('statuses.' . $orderStatus->key) : $orderStatus->name}}
                                                    @endif
                                                </span>
                                            </span>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade active in" id="tab-order">

                @if ($samePhone)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box-body clearfix">
                                <div class="panel-group accordion" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed same_phone"
                                                   data-toggle="collapse"
                                                   data-parent="#accordion" href="#collapseOne"
                                                   aria-expanded="false">
                                                    @lang('orders.similar-order') <span class="badge badge-danger"
                                                                         style="background-color: #f4786e">{{ $samePhone }}</span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false"
                                             style="height: 2px;">
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-xs-12 col-md-8 col-md-push-4">
                        <form id="order_data">
                            <div class="main-box clearfix">
                                <div class="main-box-body clearfix">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group ">
                                            <label for="surname"> @lang('general.surname')</label>
                                            <input type="text" class="form-control" id="surname" name="surname"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.surname')"
                                                   value="{{$orderOne->surname}}" required disabled>
                                        </div>
                                        <div class="form-group ">
                                            <label for="name"> @lang('general.first-name')</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.first-name')" value="{{$orderOne->name}}"
                                                   required disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group ">
                                            <label for="middle"> @lang('general.middle-name')</label>
                                            <input type="text" class="form-control" id="middle" name="middle"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.middle-name')"
                                                   value="{{$orderOne->middle}}" disabled>
                                        </div>
                                        <div class="form-group ">
                                            <label for="phone"> @lang('general.phone')</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.phone')"
                                                   value="{{$orderOne->phone}}" disabled
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group form-group-select2">
                                            <label for="country"> @lang('general.country')</label>
                                            @if ($country)
                                                <select name="country" id="country" style="width: 100%" disabled>
                                                    @foreach ($country as $oc)
                                                        @if ($oc->code == $orderOne->geo)<option data-currency="{{ $oc->currency }}"
                                                                value="{{ mb_strtolower($oc->code) }}"
                                                                 selected >{{ $oc->name }}</option> @endif
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="age"> @lang('general.age')</label>
                                            <input type="text" class="form-control" id="age" name="age"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.age')" disabled
                                                   @if ($orderOne->age) value="{{$orderOne->age}}" @endif>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="gender"> @lang('general.gender')</label>
                                            <select name="gender" id="gender" class="form-control" disabled>
                                                <option value=""> @lang('general.gender')</option>
                                                <option value="1" @if ($orderOne->gender == 1) selected @endif> @lang('general.male')
                                                </option>
                                                <option value="2" @if ($orderOne->gender == 2) selected @endif> @lang('general.female')
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($script->scriptDetails))
                                @foreach($script->scriptDetails as $block)
                                    @if(!empty($block->key))
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="alert alert-success" style="border-radius: 6px">
                                                    <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">×
                                                    </button>
                                                    <i class="fa fa-info-circle fa-fw fa-lg"></i>
                                                    <strong>{{$block->block}}</strong> <br>
                                                    <div style="color: #505050"><p>{!!$block->text!!}</p></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <div class="main-box clearfix" style="position:relative;">
                                <header class="main-box-header clearfix">
                                    <h2 class="pull-left" style="color: #929292;">{{$orderOne->offer_name}}</h2>
                                </header>
                                <div class="table-responsive search_block">
                                </div>
                                <div class="table-responsive">
                                    <table class="table table_products">
                                        <thead>
                                        <tr>
                                            <th> @lang('general.name')</th>
                                            <th class="text-center"> @lang('general.storage')</th>
                                            <th class="text-center"> @lang('general.up-sell')</th>
                                            <th class="text-center"> @lang('general.up-sell') 2</th>
                                            <th class="text-center"> @lang('general.cross-sell')</th>
                                            <th class="text-center"> @lang('general.cross-sell') 2</th>
                                            <th class="text-center"> @lang('general.note')</th>
                                            <th class="text-center"> @lang('general.price')</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if ($offers->isNotEmpty())
                                            @php
                                                $productTotal = 0;
                                            @endphp
                                            @foreach ($offers as $offer)
                                                <tr @if ($offer->disabled) class="warning"
                                                    @endif data-id="{{ $offer->ooid }}">
                                                    <td class="value">
                                                        {{ $offer->title }}
                                                        <span>{{$offer->option}}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($offer->storageAmount > 10)
                                                            <img src="{{ URL::asset('img/stock_1.png') }}" alt=" @lang('orders.in-stock')">
                                                        @elseif($offer->storageAmount > 0)
                                                            <img src="{{ URL::asset('img/stock_2.png') }}" alt=" @lang('general.end')">
                                                        @else
                                                            <img src="{{ URL::asset('img/stock_3.png') }}" alt=" @lang('orders.not-in-stock')">
                                                        @endif
                                                    </td>
                                                    @if (($offer->type == 1 || $offer->type == 2 || $offer->type == 3 || $offer->type == 4 || $offer->type == 5) && !$offer->disabled)
                                                        <td class="text-center">
                                                            @if ($offer->type == 1)
                                                                <span><i class="fa fa-check"></i></span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($offer->type == 2)
                                                                    <span><i class="fa fa-check"></i></span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($offer->type == 4)
                                                                <span><i class="fa fa-check"></i></span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($offer->type == 5)
                                                                <span><i class="fa fa-check"></i></span>
                                                            @endif
                                                        </td>
                                                        <td class="comments">
                                                            <span>{{$offer->comment}}</span>
                                                        </td>
                                                    @else
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="comments">
                                                            <span>{{$offer->comment}}</span>
                                                        </td>
                                                    @endif
                                                    <td class="text-center">
                                                            {{ $offer->price }}
                                                            @php
                                                                $productTotal += $offer->price;
                                                            @endphp
                                                    </td>
                                                    <td class="text-right">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="value text-center"> @lang('general.total')</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center" id="total_price">{{$productTotal}}</td>
                                                <td class="text-center">
                                                    @if (isset($country[$orderOne->geo]))
                                                        {{ $country[$orderOne->geo]->currency }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label" for="order-price"> @lang('general.price')</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="order-price" name="order-price" value="{{$orderOne->price_total}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="result">
                                <header class="main-box-header clearfix">
                                    <h3 style="border-bottom: none;"><span
                                                style="border-bottom: none;"> @lang('orders.call-result')</span></h3>
                                </header>
                                <div class="tabs-wrapper targets">
                                    <ul class="nav nav-tabs">
                                        @if ($orderOne->target_status == 1)
                                        <li class=" target active "><a
                                                    href="#approve"
                                                    data-toggle="tab"
                                                    class="approve "> @lang('general.approved')
                                            </a>
                                        </li>
                                        @elseif ($orderOne->target_status == 2)
                                        <li class=" target  active "><a
                                                    href="#failure"
                                                    data-toggle="tab"
                                                    class="failure "> @lang('general.refusal')
                                            </a>
                                        </li>
                                        @elseif ($orderOne->target_status == 3)
                                        <li class=" target active "><a
                                                    href="#fake"
                                                    data-toggle="tab"
                                                    class="fake"> @lang('general.cancel')
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade @if ($orderOne->target_status == 1) in active @endif"
                                             id="approve">
                                            <div class="main-box clearfix">
                                                <div class="main-box-body clearfix text-center"
                                                     style="padding-top: 20px;">
                                                    @lang('general.target')
                                                    <select name="target_approve" class="form-control target" disabled>
                                                        @if ($targets_approve)
                                                            @foreach($targets_approve as $target)
                                                                @if ($target->id == $orderOne->target_approve)
                                                                    <option value="{{$target->id}}" selected
                                                                >{{$target->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="main-box-body clearfix target_block">
                                                    <p class="text-center title_tab_content"> @lang('orders.fill-data-order')</p>
                                                    <div class="target_fields form-horizontal">
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
                                        </div>
                                        <div class="tab-pane fade @if ($orderOne->target_status == 2) in active @endif"
                                             id="failure">
                                            <div class="main-box clearfix">
                                                <div class="main-box-body clearfix text-center"
                                                     style="padding-top: 20px;">
                                                    @lang('general.target')
                                                    <select name="target_refuse" class="form-control target">
                                                        <option value=""> @lang('general.select')</option>
                                                        @if ($targets_refuse)
                                                            @foreach($targets_refuse as $target)
                                                                @if ($target->id == $orderOne->target_refuse)
                                                                    <option value="{{$target->id}}" selected >{{$target->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="main-box-body clearfix target_block">
                                                    <p class="text-center title_tab_content"> @lang('orders.describe-reason-refusal')</p>
                                                    <div class="target_fields form-horizontal">
                                                        @if ($target_option['refuse'])
                                                            @if($target_value && $orderOne->target_status == 2)
                                                                @if($target_value->target_id == $orderOne->target_refuse && $target_option['refuse']->id == $target_value->target_id)
                                                                    {{renderTarget(json_decode($target_value->values), 'refuse[', ']', $target_option['refuse']->alias)}}
                                                                @else
                                                                    {{renderTarget(json_decode($target_value->values), 'refuse[', ']')}}
                                                                @endif
                                                            @else
                                                                {{renderTarget(json_decode($target_option['refuse']->options), 'refuse[', ']', $target_option['refuse']->alias)}}
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade @if ($orderOne->target_status == 3) in active @endif"
                                             id="fake">
                                            <div class="main-box clearfix">
                                                <div class="main-box-body clearfix text-center"
                                                     style="padding-top: 20px;">
                                                    @lang('general.target')
                                                    <select name="target_cancel" class="form-control target">
                                                        <option value=""> @lang('general.select')</option>
                                                        @if ($targets_cancel)
                                                            @foreach($targets_cancel as $target)
                                                                @if ($target->id == $orderOne->target_cancel)
                                                                <option value="{{$target->id}}"
                                                                         selected >{{$target->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="main-box-body clearfix target_block">
                                                    <p class="text-center title_tab_content"> @lang('orders.fill-cancellation')</p>
                                                    <div class="target_fields form-horizontal">
                                                        @if ($target_option['cancel'])
                                                            @if($target_value && $orderOne->target_status == 3)
                                                                @if($target_value->target_id == $orderOne->target_cancel && $target_option['cancel']->id == $target_value->target_id)
                                                                    {{renderTarget(json_decode($target_value->values), 'cancel[', ']', $target_option['cancel']->alias)}}
                                                                @else
                                                                    {{renderTarget(json_decode($target_value->values), 'cancel[', ']')}}
                                                                @endif
                                                            @else
                                                                {{renderTarget(json_decode($target_option['cancel']->options), 'cancel[', ']', $target_option['cancel']->alias)}}
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="error-messages" style="display: none;">
                            </div>
                        </form>
                    </div>
                    <div class="col-xs-12 col-md-4 col-md-pull-8">
                        <div class="main-box-body clearfix">
                            <table class="table first_info">
                                <tr>
                                    <td class="text-center key"> @lang('general.offer')</td>
                                    <td class=" value">
                                        {{$orderOne->offer_name}}
                                    </td>
                                </tr>
                                @if ($orderOne->source_url)
                                    <tr>
                                        <td class="text-center key"> @lang('general.source')</td>
                                        <td class=" value">
                                            <a target="_blank" href="{{$orderOne->source_url}}">
                                                @if (strlen($orderOne->source_url) > 40)
                                                    {{substr($orderOne->source_url, 0, 40)}}...
                                                @else
                                                    {{$orderOne->source_url}}
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if ($orderOne->input_data)
                                    <? $inputData = json_decode($orderOne->input_data, true); ?>
                                    @foreach ($inputData as $inDataKey => $inDataValue)
                                        <tr>
                                            <td class="text-center key">
                                                {{ $inDataKey }}
                                            </td>
                                            <td class=" value">
                                                @if (is_array($inDataValue))
                                                    @foreach($inDataValue as $value)
                                                        <span>
                                                        @if (strlen($value) > 40)
                                                                {{substr($value, 0, 40)}}...
                                                            @else
                                                                {{$value}}
                                                            @endif
                                                        </span><br>
                                                    @endforeach
                                                @else
                                                    <span>
                                                    @if (strlen($inDataValue) > 40)
                                                            {{substr($inDataValue, 0, 40)}}...
                                                        @else
                                                            {{$inDataValue}}
                                                        @endif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if (!empty($orderOne->comments))
                                    <tr>
                                        <td class="text-center key">
                                            @lang('general.comment')
                                        </td>
                                        <td class=" value">
                                            {{ $orderOne->comments }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-center key">
                                        @lang('general.ip-address')
                                    </td>
                                    <td class=" value">
                                        {{ $orderOne->host }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center key"> @lang('general.country')</td>
                                    <td class=" value">
                                        {{$orderOne->country}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center key"> @lang('general.date-created')</td>
                                    <td class="value">{{ $orderOne->time_created }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center key"> @lang('orders.date-target')</td>
                                    <td class="value">{{ $orderOne->time_modified }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="main-box-body clearfix comments_block">
                            <header class="main-box-header clearfix">
                                <h3 style="border-bottom: none;"><span
                                            style="border-bottom: none;"> @lang('orders.order-comment')</span>
                                </h3>
                            </header>
                            <div class="main-box-body clearfix" style="padding: 0">
                                <div class="conversation-wrapper">
                                    <div class="conversation-new-message">
                                        <form onsubmit="return false;">
                                            <div class="form-group">
                                        <textarea class="form-control field_comment" rows="2"
                                                  placeholder=" @lang('general.comment')..." style="resize:vertical;"></textarea>
                                            </div>
                                            <div class="clearfix text-center">
                                                <button type="submit" class="btn btn-success add_comment ">
                                                    @lang('general.add')
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="conversation-content ">
                                        <div class="conversation-inner"  id="comment_block">
                                            @if ($comments)
                                                @foreach ($comments as $co)
                                                    <div class="conversation-item item-left clearfix">
                                                        <div class="conversation-user">
                                                            <img src="{{$co->photo}}"
                                                                 alt=""/>
                                                        </div>
                                                        <div class="conversation-body">
                                                            <div class="company_user">{{$co->company}}</div>
                                                            <div class="name" style="max-width: 50%;">
                                                                {{ $co->name }} ({{ $co->login }})
                                                            </div>
                                                            <div class="time hidden-xs" style="max-width: 50%;">
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
                                <h3 style="border-bottom: none;"><span
                                            style="border-bottom: none;"> @lang('orders.history-sms')</span>
                                </h3>
                            </header>
                            <div class="main-box-body clearfix" style="padding: 0">
                                <div class="conversation-wrapper">
                                    <div class="conversation-content ">
                                        <div class="conversation-inner"  id="sms_comment_block">
                                            @if ($smsComments->count())
                                                @foreach ($smsComments as $sms)
                                                    <div class="conversation-item item-left clearfix">
                                                        <div class="conversation-user">
                                                            <img src="{{$sms->photo}}"
                                                                 alt=""/>
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

                                            @else
                                                <div class="conversation-body">
                                                    <div class="name"><span style="color:#6e6e6e; font-weight: bold">
                                                            @lang('orders.sms-not-found')
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($userCalls && isset($permissions['get_calls_page_order']))
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2> @lang('general.calls')</h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>№</th>
                                            <th class="text-center"> @lang('general.status')</th>
                                            <th class="text-center"> @lang('general.user')</th>
                                            <th class="text-center"> @lang('general.date')</th>
                                            <th class="text-center"> @lang('general.talk-time')</th>
                                            <th class="text-center"> @lang('general.trunk')</th>
                                            <th class="text-center"> @lang('general.record')</th>
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
                                                    {{ $ucvalue->date }}
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
                @if ($log && isset($permissions['get_logs_page_order']))
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2> @lang('general.logs')</h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th> @lang('general.id')</th>
                                            <th> @lang('general.user')</th>
                                            <th> @lang('general.text')</th>
                                            <th> @lang('general.date')</th>
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
                @if ($dataGrouped && isset($permissions['get_logs_page_order']))
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2> @lang('orders.logs-by-operators')</h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th class="text-center"> @lang('orders.time-opened')/@lang('general.user')/@lang('general.target')/@lang('general.status')</th>
                                            <th class="text-center"> @lang('general.calls')</th>
                                            <th class="text-center"> @lang('general.comment')</th>
                                            <th class="text-center"> @lang('general.logs')</th>
                                            <th class="text-center"> @lang('orders.add-feedback')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dataGrouped as $row)
                                            <tr id="{{$row->user->id}}" orders-opened-id="{{$row->id}}">
                                                <td class="text-center">
                                                    <div>{{ $row->date_opening}}</div>
                                                    <div style=" border-bottom: 2px solid #ebebeb; padding-bottom: 5px">
                                                        <a href="{{ route('users-edit', $row->user->id) }}"
                                                        >{{$row->user->name.'  '.$row->user->surname.'  ('.$row->user->login.')'}}</a>
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
                                                                $target = trans('orders.speaks-another-language');
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
                                                                $status = trans('orders.answerphone');
                                                                $class = 'label-default';
                                                                $classRow = 'default';
                                                                break;
                                                            case 2:
                                                                {
                                                                    $status = trans('orders.bad-connection');
                                                                    $class = 'label-success';
                                                                       $classRow = 'success';
                                                                    break;
                                                                }
                                                            case 3:
                                                                {
                                                                    $status = trans('orders.completed-target');
                                                                    $class = 'label-danger';
                                                                        $classRow = 'danger';
                                                                    break;
                                                                }
                                                            case 0:
                                                                {
                                                                    $status = trans('orders.completed-target');
                                                                    $class = 'label-danger';
                                                                     $classRow = 'danger';
                                                                    break;
                                                                }
                                                        }
                                                        @endphp
                                                        <span class="label {{$class}}">{{$status}}</span>
                                                    @endif
                                                </td>
                                                {{--<td class="text-center"--}}
                                                {{--style="width: 15%;">--}}
                                                {{----}}
                                                {{--</td>--}}
                                                <td class="text-center">
                                                    @if(count($row->call_progress_log) !== 0)
                                                        @foreach($row->call_progress_log as $log)
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                        class="btn btn-default dropdown-toggle"
                                                                        data-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                    @lang('orders.listen') <i class="fa fa-volume-up"></i>
                                                                </button>
                                                                <ul class="dropdown-menu" role="menu">

                                                                    <li>
                                                                        <?
                                                                        $url = route('get-call-by-name') . '?fileName=' . $log->file;
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
                                                                {{ $comment->date}}
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
                                                        @foreach($row->logs as $log)
                                                            <div class="comment-time">{!!$row->date_opening!!}</div>
                                                            <div>{!!$log->text!!}</div>
                                                        @endforeach
                                                    </div>
                                                    <div class="slimScrollBar"></div>
                                                </td>
                                                <td style="width: 20%;">
                                                    @if(empty($row->feedback))
                                                        <div class="feedback">
                                                            <button style="margin-left: 55px " class="btn btn-primary"
                                                                    href="" id="feedback"> @lang('orders.add-feedback')
                                                            </button>
                                                        </div>
                                                    @else
                                                        <dt style="padding-left: 15px; color: #929292"> @lang('orders.already-added-feedback'){{':'. $row->feedback->created_at}}</dt>
                                                        <a href="{{ route('operator-mistakes') }}"> @lang('orders.operator-errors')</a>
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
                    <div class="hidden">
                        <div id="feedback-block-failed">
                            <div class="icon-box pull-right">
                                <a href="#" id="close-feedback-options" class="pull-left">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                            <form class="failed_call">
                                @if(!empty($operatorMistakes))
                                    <div class="form-group">
                                        <label> @lang('orders.select-type-error')</label>
                                        <br>
                                        <div class="col-sm-12">
                                            @foreach($operatorMistakes as $mistake)
                                                <div class="checkbox-nice checkbox">
                                                    {{ Form::checkbox('mistakes[]', $mistake->id, false, ['id' => $mistake->name]) }}
                                                    {{ Form::label($mistake->name, $mistake->name) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="comment"> @lang('orders.your-feedback')</label>
                                    {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                                </div>
                                <div class="text-center">
                                    {{Form::hidden('type', 'failed_call')}}
                                    {{Form::submit(trans('general.save'), ['name' =>'form1-save-plan', 'class' => 'btn btn-success'])}}
                                </div>
                            </form>
                        </div>
                        <div id="feedback-block-success">
                            <div class="icon-box pull-right">
                                <a href="#" id="close-feedback-options" class="pull-left">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                            <form class="success_call">
                                <div class="form-group">
                                    <label for="comment"> @lang('orders.your-feedback')</label>
                                    {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 3]) }}
                                </div>
                                <div class="text-center">
                                    {{Form::hidden('type', 'success_call')}}
                                    {{Form::submit(trans('general.save'), ['name' =>'form1-save-plan', 'class' => 'btn btn-success'])}}
                                </div>
                            </form>
                        </div>
                        <div id="feedback-block">
                            <button style="margin-left: 30px" class="btn btn-success" href="" id="success_call"><i
                                        class="fa fa-thumbs-o-up"></i> @lang('orders.positive')
                            </button>
                            <br><br>
                            <button style="margin-left: 30px" class="btn btn-danger" href="" id="failed_call"><i
                                        class="fa fa-thumbs-o-down"></i> @lang('orders.negative')
                            </button>
                            <br>
                            <div class="icon-box pull-right">
                                <a href="#" id="close-feedback-options" class="pull-left">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div id="feedback-initial">
                            <button style="margin-left: 30px " class="btn btn-primary" href="" id="feedback"> @lang('orders.add-feedback')
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            @if(!empty($script->scriptDetails) && isset($permissions['only_read_script']))
                <div class="tab-pane fade" id="tab-content">
                    <div class="row">
                        <div id="block-content" class="col-lg-3">
                            <div class="block-inner">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                           style="font-size: 18px"
                                           aria-expanded="true">
                                            @lang('orders.back-to-order')
                                        </a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-content" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">
                                            @lang('general.content')</a>
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
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}"/>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="text-center">
                                        <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                           style="font-size: 14px; color: #505050; font-weight: bold">
                                            @lang('orders.back-to-order')
                                        </a>
                                    </div>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-objections" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false"> @lang('orders.objections')</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix ">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-other-questions" style="color: #000;" data-toggle="tab"
                                           aria-expanded="false"> @lang('orders.other-questions')</a>
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
                                                    @if(!empty($row->geo))
                                                        <div class="icon-box pull-right">
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}"/>
                                                        </div>
                                                    @endif
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="text-center">
                                <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                   style="font-size: 14px; color: #505050; font-weight: bold"> @lang('orders.back-to-order')</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-objections">
                    <div class="row">
                        <div id="block-objections" class="col-lg-3">
                            <div class="block-inner">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                           style="font-size: 18px"
                                           aria-expanded="true">
                                            @lang('orders.back-to-order')
                                        </a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-content" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">
                                            @lang('orders.content')</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-objections" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false"> @lang('orders.objections')</a>
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
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}"/>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                        <div class="text-center">
                                            <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                               style="font-size: 14px; color: #505050; font-weight: bold">
                                                @lang('orders.back-to-order')
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-other-questions" style="color: #000;" data-toggle="tab"
                                           aria-expanded="false"> @lang('orders.other-questions')</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                            </div>
                            <div class="slimScrollBar"></div>
                        </div>
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
                                                    @if(!empty($row->geo))
                                                        <div class="icon-box pull-right">
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png')  }}"/>
                                                        </div>
                                                    @endif
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="text-center">
                                <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                   style="font-size: 14px; color: #505050; font-weight: bold">
                                    @lang('orders.back-to-order')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-other-questions">
                    <div class="row">
                        <div id="block-other-questions" class="col-lg-3">
                            <div class="block-inner">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                           style="font-size: 18px"
                                           aria-expanded="true">
                                            @lang('orders.back-to-order')
                                        </a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-content" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false">
                                            @lang('orders.content')</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-objections" data-toggle="tab" style="color: #000;"
                                           aria-expanded="false"> @lang('orders.objections')</a>
                                        <div class="icon-box pull-right">
                                        </div>
                                    </header>
                                </div>
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <a href="#tab-other-questions" style="color: #000;" data-toggle="tab"
                                           aria-expanded="false"> @lang('orders.other-questions')</a>
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
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}"/>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                        <div class="text-center">
                                            <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                               style="font-size: 14px; color: #505050; font-weight: bold"> @lang('orders.back-to-order')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="slimScrollBar"></div>
                        </div>
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
                                                    @if(!empty($row->geo))
                                                        <div class="icon-box pull-right">
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png')  }}"/>
                                                        </div>
                                                    @endif
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="text-center">
                                <a href="#tab-order" class="return-to-order" data-toggle="tab"
                                   style="font-size: 14px; color: #505050; font-weight: bold"> @lang('orders.back-to-order')</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if(!empty($script->scriptDetails) && isset($permissions['only_read_script']))
            <div id="config-tool" class="closed">
                <a style="text-decoration: none" id="config-tool-cog">
                    <i class="fa fa-file-text-o" style="font-size: 1.7em"></i>
                </a>
                <div id="config-tool-options">
                    <ul class="nav nav-tabs">
                        <li class="active config-li order-li">
                            <a href="#tab-order" data-toggle="tab" aria-expanded="true"> @lang('general.order')</a>
                        </li>
                        <li class="config-li">
                            <a href="#tab-content" data-toggle="tab" aria-expanded="false"> @lang('orders.content')</a>
                        </li>
                        <li class="config-li">
                            <a href="#tab-objections" data-toggle="tab" aria-expanded="false"> @lang('orders.objections')</a>
                        </li>
                        <li class="config-li">
                            <a href="#tab-other-questions" data-toggle="tab" aria-expanded="false"> @lang('orders.other-questions')</a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
@stop
