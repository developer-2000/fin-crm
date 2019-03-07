@extends('layouts.app')

@section('title') @lang('general.menu') @stop

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
    <script src="{{ URL::asset('js/menu/index.js') }}"></script>
@stop

@section('content')
    <div class="md-modal md-effect-2" id="create_menu">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">
                    @lang('general.create')
                </h4>
            </div>
            <div class="modal-body">
                <form role="form" id="form_menu" action="{{route('menu.store')}}">
                    @include('menu.form')
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">@lang('general.create')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('general.menu')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.menu')</h1>
            </div>
        </div>
    </div>
    <div class="row" id="page_categories">
        <div class="col-md-6">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2>
                        @lang('general.menu')
                        <div class="pull-right">
                            <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="create_menu">@lang('general.create')</button>
                        </div>
                    </h2>
                </header>
                <div class="main-box-body clearfix">
                    <div class="dd nestable">
                        <ol class="dd-list">
                            @include('menu.item', ['menuItems' => $menuItems])
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div id="edit_block" style="display: none;"></div>
    </div>
@stop
