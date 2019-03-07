@extends('layouts.app')
@section('title') @lang('general.campaigns') @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('general.all-campaigns')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.all-campaigns')</h1>
                @if (isset($permissions['create_edit_cold_call_list']))
                    <div class="pull-right top-page-ui">
                        <a href="{{route('campaigns-create')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('general.campaign-create')
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{ route('cold-calls-campaigns') }}"> @lang('general.all-campaigns')</a>
                        </li>
                        <li class="">
                            <a href="{{ route('cold-calls-operators-settings') }}"> @lang('general.operators-setting')</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">

                                        @if (!empty($campaigns))

                                            <div class="main-box-body clearfix"
                                                 style="margin-top: 20px;padding: 0 0 20px 0;">
                                                <form method="post">
                                                    <div class="form-group" style="width: 300px">
                                                        <label for="company-select2"> @lang('general.company') </label>
                                                        <input type="hidden" name="company" id="company"
                                                               class="company-select2 required"
                                                               style="width: 100%"/>
                                                    </div>
                                                    <div class="form-group" style="width: 300px">
                                                        <input class="btn btn-success" type="submit"
                                                               name="button_filter" value="Фильтровать">
                                                    </div>

                                                </form>

                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th> @lang('general.id')</th>
                                                            <th class="text-center"> @lang('general.name')</th>
                                                            <th class="text-center"> @lang('general.company')</th>
                                                            <th class="text-center"> @lang('cold-calls.lists-quantity')</th>
                                                            <th class="text-center"> @lang('cold-calls.lists-quantity')</th>
                                                            <th class="text-center"> @lang('cold-calls.list-active-records')</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if ($campaigns)
                                                            @foreach($campaigns as $campaign)
                                                                <tr>
                                                                    <td>
                                                                        {{$campaign->id}}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{$campaign->name}}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{$campaign->company->name}}
                                                                    </td>
                                                                    <td class="text-center">{{count($campaign->coldCallFile)}}</td>
                                                                    @php
                                                                        $campaign->activePercent  = !empty($campaign->activePercent)  ? $campaign->activePercent : 0;
                                                                    @endphp
                                                                    <td class="text-center">{{(!empty($campaign->count_active_rows->active_rows) ?$campaign->count_active_rows->active_rows : 0 ) .' / '.
                                                                        (!empty($campaign->count_active_rows->quantity_lists) ? $campaign->count_active_rows->quantity_lists : 0). ' (' .  $campaign->activePercent. ') % '}}
                                                                        <div class="progress">
                                                                            <div style="width: {{$campaign->activePercent}}%;"
                                                                                 aria-valuemax="100"
                                                                                 aria-valuemin="0"
                                                                                 aria-valuenow="{{$campaign->activePercent}}"
                                                                                 role="progressbar"
                                                                                 class="progress-bar">
                                                                                    <span class="sr-only">{{$campaign->activePercent}}
                                                                                        % Complete</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center">{{count($campaign->users)}}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
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
    {{--{{ $campaigns->links() }}--}}
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-campaigns.js') }}"></script>

@stop
