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
    <div class="md-modal md-effect-2" id="key_add">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 clаss="modal-title">Добавление отправителя</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form role="form" method="post" class="form-horizontal" id="key-add">
                            <div class="form-group col-lg-6">
                                <label class="col-lg-3 control-label" for="subproject_id">Субпроект</label>
                                @if ($subProjects->count())
                                    <div class="col-lg-9">
                                        <select name="subproject_id" class="form-control">
                                            @foreach($subProjects as $subProject)
                                                <option value="{{$subProject->id}}">{{$subProject->parent->name . " : " . $subProject->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="col-lg-3 control-label text-center" for="name">Название</label>
                                <div class="col-lg-9">
                                    <input required placeholder="name" name="name" id="name"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="col-lg-3 control-label text-center" for="name">Страна</label>
                                <div class="col-lg-9">
                                    <select id="country" name="country"
                                            class="form-control">
                                        <option value=""></option>
                                        @if(isset($countries))
                                            @foreach($countries as $country)
                                                <option value="{{$country->code}}">@lang('countries.' . $country->code)</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="col-lg-3 control-label text-center" for="postcode">Postcode</label>
                                <div class="col-lg-9">
                                    <input required placeholder="Postcode" name="postcode" id="postcode"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label class="col-lg-3 control-label text-center" for="address">Address</label>
                                <div class="col-lg-9">
                                    <input required placeholder="Address" name="address" id="address"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label class="col-lg-3 control-label text-center" for="client_id">Client ID</label>
                                <div class="col-lg-9">
                                    <input required placeholder="client id" name="client_id" id="client_id"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label class="col-lg-3 control-label text-center" for="client_secret">Client
                                    Secret</label>
                                <div class="col-lg-9">
                                    <input required placeholder="client secret" name="client_secret" id="client_secret"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label class="col-lg-3 control-label text-center" for="phone">Phone</label>
                                <div class="col-lg-9">
                                    <input required placeholder="Phone" name="phone" id="phone"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="col-lg-3 control-label text-center" for="email">Email</label>
                                <div class="col-lg-9">
                                    <input required placeholder="Email" name="email" id="email"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="input-group col-md-6">
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
                            <div class="col-lg-12">
                                <h4>Параметры посылки</h4>
                                <div class="form-group col-lg-6">
                                    <label class="col-lg-3 control-label text-center" for="size">Размер</label>
                                    <div class="col-lg-9">
                                        <input required placeholder="Размер" name="size" id="size"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="col-lg-3 control-label text-center" for="weight">Вес</label>
                                    <div class="col-lg-9">
                                        <input required placeholder="Вес" name="weight" id="weight"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="col-lg-3 control-label text-center" for="volume">Обьем</label>
                                    <div class="col-lg-9">
                                        <input required placeholder="Обьем" name="volume" id="volume"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="col-lg-3 control-label text-center" for="length">Длина</label>
                                    <div class="col-lg-9">
                                        <input required placeholder="Длина" name="length" id="length"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="col-lg-3 control-label text-center" for="width">Ширина</label>
                                    <div class="col-lg-9">
                                        <input required placeholder="Ширина" name="width" id="width"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="col-lg-3 control-label text-center" for="height">Высота</label>
                                    <div class="col-lg-9">
                                        <input required placeholder="Высота" name="height" id="height"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary key-add">Добавить</button>
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
                        <button data-modal="key_add"
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
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Подпроект</th>
                                                    <th>Название</th>
                                                    <th>Email</th>
                                                    <th>Active</th>
                                                    <th>Client ID</th>
                                                    <th>Client SECRET</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if ($keys->count())
                                                    @include('integrations.ninjaxpress.accounts-table')
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
    <script src="{{ URL::asset('js/integrations/ninjaxpress/index.js') }}"></script>
@stop


