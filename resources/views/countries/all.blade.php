@extends('layouts.app')

@section('title') @lang('general.countries') @stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/custom-onoffswitch.css') }}" type="text/css"/>
@stop

@section('jsBottom')
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ URL::asset('js/countries/countries.js') }}"></script>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><span> @lang('general.countries')</span></li>
            </ol>
        </div>
    </div>


    <div class="alert alert-success" style="display:none;">
        <i class="fa fa-check-circle fa-fw fa-lg"></i>
    </div>

    <div class="alert alert-danger" id="error" style="display:none;">
        <i class="fa fa-exclamation fa-fw fa-lg"></i>
    </div>

    <div class="clearfix">
        <h1 class="pull-left"> @lang('general.countries'):</h1>
    </div>

    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            <div class="table-responsive">
                <table id="countries-table" class="table table-striped table-hover">
                    <thead>
                    <tr style="border-bottom:none;">
                        <th></th>
                        <th><span>code</span></th>
                        <th><span> @lang('general.flag')</span></th>
                        <th><span> @lang('general.name')</span></th>
                        <th><span> @lang('general.currency')</span></th>
                        <th><span> @lang('general.per-one')</span></th>
                        <th><span> @lang('general.use')</span></th>
                    </tr>
                    </thead>

                    @if($countries->isNotEmpty())
                        <tbody>
                        @foreach($countries as $country)
                            <tr data-code="{{ $country->code }}" data-url="{{ route('country-replace') }}">
                                <td><i class="handle fa fa-arrows-v"></i></td>
                                <td><span>{{ $country->code }}</span></td>
                                <td>
                                    @if ($country->flag)
                                        <img class="country-flag" src="{{ asset('img/flags/' . $country->flag) }}" />
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $country->name }}</span><br />
                                    {{-- <span{!! trans('countries.' . $country->name) ==  'countries.' . $country->name ? ' style="color:red;"' : '' !!}>
                                        @lang('countries.' . $country->name)
                                    </span> --}}
                                </td>

                                <td>
                                    <span>
                                        {{ $country->currency ?: '' }}
                                        {{ $country->currency && $country->currency_symbol && ($country->currency != $country->currency_symbol) ? ' = ' : '' }}
                                        {{ $country->currency_symbol && ($country->currency != $country->currency_symbol) ? $country->currency_symbol : '' }}
                                    </span><br />
                                    <span>
                                        {{ $country->currency_name ?: '' }}
                                        {{ $country->currency_name && $country->currency_sub_unit ? ' . ' : '' }}
                                        {{ $country->currency_sub_unit ?: '' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <span>{{ ($country->exchange_rate > 0) ? $country->exchange_rate : '-' }}</span>
                                </td>
                                <td>
                                    <div class="onoffswitch">
                                        {!! Form::checkbox('use', $country->code, $country->use, [
                                            'data-url' => route('country-use'),
                                            'data-code' => $country->code,
                                            'class' => 'onoffswitch-checkbox country-use',
                                            'id' => 'use-' . $country->code
                                        ]) !!}
                                        <label class="onoffswitch-label" for="use-{{ $country->code }}">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>

@stop
