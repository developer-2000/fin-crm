@extends('layouts.app')

@section('title')@lang('warehouses.moving-create')@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/select2.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::asset('css/storages-common.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::asset('css/moving_create.css') }}" type="text/css"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/delivery/creat_order.js') }}"></script>
    {{--<script src="{{ URL::asset('js/delivery/all_orders_create.js') }}"></script>--}}
@stop

@section('content')



    <style>
        #product_id_papa,
        #block_order{
            display: none;
        }
        .input-group-addon{
            border-left: 2px solid #e7ebee!important;
            line-height: 19px!important;
        }
        #block_order{
            /*outline: 1px solid red;*/
            margin: 65px 0px 100px 0px;
        }
        .product_papa{
            border-top: 1px solid #bababa;
            border-bottom: 1px solid #bababa;
            position: relative;
        }
        .product_papa{
            padding: 20px!important;
        }
        .for_title{
            overflow-x: hidden;
            max-width: 200px;
            /*min-width: 400px;*/
            text-overflow: ellipsis;
        }
        .flex_one_product textarea{
            min-height: 200px;
        }
        .numbo_inut{
            min-width: 92px;
            width: 100px;
        }
        .block_text{
            /*width: 100%;*/
        }
        .block_text textarea{
            min-height: 210px;
        }
        .numbo_product{
            position: absolute;
            top: -14px;
            left: -3px;
            background-color: #dedede;
            padding: 3px 9px;
            border: 1px solid #b1b1b1;
            border-radius: 3px;
        }

        /* ========== */

        .block_color{
            /*outline: 1px solid red;*/
        }
        .one_color{
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-content: stretch;
            align-items: flex-start;
            margin-top: 10px;
        }
        .color{
            width: 82%;
            line-height: 30px;
            text-align: center;
            border-top: 2px solid #E7EBEE;
            border-bottom: 2px solid #E7EBEE;
            border-left: 2px solid #E7EBEE;
        }
        #alert_error{
            padding: 10px;
            text-align: center;
            margin-bottom: 45px;
            display: none;
        }








        @media only screen and (max-width : 1100px) {
            .block_text{
                margin: 40px 0px 0px 0px;
            }
            .for_title {
                width: 100%;
            }
        }

        @media only screen and (max-width : 650px) {
            .for_title {
                max-width: 150px;
                min-width: 0px;
            }
        }

    </style>

    <script>
        var Sub = [];

        <?php foreach ($subProjects->toArray() as $key => $val){
            print "Sub.push({'id': ".$val['id'].", 'name': '".$val['name']."', 'parent_id': ".$val['parent_id']." });";
        }
        ?>
    </script>

{{--HEADER--}}
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('index') }}"> @lang('general.main')</a></li>
                <li><a href="{{ route('all_orders') }}"> @lang('general.all-orders')</a></li>
                <li class="active"><span> @lang('general.create order')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.create order')</h1>
            </div>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success"> <i class="fa fa-check-circle fa-fw fa-lg"></i> {{ session('message') }} </div>
    @endif


{{--BODY--}}
    <div class="main-box clearfix">
        <div class="main-box-body clearfix content">

    {{--block_projects--}}
    <div class="row block_projects" style="padding-top:32px;">


{{--ПРОЕКТ--}}
                {{-- если колекция не пуста --}}
                @if ($projects->isEmpty())

                {{-- если в колекции 1 проект - тоесть я юзер этого проекта --}}
                @elseif ($projects->count() == 1)
                    <div class="col-sm-3">
                        <input id="project_id" type="hidden" name="project_id" value="{{ $projects[0]->id }}" />
                        <h3 class="h3-create-moving">{{ $projects[0]->name }}</h3>
                    </div>

                {{-- если в колекции больше 1 проекта --}}
                @else
                    <div class="col-sm-3 form-group form-group-select2" id="block_proj">
                        {!! Form::label('project_id', trans('general.project'), ['class' => 'storage-label']) !!}
                        {!! Form::select( 'project_id', $projects->pluck('name', 'id'), null,
                            [
                                'placeholder' => '--' . trans('general.select') . '--',
                                'id' => 'project_id',
                                'class' => 'storage',
                                'style' => 'width:100%;',
                                'data-url' => route('moving-get-storages')
                            ]
                        ) !!}
                    </div>
                @endif

{{-- SUB-ПРОЕКТ--}}
                @if (auth()->user()->sub_project_id)
                    <div class="col-sm-3 col-sm-offset-1">
                        <input id="subproj" type="hidden" name="subproj" value="{{ $subProjects[0]->id }}" />
                        <h3 class="h3-create-moving"> <i class="fa fa-cubes"></i> {{ $subProjects[0]->name }} </h3>
                    </div>
                @elseif(!auth()->user()->sub_project_id)
                    <div class="col-sm-3 col-sm-offset-1 form-group form-group-select2" id="block_sub">
                        <label for="sub_id" class="storage-label">Подпроекты</label>
                        <select id="sub_id" class="storage" style="width:100%;" data-url="http://crm.lara/storages/movings/get-products" name="sub_id"></select>
                    </div>
                @endif


{{-- поле поиска --}}
                <div class="col-sm-7" id="product_id_papa" style="padding-top:12px;">
                    <div class="form-group form-group-select2">
                        <label for="product_id" class="storage-label">Добавить товар</label>
                        <div class="input-group" style="width:100%;">

                            <span class="input-group-addon "> <i class="fa fa-plus"></i> </span>

                            <input id="product_id" name="product_id"
                                   data-url="{{ route('all_orders-products-list') }}"
                                   data-url2="{{ route('all_orders-plus-product') }}"
                                   value="" placeholder="--@lang('general.select-product')--" style="width:100%;" />
                        </div>
                    </div>
                </div>



                <div style="clear: both; margin-bottom: 30px;"></div>

{{-- блок для найденного продукта --}}
                <div class="block_insert"></div> {{-- / block_insert --}}


{{-- кнопка отправки заказа --}}
                <div id="block_order">
                    <div class="block_product row">

                        {{-- alert error --}}
                        <p class="bg-danger" id="alert_error"></p>

                        <div class="col-sm-4 col-sm-offset-4" id="button_papa" style="padding-top:12px;">
                                <div class="form-controll">
                                    <button class="btn btn-primary" id="but_order" style="width:100%;"  data-url2="{{route('all_orders')}}" data-url="{{route('all_orders-add-order')}}">
                                        <i class="fa fa-road"></i>Создать заказ
                                    </button>
                                </div>
                        </div>

                    </div>
                </div>

    </div> {{-- / block_projects--}}


            {{--<div id="for_products"> </div>--}}
            {{--<div id="for_errors" style="display:none; padding-top:12px;">--}}
                {{--<div class="alert alert-warning text-center">--}}
                    {{--<i class="fa fa-exclamation-circle fa-fw fa-lg"></i>--}}
                    {{--<span></span>--}}
                {{--</div>--}}
            {{--</div>--}}

        </div>
    </div>

@stop
