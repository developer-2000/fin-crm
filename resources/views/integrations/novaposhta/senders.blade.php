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
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('integrations')}}"><span>Все интеграции</span></a></li>
                <li class="active"><span>Редактировать интеграцию</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Редактирование</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="">
                            <a href="{{route('integrations-edit' , $integration->alias)}}">Все ключи</a>
                        </li>
                        <li class="active">
                            <a href="{{route('novaposhta-senders',  $integration->alias)}}">Отправители</a>
                        </li>
                        @if(\App\Models\Api\Posts\Novaposhta::TRACKING)
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
                                            @if ($keys->count())
                                                <table class="table table-hover table-striped integrations_table">
                                                    <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Отправитель</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($keys as $key)
                                                        <tr>
                                                            <td>{{ $key->sender_id }}</td>
                                                            <td>
                                                                @php
                                                                    $contacts = json_decode($key->contacts);
                                                                @endphp
                                                                {{ $contacts ? $contacts->full_name : ''}}</td>
                                                            <td>
                                                                <div class="sender" id="sender_{{$key->id}}">
                                                                    <a href="{{ route('sender', [ 'alias' => $integration->alias, 'id' => $key->id]) }}" class="btn btn-success">Изменить</a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
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
    </div>

@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery3.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.datetimepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
    <script src="{{ URL::asset('js/integrations/index.js') }}"></script>
@stop


