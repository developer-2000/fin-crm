@extends('layouts.app')

@section('title')@lang('transactions.title')@stop

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/select2.css') }}" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/storages-common.css') }}">
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/storages/transactions.js') }}"></script>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li><span> @lang('transactions.title')</span></li>
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
        <h1 class="pull-left"> @lang('transactions.found') (<span class="badge">{{ $transactions->total() }}</span>)</h1>

        @can('manual_transaction')
        <a href="{{ route('transaction-create') }}" class="btn btn-primary pull-right">
            <i class="fa fa-plus"></i>
            @lang('transactions.manual-create')
        </a>
        @endcan
    </div>

    <div class="main-box clearfix">
        <div class="main-box-body clearfix" style="padding-top:12px;">
            <div class="row">
            {!! Form::open(['route' => ['transactions'], 'method' => 'get', 'id' => 'transactions_form']) !!}
                {!! Form::token() !!}

                <div class="form-group col-sm-2 form-horizontal" style="width:15%">
                    {!! Form::number('id',
                        \Request::get('id', null),
                        ['placeholder' => '[id]', 'id' => 'id', 'min' => 0, 'class' => 'form-control']
                    ) !!}
                </div>

                <div class="form-group col-sm-2 form-horizontal" style="width:15%">
                    {!! Form::number('moving_id',
                        \Request::get('moving_id', null),
                        ['placeholder' => '['.trans('transactions.moving').']', 'id' => 'moving_id', 'min' => 0, 'class' => 'form-control']
                    ) !!}
                </div>

                <div class="form-group col-sm-3 form-horizontal" style="width:30%">
                    {!! Form::text('product_id', \Request::get('product_id', null), [
                        'placeholder' => '[' . trans('general.product') . ']',
                        'id' => 'product_id',
                        'product_name' => $product_name,
                        'data-url' => route('transaction-get-products-list2'),
                        'style' => 'width:100%',
                    ]) !!}
                </div>

                <div class="form-group col-sm-3 form-horizontal" style="width:20%">
                    {!! Form::select(
                        'user_id',
                        $users,
                        \Request::get('user_id', null),
                        ['placeholder' => '['.trans('general.user').']', 'id' => 'user_select', 'style' => 'width:100%;']
                    ) !!}
                </div>

                <div class="form-group col-sm-3 form-horizontal" style="width:20%">
                    {!! Form::select(
                        'type',
                        $types,
                        \Request::get('type', null),
                        ['placeholder' => '['.trans('general.type').']', 'id' => 'type_select', 'style' => 'width:100%;']
                    ) !!}
                </div>

                <div class="form-group col-sm-2 form-horizontal" style="width:15%">
                    {!! Form::number(
                        'amount1',
                        \Request::get('amount1', null),
                        ['placeholder' => '['.trans('warehouses.amount-before').']', 'id' => 'amount1', 'min' => 0, 'class' => 'form-control']
                    ) !!}
                </div>

                <div class="form-group col-sm-2 form-horizontal" style="width:15%">
                    {!! Form::number(
                        'amount2',
                        \Request::get('amount2', null),
                        ['placeholder' => '['.trans('warehouses.amount-after').']', 'id' => 'amount2', 'min' => 0, 'class' => 'form-control']
                    ) !!}
                </div>

                <div class="form-group col-sm-2 form-horizontal" style="width:15%">
                    {!! Form::number(
                        'hold1',
                        \Request::get('hold1', null),
                        ['placeholder' => '['.trans('warehouses.hold-before').']', 'id' => 'hold1', 'min' => 0, 'class' => 'form-control']
                    ) !!}
                </div>

                <div class="form-group col-sm-2 form-horizontal" style="width:15%">
                    {!! Form::number(
                        'hold2',
                        \Request::get('hold2', null),
                        ['placeholder' => '['.trans('warehouses.hold-after').']', 'id' => 'hold2', 'min' => 0, 'class' => 'form-control']
                    ) !!}
                </div>

                @if (!auth()->user()->project_id)
                    <div class="form-group col-sm-3 form-horizontal" style="width:20%">
                        {!! Form::select(
                            'pj_id',
                            $projects,
                            \Request::get('pj_id', null),
                            ['placeholder' => '['.trans('general.project').']', 'id' => 'project_select', 'style' => 'width:100%;']
                        ) !!}
                    </div>
                @endif

                @if (!auth()->user()->sub_project_id)
                    <div class="form-group col-sm-3 form-horizontal" style="width:20%">
                        {!! Form::select(
                            'subproject_id',
                            $subprojects,
                            \Request::get('subproject_id', null),
                            ['placeholder' => '['.trans('general.subproject').']', 'id' => 'subproject_select', 'style' => 'width:100%;']
                        ) !!}
                    </div>
                @endif

                <div style="clear:both;"></div>

                <div class="form-group col-sm-3" style="width:22.5%">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input class="form-control" id="created_at_start" type="text"
                               data-toggle="tooltip" name="created_at_start"
                               data-placement="bottom"
                               placeholder=" @lang('general.date-start')"
                               value="{{ \Request::get('created_at_start', null) }}">
                    </div>
                </div>
                <div class="form-group col-sm-3" style="width:22.5%">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input class="form-control" id="created_at_end" type="text"
                               data-toggle="tooltip" name="created_at_end"
                               data-placement="bottom"
                               placeholder=" @lang('general.date-end')"
                               value="{{ \Request::get('created_at_end', null) }}">
                    </div>
                </div>
                <div class="col-sm-6" style="width:55%">
                    <div class="input-group" style="width:100%;">
                        <div class="btn-group" data-toggle="buttons" id="date_template" style="width:100%;">
                            <label class="btn btn-default pattern_date">
                                <input type="radio" name="date_template" value="1"> @lang('general.today')
                            </label>
                            <label class="btn btn-default pattern_date">
                                <input type="radio" name="date_template" value="5"> @lang('general.yesterday')
                            </label>
                            <label class="btn btn-default pattern_date">
                                <input type="radio" name="date_template" value="9"> @lang('general.week')
                            </label>
                            <label class="btn btn-default pattern_date">
                                <input type="radio" name="date_template" value="10"> @lang('general.month')
                            </label>
                            <label class="btn btn-default pattern_date pattern_date_big">
                                <input type="radio" name="date_template" value="2"> @lang('general.last-month')
                            </label>
                        </div>
                    </div>
                </div>

                <div class="btns_filter">
                    <button class="btn btn-warning" type="reset" id="clear_form" data-url="{{ route('transactions') }}">
                        <i class="fa fa-times"></i>
                        @lang('general.reset')
                    </button>
                    <button class="btn btn-success" type="submit" id="submit">
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
                <table id="table_transactions" class="table table-striped table-hover storage-table">
                    <thead>
                    <tr style="border-bottom:none;">
                        <th>
                            <span>ID</span><br />
                            {{-- @include('transactions.sort-links', ['sortLinks' => $sortLinks, 'name' => 'id']) --}}
                        </th>
                        @if (!auth()->user()->project_id)
                        <th>
                            <span> @lang('general.project')</span><br />
                            @if (!auth()->user()->sub_project_id)
                            <span> @lang('general.subproject')</span>
                            @endif
                        </th>
                        @endif
                        <th>
                            <span> @lang('transactions.moving')</span><br />
                            {{-- @include('transactions.sort-links', ['sortLinks' => $sortLinks, 'name' => 'moving_id']) --}}
                        </th>
                        <th>
                            <span> @lang('general.product')</span><br />
                            {{-- @include('transactions.sort-links', ['sortLinks' => $sortLinks, 'name' => 'product_name']) --}}
                        </th>
                        <th>
                            <span> @lang('general.user')</span><br />
                            {{-- @include('transactions.sort-links', ['sortLinks' => $sortLinks, 'name' => 'user_name']) --}}
                        </th>
                        <th>
                            <span> @lang('general.count')</span>
                        </th>
                        <th>
                            <span> @lang('general.hold')</span>
                        </th>
                        <th>
                            <span> @lang('general.type')</span><br />
                            {{-- @include('transactions.sort-links', ['sortLinks' => $sortLinks, 'name' => 'type']) --}}
                        </th>
                        <th>
                            <span> @lang('general.date-created')</span><br />
                            {{-- @include('transactions.sort-links', ['sortLinks' => $sortLinks, 'name' => 'created_at']) --}}
                        </th>
                        <th></th>
                    </tr>
                    </thead>

                    @if($transactions->isNotEmpty())
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td><span>{{ $transaction->id }}</span></td>
                                @if (!auth()->user()->project_id)
                                <td>
                                    <span>{{ $transaction->project_name }}</span><br />
                                    @if (!auth()->user()->subproject_id)
                                    <span>{{ $transaction->subproject_name }}</span>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    <a href="{{ route('moving', $transaction->moving_id) }}" target="_blank">
                                        @if ($transaction->moving_id)
                                            #{{ $transaction->moving_id }}
                                        @else
                                            -
                                        @endif
                                    </a>
                                </td>
                                <td><span>{{ $transaction->product_name }}</span></td>
                                <td><span>{{ $transaction->user_name }}</span></td>
                                <td style="text-align:center;">
                                    @if ($transaction->amount1 || $transaction->amount2)
                                    <span style="white-space: nowrap">
                                        {{ $transaction->amount1 }}
                                        <i class="fa  fa-angle-double-right"></i>
                                        {{ $transaction->amount2 }}
                                    </span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    @if ($transaction->hold1 || $transaction->hold2)
                                    <span style="white-space: nowrap">
                                        {{ $transaction->hold1 }}
                                        <i class="fa  fa-angle-double-right"></i>
                                        {{ $transaction->hold2 }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="label label-success label-transaction-{!! $transaction->type !!}">
                                       {{\App\Models\StorageTransaction::$types[$transaction->type]}}
                                    </span>
                                </td>
                                <td>
                                    <span class="trans-time">
                                        {{ $transaction->created_at->format('H:i:s') }}
                                    </span>
                                    <span class="trans-date">
                                        {{ $transaction->created_at->format('d.m.Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($transaction->comment)
                                    <div style="position:relative;">
                                        <i class="fa fa-comment-o popup_trigger"></i>
                                        <div class="popup_shower hide">

                                            <div class="conversation-wrapper">
                                                <div class="conversation-content">
                                                    <div class="conversation-inner">
                                                        <div class="conversation-item clearfix item-left">
                                                            <div class="conversation-user">
                                                                <img src="{{ URL::asset('img' . $transaction->comment_user_photo) }}" alt="" />
                                                            </div>
                                                            <div class="conversation-body">
                                                                <div class="name">
                                                                    {{ $transaction->comment_user }}
                                                                </div>
                                                                <div class="time hidden-xs">
                                                                    {{ \Carbon\Carbon::createFromTimestamp($transaction->comment_date)->toDateTimeString() }}
                                                                </div>
                                                                <div class="text">
                                                                    {{ $transaction->comment }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            </div>

            @if($transactions->isEmpty())
                <p> @lang('general.no-results')</p>
            @endif
        </div>
    </div>

    {!! $transactions->appends($appendPage)->links() !!}

@stop
