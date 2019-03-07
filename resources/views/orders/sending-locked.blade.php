@extends('layouts.app')

@section('title')@lang('general.order') # {{ $orderOne->id }} @stop

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
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jBox.all.min.css') }}"/>
    <style>
        .linkIsDisabled {
            cursor: not-allowed;
            opacity: 0.5;
            display: inline-block; /* For IE11/ MS Edge bug */
            pointer-events: none;
            text-decoration: none;
        }

        .search_block_locked {
            padding-top: 0;
            display: none;
            max-height: 100%;
            border: 2px solid #e7ebee;
            border-radius: 5px;
            position: absolute;
            right: 0;
            top: 50px;
            background-color: #fff;
            z-index: 500;
        }

        .save-order-changes {
            text-decoration: none !important;
            border-bottom: none;
            background-color: #1ABC9C;
            border-color: #1ABC9C;
            cursor: pointer;
            border: none;
            padding: 6px 12px;
            border-bottom: 2px solid;
            transition: border-color 0.1s ease-in-out 0s, background-color 0.1s ease-in-out 0s;
            outline: none;
            border-radius: 3px;
            background-clip: padding-box;
        }

        #change_locked_order_data .editableform {
            text-align: center;
        }

        .table_products_locked th {
            color: #929292;
        }

        .table_products_locked .value {
            color: #5ac9b9;
        }

        .table_products_locked td.comments {
            max-width: 100px;
            word-wrap: break-word;
            text-align: center;
        }

        .table_products_locked td.comments a, .table_products_locked td.comments a:hover {
            text-decoration: none;
            border-bottom: dashed 1px #0088cc;
        }

        .table_products_locked tbody tr:last-of-type {
            background-color: #f5f5f5;
        }

        .fa-info-circle {
            color: cadetblue;
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
    <script src="{{ URL::asset('js/vendor/jBox.all.min.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order_one.js?x=1') }}"></script>
    <script src="{{ URL::asset('js/feedbacks/feedback-add.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nouislider.js') }}"></script>
    <script src="{{ URL::asset('js/orders/moderator-panel.js') }}"></script>
    <script src="{{ URL::asset('js/orders/sms.js') }}"></script>
    <script src="{{ URL::asset('js/orders/sending.js') }}"></script>
    <script src="{{ URL::asset('js/orders/locked.js') }}"></script>
    <script src="{{ URL::asset('js/orders/change-locked-order.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jBox.all.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $.fn.editable.defaults.mode = 'popup';
            $('.save-order-changes').editable({
                type: 'none',
                escape: true,
                pk: 1,
                title: "Внимание! Данные изменения приведут к изменениям транзакцй по заказу!\n Вы действительно хотите сохранить изменения?",
                tpl: '',
            });
        });

        var options = {
            attach: '.info-tooltip',
            title: 'Операции введенные вручную ',
            content: 'Функционал для внесения <br> изменений в заказ если он в статусе Забран или Возврат',
            theme: 'TooltipBorderThick'
        };
        new jBox(options);
        new jBox('Tooltip', options);

        var options = {
            attach: '.operations-tooltip',
            title: 'Корректировки',
            content: 'Ниже представлена детальная информация <br> по внесенным изменениям в заказ:<br> -дата <br>  - инициатор <br> - изменения <br> - причина корректировки.',
            theme: 'TooltipBorderThick'
        };
        new jBox(options);
        new jBox('Tooltip', options);
    </script>
@stop
@section('content')
    @if(isset($permissions['sms_send']))
        <div class="md-modal md-effect-15" id="sms-send">
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
                                    {{Form::submit(trans('general.send'), ['class' => 'btn btn-success'])}}
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
                            <li class="{{Request::get('tab') ? '' : 'active'}}">
                                <a href="#order" data-toggle="tab">
                                    @lang('general.order')
                                </a>
                            </li>
                            @if (isset($permissions['get_logs_page_order']))
                                <li>
                                    <a href="#logs" data-toggle="tab">
                                        @lang('general.logs')
                                    </a>
                                </li>
                            @endif
                            @if (isset($permissions['get_statuses_page_order']))
                                <li>
                                    <a href="#statuses_logs" data-toggle="tab">
                                        @lang('general.statuses-log')
                                    </a>
                                </li>
                            @endif
                            @if (isset($permissions['get_calls_page_order']))
                                <li>
                                    <a href="#recordings" data-toggle="tab">
                                        @lang('general.calls')
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="#tracking" data-toggle="tab">
                                    @lang('general.tracking')
                                </a>
                            </li>
                            @if (isset($permissions['change_locked_order_data']) &&  $orderOne->locked)
                                <li class="{{Request::get('tab') ? 'active' : ''}}">
                                    <a href="#change_locked_order_data" data-toggle="tab">
                                        @lang('general.operations-manually')
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    @if(isset($permissions['page_order_create_clone']))
                        <div class="pull-right top-page-ui clone_order">
                            <a href="{{ route("order-create-clone", $orderOne->id) }}"
                               class="btn btn-primary pull-right">
                                <i class="fa fa-plus-circle fa-lg"></i> @lang('general.order-clone')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade in {{Request::get('tab') ? '' : 'active'}}"
                 id="order">
                <div class="row">
                    <div class="col-xs-12 col-md-8 col-md-push-4">
                        <div id="order_data">
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
                                                   value="{{$orderOne->name_last}}" required
                                                   disabled>
                                        </div>
                                        <div class="form-group ">
                                            <label for="name"> @lang('general.first-name')</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.first-name')"
                                                   value="{{$orderOne->name_first}}"
                                                   required
                                                   disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group ">
                                            <label for="middle"> @lang('general.middle-name')</label>
                                            <input type="text" class="form-control" id="middle" name="middle"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.middle-name')"
                                                   value="{{$orderOne->name_middle}}"
                                                   disabled>
                                        </div>
                                        <div class="form-group ">
                                            <label for="phone"> @lang('general.phone')</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.phone')"
                                                   value="{{$orderOne->phone}}"
                                                   required
                                                   disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group form-group-select2">
                                            <label for="country"> @lang('general.country')</label>
                                            @if ($country)
                                                <select name="country" id="country" style="width: 100%" disabled>
                                                    @foreach ($country as $oc)
                                                        @if ($oc->code == $orderOne->geo)
                                                            <option data-currency="{{ $oc->currency }}"
                                                                    value="{{ mb_strtolower($oc->code) }}"
                                                                    selected>{{ $oc->name }}
                                                            </option>
                                                        @endif
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
                                </header>
                                <div class="table-responsive search_block">
                                </div>
                                <div class="table-responsive">
                                    <table class="table table_products">
                                        <thead>
                                        <tr>
                                            <th> @lang('products.name')</th>
                                            <th class="text-center"> @lang('general.storage')</th>
                                            <th class="text-center"> @lang('general.note')</th>
                                            <th class="text-center"> @lang('general.price')</th>
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
                                                    <td class="value">
                                                        {{ $offer->title }}
                                                    </td>
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
                                                        {{ $offer->price }}
                                                        @php
                                                            if(!$offer->disabled){
                                                             $productTotal += $offer->price;
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td class="text-right">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="value text-center">@lang('general.total')</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center" id="total_price">{{$productTotal}}</td>
                                                <td class="text-center">
                                                    @if (isset($country[$orderOne->geo]))
                                                        {{$country[$orderOne->geo]->currency}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="value text-center">
                                                    @lang('general.declared-order-price')
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center" id="">{{$orderOne->price_total}}</td>
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
                            <!--delivery data-->
                            <div class="main-box clearfix project-box gray-box" id="delivery_block">
                                <div class="main-box-body clearfix">
                                    <div class="project-box-header gray-bg">
                                        <div class="name">
                                    <span class="data text-center">
                                        @lang('general.order-sending')
                                    </span>
                                        </div>
                                    </div>
                                    <div class="project-box-content">
                                        @if ($targets_approve)
                                            <label class="col-lg-3 control-label">
                                                @lang('general.target')
                                            </label>
                                            <div class="form-group result-sending">
                                                <div class="col-lg-8">
                                                    <select required name="target_approve"
                                                            class="form-control target" id="target_approve" disabled="">
                                                        @foreach($targets_approve as $target)
                                                            @if ($target->id == $orderOne->target_approve)
                                                                <option data-alias="{{$target->alias}}"
                                                                        value="{{$target->id}}"
                                                                        selected
                                                                >{{$target->name}}</option>@endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="main-box-body clearfix target_block">
                                            <p class="text-center title_tab_content">
                                                @lang('orders.fill-data-order')
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
                            @if (!$orderOne->final_target)
                                <div class="row">
                                    <div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label" for="procStatus"
                                                   style="padding-left: 45px">
                                                @lang('general.status-current')
                                            </label>
                                            <div class="col-lg-8">
                                                @if ($orderOne->procStatus && $orderOne->procStatus->action == 'sent')
                                                    <input required name="proc_status2" id="procStatus"
                                                           class="form-control" disabled
                                                           value="{{!empty($orderOne->procStatus->key) ? trans('statuses.' . $orderOne->procStatus->key) : $orderOne->procStatus->name}}">
                                                @else
                                                    <select required name="proc_status" id="procStatusNew"
                                                            class="form-control">
                                                        @if ($statuses->count())
                                                            @foreach($statuses as $status)
                                                                <option data-action-alias="{{!empty($status->action_alias) ? $status->action_alias : ''}}"
                                                                        value="{{$status->id}}"
                                                                        @if ($status->id == $orderOne->proc_status) selected @endif>{{!empty($status->key) ? trans('statuses.' . $status->key) : $status->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <br>
                                                    <div class="row" style="padding-left: 5em">
                                                        <button type="button" class="btn btn-lg btn-success col-lg-8"
                                                                id="save_locked_order"
                                                                style="display: block; margin: 0 auto;">
                                                            @lang('general.status-save')
                                                        </button>
                                                    </div>
                                                @endif
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
                                                                class="form-control" disabled>
                                                            <option value=""> @lang('general.select')</option>
                                                            @if ($procStatuses2->count())
                                                                @foreach($procStatuses2 as $procStatus2)
                                                                    @if ($procStatus2->id == $orderOne->proc_status_2)
                                                                        <option value="{{$procStatus2->id}}"
                                                                                selected>{{checkTranslate('statuses', $procStatus2->name)}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <br>
                            <div class="text-center">
                                <div class="row">
                                    @if ($orderOne->procStatus && $orderOne->procStatus->action == 'sent' && !$orderOne->final_target ||
                                       $orderOne->procStatus && $orderOne->procStatus->action == 'reversal' && !$orderOne->final_target)
                                        @if (isset($permissions['cancel_sending_button']))
                                            <button class="btn btn-warning" id="cancel_send"
                                                    data-id="{{$orderOne->id}}">
                                                @lang('general.cancel-sending')
                                            </button>
                                        @endif
                                        <br>
                                        <br>
                                        <br>
                                        <label class="col-lg-3 control-label" style="padding-right: 25px">
                                            @lang('general.status-change')
                                        </label>
                                        <div class="col-lg-8">
                                            <select required name="proc_status" id="procStatusNew" class="form-control">
                                                @if ($statuses->count())
                                                    @foreach($statuses as $status)
                                                        <option data-action-alias="{{!empty($status->action_alias) ? $status->action_alias : ''}}"
                                                                value="{{$status->id}}"
                                                                @if ($status->id == $orderOne->proc_status) selected @endif>{{$status->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <br>
                                            <div class="row" style="padding-left: 5em">
                                                <button type="button" class="btn btn-lg btn-success col-lg-8"
                                                        id="save_locked_order" style="display: block; margin: 0 auto;">
                                                    @lang('general.status-save')
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
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
                                              style="font-size: 13px; background-color: {{$orderOne->procStatus->color ?? ''}};">
                                            {{ !empty($orderOne->procStatus->key) ? trans('statuses.' . $orderOne->procStatus->key) : $orderOne->procStatus->name}}
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
                                    <td class="text-center key">
                                        @lang('general.date-created')
                                    </td>
                                    <td class="value">{{$orderOne->time_created}}</td>
                                </tr>
                                <tr>
                                    <td class="text-center key">
                                        @lang('general.date-target')
                                    </td>
                                    <td class="value">{{ $orderOne->time_modified}}</td>
                                </tr>
                                <tr>
                                    <td class="text-center key">
                                        @lang('general.date-changed-status')
                                    </td>
                                    <td class=" order_status_date">{{isset($orderOne->time_status_updated) ? \Carbon\Carbon::parse($orderOne->time_status_updated)->format(' H:i:s d/m/y') : ''}}</td>
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
                                        <h2> @lang('general.logs')</h2>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th> @lang('general.id') </th>
                                                <th> @lang('general.user') </th>
                                                <th> @lang('general.text') </th>
                                                <th> @lang('general.date') </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($log as $l)
                                                <tr>
                                                    <td> {{ $l->user_id }} </td>
                                                    <td>
                                                        @if ($l->company)
                                                            <strong>{{$l->company}}</strong>
                                                            <br>
                                                        @endif
                                                        {{ $l->surname }} {{ $l->name }}
                                                    </td>
                                                    <td> {!! $l->text !!} </td>
                                                    <td> {{ $l->date }} </td>
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
                                                    @lang('general.time-opened')/@lang('general.user')
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
                                                    @lang('general.add-feedback')
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($dataGrouped as $row)
                                                <tr id="{{$row->user->id}}" orders-opened-id="{{$row->id}}">
                                                    <td class="text-center">
                                                        <div>{{$row->date_opening}}</div>
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
                                                                    $target = trans('general.Refuse');
                                                                    $classLabel = 'label-danger';
                                                                    $classRow = 'danger';
                                                                    $classBtn = 'custom_danger';
                                                                    break;
                                                                }
                                                                case 3: {
                                                                    $target = trans('general.Cancel');
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
                                                                        @lang('general.listen') <i
                                                                                class="fa fa-volume-up"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">

                                                                        <li>
                                                                            <?
                                                                            $url = route('get-call-by-name') . '?fileName=' . $callLog->file;
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
                                                            <dt style="padding-left: 15px; color: #929292">@lang('orders.feedback-added-from') {{':'. $row->feedback->created_at}}</dt>
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
            @if (($log && isset($permissions['get_statuses_page_order'])) || ($dataGrouped && isset($permissions['get_statuses_page_order'])))
                <div class="tab-pane fade" id="statuses_logs">
                    @if ($log && isset($permissions['get_statuses_page_order']))
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <h2> @lang('general.statuses-log')</h2>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th class="text-center"> @lang('general.id') </th>
                                                <th class="text-center"> @lang('general.user') </th>
                                                <th class="text-center"> @lang('general.status') </th>
                                                <th class="text-center"> @lang('general.text') </th>
                                                <th class="text-center"> @lang('general.date') </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($log as $l)
                                                @if(isset($l->status_id) && $l->status_id != 0)
                                                    <tr>
                                                        @if(empty($l->user_id))
                                                            <td class="text-center" colspan="2">
                                                                {{ !empty($l->user_id) ? $l->user_id : '-- ' . trans('general.system') . ' --'  }}
                                                            </td>
                                                        @else
                                                            <td> {{$l->user_id}} </td>
                                                            <td>
                                                                @if ($l->company)
                                                                    <strong>{{$l->company}}</strong>
                                                                    <br>
                                                                @endif
                                                                {{ $l->surname }} {{ $l->name }}
                                                            </td>
                                                        @endif
                                                        <td class="text-center"> {{ $l->status_name }} </td>
                                                        <td> {!! $l->text !!} </td>
                                                        <td> {{ $l->date }} </td>
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
                                    <h2> @lang('general.calls')</h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>№</th>
                                            <th class="text-center"> @lang('general.status') </th>
                                            <th class="text-center"> @lang('general.user') </th>
                                            <th class="text-center"> @lang('general.date') </th>
                                            <th class="text-center"> @lang('general.talk-time') </th>
                                            <th class="text-center"> @lang('general.trunk') </th>
                                            <th class="text-center"> @lang('general.record') </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($userCalls as $ucKey => $ucvalue)
                                            <tr>
                                                <td> {{ $ucKey + 1 }} </td>
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
                                                <td class="text-center"> {{ $ucvalue->date }} </td>
                                                <td class="text-center"> {{ dateProcessing($ucvalue->talk_time) }} </td>
                                                <td class="text-center"> {{ $ucvalue->trunk }} </td>
                                                <td class="text-center">
                                                    @if ($ucvalue->status == 'Success' || $ucvalue->status == 'ShortCall')
                                                        <?
                                                        $url = route('get-call-by-name') . '?fileName=' . $ucvalue->file;
                                                        $agent = $_SERVER['HTTP_USER_AGENT'];
                                                        if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                            $output = '<p><a href="' . $url . '">
                                                            <span class="fa-stack">
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
                </div>
            @endif
        <!-- tracking documents-->
            @if ($documentTracks && isset($permissions['get_document_status']))
                <div class="tab-pane fade" id="tracking">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2> @lang('general.tracking')</h2>
                                </header>
                                <div class="main-box-body clearfix">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>№</th>
                                            <th class="text-center"> @lang('general.track') </th>
                                            <th class="text-center"> @lang('general.status-code') </th>
                                            <th class="text-center"> @lang('general.status') </th>
                                            <th class="text-center"> @lang('general.comment') </th>
                                            <th class="text-center" style="width: 10%"> @lang('general.created') </th>
                                            <th class="text-center" style="width: 10%"> @lang('general.updated') </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($documentTracks as $key=>$documentTrack)
                                            <tr>
                                                <td> {{$key+1}} </td>
                                                <td class="text-center">{{ $documentTrack->track }}</td>
                                                <td class="text-center">{{ $documentTrack->status_code }}</td>
                                                <td class="text-center">{!! $documentTrack->status !!}</td>
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
        <!-- end tracking documents-->
            <!-- Manually operations -->
            @if ($orderOne->locked && isset($permissions['change_locked_order_data']))
                <div class="tab-pane fade {{Request::get('tab') ? 'active in' : ''}}" id="change_locked_order_data">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-box clearfix">
                                <header class="main-box-header clearfix">
                                    <h2>
                                        @lang('general.operations-manually')
                                        <i class="fa fa-info-circle info-tooltip"
                                           id="Tooltip-2"
                                           data-title=" @lang('general.operations-manually')"
                                           data-content="{{ __('info.functionality-for-marking',
                                           [
                                               'Received' => trans('statuses.Received'),
                                               'Returned' => trans('statuses.Returned')
                                           ]
                                           )}}">
{{-- Functionality for making changes to the order if it is in the status of ":Received" or ":Returned" --}}
                                        </i>
                                    </h2>
                                </header>
                                @if( in_array($orderOne->procStatus->action, ['received','returned','reversal']) )
                                    <div class="main-box-body clearfix">
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
                                                        <input type="text" class="form-control searchForLocked"
                                                               placeholder=" @lang('general.search')...">
                                                        <i class="fa fa-search search-icon"></i>
                                                    </div>
                                                </div>
                                            </header>
                                            <form method="POST" id="form_locked">
                                                <div class="table-responsive search_block_locked">
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table_products_locked">
                                                        <thead>
                                                        <tr>
                                                            <th> @lang('products.name')</th>
                                                            <th class="text-center"> @lang('general.storage')</th>
                                                            <th class="text-center"> @lang('products.note')</th>
                                                            <th class="text-center"> @lang('general.price')</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if ($offers)
                                                            @php
                                                                $productTotalLocked = 0;
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
                                                                                 alt=" @lang('products.Ends')">
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
                                                                        <input type="hidden"
                                                                               name="products[{{$offer->ooid}}][id]"
                                                                               value="{{$offer->ooid}}">
                                                                        <input type="hidden"
                                                                               name="products[{{$offer->ooid}}][disabled]"
                                                                               value="{{$offer->disabled}}">
                                                                        @if ($offer->disabled)
                                                                            {{ $offer->price }}
                                                                        @else
                                                                            <input type="text"
                                                                                   style="width: 90px; display: inline-block;"
                                                                                   class="form-control price_product_locked"
                                                                                   data-value="{{ $offer->price }}"
                                                                                   value="{{ $offer->price }}"
                                                                                   placeholder=" @lang('general.Price')"
                                                                                   name="products[{{$offer->ooid}}][price]"
                                                                            >
                                                                            @php
                                                                                $productTotalLocked += $offer->price;
                                                                            @endphp
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-right">
                                                                        @if (!$offer->disabled)
                                                                            <a href="#" data-id="{{ $offer->ooid }}"
                                                                               class="table-link danger delete_product_locked">
                                                        <span class="fa-stack " data-id="{{ $offer->ooid }}">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                                            </a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr class="row-total">
                                                                <td class="value text-center"> @lang('general.total')</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-center" id="total_price">
                                                                    <input type="text"
                                                                           id="total_price_locked"
                                                                           style="width: 90px; display: inline-block;"
                                                                           class="form-control total_price_locked"
                                                                           data-value="{{ $productTotalLocked }}"
                                                                           value="{{ $productTotalLocked }}"
                                                                           name="total_price_locked"
                                                                    >
                                                                </td>
                                                                <td class="text-center">
                                                                    @if (isset($country[$orderOne->geo]))
                                                                        {{$country[$orderOne->geo]->currency}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="value text-center">
                                                                    @lang('general.cost-delivery')</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-center" id="cost_price">
                                                                    <input type="text"
                                                                           id="cost"
                                                                           style="width: 90px; display: inline-block;"
                                                                           class="form-control cost_locked"
                                                                           data-value="{{$target_value->cost}}"
                                                                           value="{{$target_value->cost}}"
                                                                           name="cost">
                                                                </td>
                                                                <td class="text-center">
                                                                    @if (isset($country[$orderOne->geo]))
                                                                        {{$country[$orderOne->geo]->currency}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $income = $target_value->cost + $orderOne->price_total;
                                                            @endphp
                                                            <tr>
                                                                <td class="value text-center">
                                                                    @lang('general.total-with-delivery-cost')
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-center" id="income">{{$income}}</td>
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
                                                <div class="main-box-body clearfix comments_block">
                                                    <div class="main-box-body clearfix" style="padding: 0">
                                                        <div class="conversation-wrapper">
                                                            <div class="conversation-new-message">
                                                                <div class="form-group">
                                                                    <label for=""></label>
                                                                    <textarea class="form-control" rows="3"
                                                                              id="operation_comment"
                                                                              name="operation_comment"
                                                                              placeholder=" @lang('general.reason-for-order-correction')...">
                                                                    </textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="main-box-body clearfix text-center">
                                                    <button type="button"
                                                            class="btn btn-success editable editable-click save-order-changes "
                                                            id="save-order-changes"
                                                            data-type="text" data-pk="1"
                                                            data-title="<strong> @lang('alerts.warning')</strong><br>
{{--('info.These corrections will change order transactions!')--}}
                                                                    <br>
@lang('info.corrections-change-transactions')<br>
@lang('alerts.save-changes')"
                                                            data-id="">
                                                        <span class="fa fa-save"></span> @lang('general.save')
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="main-box-body clearfix">
                                        <h3>
                                            <span>
                                                @lang('general.corrections')
                                                <i class="fa fa-info-circle operations-tooltip"
                                                   id="Tooltip-2"
                                                   data-title=" @lang('general.corrections')"
                                                   data-content=" @lang('info.detailed-information'):<br> -
@lang('general.date') <br>  - @lang('general.initiator') <br> - @lang('general.changes') <br> - @lang('general.correction-reason')"
                                                ></i>
                                            </span>
                                        </h3>
                                        @if($operations->count())
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <tbody>
                                                    @foreach($operations as $operation)
                                                        <tr class="active">
                                                            <td>
                                                                <i class="fa fa-clock-o">
                                                                    {{$operation->created_at}}
                                                                </i>
                                                                <br>
                                                                <span style="font-weight: bold; color: grey; ">{{$operation->user->name .' ' .
                                             $operation->user->surname}}</span>
                                                            </td>
                                                            <td>
                                                                {!! isset($operation->log) ? $operation->log->text : '' !!}
                                                            </td>
                                                            <td>
                                                                <label for=""></label>
                                                                <textarea class="form-control" rows="2" disabled=""
                                                                          id="operation_comment"
                                                                          name="operation_comment"
                                                                          placeholder=" @lang('general.comment')...">
                                                                    {{$operation->comment}}
                                                                </textarea>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="main-box-body clearfix">
                                                        <div class="alert alert-info">
                                                            <strong></strong>
                                                            {{--('info.Manually corrections have not yet been created.')--}}
                                                            @lang('info.manually-corrections-no-results')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @elseif( in_array($orderOne->procStatus->action, ['paid_up','refused']))
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="col-lg-4" style="padding-left: 2em">
                                                @if(!empty($orderOne->pass_id))
                                                    {{--<p>Статус: {{$orderOne->procStatus->name}}</p>--}}
                                                    <p class="label label-default"
                                                       style="font-size: 13px; background-color: {{$orderOne->procStatus->color ?? ''}};">{{$orderOne->procStatus->name ?? ''}}</p>
                                                    <br>
                                                    <br>
                                                    <p>
                                                        <a href="{{route('pass-one', $orderOne->pass_id)}}">Проводка {{$orderOne->pass_id}}</a>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="main-box-body clearfix">
                                                    <div class="row">
                                                        <button type="button" class="btn btn-default"
                                                                id="reverse_one_order"
                                                                data-pk="{{ $orderOne->pass_id}}">
                                                            Сторнировать заказ
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="main-box-body clearfix">
                                        <h3>
                                            <span> @lang('general.corrections')
                                                <i class="fa fa-info-circle operations-tooltip" id="Tooltip-2"></i>
                                            </span>
                                        </h3>
                                        @if($operations->count())
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <tbody>
                                                    @foreach($operations as $operation)
                                                        <tr class="active">
                                                            <td>
                                                                <i class="fa fa-clock-o">  {{$operation->created_at}}</i><br>
                                                                <span style="font-weight: bold; color: grey; ">{{$operation->user->name .' ' .
                                             $operation->user->surname}}</span>
                                                            </td>
                                                            <td> {!! isset($operation->log) ? $operation->log->text : '' !!}</td>
                                                            <td>
                                                                <label for=""></label>
                                                                <textarea class="form-control" rows="2" disabled=""
                                                                          id="operation_comment"
                                                                          name="operation_comment"
                                                                          placeholder=" @lang('general.comment')...">
                                                                    {{$operation->comment}}
                                                                </textarea>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="main-box-body clearfix">
                                                        <div class="alert alert-info">
                                                            <strong></strong> @lang('info.manually-corrections-no-results')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="main-box-body clearfix">
                                                <div class="alert alert-info">
                                                    <i class="fa fa-info-circle fa-fw fa-lg"></i>
                                                    <strong></strong>
                                                    {{--('info.Manually corrections are available only for statuses')--}}
                                                    @lang('info.manually-corrections')
                                                    @lang('info.available-statuses')
                                                    " @lang('statuses.received')"," @lang('statuses.returned')"
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        @endif
        <!-- mannuakky operations-->
        </div>
    </div>
@stop
