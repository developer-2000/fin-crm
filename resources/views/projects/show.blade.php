@extends('layouts.app')

@section('title')Просмотр проекта@stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('projects')}}">Проекты</a></li>
                @if($project->parent_id)
                    <li><a href="{{route('project-show', $project->parent_id)}}">{{ $project->parent->name }}</a></li>
                @endif
                <li class="active"><span>Просмотр @if($project->parent_id) субпроекта @else проекта @endif</span></li>
            </ol>
            <h1>@if($project->parent_id) Субпроект @else Проект @endif {{ $project->name }}</h1>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle fa-fw fa-lg"></i>
            {{ session('message') }}
        </div>
    @endif

    @if(!$project->parent_id)

        <div class="main-box clearfix">
            <header class="main-box-header clearfix">
                <h2 class="pull-left">Субпроекты:</h2>
                <a href="{{ route('subproject-create', $project->id) }}" class="btn btn-primary pull-right">
                    <i class="fa fa-plus-circle"></i> Создать субпроект
                </a>
            </header>
            <div class="main-box-body clearfix">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <th class="text-center"><span>alias</span></th>
                        <th class="text-center"><span>name</span></th>
                        <th> Интеграция</th>
                        <th></th>
                        </thead>
                        <tbody>
                        @foreach($project->children as $subproject)
                            <tr>
                                <td class="text-center">
                                    {{ $subproject->alias }}
                                </td>
                                <td class="text-center">
                                    {{ $subproject->name }}
                                </td>
                                <td><a href="#" id="integration-{{$subproject->id}}"
                                       data-type="select2"
                                       data-pk="{{$subproject->id}}" data-value=""
                                       data-name="name"
                                       data-id="{{ $subproject->id }}"
                                       data-title="Добаление интеграции"
                                       class="editable editable-click integration"
                                       data-url="/ajax/integrations/save"
                                       style="color: gray;" data-original-title="" title=""
                                    >
                                        @if(isset($subproject->integrationValue->name))
                                            {{ $subproject->integrationValue->name }}
                                        @else
                                            {{'not selected'}}
                                        @endif
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('project-edit', [$subproject->id]) }}"
                                       class="pull-right btn btn-primary">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
    @endif

@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script>
        $(document).ready(function () {


            $.fn.editable.defaults.mode = 'popup';
            $.fn.editable.defaults.params = function (params) {
                params.id = $(".integration").data("data-id");
                return params;
            };
            var integrations = $('#integrationsData').val();
            var array = [];
            $.each(JSON.parse(integrations), function (e, val) {
                array.push(val);
            });

            $('.integration').each(function (e) {
                $(this).editable({
                    prepend: "not selected",
                    source: array,
                    select2: {
                        width: 200,
                        placeholder: 'Выберите интеграцию',
                        allowClear: true
                    }
                });
            });
        });
    </script>
@stop