@extends('layouts.app')

@section('title') @lang('general.categories') @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/categories.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.nestable.js') }}"></script>
    <script src="{{ URL::asset('js/categories/categories.js') }}"></script>
@stop

@section('content')
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('general.categories')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.categories')</h1>
            </div>
        </div>
    </div>
    <div class="row" id="page_categories">

        @if ($entities)
            @foreach($entities as $entity => $title)
                <div class="col-md-12">
                    <div class="main-box clearfix">
                        <header class="main-box-header clearfix">
                            <h2>{{ $title}}
                                <div class="pull-right">
                                    <a href="#"
                                       class="add_categories"
                                       data-title=" @lang('general.name')"
                                       data-pk="0"
                                       data-entity="{{$entity}}"
                                       data-value=""
                                       style="font-size: 14px;"> @lang('general.create')</a>
                                </div>
                            </h2>
                        </header>
                        <div class="main-box-body clearfix">
                            <div class="dd nestable" data-group="{{$entity}}">
                                @if (isset($categoriesByEntities[$entity]) && $categoriesByEntities[$entity]->isNotEmpty())
                                    {{renderSubCategories($categoriesByEntities[$entity]->sortBy('position'))}}
                                @else
                                    <ol class="dd-list"></ol>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
@stop
