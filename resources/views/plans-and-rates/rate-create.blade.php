@extends('layouts.app')
@section('title')Добавить норму апрува @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/plan-rates">Все нормы</a></li>
                <li class="active"><a href=""><span>Добавить норму</span></a></li>
            </ol>
            <h1>Добавить норму</h1>
        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            @if (isset($permissions['page_plan_rates']))
                {{ Form::open(['method'=>'post', 'id'=>'rate-add'])}}
                <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
                        <header>
                            <h2>Добавление оффера</h2>
                        </header>
                        <div class="form-group">
                            <input required type="hidden" name="offers" id="offers"
                                   class="offers"/>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <header>
                        <h2>Добавление ставки</h2>
                    </header>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 ">

                        <div class="form-group">
                            <label for="geo">Выбрать страну</label>
                            <select class="form-control" name="geo" id="geo" required>
                                <option value="" selected>Выберите страну</option>
                                @foreach($countries as $country)
                                    <option value="{{ strtolower($country->code) }}"
                                            @if(!empty($scriptDetail->geo) && $scriptDetail->geo == strtolower($country->code) ))
                                            selected
                                            @endif>
                                        @lang('countries.' . $country->code)
                                    </option>
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
                </div>
        </div>
        {{ Form::close() }}
    </div>
    @endif
    <div class="all-plan-rates">
        @if(!empty($offerRatesGroupe))
            <div class="table-responsive">
                <table class="table table-striped table-hover all_lists">
                    <thead>
                    <tr>
                        <th class="text-center">Оффер ID</th>
                        <th class="text-center">Название</th>
                        <th class="text-center">Страна</th>
                        <th class="text-center">Ставка</th>
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($offerRatesGroupe as $offerRates)
                        <? $flag = 1 ?>
                        @foreach($offerRates as $offerRate)
                            <tr>
                                @if ($flag)
                                    <td rowspan="{{count($offerRates)}}" class="text-center">
                                        {{$offerRate->link}}
                                    </td>
                                @endif
                                <td class="text-center">
                                    <a href="{{route('offer', $offerRate->offer_id)}}"> {{$offerRate->offer_id}}
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if(!empty($offerRate->offer))
                                        @php
                                            $prefix = '';
                                        if ($offerRate->offer->project_id == 1) {
                                        $prefix = 'UM::';
                                        } elseif ($offerRate->offer->project_id == 2) {
                                        $prefix = 'BM::';
                                        }
                                         elseif ($offerRate->offer->project_id == 3) {
                                        $prefix = 'HP::';
                                        }
                                        @endphp
                                        <span class="crm_id">{{$prefix . $offerRate->offer->name}}<br></span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="order_phone_block">
                                        <a href="#" class="pop">
                                                    <span class="order_phone">
                                                      @if(!empty($offerRate->geo))
                                                            <img class="country-flag"
                                                                 src="{{ URL::asset('img/flags/' . mb_strtolower($offerRate->geo) . '.png')  }}" />
                                                        @endif
                                                    </span>
                                        </a>
                                        <div class="data_popup">
                                            <div class="arrow"></div>
                                            <h3 class="title">Страна</h3>
                                            <div class="content">{{\App\Models\Country::where('code', $offerRate->geo)->first()->name}}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{$offerRate->rate}} %
                                </td>
                                <td>
                                    <a href="#" class="table-link danger delete_list">
                                                                    <span class="fa-stack "
                                                                          data-id="{{ $offerRate->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                    </a>
                                </td>
                                <? $flag = 0 ?>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plan-rate.js') }}"></script>
@stop


