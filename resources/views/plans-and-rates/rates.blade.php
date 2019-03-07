@extends('layouts.app')

@section('title')Нормы апрувов # @stop

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
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/plans/rate-delete.js') }}"></script>
    <script>

        $(document).ready(function () {
            //toggle `popup` / `inline` mode
            $.fn.editable.defaults.mode = 'popup';

            //make username editable
            $('.rate-delete').editable({
                type: 'none',
                escape: true,
                pk: 1,
                title: 'Внимание! Норма содержит офферы. Вы действительно хотите удалить норму?',
                tpl: '',
            });
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12" style="padding-bottom: 3em">
            <div class="clearfix">
                <ol class="breadcrumb">
                    <li><a href="/">Home</a></li>
                    <li class="active"><a href="/plan-rates">Все нормы</a></li>
                </ol>
                <div class="pull-right top-page-ui">
                    <a href="{{route('plans-rate-add')}}"
                       class="btn btn-primary">
                        <i class="fa fa-plus-circle fa-lg"></i> Добавить норму</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="main-box clearfix">
            <div class="main-box-body clearfix">
                <div class="tabs-wrapper">
                    <div class="tab-content">
                        @if(!empty($planRates))
                            <div class="main-box-body clearfix"
                                 style="margin-top: 20px;padding: 0 0 20px 0;">
                                <div class="table-responsive">
                                    <table class="table table-hover all_lists">
                                        <thead>
                                        <tr>
                                            <th class="text-center"></th>
                                            <th class="text-center">Оффер ID/Наименование</th>
                                            <th class="text-center" colspan="3">Страна / Ставка(approve) /
                                                Ставка(upsell)
                                            </th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($planRates as $key=>$planRate)
                                            <tr>
                                                <td class="text-center">
                                                    {{$planRate->id}}
                                                </td>
                                                <td style="color: grey; font-weight: bold">
                                                    @foreach($planRate->planRatesOffers as $planRatesOffer)
                                                        <a href="{{route('offer', $planRatesOffer->offer_id)}}"> {{$planRatesOffer->offer_id}}
                                                        </a>

                                                        @if(!empty($planRatesOffer->offer))
                                                            @php
                                                                $prefix = '';
                                                            if ($planRatesOffer->offer->project_id == 1) {
                                                            $prefix = 'UM::';
                                                            } elseif ($planRatesOffer->offer->project_id == 2) {
                                                            $prefix = 'BM::';
                                                            }
                                                             elseif ($planRatesOffer->offer->project_id == 3) {
                                                            $prefix = 'HP::';
                                                            }
                                                            @endphp
                                                            {{$prefix . $planRatesOffer->offer->name}}<br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                    <td>
                                                        @if(!empty($planRate->data))
                                                            @php
                                                                $data = json_decode($planRate->data);
                                                            @endphp
                                                            @foreach($data as $dataRow)
                                                                <div class="order_phone_block">
                                                                    <a href="#" class="pop">
                                                    <span class="order_phone" style="color: grey">
                                                        <img class="country-flag"
                                                             src="{{ URL::asset('img/flags/' . mb_strtoupper($dataRow->geo) . '.png') }}" />
                                                                <span style="font-size: 14px">/ {{$dataRow->rate}}
                                                                    %</span>
                                                                <span style="font-size: 14px">/ {{$dataRow->upsell_rate}}
                                                                    %</span>
                                                    </span>
                                                                    </a>
                                                                    <div class="data_popup">
                                                                        <div class="arrow"></div>
                                                                        <h3 class="title">Страна</h3>
                                                                        @if(!empty($dataRow->geo))
                                                                        <div class="content">
                                                                            {{\App\Models\Country::where('code', $dataRow->geo)->first()->name}}
                                                                        </div>
                                                                            @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{route('plans-rates-edit',  $planRate->id)}}"
                                                           class="table-link">
                                        <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                                        </a>
                                                    </td>
                                                    <td
                                                        class="text-center">
                                                        <a href="#"
                                                           data-type="text" data-pk="1"
                                                           data-title="Внимание! Норма содержит офферы. Вы действительно хотите удалить норму?"
                                                           data-id="{{  $planRate->id}}"
                                                           class="editable editable-click table-link danger  rate-delete">Удалить</a>
                                                    </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop