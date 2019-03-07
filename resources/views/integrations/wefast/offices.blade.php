@extends('layouts.app')
@section('title')Редактирование интеграции  @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
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
        .fa-times {
            color: #f4786e;
        }
        .fa-check {
            color: #7FC8BA;
        }
    </style>
@stop
@section('content')
    <div class="md-modal md-effect-2" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Добавление отделений</h4>
            </div>
            <form method="post" class="form-horizontal" id="add_offices" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="input-group-btn">
                                    <span class="btn btn-success" style="line-height: 22px;">
                                        <span class="fa fa-photo"></span>
                                        Загрузить файл
                                        <input type="file" style="display: none;" id="excel_csv_file"
                                               name="excel_csv_file" required>
                                    </span>
                            </label>
                            <input type="text" class="form-control" id="excel_csv_file" readonly>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle fa-fw fa-lg"></i>
                        <strong>Порядок полей!</strong> Код провинции, Название провинции, Районый код, Название района, Получить товар, Доставка, Уорд, Имя уорда
                    </div>
                    <div class="error-messages">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>
    <div class="md-overlay"></div>
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
                        <button data-modal="form_block"
                                class=" md-trigger btn btn-primary pull-right mrg-b-lg create-key">
                            <i class="fa fa-plus-circle fa-lg"></i> Добавить отделения
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
                        <li >
                            <a href="{{route('integrations-edit' , $integration->alias)}}">Ключи</a>
                        </li>
                        <li >
                            <a href="{{route('wefast-counterparties')}}">Контрагенты</a>
                        </li>
                        <li class="active">
                            <a href="{{route('wefast-offices')}}">Отделения</a>
                        </li>
                        @if(\App\Models\Api\Posts\Wefast::TRACKING)
                            <li class="">
                                <a href="{{route('integration-codes-statuses',  Request::segment(2))}}">Коды/Статусы</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active ">
                            <div class="row">
                                <div class="col-lg-12" id="block_offices">
                                    <div class="main-box clearfix">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped integrations_table">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">Код района</th>
                                                    <th class="text-center">Район</th>
                                                    <th class="text-center">Код провинции</th>
                                                    <th class="text-center">Провинция</th>
                                                    <th class="text-center">wardCode</th>
                                                    <th class="text-center">wardName</th>
                                                    <th class="text-center">Получить товары</th>
                                                    <th class="text-center">Доставка</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if ($offices->isNotEmpty())
                                                    @foreach($offices as $office)
                                                        <tr>
                                                            <td class="text-center">{{$office->district_code}}</td>
                                                            <td>{{$office->district_name}}</td>
                                                            <td class="text-center">{{$office->province_code}}</td>
                                                            <td>{{$office->province_name}}</td>
                                                            <td class="text-center">{{$office->ward_code}}</td>
                                                            <td>{{$office->ward_name}}</td>
                                                            <td class="text-center">@if ($office->pickup) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif</td>
                                                            <td class="text-center">@if ($office->delivery) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="8" class="text-center">Нет отделений</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="text-center">{{$offices->links()}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/integrations/wefast.js') }}"></script>
@stop


