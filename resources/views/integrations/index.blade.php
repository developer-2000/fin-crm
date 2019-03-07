@extends('layouts.app')
@section('title') Все интеграции@stop
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
                <li class="active"><span>Все интеграции</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{route('posts.settings')}}">Все интеграции</a>
                        </li>
                        {{--<li class="">--}}
                        {{--<a href="{{route('posts-categories')}}">Категории</a>--}}
                        {{--</li>--}}
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active " id="statistics">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        @if ($integrations)
                                            <div class="main-box-body clearfix"
                                                 style="margin-top: 20px;padding: 0 0 20px 0;">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center">ID</th>
                                                            <th class="text-center">Название</th>
                                                            {{--<th class="">Активность</th>--}}
                                                            <th class="text-center"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($integrations as $integration)
                                                            <tr>
                                                                <td class="text-center">{{$integration->id}}</td>
                                                                <td class="text-center">
                                                                    {{$integration->name}}
                                                                </td>
                                                                {{--<td class="text-center">--}}
                                                                    {{--<div class="checkbox-nice checkbox">--}}
                                                                        {{--{{ Form::checkbox('activity', $integration->id, $integration->integration_status == \App\Models\TargetConfig::INTEGRATION_ACTIVE, ['id' => 'activate_'.$integration->id, 'class' => 'activity']) }}--}}
                                                                        {{--{{ Form::label('activate_'.$integration->id, ' ') }}--}}
                                                                    {{--</div>--}}
                                                                {{--</td>--}}
                                                                <td class="text-center">
                                                                    <a href="{{route('integrations-edit',  $integration->alias)}}"
                                                                       class="btn btn-primary btn-xs"><i
                                                                                class="fa fa-cog"></i></a>
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
    {{ $integrations->links() }}
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script>
        $(function () {
            $('.activity').on('change', changeActivity);
        });

        function changeActivity() {
            var postId = $(this).val();
            if ($(this).prop('checked')) {
                var status = 'active';
                $.getJSON('/post/' + postId + '/change-activity/' + status, function (json) {
                });
            }
            else {
                status = 'inactive';
                $.getJSON('/post/' + postId + '/change-activity/' + status, function (json) {
                });
            }
        }
    </script>
@stop
