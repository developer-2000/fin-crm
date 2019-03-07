@extends('layouts.app')

@section('title') @lang('general.offer-create')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/custom.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-offer-create.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('general.offer-create')</span></li>
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
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="col-sm-12 " method="post">
                        <div class="col-sm-6">
                            <header class="main-box-header clearfix">
                                <h2> @lang('cold-calls.offer-create')</h2>
                            </header>
                            <div class="form-group">
                                <div class="">
                                    <input class="form-control" type="text" name="name" id="name" value=""
                                           placeholder="{{trans('general.name')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label id="label_country"> @lang('general.country') </label>
                                <select style="width:300px;display: inline-block" id="sel-country" name="country"
                                        class="form-control">
                                    <option value=""></option>
                                    @foreach($countries as $country)
                                        <option value="{{strtolower($country->code)}}"> @lang('countries.' . $country->code)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label id="company"> @lang('general.company') </label>
                                <select style="width:300px;display: inline-block" id="company" name="company"
                                        class="form-control">
                                    <option value=""></option>
                                    @foreach($companies as $company)
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="submit" value="{{trans('general.save')}}" class="btn btn-success">
                        </div>
                        <div class="col-sm-6 billing_block">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop