@extends('layouts.app')

@section('title') @lang('general.products')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        .ns-box {
            z-index: 5000
        }

        body {
            color: grey;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/products/index.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.destroy-product').editable({
                type: 'none',
                escape: true,
                title: 'Вы действительно хотите удалить товар?',
                tpl: '',
                success: function (response) {
                    if (response.pk) {
                        var parent = $("a[data-pk='" + Number(response.pk) + "']").parents('tr');
                        parent.fadeOut(400);
                        setTimeout(function () {
                            parent.remove();
                        }, 400);
                    }
                    if (response.exist) {
                        getMessage('warning', 'С данным товаром существуют заказы. Товар удалить нельзя! ')
                    }
                }
            });
        });
        $('.product').editable({
            escape: true,
            title: 'Редактировать наименование',
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('general.products')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.products')</h1>
                @if(isset($permissions['add_new_product']))
                    <div class="pull-right">
                        <button class="md-trigger btn btn-success mrg-b-lg"
                                data-modal="product-create"> @lang('general.product-create')
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url() }}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <br>
                                <div class="row">
                                    @if (isset($permissions['filter_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="project"
                                                   class="col-sm-4 control-label"> @lang('general.project')</label>
                                            <div class="col-sm-8">
                                                @if(!auth()->user()->project_id)
                                                    <input id="project"
                                                           data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                           class="project " name="project[]"
                                                           value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                           style="width: 100%">
                                                @else
                                                    <input type="hidden" id="project"
                                                           class="project " name="project[]"
                                                           value="{{auth()->user()->project_id}}">
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="sub_project"
                                                   class="col-sm-4 control-label"> @lang('general.subproject')</label>
                                            <div class="col-sm-8">
                                                <input id="sub_project"
                                                       data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                                       class="sub_project " name="sub_project[]"
                                                       value="{{$dataSubProject ?? NULL}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group col-md-3 col-sm-6">
                                        <label for="category"
                                               class="col-sm-4 control-label"> @lang('general.category')</label>
                                        <div class="col-sm-8">
                                            <select id="category" name="category[]" style="width: 100%" multiple>
                                                @foreach ($categories as $category)
                                                    <option
                                                            @if (isset($_GET['category']))
                                                            <? $categoryGet = explode(',', $_GET['category']); ?>
                                                            @foreach ($categoryGet as $cg)
                                                            @if ($category->id == $cg)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="{{ $category->id }}">{{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @if (isset($permissions['filter_offers_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="offers"
                                                   class="col-sm-2 control-label"> @lang('general.offers')</label>
                                            <div class="col-sm-10">
                                                <input id="offers"
                                                       data-offers="{{!empty($dataOffers) ? $dataOffers : ''}}"
                                                       class="offers " name="offers[]"
                                                       value="{{!empty($dataOffersIds) ? $dataOffersIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_products_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="product"
                                                   class="col-sm-2 control-label"> @lang('general.products')</label>
                                            <div class="col-sm-10">
                                                <input id="product"
                                                       data-product="{{!empty($dataProducts) ? $dataProducts : ''}}"
                                                       name="product[]"
                                                       value="{{!empty($dataProductsIds) ? $dataProductsIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <input class="btn btn-success" type="submit" name="button_filter"
                               value='{{trans('general.search')}}'/>
                        <a href="{{ route('products') }}" class="btn btn-warning"
                           type="submit"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>
    <div class="md-modal md-effect-15" id="product-create">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title"> @lang('general.product-create')</h4>
            </div>
            <div class="modal-body">
                <div class="tabs-wrapper">
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab-failed-ticket">
                            {{ Form::open(['method'=>'POST', 'id' => 'product-create'])}}
                            <div class="form-group">
                                <label for="title"> @lang('general.name')</label>
                                {{ Form::text('title', null, ['class' => 'form-control', 'id' => 'title', 'rows' => 3]) }}
                            </div>
                            {{--@if($projects->count())--}}
                            {{--<div class="col-lg-12">--}}
                            {{--<div class="form-group">--}}
                            {{--<label class="col-lg-2 control-label"--}}
                            {{--for="project">Проект</label>--}}
                            {{--<select class="col-lg-8" id="project" name="project_id">--}}
                            {{--<option value="">Выберите проект</option>--}}
                            {{--@foreach($projects as $project)--}}
                            {{--<option value="{{$project->id}}">{{$project->name}}</option>--}}
                            {{--@endforeach--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            {{--</div>--}}

                            {{--@endif--}}
                            <div class="form-group">
                                <label for="product_id"> @lang('general.product') @lang('general.id')</label>
                                {{ Form::text('product_id', null, ['class' => 'form-control', 'id' => 'product_id']) }}
                            </div>
                            @if($categories->count())
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"
                                           for="category_id"> @lang('general.category')</label>
                                    <div class="col-lg-8">
                                        <select required id="category_id" name="category_id"
                                                class="form-control">
                                            <option value=""> @lang('general.select-category')</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="col-lg-5">
                                            <label for="weight"> @lang('general.product-weight')</label>
                                            {{ Form::number('weight', null, ['class' => 'form-control', 'id' => 'weight']) }}
                                        </div>
                                        <div class="col-lg-2">
                                        </div>
                                        <div class="col-lg-5">
                                            <label for="cost_price"> @lang('general.cost-price'), $</label>
                                            {{ Form::number('price_cost', null, ['class' => 'form-control ', 'id' => 'cost_price']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="weight"> @lang('general.status')</label>
                                <div class="radio radio-inline" style="top:-0.6em">
                                    {{ Form::radio('status', 'on', true, ['id' => 'on']) }}
                                    {{ Form::label('on','On') }}
                                </div>
                                <div class="radio radio-inline" style="top:-0.3em">
                                    {{ Form::radio('status', 'off', false, ['id' => 'off']) }}
                                    {{ Form::label('off','Off') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="text-center">
                                    {{Form::submit(trans('general.save'), ['class' => 'btn btn-success'])}}
                                </div>
                            </div>
                            {{ Form::close()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        @if ($products)
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th> @lang('general.id')</th>
                                    <th> @lang('general.project')<br> @lang('general.subproject')</th>
                                    <th> @lang('general.type')</th>
                                    <th> @lang('general.category')</th>
                                    <th> @lang('general.name')</th>
                                    @if(isset($permissions['product_edit']))
                                        <th> @lang('general.status')</th>
                                    @else
                                    @endif
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{$product->id}}</td>
                                        <td>
                                            @if(isset($product->productProjects))
                                                @foreach($product->productProjects as $productProjects)
                                                    <span style="font-weight: bold; color: #5d5c5b">{{$productProjects->project->name }}</span>
                                                    <br>
                                                    <span style="font-weight: bold; color: #848382">{{!empty($productProjects->subProject->name) ? $productProjects->subProject->name : ''}}</span>
                                                    <br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td><span style="font-weight: bold; color: #077871">
                                                {{ config('app.product_types')[$product->type] }}</span>
                                        </td>
                                        <td>
                                            <span style="font-weight: bold; color: #1ABC9C">{{ !empty($product->category->name) ? $product->category->name : '' }}</span>
                                        </td>
                                        @if(isset($permissions['product_edit']))
                                            <td>
                                                <a href="#" data-type="text"
                                                   data-pk="{{  $product->id }}"
                                                   data-name="name"
                                                   data-url="/ajax/products/edit-product-title"
                                                   data-id="{{ $product->title }}"
                                                   data-title="{{ trans('general.enter-new-name')}}"
                                                   class="editable editable-click product"
                                                   data-original-title=""
                                                   title="">{{ $product->title}}</a>
                                            </td>
                                        @else
                                            <td>
                                                {{ $product->title}}
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if(isset($permissions['product_edit']))
                                                <div class="checkbox-nice checkbox">
                                                    @php
                                                        if($product->status == 'on'){
                                                        $status = true;
                                                        }else{
                                                            $status = false;}
                                                    @endphp
                                                    {{ Form::checkbox('activity',  $product->id, $status, ['id' => 'activate_'. $product->id, 'class' => 'activate_product']) }}
                                                    {{ Form::label('activate_'. $product->id, ' ') }}
                                                </div>
                                            @else
                                            @endif
                                        </td>
                                        @if(isset($permissions['product_edit']))
                                            <td>
                                                <a href="{{route('products-edit', $product->id)}}"
                                                   class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                </a>
                                            </td>
                                        @endif
                                        @if(isset($permissions['product_destroy']))
                                            <td>
                                                <a href="#"
                                                   data-type="text"
                                                   data-pk="{{ $product->id }}"
                                                   data-title="{{trans('general.delete')}}?"
                                                   data-id="{{ $product->id }}"
                                                   data-url="/ajax/products/destroy"
                                                   class="editable editable-click table-link danger  destroy-product"> @lang('general.delete')</a>
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
            <div class="text-center">{{$products->links()}} </div>
        </div>
    </div>
@stop
