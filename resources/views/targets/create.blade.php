@extends('layouts.app')

@section('title')Создание цели@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/targets.css') }}" />
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/targets/target-create.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('targets')}}">Цели</a></li>
                <li class="active">Создание цели</li>
            </ol>
            <h1>Создание цели</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="main-box">
                <header class="main-box-header clearfix"></header>
                <div class="main-box-body">
                    <form class="form-horizontal" id="create_target">
                        <div class="form-group">
                            <div class="col-md-8">
                                <div class="checkbox-nice">
                                    <input type="checkbox" name="active" id="active" value="1" checked>
                                    <label for="active">Активировать</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                Имя цели
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="alias" class="col-md-4 control-label">
                                Псевдоним
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="alias" id="alias" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="entity" class="col-md-4 control-label">
                                Сущность
                            </label>
                            <div class="col-md-8">
                                <select name="entity" id="entity" class="form-control">
                                    <option value=""></option>
                                    <option value="order">Заказ</option>
                                    <option value="cold_call">ХП</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="target_type" class="col-md-4 control-label">
                                Тип цели
                            </label>
                            <div class="col-md-8">
                                <select name="target_type" id="target_type" class="form-control">
                                    <option value="approve">Подвержден</option>
                                    <option value="refuse">Отказ</option>
                                    <option value="cancel">Аннулирован</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="country" class="col-md-4 control-label">
                                Страна
                            </label>
                            <div class="col-md-8">
                                @if ($countries)
                                    <select name="country" id="country" class="form-control">
                                        <option value=""></option>
                                        @foreach($countries as $country)
                                            <option value="{{mb_strtolower($country->code)}}">
                                                @lang('countries.' . $country->code)
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="offer" class="col-md-4 control-label">
                                Оффер
                            </label>
                            <div class="col-md-8">
                                @if ($offers)
                                <select name="offer" id="offer" style="width:100%">
                                    <option value="">Все</option>
                                    @foreach($offers as $offer)
                                        <option value="{{$offer->id}}">{{$offer->name}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="project" class="col-md-4 control-label">
                                Проект
                            </label>
                            <div class="col-md-8">
                                @if ($projects)
                                    <select name="project" id="project" class="form-control">
                                        <option value=""></option>
                                        @foreach($projects as $project)
                                            <option value="{{$project->id}}">{{$project->name}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        @php
                            $tags = [
                                'tag_campaign',
                                'tag_content',
                                'tag_medium',
                                'tag_source',
                                'tag_term',
                            ];
                        @endphp
                        @foreach($tags as $tag)
                            <div class="form-group">
                                <label for="{{$tag}}" class="col-md-4 control-label">
                                    {{$tag}}
                                </label>
                                <div class="col-md-8">
                                    <input type="text" name="{{$tag}}" id="{{$tag}}" class="form-control">
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group">
                            <label for="template" class="col-md-4 control-label">
                                Шаблон
                            </label>
                            <div class="col-md-8">
                                <select name="template" id="template" class="form-control">
                                    <option value="">Выберите шаблон</option>
                                    <option value="product">Для товаров</option>
                                    <option value="custom">Свой</option>
                                </select>
                            </div>
                        </div>
                        <div class="templates">
                            <div class=" product_template" id="product_template">
                                    @php
                                        $productFields = [
                                            'track',
                                            'track2',
                                            'state',
                                            'date',
                                            'time_min',
                                            'time_max',
                                            'country',
                                            'postal_code',
                                            'city',
                                            'region',
                                            'district',
                                            'locality',
                                            'street',
                                            'house',
                                            'flat',
                                            'warehouse',
                                            'cost',
                                            'cost_take',
                                            'cost_actual',
                                            'note'
                                        ];
                                        $i = 0;
                                    @endphp
                                    @foreach($productFields as $field)
                                        <div class="product_field">
                                            <header class="main-box-header clearfix"></header>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Название поля</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="product_template[{{$i}}][field-title]" id="product_template[{{$i}}][field-title]" value="{{$field}}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Имя поля(name)</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="product_template[{{$i}}][field-name]" id="product_template[{{$i}}][field-name]" value="{{$field}}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">
                                                    Тип
                                                </label>
                                                <div class="col-md-8">
                                                    <select name="product_template[{{$i}}][field-type]" class="form-control typeFiled" id="product_template[{{$i}}][field-type]">
                                                        <option value="text">textField</option>
                                                        <option value="number">number</option>
                                                        <option value="textarea">textarea</option>
                                                        <option value="checkbox" data-options="1">checkbox</option>
                                                        <option value="radio" data-options="1">radio</option>
                                                        <option value="select" data-options="1">select</option>
                                                    </select>
                                                    <div class="options hidden"></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Range</label>
                                                <div class="col-md-4">
                                                    <input type="text" name="product_template[{{$i}}][range-min]" id="product_template[{{$i}}][range-min]" class="form-control" placeholder="Min">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="product_template[{{$i}}][range-max]" id="product_template[{{$i}}][range-max]" class="form-control" placeholder="Max">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Связь</label>
                                                <div class="col-md-4">
                                                    <input type="text" name="product_template[{{$i}}][field-relation-name]" id="product_template[{{$i}}][field-relation-name]" class="form-control" placeholder="name">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="product_template[{{$i}}][field-relation-value]" id="product_template[{{$i}}][field-relation-value]" class="form-control" placeholder="value">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-4 col-md-8">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" name="product_template[{{$i}}][field-required]" id="product_template[{{$i}}][field-required]" checked>
                                                        <label for="product_template[{{$i}}][field-required]">Обязательное</label>
                                                    </div>
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" name="product_template[{{$i}}][field-show-result]" id="product_template[{{$i}}][field-show-result]">
                                                        <label for="product_template[{{$i}}][field-show-result]">Отображать результат на страницах</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-offset-4">
                                                <div class="form-group">
                                                    <button class="btn btn-success add_field">Добавить поле</button>
                                                    <button class="btn btn-success del_field">Удалить поле</button>
                                                </div>
                                            </div>
                                        </div>
                                        @php($i++)
                                    @endforeach
                            </div>
                            <div class="custom_template"></div>
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Сохранить">
                        </div>
                    </form>
                    <div class="options_hidden hidden">
                        <div class="row">
                            <div class="option">
                                <div class="col-md-4">
                                    <input type="text" name="option-value" id="option-value" class="form-control" placeholder="value">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="option-title" id="option-title" class="form-control" placeholder="title">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-success add_option"><b>+</b></button>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-danger del_option"><b>x</b></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom_template_hidden hidden">
                        <div class="custom_field">
                            <header class="main-box-header clearfix"></header>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Название поля</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="custom_template[field-title]" id="custom_template[field-title]">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Имя поля(name)</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="custom_template[field-name]" id="custom_template[field-name]">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                    Тип
                                </label>
                                <div class="col-md-8">
                                    <select name="custom_template[field-type]" class="form-control typeFiled" id="custom_template[field-type]">
                                        <option value="text">textField</option>
                                        <option value="number">number</option>
                                        <option value="textarea">textarea</option>
                                        <option value="checkbox" data-options="1">checkbox</option>
                                        <option value="radio" data-options="1">radio</option>
                                        <option value="select" data-options="1">select</option>
                                    </select>
                                    <div class="options hidden"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Range</label>
                                <div class="col-md-4">
                                    <input type="text" name="custom_template[field-range-min]" id="custom_template[field-range-min]" class="form-control" placeholder="Min">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="custom_template[field-range-max]" id="custom_template[field-range-max]" class="form-control" placeholder="Max">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Связь</label>
                                <div class="col-md-4">
                                    <input type="text" name="custom_template[field-relation-name]" id="custom_template[field-relation-name]" class="form-control" placeholder="name">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="custom_template[field-relation-value]" id="custom_template[field-relation-value]" class="form-control" placeholder="value">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-4 col-md-8">
                                    <div class="checkbox-nice">
                                        <input type="checkbox" name="custom_template[field-required]" id="custom_template[field-required]">
                                        <label for="custom_template[field-required]">Обязательное</label>
                                    </div>
                                    <div class="checkbox-nice">
                                        <input type="checkbox" name="custom_template[field-show-result]" id="custom_template[field-show-result]">
                                        <label for="custom_template[field-show-result]">Отображать результат на страницах</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-offset-4">
                                <div class="form-group">
                                    <button class="btn btn-success add_field">Добавить поле</button>
                                    <button class="btn btn-success del_field">Удалить поле</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop