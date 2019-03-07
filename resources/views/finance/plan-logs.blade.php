@extends('layouts.app')

@section('title') @lang('finance.bonus-programs') @stop

@section('css')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/profile.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plans.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('finance.logs-by-transactions') </span></li>
                {{-- Логи по проведенным плановым транзакциям --}}
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('finance.logs-by-transactions')</h1>
                {{--@if (isset($permissions['create_edit_plan']))--}}
                {{--<div class="pull-right top-page-ui">--}}
                {{--<a href="{{route('plans-create')}}" class="btn btn-primary pull-right">--}}
                {{--<i class="fa fa-plus-circle fa-lg"></i> Добавить программу--}}
                {{--</a>--}}
                {{--</div>--}}
                {{--@endif--}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="">
                            <a href="{{ route('plans') }}"> @lang('finance.plans')</a>
                        </li>
                        <li class="active">
                            <a href="{{ route('plans-logs') }}"> @lang('finance.logs-by-transactions')</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        @if (!$planLogs->isEmpty())
                                            <div class="main-box-body clearfix"
                                                 style="margin-top: 20px;padding: 0 0 20px 0;">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center"> @lang('general.id')</th>
                                                            <th class="text-center"> @lang('general.create')</th>
                                                            <th class="text-center"> @lang('general.plan') @lang('general.id')</th>
                                                            <th class="text-center"> @lang('general.company')</th>
                                                            <th class="text-center"> @lang('general.operator')</th>
                                                            <th class="text-center"> @lang('finance.text-log')</th>
                                                            <th class="text-center"> @lang('finance.plan-completed')/@lang('finance.plan-not-completed')</th>
                                                            <th class="text-center"> @lang('finance.type-transaction')</th>
                                                            <th class="text-center"> @lang('general.sum'), грн</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($planLogs as $planLog)
                                                            <tr>
                                                                {{--{{dump($planLog)}}--}}
                                                                <td class="text-center">{{$planLog->id}}</td>
                                                                <td class="text-center">{{$planLog->created_at}}</td>
                                                                <td class="text-center">{{$planLog->plan_id}}</td>
                                                                <td class="text-center">{{$planLog['company']->name}}</td>
                                                                <td class="text-center">{{$planLog['operator']->surname.' '. $planLog['operator']->name}}</td>
                                                                <td class="text-center">
                                                                {{$planLog->text}}
                                                                <td class="text-center">
                                                                    @if($planLog->type == 'success')
                                                                        <span class="label label-success"> @lang('general.success')</span>
                                                                    @else
                                                                        <span class="label label-danger"> @lang('general.failed')</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($planLog->type == 'success' && intval($planLog->result) !==0)
                                                                        <span class="label label-primary"> @lang('general.bonus')</span>
                                                                    @elseif($planLog->type == 'success' && intval($planLog->result) ==0
                                                                    ||$planLog->type == 'failed' && intval($planLog->result) ==0)
                                                                        <span class="label label-default">N/A</span>
                                                                    @elseif($planLog->type == 'failed' && intval($planLog->result) !==0)
                                                                        <span class="label label-warning"> @lang('general.retention')</span>
                                                                    @endif</td>
                                                                <td class="text-center">
                                                                    @if($planLog->type == 'success' && $planLog->result !==0 ||
                                                                    $planLog->type == 'failed' && $planLog->result !==0)
                                                                        {{$planLog->result}}
                                                                    @else
                                                                        {{'N/A'}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                @lang('finance.transactions-not-created')
                                                {{-- Ни одной транзакции по планам еще не создано! --}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $planLogs->links() }}
@stop
