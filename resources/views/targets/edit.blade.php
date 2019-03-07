@extends('layouts.app')

@section('title')Редактирование цели@stop

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
                <li class="active">{{$target->name}}</li>
            </ol>
            <h1>{{$target->name}}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="main-box">
                <header class="main-box-header clearfix"></header>
                <div class="main-box-body">
                    <form class="form-horizontal" id="change_target">
                        <input type="hidden" value="{{$target->id}}" name="id">
                        <div class="form-group">
                            <div class="col-md-8">
                                <div class="checkbox-nice">
                                    <input type="checkbox" name="active" id="active" value="1" @if ($target->active) checked @endif>
                                    <label for="active">Активировать</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                Имя цели
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" class="form-control" value="{{$target->name}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="alias" class="col-md-4 control-label">
                                Псевдоним
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="alias" id="alias" class="form-control" value="{{$target->alias}}" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="entity" class="col-md-4 control-label">
                                Сущность
                            </label>
                            <div class="col-md-8">
                                @php
                                    $entities = [
                                        ''  => '',
                                        'order' => 'Заказ',
                                        'cold_call' => 'ХП',
                                    ];
                                @endphp
                                <select name="entity" id="entity" class="form-control" disabled>
                                    @foreach($entities as $entity => $title)
                                        <option value="{{$entity}}" @if ($entity == $target->entity) selected @endif>{{$title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="target_type" class="col-md-4 control-label">
                                Тип цели
                            </label>
                            <div class="col-md-8">
                                @php
                                    $targetTypes = [
                                       'approve' => 'Подвержден',
                                       'refuse' => 'Отказ',
                                       'cancel' => 'Аннулирован'
                                   ];
                                   @endphp
                                   <select name="target_type" id="target_type" class="form-control" disabled>
                                       @foreach($targetTypes as $type => $title)
                                           <option value="{{$type}}" @if ($type == $target->target_type) selected @endif>{{$title}}</option>
                                       @endforeach
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
                                               <option value="{{mb_strtolower($country->code)}}" @if (mb_strtolower($country->code) == $target->filter_geo) selected @endif>
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
                                           <option value="{{$offer->id}}" @if ($offer->id == $target->filter_offer) selected @endif>{{$offer->name}}</option>
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
                                               <option value="{{$project->id}}" @if ($project->id == $target->filter_project) selected @endif>{{$project->name}}</option>
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
                                    <input type="text" name="{{$tag}}" id="{{$tag}}" class="form-control" value="{{$target->$tag}}">
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group">
                            <label for="template" class="col-md-4 control-label">
                                Шаблон
                            </label>
                            <div class="col-md-8">
                                <select name="template" id="template" class="form-control" disabled>
                                    <option value="product" @if ($target->template == 'product') selected @endif>Для товаров</option>
                                    <option value="custom" @if ($target->template == 'custom') selected @endif>Свой</option>
                                </select>
                            </div>
                        </div>
                        <div class="templates">
                            @php
                                $fields = json_decode($target->options);
                                $classId = $target->template . '_template';
                                $i = 0;
                            @endphp
                            <div class="{{$classId}} active " id="{{$classId}}">
                                    @forelse($fields as $field)
                                        <div class="product_field">
                                            <header class="main-box-header clearfix"></header>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Название поля</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="{{$classId}}[{{$i}}][field-title]" id="{{$classId}}[{{$i}}][field-title]" value="{{$field->field_title}}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Имя поля(name)</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="{{$classId}}[{{$i}}][field-name]" id="{{$classId}}[{{$i}}][field-name]" value="{{$field->field_name}}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">
                                                    Тип
                                                </label>
                                                <div class="col-md-8">
                                                    <select name="{{$classId}}[{{$i}}][field-type]" class="form-control typeFiled" id="{{$classId}}[{{$i}}][field-type]" disabled>
                                                        <option value="text" @if ($field->field_type == 'text') selected @endif>textField</option>
                                                        <option value="number" @if ($field->field_type == 'number') selected @endif>number</option>
                                                        <option value="textarea" @if ($field->field_type == 'textarea') selected @endif>textarea</option>
                                                        <option value="checkbox" data-options="1" @if ($field->field_type == 'checkbox') selected @endif>checkbox</option>
                                                        <option value="radio" data-options="1" @if ($field->field_type == 'radio') selected @endif>radio</option>
                                                        <option value="select" data-options="1" @if ($field->field_type == 'select') selected @endif>select</option>
                                                    </select>
                                                    <div class="options">
                                                        @php($j = 0)
                                                        @forelse($field->options as $value => $title)
                                                            <div class="row">
                                                                <div class="option">
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="{{$classId}}[{{$i}}][option][{{$j}}][value]"
                                                                               id="{{$classId}}[{{$i}}][option][{{$j}}][value]"
                                                                               class="form-control" placeholder="value"
                                                                               value="{{$value}}" disabled>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="{{$classId}}[{{$i}}][option][{{$j}}][title]"
                                                                               id="{{$classId}}[{{$i}}][option][{{$j}}][title]"
                                                                               class="form-control" placeholder="title"
                                                                               value="{{$title}}" disabled>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @php($j++)
                                                        @empty
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Range</label>
                                                <div class="col-md-4">
                                                    <input type="text" name="{{$classId}}[{{$i}}][field-range-min]" id="{{$classId}}[{{$i}}][field-range-min]" class="form-control" placeholder="Min" value="@if (!empty($field->field_settings->range_min)) {{$field->field_settings->range_min}} @endif">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="{{$classId}}[{{$i}}][field-range-max]" id="{{$classId}}[{{$i}}][field-range-max]" class="form-control" placeholder="Max" value="@if (!empty($field->field_settings->range_max)) {{$field->field_settings->range_max}} @endif">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Связь</label>
                                                <div class="col-md-4">
                                                    <input type="text" name="{{$classId}}[{{$i}}][field-relation-name]"
                                                           id="{{$classId}}[{{$i}}][field-relation-name]" class="form-control"
                                                           placeholder="name" value="@if ($field->field_relation_name) {{$field->field_relation_name}} @endif" disabled>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="{{$classId}}[{{$i}}][field-relation-value]"
                                                           id="{{$classId}}[{{$i}}][field-relation-value]" class="form-control"
                                                           placeholder="value" value="@if ($field->field_relation_value) {{$field->field_relation_value}} @endif" disabled>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-4 col-md-8">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" name="{{$classId}}[{{$i}}][field-required]"
                                                               id="{{$classId}}[{{$i}}][field-required]" @if ($field->field_required) checked @endif>
                                                        <label for="{{$classId}}[{{$i}}][field-required]">Обязательное</label>
                                                    </div>
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" name="{{$classId}}[{{$i}}][field-show-result]" id="{{$classId}}[{{$i}}][field-show-result]" @if ($field->field_show_result) checked @endif>
                                                        <label for="{{$classId}}[{{$i}}][field-show-result]">Отображать результат на страницах</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php($i++)
                                    @empty
                                    @endforelse
                            </div>
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
                                        <input type="checkbox" name="custom_template[field-moderation]" id="custom_template[field-moderation]">
                                        <label for="custom_template[field-moderation]">Отображать на странице модерации</label>
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