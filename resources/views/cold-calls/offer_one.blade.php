@extends('layouts.app')

@section('title') @lang('general.offer')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/offer_one.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-offer_one.js?v=1') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-setting_offer.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('general.offer') #<span class='offer_id'>{{ $data->id }}</span> ({{ $data->name }})</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <input class="hidden" id="of_id" value="{{$data->id}}">
                    <form class="col-sm-6 form-horizontal" id="change_offer" method="post">
                        <header class="main-box-header clearfix">
                            <h2> @lang('general.offer-setting')</h2>
                        </header>
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-left"> @lang('general.name') </label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="name" id="name" value="{{$data->name}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-left"> @lang('general.project')</label>
                            <div class="col-sm-8">
                                <input class="form-control" value="{{$data->project->name}}" disabled>
                            </div>
                        </div>
                        {{--<div class="form-group text-center">--}}
                        {{--<input type="submit" value="Сохранить" class="btn btn-success">--}}
                        {{--</div>--}}
                    </form>
                    @if (isset($permissions['create_edit_cold_call_offer']))
                        <div class="col-sm-12">
                            <header class="main-box-header clearfix">
                                <h2> @lang('general.product-add')</h2>
                            </header>
                            <form class="form" id="add_product" method="post" action="#">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {{--<label for="products-select2">Выберите товар</label>--}}
                                        <input type="hidden" name="products-select2" class="products-select2"
                                               style="width: 100%"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="type" id="type" class="form-control" style="width: 100%; height: 40px;" >
                                            <option value=""> @lang('general.select-type')</option>
                                            <option value="1"> @lang('general.up-sell') 1</option>
                                            <option value="2"> @lang('general.up-sell') 2</option>
                                            <option value="4"> @lang('general.cross-sell')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="price" id="price" placeholder="{{trans('general.price')}}" style="height: 39px" >
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input class="btn btn-success" type="submit" name="price" placeholder="{{trans('general.price')}}" value="{{trans('general.add')}}">
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                    <div class="col-sm-12">
                        <div class="table-responsive all_products">
                            <table class="table">
                                <tbody>
                                @if ($products)
                                    @foreach($products as $product)
                                        @if ($product[0]->type == 1)
                                            <tr>
                                                <td colspan="4"> <b> @lang('general.up-sell')</b></td>
                                            </tr>
                                            @foreach($product as $pr)
                                                <tr>
                                                    <td>{{$pr->product}}</td>
                                                    <td>
                                                        @if (isset($countries[$pr->geo]))
                                                            {{$countries[$pr->geo]->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$pr->price}}
                                                        @if (isset($countries[$pr->geo]) )
                                                            {{$countries[$pr->geo]->currency}}
                                                        @endif
                                                    </td>
                                                    <td style="width: 40px">
                                                        <a href="#" class="table-link danger delete_product">
                                                            <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif($product[0]->type == 2)
                                            <tr>
                                                <td colspan="4"> <b> @lang('general.up-sell') 2 </b></td>
                                            </tr>
                                            @foreach($product as $pr)
                                                <tr>
                                                    <td>{{$pr->product}}</td>
                                                    <td>
                                                        @if (isset($countries[$pr->geo]) )
                                                            {{$countries[$pr->geo]->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$pr->price}}

                                                        @if (isset($countries[$pr->geo]) )
                                                            {{$countries[$pr->geo]->currency}}
                                                        @endif
                                                    </td>
                                                    <td style="width: 40px">
                                                        <a href="#" class="table-link danger delete_product">
                                                                    <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif($product[0]->type == 4)
                                            <tr>
                                                <td colspan="4"> <b> @lang('general.cross-sell')</b></td>
                                            </tr>
                                            @foreach($product as $pr)
                                                <tr>
                                                    <td>{{$pr->product}}</td>
                                                    <td>
                                                        @if (isset($countries[$pr->geo]) )
                                                            {{$countries[$pr->geo]->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$pr->price}}
                                                        @if (isset($countries[$pr->geo]) )
                                                            {{$countries[$pr->geo]->currency}}
                                                        @endif
                                                    </td>
                                                    <td style="width: 40px">
                                                        <a href="#" class="table-link danger delete_product">
                                                                    <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif($product[0]->type == 0)
                                            <tr>
                                                <td colspan="4"> <b>Товар</b></td>
                                            </tr>
                                            @foreach($product as $pr)
                                                <tr>
                                                    <td>{{$pr->product}}</td>
                                                    <td>
                                                        @if (isset($countries[$pr->geo]) )
                                                            {{$countries[$pr->geo]->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$pr->price}}
                                                        @if (isset($countries[$pr->geo]) )
                                                            {{$countries[$pr->geo]->currency}}
                                                        @endif
                                                    </td>
                                                    <td style="width: 40px">
                                                        <a href="#" class="table-link danger delete_product">
                                                                    <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop