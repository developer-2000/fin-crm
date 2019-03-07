@extends('layouts.app')
@section('title') @lang('cold-calls.all-lists')@stop
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
                <li class="active"><span> @lang('cold-calls.all-lists')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left" > @lang('cold-calls.all-lists')</h1>
                @if (isset($permissions['create_edit_cold_call_list']))
                    <div class="pull-right top-page-ui">
                        <a href="{{route('cold-calls-import')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('cold-calls.list-upload')
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
                            <a href="{{ route('cold-calls-lists') }}"> @lang('cold-calls.all-lists')</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        @if (!$lists->isEmpty())
                                            <div class="main-box-body clearfix"
                                                 style="margin-top: 20px;padding: 0 0 20px 0;">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover all_lists">
                                                        <thead>
                                                        <tr>
                                                            <th> @lang('general.id')</th>
                                                            <th> @lang('general.activity')</th>
                                                            <th class="text-center"> @lang('general.date-created')</th>
                                                            <th class="text-center"> @lang('general.file-name')</th>
                                                            <th class="text-center"> @lang('general.country')</th>
                                                            <th class="text-center"> @lang('general.company')</th>
                                                            <th class="text-center"> @lang('general.campaign')</th>
                                                            <th class="text-center"> @lang('general.operators-quantity')</th>
                                                            <th class="text-center"> @lang('cold-calls.list-active-records')</th>
                                                            <th class="text-center"> @lang('general.comment')</th>
                                                            <th class="text-center"></th>
                                                            <th class="text-center"></th>
                                                            <th class="text-center"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($lists as $list)
                                                            <tr>
                                                                <td class="text-center">{{$list->id}}</td>
                                                                <td class="text-center">
                                                                    @if($list->status == 'active')
                                                                        <span id="activity" class="label label-success"> @lang('general.active')</span>
                                                                    @elseif($list->status == 'finished')
                                                                        <span id="activity" class="label label-warning"> @lang('general.finished')</span>
                                                                    @elseif($list->status == 'inactive')
                                                                        <span id="activity" class="label label-danger"> @lang('general.inactive')</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">{{$list->created_at->format('m/d/Y')}}</td>
                                                                <td class="text-center">{{$list->file_name}} </td>
                                                                <td class="text-center">{{!empty($list['country']->name) ? $list['country']->name : NULL}}</td>
                                                                <td class="text-center">{{ $list->company->name??null }}</td>
                                                                <td class="text-center">
                                                                    @if($list->campaign)
                                                                        {{$list->campaign->name}}
                                                                    @else
                                                                        {{'N/A'}}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">@if($list->campaign) {{ $list->campaign->users->count() }} @else N\A @endif</td>
                                                                <td class="text-center">{{(!empty($list->countListProcess) ? $list->countListProcess : 0 ) .' / '.
                                                               (!empty($list->countList) ? $list->countList : 0). ' (' . ($list->countList > 0 ? round($list->countListProcess / $list->countList * 100, 2) : 0) . ') % '}}
                                                                    <div class="progress">
                                                                        <div style="width: {{ $list->countList > 0 ? round($list->countListProcess / $list->countList * 100, 2) : 0 }}%;"
                                                                             aria-valuemax="100" aria-valuemin="0"
                                                                             aria-valuenow="{{$list->countList > 0 ? round($list->countListProcess / $list->countList * 100, 2) : 0}}"
                                                                             role="progressbar" class="progress-bar">
                                                                            <span class="sr-only">{{ $list->countList > 0 ? round($list->countListProcess / $list->countList * 100, 2) : 0}}
                                                                                % @lang('general.complete')</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">{{$list->comment}}</td>
                                                                @if (isset($permissions['create_edit_cold_call_list']))
                                                                    <td>
                                                                        <a href="{{route('cold-calls-lists-edit',  $list->id)}}"
                                                                           class="table-link">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                                        </a>
                                                                    </td>
                                                                @endif
                                                                <td class="text-center">
                                                                    <a href="{{route('cold-calls-lists-info', $list->id)}}"
                                                                       class="table-link ">
                                                                <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x "></i>
                                                                <i class="fa fa-file-text-o fa-stack-1x fa-inverse"></i>
                                                                </span>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="table-link danger delete_list">
                                                                    <span class="fa-stack " data-id="{{ $list->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                @lang('cold-calls.cold-call-list-no-result')
                                                <a href="{{route('cold-calls-import')}}" target="_blank"> @lang('cold-calls.create')</a>
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
    {{ $lists->links() }}
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-lists.js') }}"></script>
@stop
