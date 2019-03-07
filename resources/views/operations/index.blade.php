@extends('layouts.app')

@section('title')Операции@stop

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

        body {
            color: grey;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/operations/index.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Операции введенные вручную</span></li>
            </ol>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url() }}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            <div class="main-box-body clearfix">
                                <br>
                                <div class="row">
                                    @if (isset($permissions['filter_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="project" class="col-sm-4 control-label">Проект</label>
                                            <div class="col-sm-8">
                                                @if(!auth()->user()->project_id)
                                                    <input id="project"
                                                           data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                           class="project " name="project[]"
                                                           value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                           style="width: 100%">
                                                @else
                                                    <input type="hidden" id="project"
                                                           class="project " name="project[]"
                                                           value="{{auth()->user()->project_id}}">
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="sub_project" class="col-sm-4 control-label">Под пректы</label>
                                            <div class="col-sm-8">
                                                <input id="sub_project"
                                                       data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                                       class="sub_project " name="sub_project[]"
                                                       value="{{$dataSubProject ?? NULL}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <input class="btn btn-success" type="submit" name="button_filter" value='Фильтровать'/>
                        <a href="{{ route('operations') }}" class="btn btn-warning" type="submit">Сбросить
                            фильтр</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        @if ($operations)
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Дата</th>
                                    <th>Тип</th>
                                    <th>Заказ</th>
                                    <th>Инициатор</th>
                                    <th>Комментарий</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($operations as $operation)
                                    <tr>
                                        <td>{{$operation->id}}</td>
                                        <td>
                                            {{$operation->created_at}}
                                        </td>
                                        <td>
                                            {{$operation->type}}
                                        </td>
                                        <td>
                                            <a href="{{ route('order-sending', $operation->order_id) }}"
                                               class="crm_id">{{$operation->order_id}}</a>
                                        </td>
                                        <td>
                                            {{$operation->user->name .' '. $operation->user->surname}}
                                        </td>
                                        <td>
                                            {{$operation->comment}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-center">{{$operations->links()}} </div>
        </div>
    </div>
@stop