@extends('layouts.app')
@section('title')Изменить норму  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        #s2id_autogen1 {
            height: 2.2em;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/plan-rates">Нормы</a></li>
                <li class="active"><a href=""><span>Изменить норму</span></a></li>
            </ol>
            <h1>Изменить норму </h1>

        </div>
    </div>
    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            @if (isset($permissions['page_plan_rates']))
                <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
                        {{ Form::open(['method'=>'post', 'id'=>'offer-add'])}}
                        <header>
                            <h2>Добавление оффера</h2>
                        </header>
                        <div class="form-group">
                            <input required type="hidden" name="offers" id="offers"
                                   class="offers"/>
                            <input type="hidden" name="planRateOffers" id="planRateOffers" value="{{$planRateOffers}}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
                        <div style="padding-top: 2.2em">
                            {{Form::submit('Сохранить', ['class' => 'rate-add btn btn-success'])}}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
        </div>
    </div>
    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            <div class="row ">
                {{ Form::open(['method'=>'post', 'id'=>'rate-add'])}}
                <header>
                    <h2>Добавление ставки</h2>
                </header>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 ">

                    <div class="form-group">
                        <label for="geo">Выбрать страну</label>
                        <select class="form-control" name="geo" id="geo" required>
                            <option value="" selected>Выберите страну</option>
                            @foreach($countries as $country)
                                <option value="{{strtolower($country->code)}}"
                                    @if(!empty($scriptDetail->geo) && $scriptDetail->geo == strtolower($country->code) ))
                                        selected
                                    @endif> @lang('countries.' . $country->code)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 ">
                    <div class="form-group">
                        <label for="geo">Установить ставку (approve)</label>
                        <input required type="number" name="rate" id="rate" class="form-control">
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 ">
                    <div class="form-group">
                        <label for="geo">Установить ставку (upsell)</label>
                        <input required type="number" name="upsell_rate" id="upsell_rate" class="form-control">
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 ">
                    <div class="form-group" style="text-align: center; padding-top: 23px">
                        {{Form::submit('Добавить', ['class' => 'rate-add btn btn-success'])}}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        @endif
        <div class="all-plan-rates">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                @if(!empty($planRate->planRatesOffers))
                    <div class="table-responsive">
                        <table class="table table-striped table-hover all_lists">
                            <thead>
                            <tr>
                                <th class="text-center">Оффер ID</th>
                                <th class="text-center">Название</th>
                                <th class="text-center"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($planRate->planRatesOffers as $row)
                                <tr>
                                    <td class="text-center">
                                        <a href="{{route('offer', $row->offer)}}">
                                            {{$row->offer_id}}
                                        </a>
                                    </td>
                                    <td>
                                        @if(!empty($row->offer))
                                            @php
                                                $prefix = '';
                                            if ($row->offer->project_id == 1) {
                                            $prefix = 'UM::';
                                            } elseif ($row->offer->project_id == 2) {
                                            $prefix = 'BM::';
                                            }
                                             elseif ($row->offer->project_id == 3) {
                                            $prefix = 'HP::';
                                            }
                                            @endphp
                                            <span class="crm_id">{{$prefix . $row->offer->name}}<br></span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="table-link danger" id="delete_offer">
                                                                    <span class="fa-stack "
                                                                          data-id="{{ json_encode($row) }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <div class="hidden planRateId">{{Request::segment(3)}}</div>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                @if(!empty($planRate))
                    <div class="table-responsive">
                        <table class="table table-striped table-hover all_lists">
                            <thead>
                            <tr>
                                <th class="text-center">Страна</th>
                                <th class="text-center">Ставка(approve)</th>
                                <th class="text-center">Ставка(upsell)</th>
                                <th class="text-center"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($planRate->data) as $row)
                                @if(!empty($row->geo))
                                    <tr>
                                        <td class="text-center">
                                            <div class="order_phone_block">
                                                <a href="#" class="pop">
                                                    <span class="order_phone">
                                                      @if(!empty($row->geo))
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($row->geo) . '.png') }}" />
                                                        @endif
                                                    </span>
                                                </a>
                                                <div class="data_popup">
                                                    <div class="arrow"></div>
                                                    <h3 class="title">Страна</h3>
                                                    <div class="content">{{\App\Models\Country::where('code', $row->geo)->first()->name}}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{$row->rate}} %
                                        </td>
                                        <td class="text-center">
                                            {{$row->upsell_rate}} %
                                        </td>
                                        <td>
                                            <a href="#" class="table-link danger" id="delete_rate">
                                                                    <span class="fa-stack "
                                                                          data-id="{{ $row->geo}}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
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
    </div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plan-rate-edit.js') }}"></script>
@stop


