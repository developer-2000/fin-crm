@extends('layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/custom-onoffswitch.css') }}" />
@endsection

@section('jsBottom')
    <script src="{{ URL::asset('js/other/variables.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li class="active"><span>Переменные</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">Переменные</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="main-box no-header clearfix">
                <div class="main-box-body clearfix">
                    @if ($variables)
                        @foreach($variables as $variable)
                            <div class="row">
                                <div class="col-sm-8 col-xs-7">
                                    @php
                                        $title = '';
                                        switch ($variable->key) {
                                            case 'cron_nightShift' : {
                                                $title = 'Крон nightShift';
                                                break;
                                            }
                                            case 'cron_online' : {
                                                $title = 'Крон Online';
                                                break;
                                            }
                                            case 'cron_cold_calls' : {
                                                $title = 'Крон ColdCalls';
                                                break;
                                            }
                                            case 'cron_audit_operator' : {
                                                $title = 'Крон AuditOperator';
                                                break;
                                            }
                                            case 'cron_add_calls' : {
                                                $title = 'Крон AddCalls';
                                                break;
                                            }
                                            case 'cron_get_calls' : {
                                                $title = 'Крон GetCalls';
                                                break;
                                            }
                                            case 'cron_add_learning_calls' : {
                                                $title = 'Крон AddLearningCalls';
                                                break;
                                            }
                                            default : {
                                                $title = $variable->key;
                                            }
                                        }
                                    @endphp
                                    {{$title}}
                                </div>
                                <div class="col-sm-4 col-xs-5">
                                    <div class="onoffswitch">
                                        <input type="checkbox"
                                               name="{{$variable->key}}"
                                               class="onoffswitch-checkbox var_value"
                                               id="{{$variable->key}}"
                                               @if (!isset($permissions['change_variables']))
                                               disabled
                                               @endif
                                               @if ($variable->value)
                                                    checked
                                               @endif
                                        >
                                        <label class="onoffswitch-label" for="{{$variable->key}}">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection