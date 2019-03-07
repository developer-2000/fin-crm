@extends('layouts.app')
@section('title')Редактирование отправителя  @stop
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
    <div class="md-modal md-effect-15" id="form_block">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Добавление адреса</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(['method'=>'post', 'id' => 'address-create', 'class' => 'form-horizontal', 'url' => route('sender-address-create', Request::segment(3) )])}}
                <div class="form-group">
                    <label for="street" class="col-lg-3 control-label">Улица</label>
                    <div class="col-lg-8">
                        <input required id="street" class="street" name="street" value="" style="width: 100%">
                    </div>
                </div>
                <div class="form-group">
                    <label for="building" class="col-lg-3 control-label">Дом</label>
                    <div class="col-lg-8">
                        <input required type="number" id="building" class="building form-control" name="building"
                               value="" style="width: 100%">
                    </div>
                </div>
                <div class="form-group">
                    <label for="flat" class="col-lg-3 control-label">Квартира</label>
                    <div class="col-lg-8">
                        <input type="number" id="flat" class="flat form-control" name="flat" value=""
                               style="width: 100%">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label" for="comment">Комментарий</label>
                    <div class="col-lg-8">
                        <textarea type="text" id="comment" name="comment"
                                  class="description form-control">
                         </textarea>
                    </div>
                </div>
                <input type="hidden" id="sender_id" value="{{$integration->sender_id}}" name="counterpartyRef"
                       style="width: 100%">
                <input type="hidden" id="sender" value="{{$integration}}" name="sender"
                       style="width: 100%">
                <div class="text-center">
                    {{Form::submit('Создать', ['class' => 'btn btn-success'])}}
                </div>
            </div>
            {{Form::close()}}
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="{{route('novaposhta-senders','novaposhta' )}}">Вернуться к странице
                        отправителя</a></li>
                <li class="active"><a href=""><span>Редактировать отправителя</span></a></li>
            </ol>
            <div class="clearfix">
                <h1>Редактировать отправителя</h1>
                {{--@if (isset($permissions['sender_address_create']))--}}
                    {{--<div class="pull-right top-page-ui">--}}
                        {{--<button data-modal="form_block"--}}
                                {{--class=" md-trigger btn btn-primary pull-right mrg-b-lg create-address">--}}
                            {{--<i class="fa fa-plus-circle fa-lg"></i> Добавить адрес--}}
                        {{--</button>--}}
                    {{--</div>--}}
                {{--@endif--}}
            </div>
        </div>
    </div>

    @if($integration)
        {{ Form::open(['method'=>'post', 'id' => 'save-sender', 'class' => 'form-horizontal',  'url' => route('sender',['novaposhta', Request::segment(4)] )])}}
        <div class="main-box clearfix">
            <div class="row">
                <div class="col-lg-6">
                    <header class="main-box-header clearfix">
                        <span style="font-weight: bold"> {{json_decode($integration->contacts)->full_name}} </span>
                        @if(Session::has('message'))
                            <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('message') }}</p>
                        @endif
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="form-group">
                            <label for="city" class="col-lg-3 control-label">Город</label>
                            <div class="col-lg-8">
                                <input required id="city" class="city" name="city" value="" style="width: 100%">
                                <input type="hidden" id="city-name" class="city-name" name="city_name" value="{{$city}}"
                                       style="width: 100%">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="warehouse" class="col-lg-3 control-label">Адрес отправки</label>
                            <div class="col-lg-8">
                                <input required id="warehouse" class="warehouse" name="warehouse" value=""
                                       style="width: 100%">
                                <input type="hidden" id="warehouse-name" class="warehouse-name" name="warehouse_name"
                                       value="{{$warehouse}}" style="width: 100%">
                            </div>
                            {{dd($warehouse)}}
                        </div>
                        {{--<div class="form-group">--}}
                            {{--<label for="address" class="col-lg-3 control-label">Адрес отправителя</label>--}}
                            {{--<div class="col-lg-8">--}}
                                {{--<select name="address" class="form-control">--}}
                                    {{--@if($integration->senderAddresses->count())--}}
                                        {{--<option value="">Выберите адрес</option>--}}
                                        {{--@foreach($integration->senderAddresses as $address)--}}
                                            {{--<option--}}
                                                    {{--@if($integration->sender_address_id == $address->id)--}}
                                                    {{--selected--}}
                                                    {{--@endif--}}
                                                    {{--value="{{ $address->id}}">{{ $address->name }} </option>--}}
                                        {{--@endforeach--}}
                                    {{--@endif--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <br>
                        <br>
                        <div class="form-group">
                            {{Form::submit('Сохранить', ['class' => 'btn btn-success'])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="senderId" value="{{$integration->id}}" name="senderId"
               style="width: 100%">
        <input type="hidden" id="senderData" value="{{$integration}}" style="width: 100%">
        <input type="hidden" id="cityRef" value="{{json_decode($integration->contacts)->city}}" style="width: 100%">
        {{ Form::close() }}
    @endif
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/wizard.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/integrations/sender.js') }}"></script>
@stop


