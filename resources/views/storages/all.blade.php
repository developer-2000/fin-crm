@extends('layouts.app')

@section('title')@lang('warehouses.title')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.min.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('css/select2.css') }}" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/storages-common.css') }}">
@stop

@section('jsBottom')
    <script src="{{ URL::asset('assets/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/storages/remainders.js') }}"></script>
@stop

@section('content')

    @php $status_colors = ['danger', 'warning', 'success', 'default']; @endphp

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('warehouses.storage-by-product')</span></li>
            </ol>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle fa-fw fa-lg"></i>
            {{ session('message') }}
        </div>
    @endif

    <div class="clearfix">
        <h1 class="pull-left">
            @lang('warehouses.found') : (<span class="badge">{{ $remainders->total() }}</span>)
        </h1>
    </div>

    <div class="main-box clearfix">
        <div class="main-box-body clearfix" style="padding-top:12px;">
            <div class="row">
                {!! Form::open(['route' => ['storages'], 'method' => 'get', 'id' => 'remainders_form']) !!}
                {!! Form::token() !!}

                @php
                if (auth()->user()->project_id) {
                    if (auth()->user()->sub_project_id) {
                        $class = ' col-sm-4 ';
                        $style = '';
                    } else {
                        $class = ' col-sm-3 ';
                        $style = '';
                    }
                } else {
                    $class = ' col-sm-3 ';
                    $style = ' width:20%;';
                }
                @endphp


                @if (auth()->user()->project_id)
                    <input type="hidden" name="project_id" id="project_id" value="{{ auth()->user()->project_id }}" />
                @else
                    <div class="form-group {{ $class }}" style="{{ $style }}">
                        {!! Form::select('pj_id', $projects, \Request::get('pj_id', null), [
                            'placeholder' => '['.trans('general.project').']',
                            'id' => 'project_id',
                            'style' => 'width:100%;'
                        ]) !!}
                    </div>
                @endif

                @if (auth()->user()->sub_project_id)
                    <input type="hidden" name="storage_id" id="storage_id" value="{{ auth()->user()->sub_project_id }}" />
                @else
                    <div class="form-group {{ $class }}" style="{{ $style }}">
                        {!! Form::select('sp_id', $storages, \Request::get('sp_id', null), [
                            'placeholder' => '['.trans('general.storage').']',
                            'id' => 'storage_id',
                            'style' => 'width:100%;'
                        ]) !!}
                    </div>
                @endif

                <div class="form-group {{ $class }}" style="{{ $style }}">
                    {!! Form::text('product_id', \Request::get('product_id', null), [
                        'placeholder' => '[' . trans('general.product') . ']',
                        'id' => 'product_id',
                        'product_name' => $product_name,
                        'data-url' => route('storage-get-products-list'),
                        'style' => 'width:100%',
                    ]) !!}
                </div>

                <div class="form-group {{ $class }}" style="{{ $style }}">
                    {!! Form::text('date', \Request::get('date', date('d.m.Y H:i:s')), [
                        'id' => 'date',
                        'placeholder' => '[' . trans('general.date') . ']',
                        'class' => 'form-control',
                        'style' => 'width:100%'
                    ]) !!}
                </div>

                <div class="form-group {{ $class }}" style="{{ $style }}">
                    {!! Form::select('user_id', $users, \Request::get('user_id', null), [
                        'placeholder' => '['.trans('general.user').']',
                        'id' => 'form_user_id',
                        'style' => 'width:100%;'
                    ]) !!}
                </div>

                <div class="btns_filter">
                    <button class="btn btn-warning" type="reset" id="clear" data-url="{{ route('storages') }}"
                            style="width:calc(50% - 15px);">
                        <i class="fa fa-times"></i>
                        @lang('general.reset')
                    </button>
                    <button class="btn btn-success" type="submit" id="button" style="width:calc(50% - 15px);">
                        <i class="fa fa-filter"></i>
                        @lang('general.search')
                    </button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            <div class="table-responsive">
                <table id="table_movings" class="table table-striped table-hover storage-table">
                    <thead>
                    <tr>
                        <th><span> @lang('general.project') / @lang('general.storage')</span></th>
                        <th><span> @lang('general.product')</span></th>
                        <th><span> @lang('general.count')</span></th>
                        <th><span> @lang('warehouses.hold')</span></th>
                        <th>
                            <span title="{{ trans('warehouses.date-time-change') }}">
                                @lang('general.date')
                            </span>
                        </th>
                        <th>
                            <span title="{{ trans('warehouses.last-changer') }}">
                                @lang('general.user')
                            </span>
                        </th>
                    </tr>
                    </thead>

                    @if($remainders->isNotEmpty())
                        <tbody>
                        @foreach($remainders as $remainder)
                            <tr>
                                <td><span>{{ $remainder->pj_name }} / {{ $remainder->sp_name }}</span></td>
                                <td><span>{{ $remainder->product_name }} [id: {{ $remainder->product_id }}]</span></td>
                                <td><span>{{ $remainder->amount }}</span></td>
                                <td><span>{{ $remainder->hold }}</span></td>
                                <td>
                                    <span class="trans-time">
                                        @if ($remainder->date)
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $remainder->date)->format('H:i:s') }}
                                        @endif
                                    </span>
                                    <span class="trans-date">
                                        @if ($remainder->date)
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $remainder->date)->format('d.m.Y') }}
                                        @endif
                                    </span>
                                </td>
                                <td><span>{{ $remainder->user_name }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            </div>

            @if($remainders->isEmpty())
                <p> @lang('warehouses.no-results')</p>
            @endif
        </div>
    </div>

    {!! $remainders->appends($appendPage)->links() !!}

@stop
