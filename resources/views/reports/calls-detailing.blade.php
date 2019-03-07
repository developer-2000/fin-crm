@extends('layouts.app')

@section('title')Детализация звонков @stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop
@section('content')
    @if ($userCalls && isset($permissions['get_calls_page_order']))
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2>Звонки</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th class="text-center">Статус</th>
                                <th class="text-center">ФИО(ID)</th>
                                <th class="text-center">Дата</th>
                                <th class="text-center">Время разговора</th>
                                <th class="text-center">Trunk</th>
                                <th class="text-center">Запись разговора</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($userCalls as $ucKey => $ucvalue)
                                <tr>
                                    <td>
                                        {{ $ucKey + 1 }}
                                    </td>
                                    <td class="text-center">{{ $ucvalue->status }}</td>
                                    <td class="text-center">
                                        {{--@if ($ucvalue->status == 'Success' || $ucvalue->status == 'ShortCall')--}}
                                            {{--{{ $ucvalue->name }} {{ $ucvalue->surname }} ({{ $ucvalue->login }})--}}
                                        {{--@endif--}}
                                    </td>
                                    <td class="text-center">
                                        {{ date('Y-m-d H:i:s', $ucvalue->date) }}
                                    </td>
                                    <td class="text-center">
                                        {{--{{ dateProcessing($ucvalue->talk_time) }}--}}
                                    </td>
                                    <td class="text-center">
                                        {{ $ucvalue->trunk }}
                                    </td>
                                    <td class="text-center">
                                        @if ($ucvalue->status == 'Success' || $ucvalue->status == 'ShortCall')
                                            <?
                                            $url = route('get-call-by-name') . '?fileName=' . $ucvalue->file;
                                            $agent = $_SERVER['HTTP_USER_AGENT'];
                                            if (preg_match('/(OPR|Firefox)/i', $agent))
                                            {
                                                $output = '<p><a href="' . $url . '"><span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                            </span></a></p>';
                                            } else {
                                                $output = '
                                            <audio controls>
                                                <source src="' . $url . '" type="audio/mpeg">
                                            </audio>
                                    ';
                                            }
                                            echo $output?>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{ $userCalls->links() }}
@stop
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-timepicker.min.js') }}"></script>
@stop