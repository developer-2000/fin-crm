@extends('layouts.app')

@section('title')Курсы валют@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop

@section('jsBottom')
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('general.exchange-rates')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.exchange-rates')</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box no-header clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table table-striped" id="statuses">
                            <thead>
                            <tr>
                                <th> @lang('general.country')</th>
                                <th> @lang('general.code')</th>
                                <th> @lang('general.currency')</th>
                                <th> @lang('general.rate')</th>
                                <th> @lang('general.updated')</th>
                            </tr>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($countries)
                                @foreach($countries as $country)
                                    <tr>
                                        <td>
                                            @lang('countries.' . $country->code)
                                        </td>
                                        <td>
                                            {{$country->code}}
                                        </td>
                                        <td>
                                            {{$country->currency}}
                                        </td>
                                        <td>
                                            {{$country->exchange_rate}}
                                        </td>
                                        <td>
                                            {{$country->updated_at->format('d/m/Y h:i:s')}}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pull-right">
                    {{$countries->links()}}
                </div>
            </div>
        </div>
    </div>
@stop
