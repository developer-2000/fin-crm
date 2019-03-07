@extends('layouts.app')

@section('title') @lang('general.monitoring')@stop

@section('css')
    <style>
        td {
            transition: all 1s;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/monitoring/monitoring-ws-client.js') }}"></script>
    <script src="{{ URL::asset('js/monitoring/monitoring_calls.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span>  @lang('monitoring.monitoring-processing-calls')</span></li>
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
                    @if ($order)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th class="text-center"> @lang('monitoring.monitoring-processing-calls')</th>
                                    <th class="text-center"> @lang('general.processing') СRМ</th>
                                    <th class="text-center"> @lang('general.processing') Elastix</th>
                                    <th class="text-center"> @lang('monitoring.calls-today')</th>
                                    <th class="text-center"> @lang('monitoring.calls-new')</th>
                                    <th class="text-center"> @lang('general.in-processing')</th>
                                    <th class="text-center"> @lang('general.dialing')</th>
                                    <th class="text-center"> @lang('monitoring.operators-on-pause')</th>
                                    <th class="text-center"> @lang('monitoring.operators-today')</th>
                                    <th class="text-center"> @lang('monitoring.operators-total')</th>
                                    <th class="text-center"> @lang('monitoring.operators-online')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($order as $co)
                                    <tr id="{{$co->id}}">
                                        <td>
                                            {{$co->id}}
                                        </td>
                                        <td>
                                            {{ $co->name }}
                                        </td>
                                        <td class="text-center proc_crm">
                                            0
                                        </td>
                                        <td class="text-center proc_elastix">
                                            0
                                        </td>
                                        <td class="text-center call_today">
                                            0
                                        </td>
                                        <td class="text-center new_order">
                                            0
                                        </td>
                                        <td class="text-center processing">
                                            0
                                        </td>
                                        <td class="text-center dialing">
                                            0
                                        </td>
                                        <td class="text-center oper_break">
                                            0
                                        </td>
                                        <td class="text-center oper_today">
                                            0
                                        </td>
                                        <td class="text-center oper_all">
                                            0
                                        </td>
                                        <td class="text-center oper_online">
                                            0
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <header class="main-box-header">
                        <h1> @lang('general.cold-calls')</h1>
                    </header>
                    @if ($hp)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th class="text-center"> @lang('monitoring.monitoring-processing-calls')</th>
                                    <th class="text-center"> @lang('general.processing') СRМ</th>
                                    <th class="text-center"> @lang('general.processing') Elastix</th>
                                    <th class="text-center"> @lang('monitoring.calls-today')</th>
                                    <th class="text-center"> @lang('monitoring.calls-new')</th>
                                    <th class="text-center"> @lang('general.in-processing')</th>
                                    <th class="text-center"> @lang('general.dialing')</th>
                                    <th class="text-center"> @lang('monitoring.operators-on-pause')</th>
                                    <th class="text-center"> @lang('monitoring.operators-today')</th>
                                    <th class="text-center"> @lang('monitoring.operators-total')</th>
                                    <th class="text-center"> @lang('monitoring.operators-online')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($hp as $co)
                                    <tr id="{{$co->id}}">
                                        <td>
                                            {{$co->id}}
                                        </td>
                                        <td>
                                            {{ $co->name }}
                                        </td>
                                        <td class="text-center proc_crm">
                                            0
                                        </td>
                                        <td class="text-center proc_elastix">
                                            0
                                        </td>
                                        <td class="text-center call_today">
                                            0
                                        </td>
                                        <td class="text-center new_order">
                                            0
                                        </td>
                                        <td class="text-center processing">
                                            0
                                        </td>
                                        <td class="text-center dialing">
                                            0
                                        </td>
                                        <td class="text-center oper_break">
                                            0
                                        </td>
                                        <td class="text-center oper_today">
                                            0
                                        </td>
                                        <td class="text-center oper_all">
                                            0
                                        </td>
                                        <td class="text-center oper_online">
                                            0
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop
