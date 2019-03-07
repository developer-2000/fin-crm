@extends('layouts.app')

@section('title') @lang('orders.all')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order.js?a=1') }}"></script>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('orders.all-supplies')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> Все заказы (<span class="badge">{{ \App\Models\OrdProduct::count() }}</span>)</h1>
                <a href="{{route('all_orders-create')}}" class="btn btn-primary pull-right">
                    <i class="fa fa-plus"></i> Новый заказ </a>
            </div>
        </div>
    </div>


    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            <div class="table-responsive">
                <table id="table_movings" class="table table-striped table-hover storage-table">
                    <thead>
                    <tr style="border-bottom:none;">
                        <th><span>ID</span></th>
                        <th><span> Проект, <br> Субпроект</span></th>
                        <th><span> Заказчик </span></th>
                        <th><span> Товар</span></th>
                        <th><span> Цвет</span></th>
                        <th><span> Кол-во</span></th>
                        <th><span> Статус</span></th>
                        <th><span> Фин-статус</span></th>
                        <th><span> Дата создания</span></th>
                    </tr>
                    </thead>

                    @if($paginate->isNotEmpty())
                        <tbody>
                        @foreach($paginate as $order)
                            @if(count($order->products))
                                @foreach($order->products as $product)
                            <tr>
                                <td>
                                    {{-- id заказа--}}
                                    <a href="{{ route('all_orders-one', $order->id) }}" target="_blank"> #{{ $order->id }} </a>
                                </td>
                                <td>
                                    {{-- проект - sub-проект --}}
                                    {{$order->project_name}} / {{$order->sub_name}}
                                </td>
                                <td>
                                    {{-- имя фамилия заказчика --}}
                                    {{$order->user['name']}} / {{$order->user['surname']}}
                                </td>
                                <td>
                                    {{-- название продукта --}}
                                    <?php print $order->products[0]->prod_title; ?>
                                </td>
                                <td>
                                    <?php
                                    $a = $product->pivot['color_id'] ? $product->pivot['color_id'] : 4;
                                    print \App\Models\OrdOrder::getSetingsColor()[$a];
                                    ?>
                                </td>
                                <td>
                                    {{$product->pivot['color_amount']}}
                                </td>
                                <td>
                                    <span class="label label-danger">{{\App\Models\OrdOrder::getStatus()[1]}}</span>
                                </td>
                                <td>
                                    <span class="label label-danger">{{\App\Models\OrdOrder::getStatus()[1]}}</span>
                                </td>
                                <td>{{$order->creat_order}}</td>
                            </tr>
                                @endforeach
                            @endif
                        @endforeach
                        </tbody>
                    @endif
                </table>
            </div>

            @if($paginate->isEmpty())
                <p> @lang('general.no-results')</p>
            @endif
        </div>
    </div>


    {!! $paginate->links() !!}


@stop
