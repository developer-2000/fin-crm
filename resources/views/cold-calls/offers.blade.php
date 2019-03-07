@extends('layouts.app')

@section('title')Офферы@stop

@section('css')
    <style>
        .item_rows {
            position: relative;
            padding-top: 15px;
            border-bottom: 1px solid #e6ebef;
        }

        .item_rows:last-of-type {
            border-bottom: none;
        }

        .item_rows .main-box-body {
            /*padding-left: 59px;*/
            padding-bottom: 0;
        }

        .item_rows .form-horizontal .control-label {
            text-align: left;
        }

        .btns_filter {
            text-align: center;
            padding-top: 4px;
            padding-bottom: 20px;
        }

        .btns_filter .btn {
            margin-right: 8px;
            width: 144px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/cold-call.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-offers.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Все офферы</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Все офферы Х/П</h1>
                @if (isset($permissions['create_edit_cold_call_list']))
                    <div class="pull-right top-page-ui">
                        <a href="{{route('cold-calls-offers-create')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Создать оффер
                        </a>
                    </div>
                @endif
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
                                        <div class="form-group col-md-6 col-sm-6 form-horizontal">
                                            <label for="name" class="col-sm-3 control-label">Название</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="name" name="name"
                                                       value="@if (isset($_GET['name'])){{ $_GET['name'] }}@endif">
                                            </div>
                                        </div>
                                        {{--<div class="form-group col-md-6 col-sm-6 form-horizontal">--}}
                                        {{--<label for="company-select2">Компания</label>--}}
                                        {{--<input type="hidden" name="company" id="company" class="company-select2 required"--}}
                                        {{--style="width: 100%"/>--}}
                                        {{--</div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btns_filter">
                            <input class="btn btn-success" type="submit" name="button_filter" value='Фильтровать'/>
                            <a href="{{ route('offers') }}" class="btn btn-warning" type="submit">Сбросить фильтр</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            @if ($data)
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Проект</th>
                                        <th>Название</th>
                                        <th>Компания</th>
                                        <th class="text-center">Product/Up/Up2/Cross</th>
                                        @if (isset($permissions['page_setting_offers']))
                                            <th></th>
                                            <th></th>
                                        @endif

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($data as $offer)
                                        <tr id="{{$offer->id}}">
                                            <td>{{ $offer->id }}</td>
                                            <td>{{ $offer->project }}</td>
                                            <td>{{ $offer->name }}</td>
                                            <td>{{ $offer->company_name }}</td>
                                            <td class="text-center">
                                                @php
                                                    $up_sell_1 = isset($offer->count_types->up_1) ?$offer->count_types->up_1 : 0;
                                                    $up_sell_2 = isset($offer->count_types->up_2) ?$offer->count_types->up_2 : 0;
                                                    $cross = isset($offer->count_types->cross_sell) ?$offer->count_types->cross_sell : 0;
                                                    $product = isset($offer->count_types->product) ?$offer->count_types->product : 0;
                                                @endphp
                                                {{$product}}/{{$up_sell_1}}/{{$up_sell_2}}/{{$cross}}
                                            </td>
                                            @if (isset($permissions['create_edit_cold_call_offer']))
                                                <td>
                                                    <a href="{{ route("cold-call-offer", $offer->id) }}"
                                                       class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" class="checkbox_status"
                                                               id="status{{$offer->id}}"
                                                               data-id="{{ $offer->id}}"
                                                               @if ($offer->status =='active')
                                                               checked="checked"
                                                                @endif
                                                        >
                                                        <label for="status{{$offer->id}}">
                                                        </label>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{$data->links()}}
@stop
<?php
      //  use App\Models\ColdCallList;
     //  (new ColdCallList)->addColdCallsInElastix();

//use App\Models\Auth;
//
//$result = Auth::whereIn('login_sip', [1165, 1014])->get(['id', 'name', 'surname'])->toArray();
//dd($result);
        ?>
