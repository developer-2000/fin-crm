@extends('layouts.app')

@section('title')Мониторинг@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}" />
    <style>
        td {
            transition: all 1s;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/monitoring/monitoring-ws-client.js') }}"></script>
    <script src="{{ URL::asset('js/monitoring/monitoringOrderByWeight.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('monitoring.priority-monitoring')</span></li>
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
                    <div class="table-responsive">
                        <table class="table table_weights">
                            <thead>
                            <tr>
                                <th> @lang('general.weight')</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden">
        @if ($campaigns)
            @foreach($campaigns as $campaign)
                <div id="campaign_{{$campaign->id}}" data-id="{{$campaign->id}}">
                    {{$campaign->name}}
                </div>
            @endforeach
        @endif
    </div>
@stop