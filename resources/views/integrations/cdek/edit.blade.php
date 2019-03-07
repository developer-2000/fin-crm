@extends('layouts.app')
@section('title') @lang('integrations.edit')  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.nouislider.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel=" stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        body {
            color: grey;
        }

        .ns-box {
            z-index: 5000
        }
    </style>
@stop
@section('content')
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <div class="md-modal md-effect-2" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title"> @lang('integrations.add-key')</h4>
            </div>
            <form method="post" class="form-horizontal" id="account-create">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="sub_project_id"> @lang('general.subproject')</label>
                                @if ($subProjects->count())
                                    <div class="col-md-9">
                                        <select name="sub_project_id" class="form-control" style="width: 100%">
                                            @foreach($subProjects as $subProject)
                                                <option value="{{$subProject->id}}">{{$subProject->parent->name . " : " . $subProject->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label text-center" for="name"> @lang('general.name')</label>
                                <div class="col-md-9">
                                    <input required placeholder="Название" name="name" id="name"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label text-center" for="account"> @lang('general.account')</label>
                                <div class="col-md-9">
                                    <input required placeholder=" @lang('general.account')" name="account" id="account"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label text-center" for="first-name">Secure</label>
                                <div class="col-md-9">
                                    <input required placeholder=" @lang('general.secure')" name="secure" id="secure"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="error-messages">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary account_register"> @lang('integrations.add-key')</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><a href="{{route('integrations')}}"><span> @lang('integrations.all')</span></a></li>
                <li class="active"><span> @lang('integrations.edit')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.edit')</h1>
                @if (isset($permissions['integrations_keys_create']))
                    <div class="pull-right top-page-ui">
                        <button data-modal="form_block"
                                class=" md-trigger btn btn-primary pull-right mrg-b-lg account_create">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('integrations.add-key')
                        </button>
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
                            <a href="{{route('integrations-edit' , Request::segment(2))}}"> @lang('integrations.all-keys')</a>
                        </li>
                        @if(\App\Models\Api\Posts\Viettel::TRACKING)
                            <li class="">
                                <a href="{{route('integration-codes-statuses',  Request::segment(2))}}"> @lang('integrations.kode-status')</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped integrations_table">
                                                <thead>
                                                <tr>
                                                    <th> @lang('general.id')</th>
                                                    <th> @lang('general.subproject')</th>
                                                    <th> @lang('general.name')</th>
                                                    <th> @lang('general.active')</th>
                                                    <th> @lang('general.account')</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if ($keys->count())
                                                    @include('integrations.cdek.accounts-table')
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.datetimepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/integrations/cdek/index.js') }}"></script>
@stop
