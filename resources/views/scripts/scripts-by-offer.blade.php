@extends('layouts.app')
@section('title') Все скрипты@stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
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
                <li class="active"><span>Все скрипты</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Все скрипты</h1>
                @php
                    $offerId = request()->segment(2);
                @endphp
                <div class="pull-right top-page-ui">
                    <a href="/offer/{{$offerId}}/script/add" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Добавить скрипт</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{ route('scripts-offers', $offerId) }}">Все скрипты</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        @if ($scripts)
                                            <div class="main-box-body clearfix"
                                                 style="margin-top: 20px;padding: 0 0 20px 0;">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover all_lists">
                                                        <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th class="text-center">Дата создания</th>
                                                            <th class="text-center">Название</th>
                                                            <th class="text-center">Оффер</th>
                                                            <th class="text-center">Комментарий</th>
                                                            <th class="text-center">Активность</th>
                                                            <th class="text-center"></th>
                                                            <th class="text-center"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($scripts as $script)
                                                            <tr>
                                                                <td class="text-center">{{$script->id}}</td>
                                                                <td class="text-center">
                                                                    {{$script->created_at->format('m/d/Y')}}
                                                                </td>
                                                                <td class="text-center">
                                                                    {{$script->name}}
                                                                </td>
                                                                <td class="text-center">
                                                                    @if(!empty($script->scriptOffers))
                                                                        @foreach($script->scriptOffers as $offer)
                                                                            @if(!empty($offer))
                                                                                @php
                                                                                    $offer = \App\Models\Offer::where('id', $offer)->first();
                                                                                        $prefix = '';
                                                                                    if ($offer->project_id == 1) {
                                                                                    $prefix = 'UM::';
                                                                                    } elseif ($offer->project_id == 2) {
                                                                                    $prefix = 'BM::';
                                                                                    }
                                                                                     elseif ($offer->project_id == 3) {
                                                                                    $prefix = 'HP::';
                                                                                    }
                                                                                @endphp
                                                                                <span class="crm_id">{{$prefix . $offer->name}}
                                                                                    <br></span>
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        {{'N/A'}}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    {{$script->comment}}
                                                                </td>
                                                                <td>
                                                                    <div class="checkbox-nice checkbox">
                                                                        @php
                                                                            if($script->status == 'active'){
                                                                            $status = true;
                                                                            }else{
                                                                                $status = false;}
                                                                        @endphp
                                                                        {{ Form::checkbox('activity', $script->id, $status, ['id' => 'activate_'.$script->id, 'class' => 'activate_script_row']) }}
                                                                        {{ Form::label('activate_'.$script->id, ' ') }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <a href="{{route('scripts-edit', $script->id)}}"
                                                                       class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="#"
                                                                       data-type="text" data-pk="1"
                                                                       data-title="Вы действительно хотите удалить блок?"
                                                                       data-id="{{ $script->id }}"
                                                                       class="editable editable-click table-link danger  delete-block">Удалить</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
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
    {{ $scripts->links() }}
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/scripts/script-delete.js') }}"></script>
    <script src="{{ URL::asset('js/scripts/scripts.js') }}"></script>
    <script>

        $(document).ready(function () {
            //toggle `popup` / `inline` mode
            $.fn.editable.defaults.mode = 'popup';

            //make username editable
            $('.delete-block').editable({
                type: 'none',
                escape: true,
                pk: 1,
                title: 'Вы действительно хотите удалить скрипт?',
                tpl: '',
            });
        });
    </script>
@stop

