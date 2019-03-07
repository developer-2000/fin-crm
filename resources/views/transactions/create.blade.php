@extends('layouts.app')

@section('title')@lang('warehouses.transaction-create')@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/select2.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::asset('css/storages-common.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::asset('css/transaction_create.css') }}" type="text/css"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/storages/transaction_create.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('index') }}"> @lang('general.main')</a></li>
                <li><a href="{{ route('transactions') }}"> @lang('transactions.title')</a></li>
                <li class="active"><span> @lang('general.create')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('general.create')</h1>
            </div>
        </div>
    </div>

    <div class="alert alert-success" id="message" style="{!! session('message') ? '' : 'display:none' !!}">
        <i class="fa fa-check-circle fa-fw fa-lg"></i>
        <span>{{ session('message') ?: '' }}</span>
    </div>

    <div class="alert alert-danger" id="error" style="{!! session('error') ? '' : 'display:none' !!}">
        <i class="fa fa-exclamation fa-fw fa-lg"></i>
        <span>{{ session('error') ?: '' }}</span>
    </div>

    <div class="main-box clearfix">
        <div class="main-box-body clearfix content">
            <div class="row" style="padding-top:32px;">
                {!! Form::open([
                    'route' => ['transaction-store'],
                    'method' => 'post',
                    'id' => 'transaction_form'
                ]) !!}

                <div class="col-sm-4">
                    @if ($projects->isEmpty())

                    @elseif ($projects->count() == 1)
                        <input id="project_id" type="hidden" name="project_id"
                               data-url="{{ route('transaction-get-storages') }}"
                               value="{{ $projects[array_keys($projects)[0]]->id }}" />
                        <h3 class="h3-create-moving">{{ $projects[array_keys($projects)[0]]->name }}</h3>
                    @else
                        <div class="form-group form-group-select2">
                            {!! Form::label('project_id', trans('general.project'), ['class' => 'storage-label']) !!}
                            {!! Form::select(
                                'project_id',
                                $projects,
                                null,
                                [
                                    'placeholder' => '--' . trans('general.select') . '--',
                                    'id' => 'project_id',
                                    'class' => 'storage',
                                    'style' => 'width:100%;',
                                    'data-url' => route('transaction-get-storages')
                                ]
                            ) !!}
                        </div>
                    @endif

                    <div id="for_storage" style="margin-bottom:20px;">
                        @if ($storage)
                            <input id="storage_id" type="hidden" name="storage_id"
                                   data-url="{{ route('moving-get-products') }}"
                                   value="{{ $storage->id }}" />
                            <h3 class="h3-create-moving">{{ $storage->name }}</h3>
                        @endif
                    </div>

                    <div id="for_product">
                    </div>
                </div>

                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('comment', trans('general.comment'), ['class' => 'storage-label']) !!}
                        {!! Form::textarea('comment', null, [
                            'class' => 'form-control', 'id' => 'comment', 'style' => 'height:76px;'
                        ]) !!}
                    </div>

                    <div class="input-group" style="width:calc(50% - 8px); float:left;">
                        <span class="input-group-addon"> @lang('general.count'):</span>
                        <span class="input-group-addon" id="current_amount">0</span>
                        <span class="input-group-btn" data-toggle="buttons">
                            <label class="btn btn-primary">
                                <input type="radio" name="amount_sign" value="+"  id="amount_plus" />
                                <i class="fa fa-plus" style="margin-top:4px;"></i>
                            </label>
                            <label class="btn btn-primary">
                                <input type="radio" name="amount_sign" value="-" id="amount_minus" />
                                <i class="fa fa-minus" style="margin-top:4px;"></i>
                            </label>
                        </span>
                        {!! Form::number('amount', 0, ['id'=> 'amount', 'class' => 'form-control', 'min' => 0]) !!}

                    </div>

                    <div class="input-group" style="width:calc(50% - 8px); float:right;">
                        <span class="input-group-addon"> @lang('general.hold'):</span>
                        <span class="input-group-addon" id="current_hold">0</span>
                        <span class="input-group-btn" data-toggle="buttons">
                            <label class="btn btn-primary">
                                <input type="radio" name="hold_sign" value="+"  id="hold_plus" />
                                <i class="fa fa-plus" style="margin-top:4px;"></i>
                            </label>
                            <label class="btn btn-primary" id="hold_minus">
                                <input type="radio" name="hold_sign" value="-" id="hold_minus" />
                                <i class="fa fa-minus" style="margin-top:4px;"></i>
                            </label>
                        </span>
                        {!! Form::number('hold', 0, ['id'=> 'hold', 'class' => 'form-control', 'min' => 0]) !!}
                    </div>

                    {!! Form::submit(trans('general.send'), [
                        'class' => 'btn btn-success',
                        'style' => 'width:100%; margin-top:24px;',
                        'id' => 'button'
                    ]) !!}
                </div>

                {!! Form::close() !!}
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
