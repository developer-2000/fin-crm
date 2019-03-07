@extends('layouts.app')
@section('title') @lang('cold-calls.list-edit') @stop
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
                <li class="active"><span>  @lang('cold-calls.list'){{$listFile->id}}</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('cold-calls.list'){{$listFile->id}}</h1>
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
                            <a href="{{ route('cold-calls-lists') }}"> @lang('cold-calls.list') {{$listFile->id . '   /    ' . $listFile->file_name}}</a>
                        </li>
                    </ul>
                    <div class="main-box clearfix">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($listFile)
                            <div class="main-box clearfix">
                                {{Form::open(['id' => 'form1', 'method' => 'post'])}}
                                <div class="col-lg-6">
                                    <div class="main-box-body clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('id', 'ID') }}
                                                {{ Form::text('id', $listFile->id, ['id' => 'id', 'class' => 'form-control', 'disabled' => true]) }}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('created_at', trans('general.create')) }}
                                                {{ Form::text('id', $listFile->created_at->format('m/d/Y'), ['id' => 'created_at', 'class' => 'form-control', 'disabled' => true]) }}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('file_name', trans('general.file-name')) }}
                                                {{ Form::text('file_name', $listFile->file_name, ['id' => 'file_name', 'class' => 'form-control', 'disabled' => true]) }}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_id"> @lang('general.company')</label>
                                                <select class="form-control" name="company_id" id="company_id" required>
                                                    <option value="{{$listFile->country->id}}">{{$listFile->country->name}}</option>
                                                    @foreach($countries as $key=>$country)
                                                        <option value="{{$country->id}}">
                                                            @lang('countries.' . $country->code)
                                                        </option>
                                                        <span class="company_type"
                                                              style="display:none">{{$country->type}}</span>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="company_id">  @lang('general.company')</label>
                                                <select class="form-control" name="company_id" id="company_id" required>
                                                    <option value="{{$listFile->company->id}}">{{$listFile->company->name}}</option>
                                                    @foreach($companies as $key=>$company)
                                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                                        <span class="company_type"
                                                              style="display:none">{{$company->type}}</span>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('comment',  trans('general.comment')) }}
                                                {{ Form::textarea('comment', $listFile->comment, ['class' => 'form-control', 'id' => 'comment', 'rows' => 2]) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="main-box-body clearfix">
                                        @if($listFile->status == 'finished')
                                        @else
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label for="company_id"> @lang('general.campaign-add')</label>
                                                    <select class="form-control" name="campaign_id" id="campaign_id"
                                                            required>
                                                        @if($campaigns)
                                                            @foreach($campaigns as $key=>$campaign)
                                                                <option value=""> @lang('general.campaign-select')</option>
                                                                <option value="{{$campaign->id}}">{{$campaign->name }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value=""> @lang('general.campaign-no-result')</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <input type="hidden" name="campaign_id_hidden" id="campaign_id_hidden"
                                                       value="{{$listFile->campaign_id}}">
                                                <input type="hidden" name="listFileId" id="listFileId"
                                                       value="{{$listFile->id}}">
                                                <div class="pull-right " style="bottom: 0px">
                                                    {{Form::submit('Сохранить', ['class' => 'btn btn-success'])}}
                                                </div>
                                            </div>
                                        @endif
                                        {{ Form::close() }}
                                        @if($listFile->status == 'active' || $listFile->status == 'inactive')
                                            <div class="col-sm-7">
                                                <div class="form-group">
                                                    <div class="pull-right top-page-ui" style="padding-top: 35px">
                                                        <label> @lang('general.upload-to-pbx')</label>
                                                        <div class="onoffswitch">
                                                            @if($listFile->status == 'active')
                                                                <input type="checkbox" name="onoffswitch"
                                                                       class="onoffswitch-checkbox" id="myonoffswitch"
                                                                       checked>
                                                            @elseif($listFile->status == 'inactive')
                                                                <input type="checkbox" name="onoffswitch"
                                                                       class="onoffswitch-checkbox" id="myonoffswitch">
                                                            @endif
                                                            <label class="onoffswitch-label" for="myonoffswitch">
                                                                <div class="onoffswitch-inner"></div>
                                                                <div class="onoffswitch-switch"></div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($listFile->status == 'finished')
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <form id="form_pbx" role="form" method="post"
                                                          action="{{route('upload-cold-call-lists-pbx-ajax')}}"
                                                    >
                                                        <div class="alert alert-warning">
                                                            <i class="fa fa-info-circle fa-fw fa-lg"></i>
                                                            <strong> @lang('general.attention')!</strong>
                                                            @lang('cold-calls.list-was-upload')
                                                            {{--Данный лист уже загружался на прозвон.--}}
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="btn-group" data-toggle="buttons">
                                                                <label class="btn btn-danger">
                                                                    <input type="checkbox" name="status[]"
                                                                           value="'Failure'" id="failure">
                                                                    @lang('general.failure')
                                                                </label>
                                                                <label class="btn btn-danger">
                                                                    <input type="checkbox" name="status[]"
                                                                           value="'Noanswer'"
                                                                           id="noanswer"> @lang('general.no-answer')
                                                                </label>
                                                                <label class="btn btn-danger">
                                                                    <input type="checkbox" name="status[]"
                                                                           value="'Abandoned'"
                                                                           id="abandoned"> @lang('general.abandoned')
                                                                </label>
                                                                <label class="btn btn-danger">
                                                                    <input type="checkbox" name="status[]"
                                                                           value="'Shortcall'"
                                                                           id="shortcall"> @lang('general.shortcall')
                                                                </label>
                                                            </div>
                                                            <input type="hidden" name="list_id"
                                                                   value="{{$listFile->id}}">
                                                            <div class="pull-right " style="bottom: 0px">
                                                                <input type="submit" value="{{trans('general.reload')}}"
                                                                       class="btn btn-success">
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th> @lang('general.id')</th>
                                            <th class="text-center"> @lang('general.phone')</th>
                                            <th class="text-center"> @lang('general.fio')</th>
                                            <th class="text-center"> @lang('general.status')</th>
                                            <th class="text-center"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($lists as $item)
                                            <tr class="list-row" id="{{$item->id}}">
                                                <td class="text-center">{{$item->id}}</td>
                                                @if(!empty($item->phone_number && $item->phone_number[1] == 1))
                                                    <td class="text-center">{{$item->phone_number[0]}}</td>
                                                    <input type="hidden" value="{{$item->phone_number[1]}}"
                                                           id="correct_number" name="correct_number">
                                                @elseif(!empty($item->phone_number && $item->phone_number[1] == 0))
                                                    <td class="text-center">{{$item->phone_number[0]}}</td>
                                                    <input type="hidden" value="{{$item->phone_number[1]}}"
                                                           id="correct_number" name="correct_number">
                                                @else
                                                    <td>{{'N/A'}}</td>
                                                @endif
                                                <td class="text-center">
                                                    @if(!empty($item->add_info->фио))
                                                        {{$item->add_info->фио}}
                                                    @elseif(!empty($item->add_info->фамилия)
                                                    && !empty($item->add_info->имя))
                                                        {{$item->add_info->фамилия.
                                                        ' '.$item->add_info->имя}}
                                                    @else
                                                        {{'N/A'}}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($item->call_status->call_status))
                                                        {{$item->call_status->call_status}}
                                                    @else
                                                        {{'N/A'}}
                                                    @endif
                                                </td>
                                                {{--<td>--}}
                                                {{--<div class="form-group">--}}
                                                {{--<div class="checkbox-nice">--}}
                                                {{--{{ Form::checkbox('modify', $item['id'], false, ['class' => 'change_row', 'id' => 'modify'.$item['id']])}}--}}
                                                {{--{{ Form::label('modify'. $item["id"], 'Изменить') }}--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                                {{--</td>--}}
                                            </tr>
                                        @endforeach
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
    {{$lists->links()}}
@endsection

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plan-create.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/cold-call-list-edit.js') }}"></script>

@stop
