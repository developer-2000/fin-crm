@extends('layouts.app')
@section('title') @lang('products.edit') @stop
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
                <li><a href="/"> @lang('general.edit')</a></li>
                <li><a href="{{route('products')}}"> @lang('products.all')</a></li>
                <li class="active"><a href=""><span> @lang('products.edit')</span></a></li>
            </ol>
            <h1> @lang('products.edit')</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{ route('products-edit', Request::segment(2)) }}"> @lang('general.edit')</a>
                        </li>
                    </ul>
                    @if(!empty($product))
                        {{ Form::open(['method'=>'POST', 'class'=> 'form-horizontal','id' => 'product-store'])}}
                        <div class="col-sm-6">
                            <div class="main-box-body clearfix">
                                <div class="form-group">
                                    <label for="name"> @lang('general.name')</label>
                                    {{ Form::text('title', $product->title, ['class' => 'form-control', 'id' => 'name', 'rows' => 3]) }}
                                </div>
                                @if($projects->count())
                                    <div class="form-group">
                                        <label class="col-lg-3" for="project">  @lang('general.project-add')</label>
                                        <input type="hidden" name="sub_projects" id="sub_projects"
                                               class="sub_projects"
                                               style="width: 100%"/>
                                        <input type="hidden" name="subProjectsJson" id="subProjectsJson"
                                               value="{{!empty($subProjectsJson) ? $subProjectsJson : NULL}}">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="product_id"> @lang('general.product') @lang('general.id')</label>
                                    {{ Form::text('product_id', $product->product_id ?? '', ['class' => 'form-control', 'id' => 'product_id']) }}
                                </div>
                                <div class="form-group">
                                    <label for="sku"> SKU </label>
                                    {{ Form::text('sku', $product->sku ?? '', ['class' => 'form-control', 'id' => 'sku']) }}
                                </div>
                                @if($categories->count())
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="category_id"> @lang('general.category')</label>
                                        <select required id="category_id" name="category_id"
                                                class="form-control">
                                            <option value=""> @lang('general.select-category')</option>
                                            @foreach($categories as $category)
                                                <option
                                                        @if($product->category_id == $category->id)
                                                        selected
                                                        @endif
                                                        value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="col-lg-5">
                                                <label for="weight"> @lang('general.product-weight')</label>
                                                {{ Form::number('weight', $product->weight, ['class' => 'form-control', 'id' => 'weight']) }}
                                            </div>
                                            <div class="col-lg-2">
                                            </div>
                                            <div class="col-lg-5">
                                                <label for="cost_price"> @lang('general.cost-price'), $</label>
                                                {{ Form::number('price_cost', $product->price_cost, ['class' => 'form-control ', 'id' => 'cost_price']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="form-group">--}}
                                {{--<label for="">Статус</label>--}}

                                {{--<div class="radio radio-inline" style="top:-0.6em">--}}
                                {{--{{ Form::radio('status', 'on', $product->productProjects->status == 'on' , ['id' => 'on']) }}--}}
                                {{--{{ Form::label('on','On') }}--}}
                                {{--</div>--}}
                                {{--<div class="radio radio-inline" style="top:-0.3em">--}}
                                {{--{{ Form::radio('status', 'off', $product->productProjects->status == 'off' , ['id' => 'off']) }}--}}
                                {{--{{ Form::label('off','Off') }}--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                <div class="form-group">
                                    <div class="text-center">
                                        {{Form::submit(trans('general.save'), ['class' => 'btn btn-success'])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="main-box-body clearfix">
                                <header>
                                    <h2> @lang('general.options')</h2>
                                </header>
                                <div id="options">
                                    @if ($product->options->isNotEmpty())
                                        @foreach($product->options as $option)
                                            <div class="form-group option">
                                                <div class="col-sm-11">
                                                    <input type="text" name="options[{{$option->id}}][value]"
                                                           class="form-control" value="{{$option->value}}">
                                                    <input type="hidden" name="options[{{$option->id}}][id]"
                                                           value="{{$option->id}}">
                                                </div>
                                                <div class="col-sm-1">
                                                    <a href="#" class="table-link delete_option">
                                                         <span class="fa-stack ">
                                                         <i class="fa fa-square fa-stack-2x"></i>
                                                         <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                         </span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="button" class="btn-success btn" id="add_option" value="{{trans('general.add')}}">
                            </div>
                            <hr>
                            @if (isset($permissions['merge_products']))
                                <div class="main-box-body clearfix">
                                    <header>
                                        <h2> @lang('products.combine-with-product')</h2>
                                    </header>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-warning fa-fw fa-lg"></i>
                                        <strong> @lang('general.attention') !</strong> @lang('products.after-combine-attention')
                                    </div>
                                    <div id="merge_products">
                                        <div class="form-group form-horizontal">
                                            {{--<label for="product_to_merge" class="col-lg-2 control-label">Товары</label>--}}
                                            <div class="col-lg-8">
                                                <input id="product_to_merge"
                                                       data-product-merge="{{!empty($dataProducts) ? $dataProducts : ''}}"
                                                       class="product_to_merge " name="product_to_merge"
                                                       value="{{!empty($dataProductsIds) ? $dataProductsIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="button" class="btn-success btn" id="merge_products_button"
                                           value="{{ trans('general.combine')}}">
                                </div>
                            @endif
                            <hr>
                            <div class="main-box-body clearfix">
                                <header>
                                    <h2> @lang('general.type-assign')</h2>
                                </header>
                                <div class="alert alert-warning">
                                    @lang('general.select-type')
                                </div>
                                <div class="form-group">
                                    @foreach(config('app.product_types') as $key => $type)
                                        <div class="radio radio-inline">
                                            {{ Form::radio('type', $key, $product->type == $key, ['id' => $key]) }}
                                            {{ Form::label($key, $type) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="productId" name="productId" value="{{Request::segment(2)}}">
                        {{ Form::close()}}
                    @endif
                </div>
            </div>
        </div>
        <div class="hidden">
            <div class="form-group option">
                <div class="col-sm-11">
                    <input type="text" name="options[][value]" class="form-control">
                    <input type="hidden" name="options[][id]">
                </div>
                <div class="col-sm-1">
                    <a href="#" class="table-link delete_option">
                     <span class="fa-stack ">
                     <i class="fa fa-square fa-stack-2x"></i>
                     <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                     </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/products/index.js') }}"></script>
@stop
