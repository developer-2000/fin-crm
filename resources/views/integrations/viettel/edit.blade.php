@extends('layouts.app')
@section('title')Редактирование интеграции  @stop
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
                <h4 class="modal-title">Добавление ключа</h4>
            </div>
            <form method="post" class="form-horizontal" id="account-create">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="sub_project_id">Субпроект</label>
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
                    <div class="col-lg-12">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-md-3 control-label text-center" for="first-name">Имя</label>
                                <div class="col-md-9">
                                    <input required placeholder="Имя" name="first_name" id="first-name"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-md-3 control-label text-center" for="last-name">Фамилия</label>
                                <div class="col-md-9">
                                    <input required placeholder="Фамилия" name="last_name" id="last-name"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="email">Email</label>
                                <div class="col-md-9">
                                    <input required placeholder="Email" name="email" id="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input id="password" type="password"
                                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       name="password" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="phone">Phone</label>
                        <div class="col-md-9">
                            <input required placeholder="Phone" name="phone" id="phone" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="introduction">Описание</label>
                        <div class="col-md-9">
                            <input required placeholder="Описание" name="introduction" id="introduction"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="district" class="col-lg-3 control-label required">District</label>
                        <div class="col-lg-8">
                            <input required id="district" class="district" name="district" value="" style="width: 100%">
                            <input type="hidden" id="district-val" name="district-val"
                                   value="{{!empty($district) ? $district : ''}}" style="width: 100%">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ward" class="col-lg-3 control-label required">Админ.округ</label>
                        <div class="col-lg-8">
                            <input required id="ward" class="ward" name="ward" value=""
                                   style="width: 100%">
                            <input type="hidden" id="ward-val" name="ward-val"
                                   value="{{!empty($wards) ? $wards : NULL}}"
                                   style="width: 100%">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="address">Адрес</label>
                        <div class="col-md-9">
                            <input required name="address" id="address" class="form-control">
                        </div>
                    </div>
                    <div class="error-messages">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary account_register">Создать учетку</button>
                </div>
                <input type="hidden" name="integration_id" value="{{$id}}">
            </form>
        </div>
    </div>
    <div class="md-modal md-effect-2" id="sender_add">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Добавление отправителя</h4>
            </div>
            <div class="modal-body">
                <form role="form" method="post" class="form-horizontal" id="sender-add">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="sub_project_id">Субпроект</label>
                        @if ($subProjects->count())
                            <div class="col-lg-9">
                                <select name="sub_project_id" class="form-control" style="width: 100%">
                                    @foreach($subProjects as $subProject)
                                        <option value="{{$subProject->id}}">{{$subProject->parent->name . " : " . $subProject->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="col-lg-3 control-label text-center" for="name">Название</label>
                        <div class="col-lg-9">
                            <input required placeholder="name" name="name" id="name"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="col-lg-3 control-label text-center" for="account_email">Email</label>
                        <div class="col-lg-9">
                            <input required placeholder="Email" name="account_email" id="account_email"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="input-group col-md-6">
                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                        <input id="account_password" type="password"
                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               name="account_password" required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary sender-add">Добавить</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('integrations')}}"><span>Все интеграции</span></a></li>
                <li class="active"><span>Редактировать интеграцию</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Редактирование</h1>
                @if (isset($permissions['integrations_keys_create']))
                    <div class="pull-right top-page-ui">
                        {{--<button data-modal="form_block"--}}
                                {{--class=" md-trigger btn btn-primary pull-right mrg-b-lg account_create">--}}
                            {{--<i class="fa fa-plus-circle fa-lg"></i>Создать учетку--}}
                        {{--</button>--}}
                        <br>
                        <button data-modal="sender_add"
                                class=" md-trigger btn btn-primary pull-right mrg-b-lg sender_add">
                            <i class="fa fa-plus-circle fa-lg"></i>Добавить отправителя
                        </button>
                        <br>
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
                            <a href="{{route('integrations-edit' , Request::segment(2))}}"> Все ключи</a>
                        </li>
                        @if(\App\Models\Api\Posts\Viettel::TRACKING)
                            <li class="">
                                <a href="{{route('integration-codes-statuses',  Request::segment(2))}}">Коды/Статусы</a>
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
                                                    <th>Id</th>
                                                    <th>Подпроект</th>
                                                    <th>Название</th>
                                                    <th>Email</th>
                                                    <th>Active</th>
                                                    <th>Имя отправителя</th>
                                                    <th>Токен</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if ($keys->count())
                                                    @include('integrations.viettel.accounts-table')
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
    <script src="{{ URL::asset('js/integrations/viettel/index.js') }}"></script>
@stop


