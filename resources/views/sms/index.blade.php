@extends('layouts.app')
@section('title') Шаблоны@stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <style>
        .ns-box {
            z-index: 5000
        }

        .alias-link {
            cursor: pointer;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active">
                    <a href="{{route('sms')}}">Шаблоны</a>
                </li>
            </ol>
            @if(isset($permissions['sms_send']))
                <div class="pull-right">
                    <button class="md-trigger btn btn-success mrg-b-lg"
                            data-modal="template-create">Создать шаблон
                    </button>
                </div>
            @endif
        </div>
    </div>
    <div class="md-modal md-effect-15" id="template-create">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">Создать шаблон</h4>
            </div>
            <div class="modal-body">
                <div class="tabs-wrapper">
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab-failed-ticket">
                            {{ Form::open(['method'=>'POST', 'id' => 'template-create'])}}
                            <div class="form-group">
                                <label for="name"> Название</label>
                                {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'rows' => 3]) }}
                            </div>
                            <div class="form-group">
                                <label for="message"> Тект сообщения</label>
                                {{ Form::textarea('message', null, ['class' => 'form-control', 'id' => 'message', 'rows' => 3]) }}
                            </div>
                            <div class="text-center">
                                {{Form::submit('Создать', ['class' => 'btn btn-success'])}}
                            </div>
                            {{ Form::close()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{route('sms')}}">Все шаблоны</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="all-plan-rates">
                                        <div class="col-lg-7">
                                            <div class="templates">
                                                @if($templates->count())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-hover all_lists">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-center">ID</th>
                                                                <th class="text-center">Название</th>
                                                                <th class="text-center">Тект сообщения</th>
                                                                <th class="text-center"></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($templates as $template)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        {{$template->id}}
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" id="category" data-type="text"
                                                                           data-pk="{{ $template->id }}"
                                                                           data-name="name"
                                                                           data-url="/ajax/sms/templates/name-edit"
                                                                           data-id="{{ $template->id }}"
                                                                           data-title="Введите новое название шаблона"
                                                                           class="editable editable-click template-name"
                                                                           data-original-title=""
                                                                           title="">{{$template->name}}</a>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" id="message" data-type="textarea"
                                                                           data-pk="{{ $template->id }}"
                                                                           data-name="name"
                                                                           data-url="/ajax/sms/templates/body-edit"
                                                                           data-id="{{ $template->id }}"
                                                                           data-title="Введите новый текст сообщения"
                                                                           class="editable editable-click template template-textarea"
                                                                           data-original-title=""
                                                                           title="">{{$template->body}}</a>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#"
                                                                           data-type="text"
                                                                           data-pk="{{ $template->id }}"
                                                                           data-title="Вы действительно хотите удалить шаблон?"
                                                                           data-id="{{ $template->id }}"
                                                                           data-url="/ajax/sms/templates/destroy"
                                                                           class="editable editable-click table-link danger  destroy-template">Удалить</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-4">
                                            <div>
                                                <div class="alert alert-success fade in">
                                                    <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">×
                                                    </button>
                                                    <i class="fa fa-check-circle fa-fw fa-lg"></i>
                                                    <strong>Заполнение сообщения</strong></br>
                                                    Для добавления номера заказа/трека почылки в текст сообщения
                                                    установите курсор мыши в нужное положение в тексте
                                                    и выберите Алиас с приведенными ниже значениями.
                                                    Значения №заказа и №ТТН будут добавлены на странице заказа.
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover all_lists">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">Алиас</th>
                                                        <th class="text-center">Значение</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @order">@order</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Номер заказа
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @track">@track</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Трек посылки
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @surname">@surname</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Фамилия
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @name">@name</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Имя
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @middle">@middle</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Отчество
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @phone">@phone</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Телефон
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @poshta">@poshta</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Курьерская служба
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @postal_code">@postal_code</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Почтовый индекс
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @region">@region</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Область
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @district">@district</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Район
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @locality">@locality</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Населенный пункт
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @street">@street</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Улица
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @house">@house</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Дом
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a title="Установите курсор мыши в нужное положение в тексте"
                                                               class="alias-link" type="button"
                                                               data-value="  @flat">@flat</a>
                                                        </td>
                                                        <td class="text-center">
                                                            Квартира
                                                        </td>
                                                    </tr>
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
    </div>
    {{ $templates->links() }}
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/sms/templates.js') }}"></script>
    <script>
        $(document).ready(function () {
            //toggle `popup` / `inline` mode
            $.fn.editable.defaults.mode = 'popup';
            $.fn.editable.defaults.params = function (params) {
                params.id = $(".category").data("data-id");
                return params;
            };

            //make username editable
            $('.destroy-template').editable({
                type: 'none',
                escape: true,
                title: 'Вы действительно хотите удалить шаблон?',
                tpl: '',
                success: function (response) {
                    if (response.pk) {
                        var parent = $("a[data-pk='" + Number(response.pk) + "']").parents('tr');
                        parent.fadeOut(400);
                        setTimeout(function () {
                            parent.remove();
                        }, 400);
                    }
                }
            });

            $('.template-name').editable({
                escape: true,
                title: 'Редактировать название',
            });

            $('.template').editable({

                escape: true,
                title: 'Редактировать шаблон',
                onblur: "ignore"
            });
        });
    </script>
@stop
{{--@php--}}
{{--use App\Models\Api\Posts\Viettel;--}}
{{--use App\Models\Api\Posts\Novaposhta;--}}
{{--//Viettel::track();--}}
{{--Novaposhta::track();--}}
{{--@endphp--}}

