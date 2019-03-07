@extends('layouts.app')

@section('title')@lang('warehouses.moving-create')@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/select2.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::asset('css/storages-common.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::asset('css/moving_create.css') }}" type="text/css"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/storages/moving_create.js') }}"></script>
@stop

@section('content')

    <style>
        #s2id_subproj, .rel, #s2id_my_storage{
            position: relative;
        }
       .above{
           position: absolute;
           top: 0px;
           left: 0px;
           width: 100%;
           height: 100%;
           background-color: rgba(225, 228, 255, 0.5);
           cursor: wait;
       }
    </style>



    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('index') }}"> @lang('general.main')</a></li>
                <li><a href="{{ route('movings') }}"> @lang('movings.title')</a></li>
                <li class="active"><span> @lang('movings.create')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('movings.create')</h1>
            </div>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle fa-fw fa-lg"></i>
            {{ session('message') }}
        </div>
    @endif

    {{-- ОТОБРАЖАЕТ ВЫБОР ПРОЕКТОВ В СОЗДАНИИ ПЕРЕМЕЩЕНИЯ НА СКЛАДЕ--}}
    <div class="main-box clearfix">
        <div class="main-box-body clearfix content">
            <div class="row" style="padding-top:32px;">

                {{-- если колекция пуста --}}
                @if ($projects->isEmpty())

                {{-- если в колекции 1 проект - тоесть я юзер этого проекта --}}
                @elseif ($projects->count() == 1)
                    <div class="col-sm-4">
                        <input id="project_id" type="hidden" name="project_id"
                               data-url="{{ route('moving-get-storages') }}" value="{{ $projects[0]->id }}" />
                        <h3 class="h3-create-moving">{{ $projects[0]->name }}</h3>
                    </div>

                {{-- если в колекции больше 1 проекта --}}
                @else
                    <div class="col-sm-4 form-group form-group-select2">
                        {!! Form::label('project_id', trans('general.project'), ['class' => 'storage-label']) !!}
                        {!! Form::select( 'project_id', $projects->pluck('name', 'id'), null,
                            [
                                'placeholder' => '--' . trans('general.select') . '--',
                                'id' => 'project_id',
                                'class' => 'storage',
                                'style' => 'width:100%;',
                                'data-url' => route('moving-get-storages')
                            ]
                        ) !!}
                    </div>
                @endif

                <div id="block_sel">
                    <div class="col-sm-4" id="for_sub" data-url="{{ route('moving-get-my-storages') }}"> </div>
                    <div class="col-sm-4" id="for_sender" data-url="{{ route('moving-get-to-storages') }}"> </div>
                    <div class="col-sm-4" id="for_receiver" style="margin: 40px 0px;"> </div>
                </div>

            </div>
            <div id="for_products">
            </div>
            <div id="for_errors" style="display:none; padding-top:12px;">
                <div class="alert alert-warning text-center">
                    <i class="fa fa-exclamation-circle fa-fw fa-lg"></i>
                    <span></span>
                </div>
            </div>
        </div>
    </div>

@stop
