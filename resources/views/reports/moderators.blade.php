@extends('layouts.app')

@section('title') @lang('general.report') @lang('reports.by-moderators')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <style>
        .click td {
            background-color: #ebebeb !important;
            font-weight: bold;
        }
        .total {
            background-color: #b8e9c8 !important;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('tablesorter_master/jquery.tablesorter.js') }}"></script>
    <script src="{{ URL::asset('js/users/account.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                                <li class="active"><span>  @lang('general.report') @lang('reports.by-moderators')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('general.report') @lang('reports.by-moderators')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <form method="POST" action="{{Request::url()}}">
                <div class="main-box clearfix">
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.filter')</div>
                        <div class="col-md-11">
                            <div class="row">
                                @if (isset($permissions['filter_projects_page_account']))
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="project" class="col-sm-4 control-label"> @lang('general.project')</label>
                                            <input id="project"
                                                   data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                   class="project " name="project"
                                                   value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                   style="width: 100%">
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sub_project" class="col-sm-4 control-label"> @lang('general.subproject')</label>
                                        <input id="sub_project"
                                               data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                               class="sub_project " name="sub_project"
                                               value="{{$dataSubProject ?? NULL}}"
                                               style="width: 100%">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="country"> @lang('general.country')</label>
                                        <select name='country' id="country" style="width: 100%">
                                            <option value="">Все</option>
                                            @if ($countries)
                                                @foreach ($countries as $c)
                                                    <option @if (isset($_GET['country']) && mb_strtolower($_GET['country']) == $c->code) selected
                                                            @endif value="{{ mb_strtolower($c->code) }}">
                                                        @lang('countries.' . $c->code)
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="moderator" class="col-sm-4 control-label"> @lang('general.moderator')</label>
                                        <select id="moderator" name="moderator[]" style="width: 100%" multiple>
                                            @if ($moderatorsById)
                                                @foreach ($moderatorsById as $key => $moderator)
                                                    <option
                                                            @if (isset($_GET['moderator']))
                                                            <? $moderatorsGet = explode(',', $_GET['moderator']); ?>
                                                            @foreach ($moderatorsGet as $moderatorGet)
                                                            @if ($key == $moderatorGet)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="{{ $key }}">{{ $moderator->surname .' '. $moderator->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="date_start"> @lang('general.from')</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" id="date_start" type="text" data-toggle="tooltip"
                                           name="date_start"
                                           data-placement="bottom"
                                           value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="date_end"> @lang('general.to')</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" id="date_end" type="text" data-toggle="tooltip"
                                           name="date_end"
                                           data-placement="bottom"
                                           value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                            <div class="input-group">
                                <div class="btn-group" data-toggle="buttons" id="date_template">
                                    <label class="btn btn-default pattern_date">
                                        <input type="radio" name="date_template" value="1"> @lang('general.today')
                                    </label>
                                    <label class="btn btn-default pattern_date">
                                        <input type="radio" name="date_template" value="5"> @lang('general.yesterday')
                                    </label>
                                    <label class="btn btn-default pattern_date">
                                        <input type="radio" name="date_template" value="9"> @lang('general.week')
                                    </label>
                                    <label class="btn btn-default pattern_date">
                                        <input type="radio" name="date_template" value="10"> @lang('general.month')
                                    </label>
                                    <label class="btn btn-default pattern_date">
                                        <input type="radio" name="date_template" value="2"> @lang('general.last-month')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value=' @lang('general.search')'/>
                    <a href="{{ route('report-moderators') }}" class="btn btn-warning" type="submit">
                        @lang('general.reset')
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    @if (!empty($moderatorsData))
                        <div class="table-responsive">
                            <table class="table tablesorter table-hover ">
                                <thead>
                                <tr>
                                    <th class="text-center" rowspan="2"> @lang('general.id')</th>
                                    <th class="text-center" rowspan="2"> @lang('general.moderator')</th>
                                    <th class="text-center" colspan="4"> @lang('general.moderated')<br>  @lang('general.total-orders')</th>
                                    <th class="text-center" colspan="6"> @lang('general.pre-moderated')<br> @lang('general.total-orders')</th>
                                </tr>
                                <tr>
                                    <th class="text-center"> @lang('general.total')</th>
                                    <th class="text-center"> @lang('general.approved')</th>
                                    <th class="text-center"> @lang('general.refusal')</th>
                                    <th class="text-center"> @lang('general.annulled')</th>
                                    <th class="text-center"> @lang('general.total')</th>
                                    <th class="text-center"> @lang('general.repeat')</th>
                                    <th class="text-center"> @lang('general.under-call')</th>
                                    <th class="text-center"> @lang('general.invalid-number')</th>
                                    <th class="text-center"> @lang('general.another-language')</th>
                                    <th class="text-center"> @lang('statuses.invalid-project')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total = 0;
                                    $totalApprove = 0;
                                    $totalCancel = 0;
                                    $totalRefused = 0;
                                    $totalPreModeration = 0;
                                    $totalRepeated= 0;
                                    $totalNotCall= 0;
                                    $totalNotData = 0;
                                    $totalOtherLanguage = 0;
                                    $totalIncorrectProject = 0;
                                @endphp
                                @foreach($moderatorsData as $key => $moderatorData)
                                    @if($key!= NULL)
                                    <tr>
                                        <td class="text-center">{{!empty($moderatorData->uId) ? $moderatorData->uId :$moderatorData->usId}}</td>
                                        <td>{{!empty($moderatorData->uName) ?  $moderatorData->uName .' '. $moderatorData->uSurname : $moderatorData->usName .' '. $moderatorData->usSurname}}</td>
                                        <td class="text-center" style="background-color :rgba(249,249,249,0.79)">{{array_sum([$moderatorData->approve,$moderatorData->cancel,$moderatorData->refused])}}</td>
                                        <td class="text-center">{{$moderatorData->approve}}</td>
                                        <td class="text-center">{{$moderatorData->cancel}}</td>
                                        <td class="text-center">{{$moderatorData->refused}}</td>
                                        <td class="text-center" style="background-color:rgba(249,249,249,0.79)">{{array_sum([$moderatorData->repeated,$moderatorData->notCall,$moderatorData->notData,$moderatorData->otherLanguage, $moderatorData->incorrectProject])}}</td>
                                        <td class="text-center">{{$moderatorData->repeated}}</td>
                                        <td class="text-center">{{$moderatorData->notCall}}</td>
                                        <td class="text-center">{{$moderatorData->notData}}</td>
                                        <td class="text-center">{{$moderatorData->otherLanguage}}</td>
                                        <td class="text-center">{{$moderatorData->incorrectProject}}</td>
                                    </tr>
                                    @php
                                        $total += array_sum([$moderatorData->approve,$moderatorData->cancel,$moderatorData->refused]);
                                        $totalApprove += !empty($moderatorData->approve) ? $moderatorData->approve :0;
                                        $totalCancel += !empty($moderatorData->cancel) ? $moderatorData->cancel :0;
                                        $totalRefused += !empty($moderatorData->refused) ? $moderatorData->refused :0;
                                        $totalPreModeration += array_sum([$moderatorData->repeated,$moderatorData->notCall,$moderatorData->notData,$moderatorData->otherLanguage, $moderatorData->incorrectProject]);
                                        $totalRepeated  += !empty($moderatorData->repeated) ? $moderatorData->repeated :0;
                                        $totalNotCall  += !empty($moderatorData->notCall) ? $moderatorData->notCall :0;
                                        $totalNotData  += !empty($moderatorData->notData) ? $moderatorData->notData :0;
                                        $totalOtherLanguage  += !empty($moderatorData->otherLanguage) ? $moderatorData->otherLanguage :0;
                                        $totalIncorrectProject  += !empty($moderatorData->incorrectProject) ? $moderatorData->incorrectProject :0;
                                    @endphp
                                    @endif
                                @endforeach
                                <tr>
                                    <td style="font-weight: bold" class="total"></td>
                                    <td style="font-weight: bold" class="total"> @lang('general.total')</td>
                                    <td style="font-weight: bold"
                                        class="text-center total" >{{!empty($total) ? $total : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalApprove) ? $totalApprove : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalCancel) ? $totalCancel : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalRefused) ? $totalRefused : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalPreModeration) ? $totalPreModeration : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalRepeated) ? $totalRepeated : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalNotCall) ? $totalNotCall : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalNotData) ? $totalNotData : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalOtherLanguage) ? $totalOtherLanguage : 0}}</td>
                                    <td style="font-weight: bold"
                                        class="text-center total">{{!empty($totalIncorrectProject) ? $totalIncorrectProject : 0}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop